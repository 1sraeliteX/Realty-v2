<?php

namespace App\Controllers;

class CommunicationController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        error_log("CommunicationController: After requireAuth, continuing execution");
        
        // Get pagination and filter parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $recipientId = $_GET['recipient_id'] ?? '';
        
        // Build query
        error_log("Building query");
        $where = ["communications.deleted_at IS NULL"];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(communications.subject LIKE ? OR communications.message LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($type)) {
            $where[] = "communications.type = ?";
            $params[] = $type;
        }
        
        if (!empty($recipientId)) {
            $where[] = "communications.tenant_id = ?";
            $params[] = $recipientId;
        }
        
        error_log("Query built, executing database query");
        
        // Get communications with tenant and property info
        $sql = "SELECT communications.*, 
                        tenants.name as tenant_name,
                        tenants.email as tenant_email,
                        tenants.phone as tenant_phone,
                        properties.name as property_name
                 FROM communications
                 LEFT JOIN tenants ON communications.tenant_id = tenants.id
                 LEFT JOIN properties ON communications.property_id = properties.id
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY communications.created_at DESC
                 LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;
        
        error_log("Executing main query with " . count($params) . " parameters");
        $communications = $this->db->query($sql, $params)->fetchAll();
        error_log("Main query executed, returned " . count($communications) . " results");
        
        // Get total count for pagination - use only filter params, not LIMIT/OFFSET
        $countParams = array_slice($params, 0, -2); // Remove LIMIT and OFFSET parameters
        $countSql = "SELECT COUNT(*) FROM communications WHERE " . implode(' AND ', $where);
        $total = $this->db->query($countSql, $countParams)->fetchColumn();
        
        // Get statistics
        $statsSql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN type = 'email' THEN 1 ELSE 0 END) as email_count,
                        SUM(CASE WHEN type = 'sms' THEN 1 ELSE 0 END) as sms_count,
                        SUM(CASE WHEN type = 'whatsapp' THEN 1 ELSE 0 END) as whatsapp_count,
                        SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                        SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_count
                     FROM communications 
                     WHERE deleted_at IS NULL";
        
        $stats = $this->db->query($statsSql, [])->fetch();
        
        // Get tenants for recipient selection
        $tenantsSql = "SELECT t.id, t.name, t.email, t.phone, p.name as property_name
                       FROM tenants t
                       LEFT JOIN properties p ON t.property_id = p.id
                       WHERE t.admin_id = ? AND t.deleted_at IS NULL 
                       ORDER BY t.name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Initialize templates if needed
        require_once __DIR__ . '/../../services/MessageTemplateService.php';
        $templateService = new \MessageTemplateService($this->db);
        $templateService->initializeDefaultTemplates($admin['id']);
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('communications', $communications);
        \ViewManager::set('pagination', [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'last_page' => ceil($total / $limit)
        ]);
        \ViewManager::set('stats', $stats);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('filters', [
            'search' => $search,
            'type' => $type,
            'recipient_id' => $recipientId
        ]);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Communications');
        
        // Capture communications content (anti-scattering compliant)
        ob_start();
        try {
            include __DIR__ . '/../../views/admin/communications/index.php';
            $content = ob_get_clean();
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
        
        // Set content and render with layout (anti-scattering compliant)
        \ViewManager::set('content', $content);
        
        // Include the layout directly (anti-scattering compliant)
        include __DIR__ . '/../../views/admin/dashboard_layout.php';
    }
    
    public function create() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get tenants for recipient selection
        $tenantsSql = "SELECT id, name, email, phone FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Get communication templates
        $templatesSql = "SELECT * FROM communication_templates WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $templates = $this->db->query($templatesSql, [$admin['id']])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('templates', $templates);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Create Communication');
        
        // Include the create view
        include __DIR__ . '/../../views/admin/communications/create.php';
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Handle JSON requests
        $input = $_POST;
        if (empty($_POST) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
        }
        
        // Validate required fields
        $required = ['recipients', 'subject', 'message', 'type'];
        if ($input['type'] !== 'whatsapp') {
            $required = array_diff($required, ['subject']);
        }
        
        foreach ($required as $field) {
            if (empty($input[$field])) {
                if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                    exit;
                }
                $_SESSION['error'] = "Field '$field' is required";
                $this->redirect('/admin/communications/create');
                return;
            }
        }
        
        // Validate communication type
        $validTypes = ['email', 'sms', 'whatsapp', 'broadcast'];
        if (!in_array($input['type'], $validTypes)) {
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid communication type']);
                exit;
            }
            $_SESSION['error'] = 'Invalid communication type';
            $this->redirect('/admin/communications/create');
            return;
        }
        
        // Validate recipients
        $recipients = is_array($input['recipients']) ? $input['recipients'] : [$input['recipients']];
        if (empty($recipients)) {
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'At least one recipient is required']);
                exit;
            }
            $_SESSION['error'] = 'At least one recipient is required';
            $this->redirect('/admin/communications/create');
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            foreach ($recipients as $recipientId) {
                // Check if recipient exists and belongs to admin
                $recipient = $this->db->query(
                    "SELECT id, name, email, phone FROM tenants WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                    [$recipientId, $admin['id']]
                )->fetch();
                
                if (!$recipient) {
                    throw new Exception("Recipient with ID $recipientId not found");
                }
                
                // Validate recipient has required contact info
                if ($input['type'] === 'email' && empty($recipient['email'])) {
                    throw new Exception("Recipient {$recipient['name']} does not have an email address");
                }
                
                if (($input['type'] === 'sms' || $input['type'] === 'whatsapp') && empty($recipient['phone'])) {
                    throw new Exception("Recipient {$recipient['name']} does not have a phone number");
                }
                
                // Insert communication record
                $sql = "INSERT INTO communications (tenant_id, property_id, subject, message, 
                          type, status, priority, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                
                $params = [
                    $recipientId,
                    1, // Default property_id - should be passed from form
                    $input['subject'] ?? '',
                    $input['message'],
                    $input['type'],
                    ($input['send_immediately'] ?? false) ? 'sent' : 'draft',
                    $input['priority'] ?? 'normal'
                ];
                
                $this->db->query($sql, $params);
                $communicationId = $this->db->lastInsertId();
                
                // Send the message if requested
                if ($input['send_immediately'] ?? false) {
                    $this->sendMessage($communicationId, $input, $recipient);
                }
            }
            
            $this->db->commit();
            
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => 'Communication created successfully',
                    'recipients_count' => count($recipients),
                    'type' => $input['type']
                ]);
                exit;
            }
            
            $_SESSION['success'] = 'Communication sent successfully to ' . count($recipients) . ' recipient(s)';
            $this->redirect('/admin/communications');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to create communication: ' . $e->getMessage()]);
                exit;
            }
            $_SESSION['error'] = 'Failed to create communication: ' . $e->getMessage();
            $this->redirect('/admin/communications/create');
        }
    }
    
    /**
     * Send message via appropriate channel
     */
    private function sendMessage($communicationId, $data, $recipient) {
        $success = false;
        $errorMessage = '';
        
        switch ($data['type']) {
            case 'email':
                // TODO: Implement email sending
                $success = true;
                error_log("Email sent to {$recipient['email']}: {$data['subject']}");
                break;
                
            case 'sms':
                // TODO: Implement SMS sending
                $success = true;
                error_log("SMS sent to {$recipient['phone']}: {$data['message']}");
                break;
                
            case 'whatsapp':
                $success = $this->sendWhatsAppMessage($recipient['phone'], $data, $communicationId);
                break;
        }
        
        // Update communication status
        $status = $success ? 'sent' : 'failed';
        $whatsappMessageId = $data['type'] === 'whatsapp' && $success ? ($data['whatsapp_message_id'] ?? null) : null;
        
        $this->db->query(
            "UPDATE communications SET status = ?, whatsapp_message_id = ?, sent_at = NOW(), updated_at = NOW() WHERE id = ?",
            [$status, $whatsappMessageId, $communicationId]
        );
        
        if (!$success) {
            throw new Exception($errorMessage ?: "Failed to send {$data['type']} message");
        }
    }
    
    /**
     * Send WhatsApp message
     */
    private function sendWhatsAppMessage($phone, $data, $communicationId) {
        require_once __DIR__ . '/../../services/WhatsAppService.php';
        
        $whatsappService = new WhatsAppService();
        
        if (!$whatsappService->isConfigured()) {
            throw new Exception('WhatsApp API is not configured');
        }
        
        $result = [];
        
        if (!empty($data['whatsapp_template'])) {
            // Send template message
            $result = $whatsappService->sendTemplateMessage($phone, $data['whatsapp_template']);
        } else {
            // Send text message
            $result = $whatsappService->sendTextMessage($phone, $data['message']);
        }
        
        if ($result['success']) {
            // Store WhatsApp message ID
            $messageId = $result['data']['messages'][0]['id'] ?? null;
            if ($messageId) {
                $this->db->query(
                    "UPDATE communications SET whatsapp_message_id = ? WHERE id = ?",
                    [$messageId, $communicationId]
                );
            }
            return true;
        } else {
            throw new Exception('WhatsApp send failed: ' . $result['message']);
        }
    }
    
    /**
     * Get templates API endpoint
     */
    public function getTemplates() {
        $admin = $this->requireAuth();
        
        require_once __DIR__ . '/../../services/MessageTemplateService.php';
        $templateService = new \MessageTemplateService($this->db);
        
        $type = $_GET['type'] ?? null;
        $templates = $templateService->getTemplates($admin['id'], $type);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'templates' => $templates]);
        exit;
    }
    
    /**
     * Get single template API endpoint
     */
    public function getTemplate($id) {
        $admin = $this->requireAuth();
        
        require_once __DIR__ . '/../../services/MessageTemplateService.php';
        $templateService = new \MessageTemplateService($this->db);
        
        $template = $templateService->getTemplate($id, $admin['id']);
        
        if (!$template) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Template not found']);
            exit;
        }
        
        // Decode variables
        $template['variables'] = json_decode($template['variables'] ?? '[]', true);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'template' => $template]);
        exit;
    }
    
    /**
     * Get WhatsApp templates API endpoint
     */
    public function getWhatsAppTemplates() {
        $admin = $this->requireAuth();
        
        require_once __DIR__ . '/../../services/WhatsAppService.php';
        $whatsappService = new WhatsAppService();
        
        if (!$whatsappService->isConfigured()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'WhatsApp API not configured']);
            exit;
        }
        
        $result = $whatsappService->getTemplates();
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    
    public function show($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $sql = "SELECT c.*, 
                        s.name as sender_name,
                        s.email as sender_email,
                        r.name as recipient_name,
                        r.email as recipient_email
                 FROM communications c
                 LEFT JOIN admins s ON c.sender_id = s.id
                 LEFT JOIN tenants r ON c.recipient_id = r.id
                 WHERE c.id = ? AND c.deleted_at IS NULL";
        
        $communication = $this->db->query($sql, [$id])->fetch();
        
        if (!$communication) {
            $_SESSION['error'] = 'Communication not found';
            $this->redirect('/admin/communications');
            return;
        }
        
        // Get communication history/updates
        $historySql = "SELECT * FROM communication_updates WHERE communication_id = ? ORDER BY created_at DESC";
        $communication['updates'] = $this->db->query($historySql, [$id])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('communication', $communication);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Communication Details');
        
        // Include the show view
        include __DIR__ . '/../../views/admin/communications/show.php';
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if communication exists and belongs to admin
        $communication = $this->db->query("SELECT * FROM communications WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$communication) {
            $_SESSION['error'] = 'Communication not found';
            $this->redirect('/admin/communications');
            return;
        }
        
        // Prevent editing sent communications
        if ($communication['status'] === 'sent') {
            $_SESSION['error'] = 'Cannot edit sent communication';
            $this->redirect('/admin/communications');
            return;
        }
        
        // Get tenants for recipient selection
        $tenantsSql = "SELECT id, name, email FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('communication', $communication);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Edit Communication');
        
        // Include the edit view
        include __DIR__ . '/../../views/admin/communications/edit.php';
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if communication exists and belongs to admin
        $communication = $this->db->query("SELECT id, status FROM communications WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$communication) {
            $_SESSION['error'] = 'Communication not found';
            $this->redirect('/admin/communications');
            return;
        }
        
        // Prevent updating sent communications
        if ($communication['status'] === 'sent') {
            $_SESSION['error'] = 'Cannot update sent communication';
            $this->redirect('/admin/communications');
            return;
        }
        
        try {
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['subject', 'message', 'type', 'priority'];
            
            foreach ($allowedFields as $field) {
                if (isset($_POST[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $_POST[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE communications SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
            }
            
            $_SESSION['success'] = 'Communication updated successfully';
            $this->redirect('/admin/communications');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to update communication: ' . $e->getMessage();
            $this->redirect("/admin/communications/$id/edit");
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if communication exists and belongs to admin
        $communication = $this->db->query("SELECT id FROM communications WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$communication) {
            $_SESSION['error'] = 'Communication not found';
            $this->redirect('/admin/communications');
            return;
        }
        
        try {
            // Soft delete communication
            $this->db->query("UPDATE communications SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            $_SESSION['success'] = 'Communication deleted successfully';
            $this->redirect('/admin/communications');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to delete communication: ' . $e->getMessage();
            $this->redirect('/admin/communications');
        }
    }
    
    public function send($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get communication details
        $sql = "SELECT c.*, t.name as recipient_name, t.email as recipient_email, t.phone as recipient_phone
                 FROM communications c
                 LEFT JOIN tenants t ON c.recipient_id = t.id
                 WHERE c.id = ? AND c.deleted_at IS NULL";
        
        $communication = $this->db->query($sql, [$id])->fetch();
        
        if (!$communication) {
            $_SESSION['error'] = 'Communication not found';
            $this->redirect('/admin/communications');
            return;
        }
        
        if ($communication['status'] === 'sent') {
            $_SESSION['error'] = 'Communication already sent';
            $this->redirect('/admin/communications');
            return;
        }
        
        try {
            // Update status to sent
            $this->db->query("UPDATE communications SET status = 'sent', sent_at = NOW(), updated_at = NOW() WHERE id = ?", [$id]);
            
            // In real implementation, this would integrate with email/SMS services
            error_log("Communication sent: ID=$id, Type={$communication['type']}, Recipient={$communication['recipient_email']}");
            
            $_SESSION['success'] = 'Communication sent successfully';
            $this->redirect('/admin/communications');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to send communication: ' . $e->getMessage();
            $this->redirect('/admin/communications');
        }
    }
    
    /**
     * Get tenants with property and lease information for template variables
     */
    public function getTenantsForTemplate() {
        $admin = $this->requireAuth();
        
        // Get tenants with their property and lease information
        $sql = "SELECT DISTINCT 
                    t.id, 
                    t.name, 
                    t.email, 
                    t.phone,
                    t.lease_start,
                    t.lease_end,
                    t.rent_amount,
                    p.name as property_name,
                    p.id as property_id,
                    u.unit_number,
                    u.id as unit_id,
                    NULL as next_payment_due
                FROM tenants t
                LEFT JOIN properties p ON t.property_id = p.id
                LEFT JOIN units u ON t.unit_id = u.id
                WHERE t.admin_id = ? AND t.deleted_at IS NULL
                ORDER BY t.name ASC";
        
        $tenants = $this->db->query($sql, [$admin['id']])->fetchAll();
        
        // Group tenants by tenant_id and collect their properties
        $tenantsWithProperties = [];
        foreach ($tenants as $tenant) {
            $tenantId = $tenant['id'];
            
            if (!isset($tenantsWithProperties[$tenantId])) {
                $tenantsWithProperties[$tenantId] = [
                    'id' => $tenant['id'],
                    'name' => $tenant['name'],
                    'email' => $tenant['email'],
                    'phone' => $tenant['phone'],
                    'lease_start_date' => $tenant['lease_start_date'],
                    'lease_end_date' => $tenant['lease_end_date'],
                    'rent_amount' => $tenant['rent_amount'],
                    'rent_frequency' => $tenant['rent_frequency'],
                    'next_payment_due' => $tenant['next_payment_due'],
                    'properties' => []
                ];
            }
            
            // Add property if it exists
            if ($tenant['property_id']) {
                $tenantsWithProperties[$tenantId]['properties'][] = [
                    'id' => $tenant['property_id'],
                    'name' => $tenant['property_name'],
                    'unit_number' => $tenant['unit_number'],
                    'unit_id' => $tenant['unit_id']
                ];
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'tenants' => array_values($tenantsWithProperties)
        ]);
        exit;
    }
    
    /**
     * Replicate templates across all communication types
     */
    public function replicateTemplates() {
        $admin = $this->requireAuth();
        
        require_once __DIR__ . '/../../services/MessageTemplateService.php';
        $templateService = new \MessageTemplateService($this->db);
        
        try {
            $templateService->replicateAllTemplates($admin['id']);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Templates successfully replicated across all communication types'
            ]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to replicate templates: ' . $e->getMessage()
            ]);
            exit;
        }
    }
    
    public function bulk() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get tenants for recipient selection
        $tenantsSql = "SELECT id, name, email FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Bulk Communication');
        
        // Include the bulk view
        include __DIR__ . '/../../views/admin/communications/bulk.php';
    }
    
    public function sendBulk() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Validate required fields
        $required = ['recipients', 'subject', 'message', 'type'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Field '$field' is required";
                $this->redirect('/admin/communications/bulk');
                return;
            }
        }
        
        if (!is_array($_POST['recipients']) || empty($_POST['recipients'])) {
            $_SESSION['error'] = 'Recipients must be a non-empty array';
            $this->redirect('/admin/communications/bulk');
            return;
        }
        
        // Validate all recipients exist
        $recipientIds = $_POST['recipients'];
        $placeholders = str_repeat('?,', count($recipientIds));
        $placeholders = rtrim($placeholders, ',');
        
        $recipientsSql = "SELECT id, name, email, phone FROM tenants WHERE id IN ($placeholders) AND admin_id = ? AND deleted_at IS NULL";
        $recipientsParams = array_merge($recipientIds, [$admin['id']]);
        
        $validRecipients = $this->db->query($recipientsSql, $recipientsParams)->fetchAll();
        
        if (count($validRecipients) !== count($recipientIds)) {
            $_SESSION['error'] = 'Some recipients not found';
            $this->redirect('/admin/communications/bulk');
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            foreach ($validRecipients as $recipient) {
                $sql = "INSERT INTO communications (admin_id, sender_id, recipient_id, subject, message, 
                          type, status, priority, sent_at, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                
                $params = [
                    $admin['id'],
                    $admin['id'],
                    $recipient['id'],
                    $_POST['subject'],
                    $_POST['message'],
                    $_POST['type'],
                    'sent',
                    $_POST['priority'] ?? 'normal'
                ];
                
                $this->db->query($sql, $params);
                
                // In real implementation, this would integrate with email/SMS services
                error_log("Bulk communication sent: Type={$_POST['type']}, Recipient={$recipient['email']}");
            }
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Bulk communication sent successfully to ' . count($validRecipients) . ' recipients';
            $this->redirect('/admin/communications');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to send bulk communication: ' . $e->getMessage();
            $this->redirect('/admin/communications/bulk');
        }
    }
}
