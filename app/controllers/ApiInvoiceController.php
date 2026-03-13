<?php

namespace App\Controllers;

class ApiInvoiceController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get pagination and filter parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $tenantId = $_GET['tenant_id'] ?? '';
        
        // Build query
        $where = ["i.admin_id = ?", "i.deleted_at IS NULL"];
        $params = [$admin['id']];
        
        if (!empty($search)) {
            $where[] = "(i.invoice_number LIKE ? OR i.amount LIKE ? OR i.notes LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($status)) {
            $where[] = "i.status = ?";
            $params[] = $status;
        }
        
        if (!empty($tenantId)) {
            $where[] = "i.tenant_id = ?";
            $params[] = $tenantId;
        }
        
        // Get invoices with tenant and property info
        $sql = "SELECT i.*, 
                        t.name as tenant_name,
                        t.email as tenant_email,
                        pr.name as property_name,
                        pr.address as property_address,
                        u.unit_number
                 FROM invoices i
                 LEFT JOIN tenants t ON i.tenant_id = t.id
                 LEFT JOIN properties pr ON t.property_id = pr.id
                 LEFT JOIN units u ON t.unit_id = u.id
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY i.created_at DESC
                 LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;
        
        $invoices = $this->db->query($sql, $params)->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM invoices i WHERE " . implode(' AND ', $where);
        $total = $this->db->query($countSql, $params)->fetchColumn();
        
        $this->json([
            'success' => true,
            'data' => $invoices,
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
        
        $sql = "SELECT i.*, 
                        t.name as tenant_name,
                        t.email as tenant_email,
                        t.phone as tenant_phone,
                        pr.name as property_name,
                        pr.address as property_address,
                        u.unit_number
                 FROM invoices i
                 LEFT JOIN tenants t ON i.tenant_id = t.id
                 LEFT JOIN properties pr ON t.property_id = pr.id
                 LEFT JOIN units u ON t.unit_id = u.id
                 WHERE i.id = ? AND i.admin_id = ? AND i.deleted_at IS NULL";
        
        $invoice = $this->db->query($sql, [$id, $admin['id']])->fetch();
        
        if (!$invoice) {
            $this->json(['success' => false, 'message' => 'Invoice not found'], 404);
            return;
        }
        
        // Get invoice items
        $itemsSql = "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY created_at";
        $invoice['items'] = $this->db->query($itemsSql, [$id])->fetchAll();
        
        $this->json(['success' => true, 'data' => $invoice]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['tenant_id', 'items'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->json(['success' => false, 'message' => "Field '$field' is required"], 400);
                return;
            }
        }
        
        // Check if tenant exists and belongs to admin
        $tenantCheck = $this->db->query("SELECT id FROM tenants WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                       [$data['tenant_id'], $admin['id']])->fetch();
        if (!$tenantCheck) {
            $this->json(['success' => false, 'message' => 'Tenant not found'], 400);
            return;
        }
        
        // Validate invoice items
        $totalAmount = 0;
        foreach ($data['items'] as $item) {
            if (empty($item['description']) || empty($item['amount'])) {
                $this->json(['success' => false, 'message' => 'Each item must have description and amount'], 400);
                return;
            }
            $totalAmount += floatval($item['amount']);
        }
        
        try {
            $this->db->beginTransaction();
            
            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($this->getNextInvoiceNumber($admin['id']), 4, '0', STR_PAD_LEFT);
            
            // Insert invoice
            $sql = "INSERT INTO invoices (admin_id, tenant_id, invoice_number, amount, due_date, 
                      status, notes, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $admin['id'],
                $data['tenant_id'],
                $invoiceNumber,
                $totalAmount,
                $data['due_date'] ?? date('Y-m-d', strtotime('+30 days')),
                $data['status'] ?? 'draft',
                $data['notes'] ?? null
            ];
            
            $this->db->query($sql, $params);
            $invoiceId = $this->db->lastInsertId();
            
            // Insert invoice items
            $itemSql = "INSERT INTO invoice_items (invoice_id, description, amount, quantity, created_at) VALUES (?, ?, ?, ?, NOW())";
            foreach ($data['items'] as $item) {
                $this->db->query($itemSql, [
                    $invoiceId,
                    $item['description'],
                    $item['amount'],
                    $item['quantity'] ?? 1
                ]);
            }
            
            $this->db->commit();
            
            $this->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => [
                    'id' => $invoiceId,
                    'invoice_number' => $invoiceNumber,
                    'amount' => $totalAmount
                ]
            ]);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to create invoice: ' . $e->getMessage()], 500);
        }
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if invoice exists and belongs to admin
        $invoice = $this->db->query("SELECT id, status FROM invoices WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$invoice) {
            $this->json(['success' => false, 'message' => 'Invoice not found'], 404);
            return;
        }
        
        // Prevent updating sent invoices
        if ($invoice['status'] === 'sent' || $invoice['status'] === 'paid') {
            $this->json(['success' => false, 'message' => 'Cannot update sent or paid invoice'], 400);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['due_date', 'status', 'notes'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            // Update invoice items if provided
            if (!empty($data['items'])) {
                // Delete existing items
                $this->db->query("DELETE FROM invoice_items WHERE invoice_id = ?", [$id]);
                
                // Add new items
                $totalAmount = 0;
                $itemSql = "INSERT INTO invoice_items (invoice_id, description, amount, quantity, created_at) VALUES (?, ?, ?, ?, NOW())";
                foreach ($data['items'] as $item) {
                    $this->db->query($itemSql, [
                        $id,
                        $item['description'],
                        $item['amount'],
                        $item['quantity'] ?? 1
                    ]);
                    $totalAmount += floatval($item['amount']);
                }
                
                // Update total amount
                $updateFields[] = "amount = ?";
                $params[] = $totalAmount;
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE invoices SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
            }
            
            $this->db->commit();
            
            $this->json(['success' => true, 'message' => 'Invoice updated successfully']);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to update invoice: ' . $e->getMessage()], 500);
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if invoice exists and belongs to admin
        $invoice = $this->db->query("SELECT id, status FROM invoices WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$invoice) {
            $this->json(['success' => false, 'message' => 'Invoice not found'], 404);
            return;
        }
        
        // Prevent deleting paid invoices
        if ($invoice['status'] === 'paid') {
            $this->json(['success' => false, 'message' => 'Cannot delete paid invoice'], 400);
            return;
        }
        
        try {
            // Soft delete invoice
            $this->db->query("UPDATE invoices SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            $this->json(['success' => true, 'message' => 'Invoice deleted successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to delete invoice: ' . $e->getMessage()], 500);
        }
    }
    
    public function sendInvoice($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get invoice details
        $invoice = $this->db->query("SELECT i.*, t.name as tenant_name, t.email as tenant_email 
                                     FROM invoices i 
                                     LEFT JOIN tenants t ON i.tenant_id = t.id 
                                     WHERE i.id = ? AND i.admin_id = ? AND i.deleted_at IS NULL", 
                                     [$id, $admin['id']])->fetch();
        
        if (!$invoice) {
            $this->json(['success' => false, 'message' => 'Invoice not found'], 404);
            return;
        }
        
        try {
            // Update status to sent
            $this->db->query("UPDATE invoices SET status = 'sent', sent_at = NOW(), updated_at = NOW() WHERE id = ?", [$id]);
            
            // In real implementation, send email here
            $this->json(['success' => true, 'message' => 'Invoice sent successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to send invoice: ' . $e->getMessage()], 500);
        }
    }
    
    public function markAsPaid($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if invoice exists and belongs to admin
        $invoice = $this->db->query("SELECT id FROM invoices WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$invoice) {
            $this->json(['success' => false, 'message' => 'Invoice not found'], 404);
            return;
        }
        
        try {
            // Update status to paid
            $this->db->query("UPDATE invoices SET status = 'paid', paid_at = NOW(), updated_at = NOW() WHERE id = ?", [$id]);
            
            $this->json(['success' => true, 'message' => 'Invoice marked as paid']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to mark invoice as paid: ' . $e->getMessage()], 500);
        }
    }
    
    public function getInvoiceStats() {
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
                    COUNT(*) as total_invoices,
                    SUM(amount) as total_amount,
                    AVG(amount) as avg_amount,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_count,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) as overdue_count
                 FROM invoices 
                 WHERE admin_id = ? AND deleted_at IS NULL $dateFilter";
        
        $stats = $this->db->query($sql, [$admin['id']])->fetch();
        
        $this->json(['success' => true, 'data' => $stats]);
    }
    
    private function getNextInvoiceNumber($adminId) {
        $sql = "SELECT COUNT(*) as count FROM invoices WHERE admin_id = ? AND YEAR(created_at) = YEAR(NOW())";
        $result = $this->db->query($sql, [$adminId])->fetch();
        return $result['count'] + 1;
    }
}
