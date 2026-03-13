<?php

namespace App\Controllers;

class ApiMaintenanceController extends BaseController {
    public function index() {
        $admin = $this->requireApiAuth();
        
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
        $where = ["m.admin_id = ?"];
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
        $countSql = "SELECT COUNT(*) FROM maintenance_requests m LEFT JOIN tenants t ON m.tenant_id = t.id LEFT JOIN properties pr ON m.property_id = pr.id LEFT JOIN units u ON m.unit_id = u.id WHERE " . implode(' AND ', $where);
        $countParams = array_slice($params, 0, count($params) - 2); // Remove limit and offset
        $total = $this->db->query($countSql, $countParams)->fetchColumn();
        
        $this->json([
            'success' => true,
            'data' => $requests,
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
            $this->json(['success' => false, 'message' => 'Maintenance request not found'], 404);
            return;
        }
        
        // Get maintenance history/updates
        $historySql = "SELECT * FROM maintenance_updates WHERE request_id = ? ORDER BY created_at DESC";
        $request['updates'] = $this->db->query($historySql, [$id])->fetchAll();
        
        $this->json(['success' => true, 'data' => $request]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['title', 'description', 'priority'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->json(['success' => false, 'message' => "Field '$field' is required"], 400);
                return;
            }
        }
        
        // Validate priority
        $validPriorities = ['low', 'medium', 'high', 'urgent'];
        if (!in_array($data['priority'], $validPriorities)) {
            $this->json(['success' => false, 'message' => 'Invalid priority'], 400);
            return;
        }
        
        // Validate property/unit if provided
        if (!empty($data['property_id'])) {
            $propertyCheck = $this->db->query("SELECT id FROM properties WHERE id = ? AND admin_id = ?", 
                                             [$data['property_id'], $admin['id']])->fetch();
            if (!$propertyCheck) {
                $this->json(['success' => false, 'message' => 'Property not found'], 400);
                return;
            }
        }
        
        if (!empty($data['unit_id'])) {
            $unitCheck = $this->db->query("SELECT id FROM units WHERE id = ? AND admin_id = ?", 
                                         [$data['unit_id'], $admin['id']])->fetch();
            if (!$unitCheck) {
                $this->json(['success' => false, 'message' => 'Unit not found'], 400);
                return;
            }
        }
        
        try {
            $sql = "INSERT INTO maintenance_requests (admin_id, tenant_id, property_id, unit_id, title, 
                      description, priority, status, requested_date, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $admin['id'],
                $data['tenant_id'] ?? null,
                $data['property_id'] ?? null,
                $data['unit_id'] ?? null,
                $data['title'],
                $data['description'],
                $data['priority'],
                $data['status'] ?? 'pending'
            ];
            
            $this->db->query($sql, $params);
            $requestId = $this->db->lastInsertId();
            
            $this->json([
                'success' => true,
                'message' => 'Maintenance request created successfully',
                'data' => ['id' => $requestId]
            ]);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to create maintenance request: ' . $e->getMessage()], 500);
        }
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$request) {
            $this->json(['success' => false, 'message' => 'Maintenance request not found'], 404);
            return;
        }
        
        try {
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['title', 'description', 'priority', 'status', 'assigned_to', 'estimated_cost', 
                              'actual_cost', 'completion_date', 'notes'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE maintenance_requests SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
                
                // Add update to history if status changed
                if (isset($data['status'])) {
                    $historySql = "INSERT INTO maintenance_updates (request_id, status, notes, created_at) VALUES (?, ?, ?, NOW())";
                    $this->db->query($historySql, [$id, $data['status'], $data['update_notes'] ?? 'Status updated']);
                }
            }
            
            $this->json(['success' => true, 'message' => 'Maintenance request updated successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update maintenance request: ' . $e->getMessage()], 500);
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$request) {
            $this->json(['success' => false, 'message' => 'Maintenance request not found'], 404);
            return;
        }
        
        try {
            // Soft delete request
            $this->db->query("UPDATE maintenance_requests SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            $this->json(['success' => true, 'message' => 'Maintenance request deleted successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to delete maintenance request: ' . $e->getMessage()], 500);
        }
    }
    
    public function assignVendor($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['vendor_id'])) {
            $this->json(['success' => false, 'message' => 'Vendor ID is required'], 400);
            return;
        }
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$request) {
            $this->json(['success' => false, 'message' => 'Maintenance request not found'], 404);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Update request with vendor assignment
            $this->db->query("UPDATE maintenance_requests SET assigned_to = ?, status = 'assigned', updated_at = NOW() WHERE id = ?", 
                              [$data['vendor_id'], $id]);
            
            // Add update to history
            $historySql = "INSERT INTO maintenance_updates (request_id, status, notes, created_at) VALUES (?, ?, ?, NOW())";
            $this->db->query($historySql, [$id, 'assigned', 'Assigned to vendor: ' . $data['vendor_id']]);
            
            $this->db->commit();
            
            $this->json(['success' => true, 'message' => 'Vendor assigned successfully']);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to assign vendor: ' . $e->getMessage()], 500);
        }
    }
    
    public function completeRequest($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if request exists and belongs to admin
        $request = $this->db->query("SELECT id FROM maintenance_requests WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$request) {
            $this->json(['success' => false, 'message' => 'Maintenance request not found'], 404);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Update request as completed
            $sql = "UPDATE maintenance_requests SET status = 'completed', completion_date = NOW(), updated_at = NOW()";
            $params = [];
            
            if (!empty($data['actual_cost'])) {
                $sql .= ", actual_cost = ?";
                $params[] = $data['actual_cost'];
            }
            
            if (!empty($data['completion_notes'])) {
                $sql .= ", notes = ?";
                $params[] = $data['completion_notes'];
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $this->db->query($sql, $params);
            
            // Add update to history
            $historySql = "INSERT INTO maintenance_updates (request_id, status, notes, created_at) VALUES (?, ?, ?, NOW())";
            $this->db->query($historySql, [$id, 'completed', $data['completion_notes'] ?? 'Request completed']);
            
            $this->db->commit();
            
            $this->json(['success' => true, 'message' => 'Maintenance request completed successfully']);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to complete maintenance request: ' . $e->getMessage()], 500);
        }
    }
    
    public function getMaintenanceStats() {
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
                 WHERE admin_id = ? AND deleted_at IS NULL $dateFilter";
        
        $stats = $this->db->query($sql, [$admin['id']])->fetch();
        
        $this->json(['success' => true, 'data' => $stats]);
    }
    
    public function getPendingRequests() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $sql = "SELECT m.*, 
                        t.name as tenant_name,
                        pr.name as property_name,
                        u.unit_number,
                        DATEDIFF(NOW(), m.created_at) as days_pending
                 FROM maintenance_requests m
                 LEFT JOIN tenants t ON m.tenant_id = t.id
                 LEFT JOIN properties pr ON m.property_id = pr.id
                 LEFT JOIN units u ON m.unit_id = u.id
                 WHERE m.admin_id = ? AND m.deleted_at IS NULL 
                 AND m.status IN ('pending', 'assigned')
                 ORDER BY m.priority DESC, m.created_at ASC";
        
        $pending = $this->db->query($sql, [$admin['id']])->fetchAll();
        
        $this->json(['success' => true, 'data' => $pending]);
    }
}
