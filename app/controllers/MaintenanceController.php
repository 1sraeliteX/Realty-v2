<?php

namespace App\Controllers;

class MaintenanceController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get pagination and filter parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $priority = $_GET['priority'] ?? '';
        $propertyId = $_GET['property_id'] ?? '';
        
        // Build query
        $where = ["m.admin_id = ?", "m.deleted_at IS NULL"];
        $params = [$admin['id']];
        
        if (!empty($search)) {
            $where[] = "(m.title LIKE ? OR m.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($status)) {
            $where[] = "m.status = ?";
            $params[] = $status;
        }
        
        if (!empty($priority)) {
            $where[] = "m.priority = ?";
            $params[] = $priority;
        }
        
        if (!empty($propertyId)) {
            $where[] = "m.property_id = ?";
            $params[] = $propertyId;
        }
        
        // Get maintenance requests with tenant and property info
        $sql = "SELECT m.*, 
                        t.name as tenant_name,
                        t.email as tenant_email,
                        pr.name as property_name,
                        pr.address as property_address,
                        u.unit_number
                 FROM maintenance_requests m
                 LEFT JOIN tenants t ON m.tenant_id = t.id
                 LEFT JOIN properties pr ON m.property_id = pr.id
                 LEFT JOIN units u ON m.unit_id = u.id
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY m.priority DESC, m.created_at DESC
                 LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;
        
        $requests = $this->db->query($sql, $params)->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM maintenance_requests m WHERE " . implode(' AND ', $where);
        $total = $this->db->query($countSql, $params)->fetchColumn();
        
        // Get statistics
        $statsSql = "SELECT 
                        COUNT(*) as total_requests,
                        SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent_count,
                        SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as high_count,
                        SUM(CASE WHEN priority = 'medium' THEN 1 ELSE 0 END) as medium_count,
                        SUM(CASE WHEN priority = 'low' THEN 1 ELSE 0 END) as low_count,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                        AVG(estimated_cost) as avg_estimated_cost,
                        SUM(actual_cost) as total_actual_cost
                     FROM maintenance_requests 
                     WHERE admin_id = ? AND deleted_at IS NULL";
        $stats = $this->db->query($statsSql, [$admin['id']])->fetch();
        
        // Get properties for filters
        $propertiesSql = "SELECT id, name FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $properties = $this->db->query($propertiesSql, [$admin['id']])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('requests', $requests);
        \ViewManager::set('pagination', [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'last_page' => ceil($total / $limit)
        ]);
        \ViewManager::set('stats', $stats);
        \ViewManager::set('properties', $properties);
        \ViewManager::set('filters', [
            'search' => $search,
            'status' => $status,
            'priority' => $priority,
            'property_id' => $propertyId
        ]);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Maintenance Requests');
        
        // Include the maintenance index view
        include __DIR__ . '/../../views/admin/maintenance/index.php';
    }
    
    public function create() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get properties and tenants for assignment
        $propertiesSql = "SELECT id, name FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $properties = $this->db->query($propertiesSql, [$admin['id']])->fetchAll();
        
        $tenantsSql = "SELECT id, name, property_id, unit_id FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Get contractors/vendors
        $contractorsSql = "SELECT id, name, specialty FROM vendors WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $contractors = $this->db->query($contractorsSql, [$admin['id']])->fetchAll();
        
        // Define categories and priorities
        $categories = [
            'plumbing', 'electrical', 'hvac', 'appliance', 'structural', 'pest_control', 'landscaping', 'other'
        ];
        
        $priorities = ['low', 'medium', 'high', 'urgent'];
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('properties', $properties);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('contractors', $contractors);
        \ViewManager::set('categories', $categories);
        \ViewManager::set('priorities', $priorities);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Create Maintenance Request');
        
        // Include the create view
        include __DIR__ . '/../../views/admin/maintenance/create.php';
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Validate required fields
        $required = ['title', 'description', 'priority'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = "Field '$field' is required";
                $this->redirect('/admin/maintenance/create');
                return;
            }
        }
        
        // Validate priority
        $validPriorities = ['low', 'medium', 'high', 'urgent'];
        if (!in_array($_POST['priority'], $validPriorities)) {
            $_SESSION['error'] = 'Invalid priority';
            $this->redirect('/admin/maintenance/create');
            return;
        }
        
        // Validate property/unit if provided
        if (!empty($_POST['property_id'])) {
            $propertyCheck = $this->db->query("SELECT id FROM properties WHERE id = ? AND admin_id = ?", 
                                             [$_POST['property_id'], $admin['id']])->fetch();
            if (!$propertyCheck) {
                $_SESSION['error'] = 'Property not found';
                $this->redirect('/admin/maintenance/create');
                return;
            }
        }
        
        if (!empty($_POST['unit_id'])) {
            $unitCheck = $this->db->query("SELECT id FROM units WHERE id = ? AND admin_id = ?", 
                                         [$_POST['unit_id'], $admin['id']])->fetch();
            if (!$unitCheck) {
                $_SESSION['error'] = 'Unit not found';
                $this->redirect('/admin/maintenance/create');
                return;
            }
        }
        
        try {
            $sql = "INSERT INTO maintenance_requests (admin_id, tenant_id, property_id, unit_id, title, 
                      description, priority, status, requested_date, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $admin['id'],
                $_POST['tenant_id'] ?? null,
                $_POST['property_id'] ?? null,
                $_POST['unit_id'] ?? null,
                $_POST['title'],
                $_POST['description'],
                $_POST['priority'],
                $_POST['status'] ?? 'pending'
            ];
            
            $this->db->query($sql, $params);
            $requestId = $this->db->lastInsertId();
            
            $_SESSION['success'] = 'Maintenance request created successfully';
            $this->redirect('/admin/maintenance');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to create maintenance request: ' . $e->getMessage();
            $this->redirect('/admin/maintenance/create');
        }
    }
    
    public function show($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $sql = "SELECT m.*, 
                        t.name as tenant_name,
                        t.email as tenant_email,
                        t.phone as tenant_phone,
                        pr.name as property_name,
                        pr.address as property_address,
                        u.unit_number
                 FROM maintenance_requests m
                 LEFT JOIN tenants t ON m.tenant_id = t.id
                 LEFT JOIN properties pr ON m.property_id = pr.id
                 LEFT JOIN units u ON m.unit_id = u.id
                 WHERE m.id = ? AND m.admin_id = ? AND m.deleted_at IS NULL";
        
        $request = $this->db->query($sql, [$id, $admin['id']])->fetch();
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        // Get maintenance history/updates
        $historySql = "SELECT * FROM maintenance_updates WHERE request_id = ? ORDER BY created_at DESC";
        $request['updates'] = $this->db->query($historySql, [$id])->fetchAll();
        
        // Get contractors for assignment
        $contractorsSql = "SELECT id, name, specialty FROM vendors WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $contractors = $this->db->query($contractorsSql, [$admin['id']])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('request', $request);
        \ViewManager::set('contractors', $contractors);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Maintenance Request Details');
        
        // Include the show view
        include __DIR__ . '/../../views/admin/maintenance/show.php';
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT * FROM maintenance_requests WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        // Get properties and tenants for assignment
        $propertiesSql = "SELECT id, name FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $properties = $this->db->query($propertiesSql, [$admin['id']])->fetchAll();
        
        $tenantsSql = "SELECT id, name FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Get contractors for assignment
        $contractorsSql = "SELECT id, name, specialty FROM vendors WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $contractors = $this->db->query($contractorsSql, [$admin['id']])->fetchAll();
        
        // Define categories and priorities
        $categories = ['plumbing', 'electrical', 'hvac', 'appliance', 'structural', 'pest_control', 'landscaping', 'other'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('request', $request);
        \ViewManager::set('properties', $properties);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('contractors', $contractors);
        \ViewManager::set('categories', $categories);
        \ViewManager::set('priorities', $priorities);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Edit Maintenance Request');
        
        // Include the edit view
        include __DIR__ . '/../../views/admin/maintenance/edit.php';
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        try {
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['title', 'description', 'priority', 'status', 'assigned_to', 'estimated_cost', 
                              'actual_cost', 'completion_date', 'notes'];
            
            foreach ($allowedFields as $field) {
                if (isset($_POST[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $_POST[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE maintenance_requests SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
                
                // Add update to history if status changed
                if (isset($_POST['status'])) {
                    $historySql = "INSERT INTO maintenance_updates (request_id, status, notes, created_at) VALUES (?, ?, ?, NOW())";
                    $this->db->query($historySql, [$id, $_POST['status'], $_POST['update_notes'] ?? 'Status updated']);
                }
            }
            
            $_SESSION['success'] = 'Maintenance request updated successfully';
            $this->redirect('/admin/maintenance');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to update maintenance request: ' . $e->getMessage();
            $this->redirect("/admin/maintenance/$id/edit");
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        try {
            // Soft delete request
            $this->db->query("UPDATE maintenance_requests SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            $_SESSION['success'] = 'Maintenance request deleted successfully';
            $this->redirect('/admin/maintenance');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to delete maintenance request: ' . $e->getMessage();
            $this->redirect('/admin/maintenance');
        }
    }
    
    public function assignVendor($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        if (empty($_POST['vendor_id'])) {
            $_SESSION['error'] = 'Vendor ID is required';
            $this->redirect("/admin/maintenance/$id");
            return;
        }
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Update request with vendor assignment
            $this->db->query("UPDATE maintenance_requests SET assigned_to = ?, status = 'assigned', updated_at = NOW() WHERE id = ?", 
                              [$_POST['vendor_id'], $id]);
            
            // Add update to history
            $historySql = "INSERT INTO maintenance_updates (request_id, status, notes, created_at) VALUES (?, ?, ?, NOW())";
            $this->db->query($historySql, [$id, 'assigned', 'Assigned to vendor: ' . $_POST['vendor_id']]);
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Vendor assigned successfully';
            $this->redirect("/admin/maintenance/$id");
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to assign vendor: ' . $e->getMessage();
            $this->redirect("/admin/maintenance/$id");
        }
    }
    
    public function complete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$request) {
            $_SESSION['error'] = 'Maintenance request not found';
            $this->redirect('/admin/maintenance');
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Update request as completed
            $sql = "UPDATE maintenance_requests SET status = 'completed', completion_date = NOW(), updated_at = NOW()";
            $params = [];
            
            if (!empty($_POST['actual_cost'])) {
                $sql .= ", actual_cost = ?";
                $params[] = $_POST['actual_cost'];
            }
            
            if (!empty($_POST['completion_notes'])) {
                $sql .= ", notes = ?";
                $params[] = $_POST['completion_notes'];
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $this->db->query($sql, $params);
            
            // Add update to history
            $historySql = "INSERT INTO maintenance_updates (request_id, status, notes, created_at) VALUES (?, ?, ?, NOW())";
            $this->db->query($historySql, [$id, 'completed', $_POST['completion_notes'] ?? 'Request completed']);
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Maintenance request completed successfully';
            $this->redirect("/admin/maintenance/$id");
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to complete maintenance request: ' . $e->getMessage();
            $this->redirect("/admin/maintenance/$id");
        }
    }
}
