<?php

namespace App\Controllers;

class ApiTenantController extends BaseController {
    public function index() {
        $admin = $this->requireApiAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get pagination and filter parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        // Build query
        $where = ["t.admin_id = ?", "t.deleted_at IS NULL"];
        $params = [$admin['id']];
        
        if (!empty($search)) {
            $where[] = "(t.name LIKE ? OR t.email LIKE ? OR t.phone LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($status)) {
            $where[] = "t.payment_status = ?";
            $params[] = $status;
        }
        
        // Get tenants with property and unit info
        $sql = "SELECT t.*, 
                        p.name as property_name,
                        p.address as property_address,
                        u.unit_number,
                        u.rent_price,
                        u.status as unit_status
                 FROM tenants t
                 LEFT JOIN properties p ON t.property_id = p.id
                 LEFT JOIN units u ON t.unit_id = u.id
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY t.created_at DESC
                 LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;
        
        $tenants = $this->db->query($sql, $params)->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM tenants t WHERE " . implode(' AND ', $where);
        $countParams = array_slice($params, 0, count($params) - 2); // Remove limit and offset
        $total = $this->db->query($countSql, $countParams)->fetchColumn();
        
        $this->json([
            'success' => true,
            'data' => $tenants,
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
        
        $sql = "SELECT t.*, 
                        p.name as property_name,
                        p.address as property_address,
                        u.unit_number,
                        u.rent_price,
                        u.status as unit_status,
                        (SELECT COUNT(*) FROM payments pay WHERE pay.tenant_id = t.id AND pay.status = 'paid') as payment_count
                 FROM tenants t
                 LEFT JOIN properties p ON t.property_id = p.id
                 LEFT JOIN units u ON t.unit_id = u.id
                 WHERE t.id = ? AND t.admin_id = ? AND t.deleted_at IS NULL";
        
        $tenant = $this->db->query($sql, [$id, $admin['id']])->fetch();
        
        if (!$tenant) {
            $this->json(['success' => false, 'message' => 'Tenant not found'], 404);
            return;
        }
        
        $this->json(['success' => true, 'data' => $tenant]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['name', 'email', 'phone', 'unit_id', 'rent_start_date', 'rent_expiry_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->json(['success' => false, 'message' => "Field '$field' is required"], 400);
                return;
            }
        }
        
        // Check if unit is available
        $unitCheck = $this->db->query("SELECT id, status FROM units WHERE id = ? AND admin_id = ?", [$data['unit_id'], $admin['id']])->fetch();
        if (!$unitCheck || $unitCheck['status'] !== 'available') {
            $this->json(['success' => false, 'message' => 'Unit is not available'], 400);
            return;
        }
        
        // Check for duplicate email
        $emailCheck = $this->db->query("SELECT id FROM tenants WHERE email = ? AND admin_id = ? AND deleted_at IS NULL", [$data['email'], $admin['id']])->fetch();
        if ($emailCheck) {
            $this->json(['success' => false, 'message' => 'Email already exists'], 400);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Insert tenant
            $sql = "INSERT INTO tenants (admin_id, unit_id, name, email, phone, next_of_kin, next_of_kin_phone, 
                      number_of_occupants, rent_start_date, rent_expiry_date, payment_status, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $admin['id'],
                $data['unit_id'],
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['next_of_kin'] ?? null,
                $data['next_of_kin_phone'] ?? null,
                $data['number_of_occupants'] ?? 1,
                $data['rent_start_date'],
                $data['rent_expiry_date'],
                'pending'
            ];
            
            $this->db->query($sql, $params);
            $tenantId = $this->db->lastInsertId();
            
            // Update unit status
            $this->db->query("UPDATE units SET status = 'occupied' WHERE id = ?", [$data['unit_id']]);
            
            $this->db->commit();
            
            $this->json([
                'success' => true,
                'message' => 'Tenant created successfully',
                'data' => ['id' => $tenantId]
            ]);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to create tenant: ' . $e->getMessage()], 500);
        }
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if tenant exists and belongs to admin
        $tenant = $this->db->query("SELECT id FROM tenants WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$tenant) {
            $this->json(['success' => false, 'message' => 'Tenant not found'], 404);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['name', 'email', 'phone', 'next_of_kin', 'next_of_kin_phone', 'number_of_occupants', 
                              'rent_start_date', 'rent_expiry_date', 'notes'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE tenants SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
            }
            
            $this->db->commit();
            
            $this->json(['success' => true, 'message' => 'Tenant updated successfully']);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to update tenant: ' . $e->getMessage()], 500);
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if tenant exists and belongs to admin
        $tenant = $this->db->query("SELECT id, unit_id FROM tenants WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$tenant) {
            $this->json(['success' => false, 'message' => 'Tenant not found'], 404);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Soft delete tenant
            $this->db->query("UPDATE tenants SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            // Update unit status back to available
            $this->db->query("UPDATE units SET status = 'available' WHERE id = ?", [$tenant['unit_id']]);
            
            $this->db->commit();
            
            $this->json(['success' => true, 'message' => 'Tenant deleted successfully']);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to delete tenant: ' . $e->getMessage()], 500);
        }
    }
    
    public function getPayments($tenantId) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $sql = "SELECT p.*, t.name as tenant_name
                 FROM payments p
                 LEFT JOIN tenants t ON p.tenant_id = t.id
                 WHERE p.tenant_id = ? AND p.admin_id = ? AND p.deleted_at IS NULL
                 ORDER BY p.payment_date DESC";
        
        $payments = $this->db->query($sql, [$tenantId, $admin['id']])->fetchAll();
        
        $this->json(['success' => true, 'data' => $payments]);
    }
    
    public function getLeaseHistory($tenantId) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // This would typically come from a lease_history table
        // For now, return basic lease info from tenant record
        $sql = "SELECT t.*, p.name as property_name, u.unit_number
                 FROM tenants t
                 LEFT JOIN properties p ON t.property_id = p.id
                 LEFT JOIN units u ON t.unit_id = u.id
                 WHERE t.id = ? AND t.admin_id = ? AND t.deleted_at IS NULL";
        
        $leaseInfo = $this->db->query($sql, [$tenantId, $admin['id']])->fetch();
        
        $this->json(['success' => true, 'data' => $leaseInfo]);
    }
}
