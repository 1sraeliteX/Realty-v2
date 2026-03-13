<?php

namespace App\Controllers;

class ApiCommunicationController extends BaseController {
    public function index() {
        $admin = $this->requireApiAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get pagination and filter parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $recipientId = $_GET['recipient_id'] ?? '';
        
        // Build query
        $where = ["c.admin_id = ?"];
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
        
        // Get communications with recipient info
        $sql = "SELECT c.*, 
                        t.name as tenant_name,
                        t.email as tenant_email
                 FROM messages c
                 LEFT JOIN tenants t ON c.recipient_id = t.id
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY c.created_at DESC
                 LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;
        
        $communications = $this->db->query($sql, $params)->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM messages c LEFT JOIN tenants t ON c.recipient_id = t.id WHERE " . implode(' AND ', $where);
        $countParams = array_slice($params, 0, count($params) - 2); // Remove limit and offset
        $total = $this->db->query($countSql, $countParams)->fetchColumn();
        
        $this->json([
            'success' => true,
            'data' => $communications,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit)
            ]
        ]);
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
            $this->json(['success' => false, 'message' => 'Communication not found'], 404);
            return;
        }
        
        $this->json(['success' => true, 'data' => $communication]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['recipient_id', 'subject', 'message', 'type'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->json(['success' => false, 'message' => "Field '$field' is required"], 400);
                return;
            }
        }
        
        // Validate communication type
        $validTypes = ['email', 'sms', 'notification', 'alert'];
        if (!in_array($data['type'], $validTypes)) {
            $this->json(['success' => false, 'message' => 'Invalid communication type'], 400);
            return;
        }
        
        // Check if recipient exists and belongs to admin
        $recipientCheck = $this->db->query("SELECT id, name, email FROM tenants WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                           [$data['recipient_id'], $admin['id']])->fetch();
        if (!$recipientCheck) {
            $this->json(['success' => false, 'message' => 'Recipient not found'], 400);
            return;
        }
        
        try {
            $sql = "INSERT INTO communications (admin_id, sender_id, recipient_id, subject, message, 
                      type, status, priority, sent_at, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $admin['id'],
                $admin['id'], // Sender is the current admin
                $data['recipient_id'],
                $data['subject'],
                $data['message'],
                $data['type'],
                $data['status'] ?? 'draft',
                $data['priority'] ?? 'normal'
            ];
            
            $this->db->query($sql, $params);
            $communicationId = $this->db->lastInsertId();
            
            // If immediate send requested, update status
            if ($data['send_immediately'] ?? false) {
                $this->sendCommunication($communicationId, $data, $recipientCheck);
            }
            
            $this->json([
                'success' => true,
                'message' => 'Communication created successfully',
                'data' => ['id' => $communicationId]
            ]);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to create communication: ' . $e->getMessage()], 500);
        }
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if communication exists and belongs to admin
        $communication = $this->db->query("SELECT id, status FROM communications WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$communication) {
            $this->json(['success' => false, 'message' => 'Communication not found'], 404);
            return;
        }
        
        // Prevent updating sent communications
        if ($communication['status'] === 'sent') {
            $this->json(['success' => false, 'message' => 'Cannot update sent communication'], 400);
            return;
        }
        
        try {
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['subject', 'message', 'type', 'priority'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE communications SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
            }
            
            $this->json(['success' => true, 'message' => 'Communication updated successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update communication: ' . $e->getMessage()], 500);
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if communication exists and belongs to admin
        $communication = $this->db->query("SELECT id FROM communications WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$communication) {
            $this->json(['success' => false, 'message' => 'Communication not found'], 404);
            return;
        }
        
        try {
            // Soft delete communication
            $this->db->query("UPDATE communications SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            $this->json(['success' => true, 'message' => 'Communication deleted successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to delete communication: ' . $e->getMessage()], 500);
        }
    }
    
    public function sendCommunication($id) {
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
            $this->json(['success' => false, 'message' => 'Communication not found'], 404);
            return;
        }
        
        if ($communication['status'] === 'sent') {
            $this->json(['success' => false, 'message' => 'Communication already sent'], 400);
            return;
        }
        
        try {
            // Update status to sent
            $this->sendCommunication($id, $communication, [
                'name' => $communication['recipient_name'],
                'email' => $communication['recipient_email'],
                'phone' => $communication['recipient_phone']
            ]);
            
            $this->json(['success' => true, 'message' => 'Communication sent successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to send communication: ' . $e->getMessage()], 500);
        }
    }
    
    public function sendBulk() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['recipients', 'subject', 'message', 'type'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->json(['success' => false, 'message' => "Field '$field' is required"], 400);
                return;
            }
        }
        
        if (!is_array($data['recipients']) || empty($data['recipients'])) {
            $this->json(['success' => false, 'message' => 'Recipients must be a non-empty array'], 400);
            return;
        }
        
        // Validate all recipients exist
        $recipientIds = array_column($data['recipients'], 'id');
        $placeholders = str_repeat('?,', count($recipientIds));
        $placeholders = rtrim($placeholders, ',');
        
        $recipientsSql = "SELECT id, name, email, phone FROM tenants WHERE id IN ($placeholders) AND admin_id = ? AND deleted_at IS NULL";
        $recipientsParams = array_merge($recipientIds, [$admin['id']]);
        
        $validRecipients = $this->db->query($recipientsSql, $recipientsParams)->fetchAll();
        
        if (count($validRecipients) !== count($recipientIds)) {
            $this->json(['success' => false, 'message' => 'Some recipients not found'], 400);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            $communicationIds = [];
            foreach ($validRecipients as $recipient) {
                $sql = "INSERT INTO communications (admin_id, sender_id, recipient_id, subject, message, 
                          type, status, priority, sent_at, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                
                $params = [
                    $admin['id'],
                    $admin['id'],
                    $recipient['id'],
                    $data['subject'],
                    $data['message'],
                    $data['type'],
                    'sent',
                    $data['priority'] ?? 'normal'
                ];
                
                $this->db->query($sql, $params);
                $communicationIds[] = $this->db->lastInsertId();
                
                // Send to each recipient
                $this->processCommunication(end($communicationIds), $data, $recipient);
            }
            
            $this->db->commit();
            
            $this->json([
                'success' => true,
                'message' => 'Bulk communication sent successfully',
                'data' => ['sent_count' => count($communicationIds)]
            ]);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to send bulk communication: ' . $e->getMessage()], 500);
        }
    }
    
    public function getCommunicationStats() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $period = $_GET['period'] ?? 'month';
        
        $dateFilter = '';
        if ($period === 'week') {
            $dateFilter = "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } elseif ($period === 'month') {
            $dateFilter = "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        } elseif ($period === 'year') {
            $dateFilter = "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        }
        
        $sql = "SELECT 
                    COUNT(*) as total_communications,
                    SUM(CASE WHEN type = 'email' THEN 1 ELSE 0 END) as email_count,
                    SUM(CASE WHEN type = 'sms' THEN 1 ELSE 0 END) as sms_count,
                    SUM(CASE WHEN type = 'notification' THEN 1 ELSE 0 END) as notification_count,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_count,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count
                 FROM communications 
                 WHERE admin_id = ? AND deleted_at IS NULL $dateFilter";
        
        $stats = $this->db->query($sql, [$admin['id']])->fetch();
        
        $this->json(['success' => true, 'data' => $stats]);
    }
    
    private function processCommunication($communicationId, $communicationData, $recipient) {
        // Update communication status to sent
        $this->db->query("UPDATE communications SET status = 'sent', sent_at = NOW() WHERE id = ?", [$communicationId]);
        
        // In real implementation, this would integrate with:
        // - Email service (SendGrid, Mailgun, etc.)
        // - SMS service (Twilio, etc.)
        // - Push notification service
        
        // For now, just log that it was "sent"
        error_log("Communication sent: ID=$communicationId, Type={$communicationData['type']}, Recipient={$recipient['email']}");
        
        return true;
    }
}
