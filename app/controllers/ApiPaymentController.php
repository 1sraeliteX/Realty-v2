<?php

namespace App\Controllers;

class ApiPaymentController extends BaseController {
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
        $propertyId = $_GET['property_id'] ?? '';
        
        // Build query
        $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
        $params = [$admin['id']];
        
        if (!empty($search)) {
            $where[] = "(p.amount LIKE ? OR p.payment_method LIKE ? OR p.notes LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($status)) {
            $where[] = "p.status = ?";
            $params[] = $status;
        }
        
        if (!empty($tenantId)) {
            $where[] = "p.tenant_id = ?";
            $params[] = $tenantId;
        }
        
        if (!empty($propertyId)) {
            $where[] = "pr.id = ?";
            $params[] = $propertyId;
        }
        
        // Get payments with tenant and property info
        $sql = "SELECT p.*, 
                        t.name as tenant_name,
                        t.email as tenant_email,
                        pr.name as property_name,
                        pr.address as property_address,
                        u.unit_number
                 FROM payments p
                 LEFT JOIN tenants t ON p.tenant_id = t.id
                 LEFT JOIN properties pr ON t.property_id = pr.id
                 LEFT JOIN units u ON t.unit_id = u.id
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY p.payment_date DESC
                 LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;
        
        $payments = $this->db->query($sql, $params)->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM payments p WHERE " . implode(' AND ', $where);
        $total = $this->db->query($countSql, $params)->fetchColumn();
        
        $this->json([
            'success' => true,
            'data' => $payments,
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
        
        $sql = "SELECT p.*, 
                        t.name as tenant_name,
                        t.email as tenant_email,
                        pr.name as property_name,
                        pr.address as property_address,
                        u.unit_number
                 FROM payments p
                 LEFT JOIN tenants t ON p.tenant_id = t.id
                 LEFT JOIN properties pr ON t.property_id = pr.id
                 LEFT JOIN units u ON t.unit_id = u.id
                 WHERE p.id = ? AND p.admin_id = ? AND p.deleted_at IS NULL";
        
        $payment = $this->db->query($sql, [$id, $admin['id']])->fetch();
        
        if (!$payment) {
            $this->json(['success' => false, 'message' => 'Payment not found'], 404);
            return;
        }
        
        $this->json(['success' => true, 'data' => $payment]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['tenant_id', 'amount', 'payment_date', 'payment_method'];
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
        
        try {
            $this->db->beginTransaction();
            
            // Insert payment
            $sql = "INSERT INTO payments (admin_id, tenant_id, amount, payment_date, payment_method, 
                      transaction_id, notes, status, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $admin['id'],
                $data['tenant_id'],
                $data['amount'],
                $data['payment_date'],
                $data['payment_method'],
                $data['transaction_id'] ?? null,
                $data['notes'] ?? null,
                $data['status'] ?? 'paid'
            ];
            
            $this->db->query($sql, $params);
            $paymentId = $this->db->lastInsertId();
            
            // Update tenant payment status
            $this->db->query("UPDATE tenants SET payment_status = 'current', updated_at = NOW() WHERE id = ?", [$data['tenant_id']]);
            
            $this->db->commit();
            
            $this->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => ['id' => $paymentId]
            ]);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to record payment: ' . $e->getMessage()], 500);
        }
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if payment exists and belongs to admin
        $payment = $this->db->query("SELECT id FROM payments WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$payment) {
            $this->json(['success' => false, 'message' => 'Payment not found'], 404);
            return;
        }
        
        try {
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['amount', 'payment_date', 'payment_method', 'transaction_id', 'notes', 'status'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE payments SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
            }
            
            $this->json(['success' => true, 'message' => 'Payment updated successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update payment: ' . $e->getMessage()], 500);
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if payment exists and belongs to admin
        $payment = $this->db->query("SELECT id, tenant_id FROM payments WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$payment) {
            $this->json(['success' => false, 'message' => 'Payment not found'], 404);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Soft delete payment
            $this->db->query("UPDATE payments SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            // Update tenant payment status if this was the last payment
            $lastPaymentCheck = $this->db->query("SELECT id FROM payments WHERE tenant_id = ? AND deleted_at IS NULL ORDER BY payment_date DESC LIMIT 1", 
                                                [$payment['tenant_id']])->fetch();
            if (!$lastPaymentCheck) {
                $this->db->query("UPDATE tenants SET payment_status = 'overdue', updated_at = NOW() WHERE id = ?", [$payment['tenant_id']]);
            }
            
            $this->db->commit();
            
            $this->json(['success' => true, 'message' => 'Payment deleted successfully']);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to delete payment: ' . $e->getMessage()], 500);
        }
    }
    
    public function getPaymentStats() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $period = $_GET['period'] ?? 'month';
        
        $dateFilter = '';
        if ($period === 'week') {
            $dateFilter = "AND payment_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } elseif ($period === 'month') {
            $dateFilter = "AND payment_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        } elseif ($period === 'year') {
            $dateFilter = "AND payment_date >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        }
        
        $sql = "SELECT 
                    COUNT(*) as total_payments,
                    SUM(amount) as total_revenue,
                    AVG(amount) as avg_payment,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count
                 FROM payments 
                 WHERE admin_id = ? AND deleted_at IS NULL $dateFilter";
        
        $stats = $this->db->query($sql, [$admin['id']])->fetch();
        
        $this->json(['success' => true, 'data' => $stats]);
    }
    
    public function getOverduePayments() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $sql = "SELECT t.id as tenant_id, t.name as tenant_name, t.email as tenant_email,
                        pr.name as property_name, u.unit_number,
                        t.rent_expiry_date, DATEDIFF(NOW(), t.rent_expiry_date) as days_overdue
                 FROM tenants t
                 LEFT JOIN properties pr ON t.property_id = pr.id
                 LEFT JOIN units u ON t.unit_id = u.id
                 WHERE t.admin_id = ? AND t.deleted_at IS NULL 
                 AND t.payment_status != 'current'
                 AND t.rent_expiry_date < NOW()
                 ORDER BY t.rent_expiry_date ASC";
        
        $overdue = $this->db->query($sql, [$admin['id']])->fetchAll();
        
        $this->json(['success' => true, 'data' => $overdue]);
    }
    
    public function generateReceipt($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $sql = "SELECT p.*, 
                        t.name as tenant_name, t.email as tenant_email, t.phone as tenant_phone,
                        pr.name as property_name, pr.address as property_address,
                        u.unit_number
                 FROM payments p
                 LEFT JOIN tenants t ON p.tenant_id = t.id
                 LEFT JOIN properties pr ON t.property_id = pr.id
                 LEFT JOIN units u ON t.unit_id = u.id
                 WHERE p.id = ? AND p.admin_id = ? AND p.deleted_at IS NULL";
        
        $payment = $this->db->query($sql, [$id, $admin['id']])->fetch();
        
        if (!$payment) {
            $this->json(['success' => false, 'message' => 'Payment not found'], 404);
            return;
        }
        
        // Generate receipt data (in real implementation, this would create PDF)
        $receiptData = [
            'receipt_number' => 'RCP-' . str_pad($id, 6, '0', STR_PAD_LEFT),
            'payment_date' => $payment['payment_date'],
            'amount' => $payment['amount'],
            'payment_method' => $payment['payment_method'],
            'transaction_id' => $payment['transaction_id'],
            'tenant' => [
                'name' => $payment['tenant_name'],
                'email' => $payment['tenant_email'],
                'phone' => $payment['tenant_phone']
            ],
            'property' => [
                'name' => $payment['property_name'],
                'address' => $payment['property_address'],
                'unit' => $payment['unit_number']
            ]
        ];
        
        $this->json([
            'success' => true,
            'message' => 'Receipt generated successfully',
            'data' => $receiptData
        ]);
    }
}
