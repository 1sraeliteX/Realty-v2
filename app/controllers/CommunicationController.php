<?php

namespace App\Controllers;

class CommunicationController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get pagination and filter parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $recipientId = $_GET['recipient_id'] ?? '';
        
        // Build query
        $where = ["c.admin_id = ?", "c.deleted_at IS NULL"];
        $params = [$admin['id']];
        
        if (!empty($search)) {
            $where[] = "(c.subject LIKE ? OR c.message LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($type)) {
            $where[] = "c.type = ?";
            $params[] = $type;
        }
        
        if (!empty($recipientId)) {
            $where[] = "c.recipient_id = ?";
            $params[] = $recipientId;
        }
        
        // Get communications with sender and recipient info
        $sql = "SELECT c.*, 
                        s.name as sender_name,
                        s.email as sender_email,
                        r.name as recipient_name,
                        r.email as recipient_email
                 FROM communications c
                 LEFT JOIN admins s ON c.sender_id = s.id
                 LEFT JOIN tenants r ON c.recipient_id = r.id
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY c.created_at DESC
                 LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;
        
        $communications = $this->db->query($sql, $params)->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM communications c WHERE " . implode(' AND ', $where);
        $total = $this->db->query($countSql, $params)->fetchColumn();
        
        // Get statistics
        $statsSql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN type = 'email' THEN 1 ELSE 0 END) as email_count,
                        SUM(CASE WHEN type = 'sms' THEN 1 ELSE 0 END) as sms_count,
                        SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                        SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_count
                     FROM communications 
                     WHERE admin_id = ? AND deleted_at IS NULL";
        $stats = $this->db->query($statsSql, [$admin['id']])->fetch();
        
        // Get tenants for recipient selection
        $tenantsSql = "SELECT id, name, email FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
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
        
        // Set user data
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        
        // Set page title
        \ViewManager::set('title', 'Communications');
        
        // Include the communications index view
        include __DIR__ . '/../../views/admin/communications/index.php';
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
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Validate required fields
        $required = ['recipient_id', 'subject', 'message', 'type'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Field '$field' is required";
                $this->redirect('/admin/communications/create');
                return;
            }
        }
        
        // Validate communication type
        $validTypes = ['email', 'sms', 'notification', 'alert'];
        if (!in_array($_POST['type'], $validTypes)) {
            $_SESSION['error'] = 'Invalid communication type';
            $this->redirect('/admin/communications/create');
            return;
        }
        
        // Check if recipient exists and belongs to admin
        $recipientCheck = $this->db->query("SELECT id, name, email FROM tenants WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                           [$_POST['recipient_id'], $admin['id']])->fetch();
        if (!$recipientCheck) {
            $_SESSION['error'] = 'Recipient not found';
            $this->redirect('/admin/communications/create');
            return;
        }
        
        try {
            $sql = "INSERT INTO communications (admin_id, sender_id, recipient_id, subject, message, 
                      type, status, priority, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $admin['id'],
                $admin['id'],
                $_POST['recipient_id'],
                $_POST['subject'],
                $_POST['message'],
                $_POST['type'],
                $_POST['send_immediately'] ?? false ? 'sent' : 'draft',
                $_POST['priority'] ?? 'normal'
            ];
            
            $this->db->query($sql, $params);
            $communicationId = $this->db->lastInsertId();
            
            $_SESSION['success'] = 'Communication created successfully';
            $this->redirect('/admin/communications');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to create communication: ' . $e->getMessage();
            $this->redirect('/admin/communications/create');
        }
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
                 WHERE c.id = ? AND c.admin_id = ? AND c.deleted_at IS NULL";
        
        $communication = $this->db->query($sql, [$id, $admin['id']])->fetch();
        
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
                 WHERE c.id = ? AND c.admin_id = ? AND c.deleted_at IS NULL";
        
        $communication = $this->db->query($sql, [$id, $admin['id']])->fetch();
        
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
