<?php

namespace App\Controllers;

// Manually require the database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

class UnitController extends BaseController {
    
    public function index() {
        $admin = $this->requireAuth();
        
        // Get search and filter parameters
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? 'all';
        $type = $_GET['type'] ?? 'all';
        $property_id = $_GET['property_id'] ?? 'all';
        $view = $_GET['view'] ?? 'grid';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 12;
        
        // Get units statistics
        $stats = $this->getUnitsStats($admin['id']);
        
        // Get properties for filter dropdown
        $properties = $this->getProperties($admin['id']);
        
        // Get filtered units
        $units = $this->getFilteredUnits($admin['id'], $search, $status, $type, $property_id, $page, $limit);
        
        // Get unit types for filter
        $unitTypes = $this->getUnitTypes();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/init_framework.php';
        
        // Set data through ViewManager (anti-scattering compliant)
        \ViewManager::set('title', 'Units Management');
        \ViewManager::set('user', $admin);
        \ViewManager::set('stats', $stats);
        \ViewManager::set('units', $units['data']);
        \ViewManager::set('properties', $properties);
        \ViewManager::set('unitTypes', $unitTypes);
        \ViewManager::set('pagination', $units['pagination']);
        \ViewManager::set('search', $search);
        \ViewManager::set('status', $status);
        \ViewManager::set('type', $type);
        \ViewManager::set('property_id', $property_id);
        \ViewManager::set('currentView', $view);
        
        // Render using ViewManager with admin dashboard layout (anti-scattering compliant)
        echo \ViewManager::render('admin.units.list', [], 'admin.dashboard_layout');
    }
    
    public function create() {
        $admin = $this->requireAuth();
        
        // Get properties for dropdown
        $properties = $this->getProperties($admin['id']);
        $unitTypes = $this->getUnitTypes();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/init_framework.php';
        
        // Set data through ViewManager (anti-scattering compliant)
        \ViewManager::set('title', 'Create Unit');
        \ViewManager::set('user', $admin);
        \ViewManager::set('properties', $properties);
        \ViewManager::set('unitTypes', $unitTypes);
        
        // Render using ViewManager with admin dashboard layout (anti-scattering compliant)
        echo \ViewManager::render('admin.units.create', [], 'admin.dashboard_layout');
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Get form data
        $data = $this->getPostData();
        
        // Validate required fields
        $required = ['property_id', 'unit_number', 'unit_type'];
        $errors = $this->validateRequired($data, $required);
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 422);
            return;
        }
        
        try {
            $pdo = $this->db->getConnection();
            
            // Check if unit number already exists for this property
            $stmt = $pdo->prepare("SELECT id FROM units WHERE property_id = ? AND unit_number = ? AND deleted_at IS NULL");
            $stmt->execute([$data['property_id'], $data['unit_number']]);
            if ($stmt->fetch()) {
                $this->json(['success' => false, 'message' => 'Unit number already exists for this property'], 422);
                return;
            }
            
            // Insert new unit
            $unitData = [
                'property_id' => $data['property_id'],
                'unit_number' => $data['unit_number'],
                'type' => $data['unit_type'],
                'bedrooms' => $data['bedrooms'] ?? $this->getBedroomsFromType($data['unit_type']),
                'bathrooms' => $data['bathrooms'] ?? $this->getBathroomsFromType($data['unit_type']),
                'kitchens' => 1,
                'rent_price' => $data['rent_price'] ?? null,
                'status' => $data['status'] ?? 'available',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('units', $unitData);
            $unitId = $pdo->lastInsertId();
            
            // Get property name for activity log
            $stmt = $pdo->prepare("SELECT name FROM properties WHERE id = ?");
            $stmt->execute([$data['property_id']]);
            $property = $stmt->fetch();
            
            // Log activity
            $this->logActivity($admin['id'], 'create', "Created unit {$data['unit_number']} in property {$property['name']}", 'unit', $unitId);
            
            $this->json(['success' => true, 'message' => 'Unit created successfully', 'unit_id' => $unitId]);
            
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to create unit: ' . $e->getMessage()], 500);
        }
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        
        // Get unit details
        $unit = $this->getUnitById($admin['id'], $id);
        if (!$unit) {
            $this->redirect('/admin/units');
            return;
        }
        
        // Get properties for dropdown
        $properties = $this->getProperties($admin['id']);
        $unitTypes = $this->getUnitTypes();
        
        $this->view('units.edit', [
            'admin' => $admin,
            'unit' => $unit,
            'properties' => $properties,
            'unitTypes' => $unitTypes
        ]);
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Get form data
        $data = $this->getPostData();
        
        // Validate required fields
        $required = ['property_id', 'unit_number', 'unit_type'];
        $errors = $this->validateRequired($data, $required);
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 422);
            return;
        }
        
        try {
            $pdo = $this->db->getConnection();
            
            // Check if unit exists and belongs to admin
            $unit = $this->getUnitById($admin['id'], $id);
            if (!$unit) {
                $this->json(['success' => false, 'message' => 'Unit not found'], 404);
                return;
            }
            
            // Check if unit number already exists for this property (excluding current unit)
            $stmt = $pdo->prepare("SELECT id FROM units WHERE property_id = ? AND unit_number = ? AND id != ? AND deleted_at IS NULL");
            $stmt->execute([$data['property_id'], $data['unit_number'], $id]);
            if ($stmt->fetch()) {
                $this->json(['success' => false, 'message' => 'Unit number already exists for this property'], 422);
                return;
            }
            
            // Update unit
            $unitData = [
                'property_id' => $data['property_id'],
                'unit_number' => $data['unit_number'],
                'type' => $data['unit_type'],
                'bedrooms' => $data['bedrooms'] ?? $this->getBedroomsFromType($data['unit_type']),
                'bathrooms' => $data['bathrooms'] ?? $this->getBathroomsFromType($data['unit_type']),
                'kitchens' => 1,
                'rent_price' => $data['rent_price'] ?? null,
                'status' => $data['status'] ?? 'available',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->update('units', $unitData, 'id = ?', [$id]);
            
            // Get property name for activity log
            $stmt = $pdo->prepare("SELECT name FROM properties WHERE id = ?");
            $stmt->execute([$data['property_id']]);
            $property = $stmt->fetch();
            
            // Log activity
            $this->logActivity($admin['id'], 'update', "Updated unit {$data['unit_number']} in property {$property['name']}", 'unit', $id);
            
            $this->json(['success' => true, 'message' => 'Unit updated successfully']);
            
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update unit: ' . $e->getMessage()], 500);
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        try {
            $pdo = $this->db->getConnection();
            
            // Check if unit exists and belongs to admin
            $unit = $this->getUnitById($admin['id'], $id);
            if (!$unit) {
                $this->json(['success' => false, 'message' => 'Unit not found'], 404);
                return;
            }
            
            // Check if unit has tenants
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tenants WHERE unit_id = ? AND deleted_at IS NULL");
            $stmt->execute([$id]);
            $tenantCount = $stmt->fetchColumn();
            
            if ($tenantCount > 0) {
                $this->json(['success' => false, 'message' => 'Cannot delete unit with active tenants'], 422);
                return;
            }
            
            // Soft delete unit
            $this->db->update('units', ['deleted_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
            
            // Get property name for activity log
            $stmt = $pdo->prepare("SELECT p.name FROM properties p JOIN units u ON p.id = u.property_id WHERE u.id = ?");
            $stmt->execute([$id]);
            $property = $stmt->fetch();
            
            // Log activity
            $this->logActivity($admin['id'], 'delete', "Deleted unit {$unit['unit_number']} from property {$property['name']}", 'unit', $id);
            
            $this->json(['success' => true, 'message' => 'Unit deleted successfully']);
            
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to delete unit: ' . $e->getMessage()], 500);
        }
    }
    
    private function getUnitsStats($adminId) {
        $stats = [];
        $pdo = $this->db->getConnection();
        
        // Total units
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE p.admin_id = ? AND u.deleted_at IS NULL");
        $stmt->execute([$adminId]);
        $stats['total_units'] = $stmt->fetchColumn();
        
        // Occupied units
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE p.admin_id = ? AND u.status = 'occupied' AND u.deleted_at IS NULL");
        $stmt->execute([$adminId]);
        $stats['occupied_units'] = $stmt->fetchColumn();
        
        // Available units
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE p.admin_id = ? AND u.status = 'available' AND u.deleted_at IS NULL");
        $stmt->execute([$adminId]);
        $stats['vacant_units'] = $stmt->fetchColumn();
        
        // Maintenance units
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE p.admin_id = ? AND u.status = 'maintenance' AND u.deleted_at IS NULL");
        $stmt->execute([$adminId]);
        $stats['maintenance_units'] = $stmt->fetchColumn();
        
        // Occupancy rate
        if ($stats['total_units'] > 0) {
            $stats['occupancy_rate'] = round(($stats['occupied_units'] / $stats['total_units']) * 100, 1);
        } else {
            $stats['occupancy_rate'] = 0;
        }
        
        return $stats;
    }
    
    private function getFilteredUnits($adminId, $search, $status, $type, $propertyId, $page, $limit) {
        $offset = ($page - 1) * $limit;
        $pdo = $this->db->getConnection();
        
        // Build query
        $whereConditions = ["p.admin_id = ?", "u.deleted_at IS NULL"];
        $params = [$adminId];
        
        if (!empty($search)) {
            $whereConditions[] = "(u.unit_number LIKE ? OR p.name LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        if ($status !== 'all') {
            $whereConditions[] = "u.status = ?";
            $params[] = $status;
        }
        
        if ($type !== 'all') {
            $whereConditions[] = "u.type = ?";
            $params[] = $type;
        }
        
        if ($propertyId !== 'all') {
            $whereConditions[] = "u.property_id = ?";
            $params[] = $propertyId;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM units u 
                      JOIN properties p ON u.property_id = p.id 
                      WHERE {$whereClause}";
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();
        
        // Get paginated results
        $query = "SELECT u.*, p.name as property_name, p.address as property_address 
                 FROM units u 
                 JOIN properties p ON u.property_id = p.id 
                 WHERE {$whereClause} 
                 ORDER BY u.created_at DESC 
                 LIMIT {$limit} OFFSET {$offset}";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $units = $stmt->fetchAll();
        
        return [
            'data' => $units,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
                'has_next' => $page < ceil($total / $limit),
                'has_prev' => $page > 1
            ]
        ];
    }
    
    private function getProperties($adminId) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT id, name FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name");
        $stmt->execute([$adminId]);
        return $stmt->fetchAll();
    }
    
    private function getUnitById($adminId, $unitId) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT u.*, p.name as property_name FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE u.id = ? AND p.admin_id = ? AND u.deleted_at IS NULL");
        $stmt->execute([$unitId, $adminId]);
        return $stmt->fetch();
    }
    
    private function getUnitTypes() {
        return [
            'studio' => 'Studio',
            '1br' => '1 Bedroom',
            '2br' => '2 Bedrooms',
            '3br' => '3 Bedrooms',
            '4br' => '4 Bedrooms',
            'penthouse' => 'Penthouse',
            'office' => 'Office',
            'retail' => 'Retail',
            'warehouse' => 'Warehouse'
        ];
    }
    
    private function getBedroomsFromType($type) {
        $bedrooms = [
            'studio' => 0,
            '1br' => 1,
            '2br' => 2,
            '3br' => 3,
            '4br' => 4,
            'penthouse' => 4,
            'office' => 0,
            'retail' => 0,
            'warehouse' => 0
        ];
        
        return $bedrooms[$type] ?? 0;
    }
    
    private function getBathroomsFromType($type) {
        $bathrooms = [
            'studio' => 1.0,
            '1br' => 1.0,
            '2br' => 2.0,
            '3br' => 2.0,
            '4br' => 3.0,
            'penthouse' => 3.5,
            'office' => 1.0,
            'retail' => 1.0,
            'warehouse' => 1.0
        ];
        
        return $bathrooms[$type] ?? 1.0;
    }
}
