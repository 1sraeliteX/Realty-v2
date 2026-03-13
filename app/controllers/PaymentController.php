<?php

namespace App\Controllers;

use DataProvider;
use ViewManager;

// Manually require the database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

class PaymentController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/init_framework.php';
        
        // Load components through registry (anti-scattering compliant)
        \ComponentRegistry::load('ui-components');
        
        // Get database connection
        $pdo = \Config\Database::getInstance()->getConnection();
        
        // Get payments data
        $stmt = $pdo->prepare("
            SELECT p.*, t.name as tenant_name, t.email as tenant_email, pr.name as property_name, u.unit_number,
                   (SELECT COUNT(*) FROM payment_receipts pr WHERE pr.payment_id = p.id) as receipt_count
            FROM payments p
            LEFT JOIN tenants t ON p.tenant_id = t.id
            LEFT JOIN properties pr ON p.property_id = pr.id
            LEFT JOIN units u ON t.unit_id = u.id
            WHERE p.admin_id = ?
            ORDER BY p.payment_date DESC
            LIMIT 50
        ");
        $stmt->execute([$admin['id']]);
        $payments = $stmt->fetchAll();
        
        // Get payment statistics
        $stmt = $pdo->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END), 0) as total_revenue,
                COALESCE(SUM(CASE WHEN status = 'paid' AND payment_date >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN amount ELSE 0 END), 0) as this_month,
                COALESCE(SUM(CASE WHEN status = 'overdue' THEN amount ELSE 0 END), 0) as overdue,
                COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) as pending
            FROM payments 
            WHERE admin_id = ?
        ");
        $stmt->execute([$admin['id']]);
        $stats = $stmt->fetch();
        
        // Get properties for filter
        $stmt = $pdo->prepare("
            SELECT DISTINCT pr.id, pr.name
            FROM properties pr
            INNER JOIN payments p ON pr.id = p.property_id
            WHERE p.admin_id = ?
            ORDER BY pr.name
        ");
        $stmt->execute([$admin['id']]);
        $properties = $stmt->fetchAll();
        
        // Get data from centralized provider (anti-scattering compliant)
        $user = DataProvider::get('user');
        $notifications = DataProvider::get('notifications');
        
        // Set data through ViewManager (anti-scattering compliant)
        ViewManager::set('user', $user);
        ViewManager::set('notifications', $notifications);
        ViewManager::set('payments', $payments);
        ViewManager::set('stats', $stats);
        ViewManager::set('properties', $properties);
        ViewManager::set('admin', $admin);
        
        // Include the payments index view
        include __DIR__ . '/../../views/admin/payments/index.php';
    }
    
    public function create() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Load components through registry (anti-scattering compliant)
        \ComponentRegistry::load('ui-components');
        
        // Get data from centralized provider (anti-scattering compliant)
        $user = DataProvider::get('user');
        $notifications = DataProvider::get('notifications');
        $tenants = DataProvider::get('tenants');
        $properties = DataProvider::get('properties');
        
        // Set data through ViewManager (anti-scattering compliant)
        ViewManager::set('user', $user);
        ViewManager::set('notifications', $notifications);
        ViewManager::set('title', 'Record Payment');
        ViewManager::set('pageTitle', 'Record Payment');
        ViewManager::set('pageDescription', 'Add a new payment record');
        ViewManager::set('tenants', $tenants);
        ViewManager::set('properties', $properties);
        ViewManager::set('admin', $admin);
        
        // Include the payments create view (which includes dashboard layout)
        include __DIR__ . '/../../views/admin/payments/create.php';
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        try {
            // Initialize framework (anti-scattering compliant)
            require_once __DIR__ . '/../../config/init_framework.php';
            
            // Get database connection
            $pdo = \Config\Database::getInstance()->getConnection();
            
            // Start transaction
            $pdo->beginTransaction();
            
            // Insert payment record
            $stmt = $pdo->prepare("
                INSERT INTO payments (admin_id, tenant_id, property_id, amount, payment_type, payment_method, due_date, payment_date, status, notes, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $stmt->execute([
                $admin['id'],
                $_POST['tenant_id'],
                $_POST['property_unit'],
                $_POST['amount'],
                $_POST['payment_type'],
                $_POST['payment_method'] ?? 'cash',
                $_POST['payment_date'],
                $_POST['payment_date'],
                $_POST['status'],
                $_POST['notes'] ?? null
            ]);
            
            $paymentId = $pdo->lastInsertId();
            
            // Handle file uploads if any
            if (!empty($_FILES['receipt_files']['name'][0])) {
                $this->handleReceiptUploads($pdo, $paymentId, $admin['id'], $_FILES['receipt_files'], $_POST['receipt_description'] ?? null);
            }
            
            // Commit transaction
            $pdo->commit();
            
            // Check if this is an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Payment recorded successfully!']);
                exit;
            }
            
            $_SESSION['success'] = 'Payment recorded successfully!';
            $this->redirect('/admin/finances');
            
        } catch (Exception $e) {
            // Rollback transaction on error
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            
            // Log error
            error_log('Payment creation error: ' . $e->getMessage());
            
            // Handle AJAX error response
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error recording payment: ' . $e->getMessage()]);
                exit;
            }
            
            $_SESSION['error'] = 'Error recording payment: ' . $e->getMessage();
            $this->redirect('/admin/payments/create');
        }
    }
    
    private function handleReceiptUploads($pdo, $paymentId, $adminId, $files, $description = null) {
        $uploadDir = __DIR__ . '/../../storage/uploads/receipts/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Process each uploaded file
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = $files['name'][$i];
                $fileTmpPath = $files['tmp_name'][$i];
                $fileSize = $files['size'][$i];
                $fileType = $files['type'][$i];
                
                // Generate unique filename
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $uniqueFileName = 'receipt_' . $paymentId . '_' . time() . '_' . $i . '.' . $fileExtension;
                $filePath = $uploadDir . $uniqueFileName;
                
                // Move uploaded file
                if (move_uploaded_file($fileTmpPath, $filePath)) {
                    // Insert file record into database
                    $stmt = $pdo->prepare("
                        INSERT INTO payment_receipts (payment_id, admin_id, file_name, original_name, file_path, file_size, file_type, mime_type, description, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    
                    $stmt->execute([
                        $paymentId,
                        $adminId,
                        $uniqueFileName,
                        $fileName,
                        'storage/uploads/receipts/' . $uniqueFileName,
                        $fileSize,
                        $fileExtension,
                        $fileType,
                        $description
                    ]);
                } else {
                    throw new Exception('Failed to upload file: ' . $fileName);
                }
            } else {
                throw new Exception('File upload error: ' . $files['error'][$i]);
            }
        }
    }
    
    public function show($id) {
        $admin = $this->requireAuth();
        
        try {
            // Initialize framework (anti-scattering compliant)
            require_once __DIR__ . '/../../config/bootstrap.php';
            
            // Load components through registry (anti-scattering compliant)
            \ComponentRegistry::load('ui-components');
            
            // Get database connection
            $pdo = \Config\Database::getInstance()->getConnection();
            
            // Get payment details
            $stmt = $pdo->prepare("
                SELECT p.*, t.name as tenant_name, pr.name as property_name, u.unit_number
                FROM payments p
                LEFT JOIN tenants t ON p.tenant_id = t.id
                LEFT JOIN properties pr ON p.property_id = pr.id
                LEFT JOIN units u ON t.unit_id = u.id
                WHERE p.id = ? AND p.admin_id = ?
            ");
            $stmt->execute([$id, $admin['id']]);
            $payment = $stmt->fetch();
            
            if (!$payment) {
                $_SESSION['error'] = 'Payment not found';
                $this->redirect('/admin/finances');
            }
            
            // Get payment receipts
            $stmt = $pdo->prepare("
                SELECT * FROM payment_receipts 
                WHERE payment_id = ? AND admin_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$id, $admin['id']]);
            $receipts = $stmt->fetchAll();
            
            // Get data from centralized provider (anti-scattering compliant)
            $user = DataProvider::get('user');
            $notifications = DataProvider::get('notifications');
            
            // Set data through ViewManager (anti-scattering compliant)
            ViewManager::set('user', $user);
            ViewManager::set('notifications', $notifications);
            ViewManager::set('title', 'Payment Details');
            ViewManager::set('pageTitle', 'Payment Details');
            ViewManager::set('pageDescription', 'View payment information and receipts');
            ViewManager::set('payment', $payment);
            ViewManager::set('receipts', $receipts);
            ViewManager::set('admin', $admin);
            
            // Include the payments show view (which includes dashboard layout)
            include __DIR__ . '/../../views/admin/payments/show.php';
            
        } catch (Exception $e) {
            error_log('Payment view error: ' . $e->getMessage());
            $_SESSION['error'] = 'Error loading payment details';
            $this->redirect('/admin/finances');
        }
    }
    
    public function downloadReceipt($id) {
        $admin = $this->requireAuth();
        
        try {
            $pdo = \Config\Database::getInstance()->getConnection();
            
            // Get receipt details
            $stmt = $pdo->prepare("
                SELECT pr.*, p.admin_id 
                FROM payment_receipts pr
                JOIN payments p ON pr.payment_id = p.id
                WHERE pr.id = ? AND pr.admin_id = ?
            ");
            $stmt->execute([$id, $admin['id']]);
            $receipt = $stmt->fetch();
            
            if (!$receipt) {
                http_response_code(404);
                echo 'Receipt not found';
                exit;
            }
            
            $filePath = __DIR__ . '/../../' . $receipt['file_path'];
            
            if (!file_exists($filePath)) {
                http_response_code(404);
                echo 'File not found';
                exit;
            }
            
            // Set headers for file download
            header('Content-Type: ' . $receipt['mime_type']);
            header('Content-Disposition: attachment; filename="' . $receipt['original_name'] . '"');
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: private, no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            readfile($filePath);
            exit;
            
        } catch (Exception $e) {
            error_log('Receipt download error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Error downloading file';
            exit;
        }
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get database connection
        $pdo = \Config\Database::getInstance()->getConnection();
        
        // Get payment details
        $stmt = $pdo->prepare("
            SELECT p.*, t.name as tenant_name, pr.name as property_name, u.unit_number
            FROM payments p
            LEFT JOIN tenants t ON p.tenant_id = t.id
            LEFT JOIN properties pr ON p.property_id = pr.id
            LEFT JOIN units u ON t.unit_id = u.id
            WHERE p.id = ? AND p.admin_id = ?
        ");
        $stmt->execute([$id, $admin['id']]);
        $payment = $stmt->fetch();
        
        if (!$payment) {
            $_SESSION['error'] = 'Payment not found';
            $this->redirect('/admin/payments');
            return;
        }
        
        // Get tenants for selection
        $tenantsSql = "SELECT id, name FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Get properties for selection
        $propertiesSql = "SELECT id, name FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $properties = $this->db->query($propertiesSql, [$admin['id']])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('payment', $payment);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('properties', $properties);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Edit Payment');
        
        // Include the edit view
        include __DIR__ . '/../../views/admin/payments/edit.php';
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if payment exists and belongs to admin
        $payment = $this->db->query("SELECT id FROM payments WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$payment) {
            $_SESSION['error'] = 'Payment not found';
            $this->redirect('/admin/payments');
            return;
        }
        
        // Validate required fields
        $required = ['tenant_id', 'amount', 'payment_type', 'payment_method'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Field '$field' is required";
                $this->redirect("/admin/payments/$id/edit");
                return;
            }
        }
        
        try {
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['tenant_id', 'property_id', 'amount', 'payment_type', 'payment_method', 
                              'due_date', 'payment_date', 'status', 'notes'];
            
            foreach ($allowedFields as $field) {
                if (isset($_POST[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $_POST[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE payments SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
            }
            
            $_SESSION['success'] = 'Payment updated successfully';
            $this->redirect('/admin/payments');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to update payment: ' . $e->getMessage();
            $this->redirect("/admin/payments/$id/edit");
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if payment exists and belongs to admin
        $payment = $this->db->query("SELECT id FROM payments WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$payment) {
            $_SESSION['error'] = 'Payment not found';
            $this->redirect('/admin/payments');
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Soft delete payment
            $this->db->query("UPDATE payments SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            // Soft delete associated receipts
            $this->db->query("UPDATE payment_receipts SET deleted_at = NOW() WHERE payment_id = ?", [$id]);
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Payment deleted successfully';
            $this->redirect('/admin/payments');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to delete payment: ' . $e->getMessage();
            $this->redirect('/admin/payments');
        }
    }
}
