<?php

namespace App\Controllers;

class InvoiceController extends BaseController {
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
        
        // Get statistics
        $statsSql = "SELECT 
                        COUNT(*) as total_invoices,
                        SUM(amount) as total_amount,
                        AVG(amount) as avg_amount,
                        SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_count,
                        SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                        SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                        SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) as overdue_count
                     FROM invoices 
                     WHERE admin_id = ? AND deleted_at IS NULL";
        $stats = $this->db->query($statsSql, [$admin['id']])->fetch();
        
        // Get tenants for filters
        $tenantsSql = "SELECT id, name FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('invoices', $invoices);
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
            'status' => $status,
            'tenant_id' => $tenantId
        ]);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Invoices');
        
        // Include the invoices index view
        include __DIR__ . '/../../views/admin/invoices/index.php';
    }
    
    public function create() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get tenants for selection
        $tenantsSql = "SELECT id, name, email, property_id, unit_id FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Get invoice templates
        $templatesSql = "SELECT * FROM invoice_templates WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $templates = $this->db->query($templatesSql, [$admin['id']])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('templates', $templates);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Create Invoice');
        
        // Include the create view
        include __DIR__ . '/../../views/admin/invoices/create.php';
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Validate required fields
        $required = ['tenant_id', 'items'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Field '$field' is required";
                $this->redirect('/admin/invoices/create');
                return;
            }
        }
        
        // Check if tenant exists and belongs to admin
        $tenantCheck = $this->db->query("SELECT id FROM tenants WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                       [$_POST['tenant_id'], $admin['id']])->fetch();
        if (!$tenantCheck) {
            $_SESSION['error'] = 'Tenant not found';
            $this->redirect('/admin/invoices/create');
            return;
        }
        
        // Validate invoice items
        $items = json_decode($_POST['items'], true) ?? [];
        if (empty($items)) {
            $_SESSION['error'] = 'Invoice items are required';
            $this->redirect('/admin/invoices/create');
            return;
        }
        
        $totalAmount = 0;
        foreach ($items as $item) {
            if (empty($item['description']) || empty($item['amount'])) {
                $_SESSION['error'] = 'Each item must have description and amount';
                $this->redirect('/admin/invoices/create');
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
                $_POST['tenant_id'],
                $invoiceNumber,
                $totalAmount,
                $_POST['due_date'] ?? date('Y-m-d', strtotime('+30 days')),
                $_POST['status'] ?? 'draft',
                $_POST['notes'] ?? null
            ];
            
            $this->db->query($sql, $params);
            $invoiceId = $this->db->lastInsertId();
            
            // Insert invoice items
            $itemSql = "INSERT INTO invoice_items (invoice_id, description, amount, quantity, created_at) VALUES (?, ?, ?, ?, NOW())";
            foreach ($items as $item) {
                $this->db->query($itemSql, [
                    $invoiceId,
                    $item['description'],
                    $item['amount'],
                    $item['quantity'] ?? 1
                ]);
            }
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Invoice created successfully';
            $this->redirect('/admin/invoices');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to create invoice: ' . $e->getMessage();
            $this->redirect('/admin/invoices/create');
        }
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
            $_SESSION['error'] = 'Invoice not found';
            $this->redirect('/admin/invoices');
            return;
        }
        
        // Get invoice items
        $itemsSql = "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY created_at";
        $invoice['items'] = $this->db->query($itemsSql, [$id])->fetchAll();
        
        // Get payment history
        $paymentsSql = "SELECT * FROM payments WHERE invoice_id = ? AND deleted_at IS NULL ORDER BY created_at DESC";
        $invoice['payments'] = $this->db->query($paymentsSql, [$id])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('invoice', $invoice);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Invoice Details');
        
        // Include the show view
        include __DIR__ . '/../../views/admin/invoices/show.php';
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if invoice exists and belongs to admin
        $invoice = $this->db->query("SELECT * FROM invoices WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$invoice) {
            $_SESSION['error'] = 'Invoice not found';
            $this->redirect('/admin/invoices');
            return;
        }
        
        // Prevent editing sent invoices
        if ($invoice['status'] === 'sent' || $invoice['status'] === 'paid') {
            $_SESSION['error'] = 'Cannot edit sent or paid invoice';
            $this->redirect('/admin/invoices');
            return;
        }
        
        // Get tenants for selection
        $tenantsSql = "SELECT id, name FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Get invoice items
        $itemsSql = "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY created_at";
        $invoice['items'] = $this->db->query($itemsSql, [$id])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('invoice', $invoice);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Edit Invoice');
        
        // Include the edit view
        include __DIR__ . '/../../views/admin/invoices/edit.php';
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if invoice exists and belongs to admin
        $invoice = $this->db->query("SELECT id, status FROM invoices WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$invoice) {
            $_SESSION['error'] = 'Invoice not found';
            $this->redirect('/admin/invoices');
            return;
        }
        
        // Prevent updating sent invoices
        if ($invoice['status'] === 'sent' || $invoice['status'] === 'paid') {
            $_SESSION['error'] = 'Cannot update sent or paid invoice';
            $this->redirect('/admin/invoices');
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['due_date', 'status', 'notes'];
            
            foreach ($allowedFields as $field) {
                if (isset($_POST[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $_POST[$field];
                }
            }
            
            // Update invoice items if provided
            if (!empty($_POST['items'])) {
                // Delete existing items
                $this->db->query("DELETE FROM invoice_items WHERE invoice_id = ?", [$id]);
                
                // Add new items
                $items = json_decode($_POST['items'], true) ?? [];
                $totalAmount = 0;
                $itemSql = "INSERT INTO invoice_items (invoice_id, description, amount, quantity, created_at) VALUES (?, ?, ?, ?, NOW())";
                foreach ($items as $item) {
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
            
            $_SESSION['success'] = 'Invoice updated successfully';
            $this->redirect('/admin/invoices');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to update invoice: ' . $e->getMessage();
            $this->redirect("/admin/invoices/$id/edit");
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if invoice exists and belongs to admin
        $invoice = $this->db->query("SELECT id, status FROM invoices WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$invoice) {
            $_SESSION['error'] = 'Invoice not found';
            $this->redirect('/admin/invoices');
            return;
        }
        
        // Prevent deleting paid invoices
        if ($invoice['status'] === 'paid') {
            $_SESSION['error'] = 'Cannot delete paid invoice';
            $this->redirect('/admin/invoices');
            return;
        }
        
        try {
            // Soft delete invoice
            $this->db->query("UPDATE invoices SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            $_SESSION['success'] = 'Invoice deleted successfully';
            $this->redirect('/admin/invoices');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to delete invoice: ' . $e->getMessage();
            $this->redirect('/admin/invoices');
        }
    }
    
    public function send($id) {
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
            $_SESSION['error'] = 'Invoice not found';
            $this->redirect('/admin/invoices');
            return;
        }
        
        try {
            // Update status to sent
            $this->db->query("UPDATE invoices SET status = 'sent', sent_at = NOW(), updated_at = NOW() WHERE id = ?", [$id]);
            
            // In real implementation, send email here
            error_log("Invoice sent: ID=$id, Number={$invoice['invoice_number']}, Email={$invoice['tenant_email']}");
            
            $_SESSION['success'] = 'Invoice sent successfully';
            $this->redirect('/admin/invoices');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to send invoice: ' . $e->getMessage();
            $this->redirect('/admin/invoices');
        }
    }
    
    public function markAsPaid($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if invoice exists and belongs to admin
        $invoice = $this->db->query("SELECT id FROM invoices WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$invoice) {
            $_SESSION['error'] = 'Invoice not found';
            $this->redirect('/admin/invoices');
            return;
        }
        
        try {
            // Update status to paid
            $this->db->query("UPDATE invoices SET status = 'paid', paid_at = NOW(), updated_at = NOW() WHERE id = ?", [$id]);
            
            $_SESSION['success'] = 'Invoice marked as paid';
            $this->redirect('/admin/invoices');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to mark invoice as paid: ' . $e->getMessage();
            $this->redirect('/admin/invoices');
        }
    }
    
    private function getNextInvoiceNumber($adminId) {
        $sql = "SELECT COUNT(*) as count FROM invoices WHERE admin_id = ? AND YEAR(created_at) = YEAR(NOW())";
        $result = $this->db->query($sql, [$adminId])->fetch();
        return $result['count'] + 1;
    }
}
