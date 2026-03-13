<?php

namespace App\Controllers;

class ApiUnitController extends BaseController {
    public function index() {
        $admin = $this->requireApiAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get pagination and filter parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $propertyId = $_GET['property_id'] ?? '';
        
        // Build query
        $where = ["p.admin_id = ?", "u.deleted_at IS NULL"];
        $params = [$admin['id']];
        
        if (!empty($search)) {
            $where[] = "(u.unit_number LIKE ? OR u.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($status)) {
            $where[] = "u.status = ?";
            $params[] = $status;
        }
        
        if (!empty($propertyId)) {
            $where[] = "u.property_id = ?";
            $params[] = $propertyId;
        }
        
        // Get units with property and tenant info
        $sql = "SELECT u.*, 
                        p.name as property_name,
                        p.address as property_address,
                        t.name as tenant_name,
                        t.email as tenant_email
                 FROM units u
                 LEFT JOIN properties p ON u.property_id = p.id
                 LEFT JOIN tenants t ON u.id = t.unit_id AND t.deleted_at IS NULL
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY u.created_at DESC
                 LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;
        
        $units = $this->db->query($sql, $params)->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM units u LEFT JOIN properties p ON u.property_id = p.id WHERE " . implode(' AND ', $where);
        $countParams = array_slice($params, 0, count($params) - 2); // Remove limit and offset
        $total = $this->db->query($countSql, $countParams)->fetchColumn();
        
        $this->json([
            'success' => true,
            'data' => $units,
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
        
        $sql = "SELECT u.*, 
                        p.name as property_name,
                        p.address as property_address,
                        t.name as tenant_name,
                        t.email as tenant_email,
                        t.phone as tenant_phone
                 FROM units u
                 LEFT JOIN properties p ON u.property_id = p.id
                 LEFT JOIN tenants t ON u.id = t.unit_id AND t.deleted_at IS NULL
                 WHERE u.id = ? AND u.admin_id = ? AND u.deleted_at IS NULL";
        
        $unit = $this->db->query($sql, [$id, $admin['id']])->fetch();
        
        if (!$unit) {
            $this->json(['success' => false, 'message' => 'Unit not found'], 404);
            return;
        }
        
        $this->json(['success' => true, 'data' => $unit]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['property_id', 'unit_number', 'rent_price', 'bedrooms', 'bathrooms'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->json(['success' => false, 'message' => "Field '$field' is required"], 400);
                return;
            }
        }
        
        // Check if property exists and belongs to admin
        $propertyCheck = $this->db->query("SELECT id FROM properties WHERE id = ? AND admin_id = ?", [$data['property_id'], $admin['id']])->fetch();
        if (!$propertyCheck) {
            $this->json(['success' => false, 'message' => 'Property not found'], 400);
            return;
        }
        
        // Check for duplicate unit number in same property
        $unitCheck = $this->db->query("SELECT id FROM units WHERE unit_number = ? AND property_id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                     [$data['unit_number'], $data['property_id'], $admin['id']])->fetch();
        if ($unitCheck) {
            $this->json(['success' => false, 'message' => 'Unit number already exists in this property'], 400);
            return;
        }
        
        try {
            $sql = "INSERT INTO units (admin_id, property_id, unit_number, rent_price, bedrooms, bathrooms, 
                      square_feet, description, status, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $admin['id'],
                $data['property_id'],
                $data['unit_number'],
                $data['rent_price'],
                $data['bedrooms'],
                $data['bathrooms'],
                $data['square_feet'] ?? null,
                $data['description'] ?? null,
                $data['status'] ?? 'available'
            ];
            
            $this->db->query($sql, $params);
            $unitId = $this->db->lastInsertId();
            
            $this->json([
                'success' => true,
                'message' => 'Unit created successfully',
                'data' => ['id' => $unitId]
            ]);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to create unit: ' . $e->getMessage()], 500);
        }
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if unit exists and belongs to admin
        $unit = $this->db->query("SELECT id FROM units WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$unit) {
            $this->json(['success' => false, 'message' => 'Unit not found'], 404);
            return;
        }
        
        try {
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['unit_number', 'rent_price', 'bedrooms', 'bathrooms', 'square_feet', 
                              'description', 'status'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE units SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
            }
            
            $this->json(['success' => true, 'message' => 'Unit updated successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update unit: ' . $e->getMessage()], 500);
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if unit exists and belongs to admin
        $unit = $this->db->query("SELECT id FROM units WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$unit) {
            $this->json(['success' => false, 'message' => 'Unit not found'], 404);
            return;
        }
        
        // Check if unit has active tenant
        $tenantCheck = $this->db->query("SELECT id FROM tenants WHERE unit_id = ? AND deleted_at IS NULL", [$id])->fetch();
        if ($tenantCheck) {
            $this->json(['success' => false, 'message' => 'Cannot delete unit with active tenant'], 400);
            return;
        }
        
        try {
            // Soft delete unit
            $this->db->query("UPDATE units SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            $this->json(['success' => true, 'message' => 'Unit deleted successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to delete unit: ' . $e->getMessage()], 500);
        }
    }
    
    public function getAvailableUnits() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $propertyId = $_GET['property_id'] ?? '';
        
        $sql = "SELECT u.*, p.name as property_name
                 FROM units u
                 LEFT JOIN properties p ON u.property_id = p.id
                 WHERE u.admin_id = ? AND u.status = 'available' AND u.deleted_at IS NULL";
        $params = [$admin['id']];
        
        if (!empty($propertyId)) {
            $sql .= " AND u.property_id = ?";
            $params[] = $propertyId;
        }
        
        $sql .= " ORDER BY u.unit_number";
        
        $units = $this->db->query($sql, $params)->fetchAll();
        
        $this->json(['success' => true, 'data' => $units]);
    }
    
    public function getUnitStats() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $sql = "SELECT 
                    COUNT(*) as total_units,
                    SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied_units,
                    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_units,
                    SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance_units,
                    AVG(rent_price) as avg_rent_price
                 FROM units 
                 WHERE admin_id = ? AND deleted_at IS NULL";
        
        $stats = $this->db->query($sql, [$admin['id']])->fetch();
        
        $this->json(['success' => true, 'data' => $stats]);
    }
}
