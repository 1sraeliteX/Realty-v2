<?php

namespace App\Controllers;

use App\Middleware\JwtMiddleware;

class ApiPropertyController extends BaseController {
    private $jwtMiddleware;

    public function __construct() {
        parent::__construct();
        $this->jwtMiddleware = new JwtMiddleware();
    }

    public function index() {
        $admin = $this->jwtMiddleware->getCurrentUser();
        
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $status = $_GET['status'] ?? '';
        
        // Build query
        $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
        $params = [$admin['id']];
        
        if (!empty($search)) {
            $where[] = "(p.name LIKE ? OR p.address LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($type)) {
            $where[] = "p.type = ?";
            $params[] = $type;
        }
        
        if (!empty($status)) {
            $where[] = "p.status = ?";
            $params[] = $status;
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Get properties with unit counts
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                FROM properties p 
                WHERE {$whereClause}
                ORDER BY p.created_at DESC";
        
        $result = $this->paginate($sql, $page, 10);
        
        $this->json($result);
    }

    public function show($id) {
        $admin = $this->jwtMiddleware->getCurrentUser();
        
        // Get property with units
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                FROM properties p 
                WHERE p.id = ? AND p.admin_id = ? AND p.deleted_at IS NULL";
        
        $property = $this->db->fetch($sql, [$id, $admin['id']]);
        
        if (!$property) {
            $this->json(['error' => 'Property not found'], 404);
        }

        // Get units for this property
        $unitsSql = "SELECT * FROM units WHERE property_id = ? AND deleted_at IS NULL ORDER BY unit_number";
        $units = $this->db->fetchAll($unitsSql, [$id]);

        // Get tenants for this property
        $tenantsSql = "SELECT t.*, u.unit_number FROM tenants t 
                       JOIN units u ON t.unit_id = u.id 
                       WHERE t.property_id = ? AND t.deleted_at IS NULL 
                       ORDER BY t.name";
        $tenants = $this->db->fetchAll($tenantsSql, [$id]);

        $this->json([
            'property' => $property,
            'units' => $units,
            'tenants' => $tenants
        ]);
    }

    public function store() {
        $admin = $this->jwtMiddleware->getCurrentUser();
        $data = $this->getPostData();
        
        // Validate required fields
        $required = ['name', 'address', 'type'];
        $errors = $this->validateRequired($data, $required);
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
        }

        // Prepare property data
        $propertyData = [
            'admin_id' => $admin['id'],
            'name' => $data['name'],
            'address' => $data['address'],
            'type' => $data['type'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'year_built' => $data['year_built'] ?? null,
            'bedrooms' => $data['bedrooms'] ?? null,
            'bathrooms' => $data['bathrooms'] ?? null,
            'kitchens' => $data['kitchens'] ?? 1,
            'parking' => $data['parking'] ?? 0,
            'rent_price' => $data['rent_price'] ?? null,
            'status' => $data['status'] ?? 'active',
            'amenities' => !empty($data['amenities']) ? json_encode($data['amenities']) : null,
            'images' => !empty($data['images']) ? json_encode($data['images']) : null
        ];

        $propertyId = $this->db->insert('properties', $propertyData);

        // Log activity
        $this->logActivity($admin['id'], 'create', "Created property: {$data['name']}", 'property', $propertyId);

        $this->json([
            'message' => 'Property created successfully',
            'property_id' => $propertyId
        ], 201);
    }

    public function update($id) {
        $admin = $this->jwtMiddleware->getCurrentUser();
        $data = $this->getPostData();
        
        // Check if property exists and belongs to admin
        $sql = "SELECT id FROM properties WHERE id = ? AND admin_id = ? AND deleted_at IS NULL";
        $property = $this->db->fetch($sql, [$id, $admin['id']]);
        
        if (!$property) {
            $this->json(['error' => 'Property not found'], 404);
        }

        // Validate required fields
        $required = ['name', 'address', 'type'];
        $errors = $this->validateRequired($data, $required);
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
        }

        // Prepare update data
        $updateData = [
            'name' => $data['name'],
            'address' => $data['address'],
            'type' => $data['type'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'year_built' => $data['year_built'] ?? null,
            'bedrooms' => $data['bedrooms'] ?? null,
            'bathrooms' => $data['bathrooms'] ?? null,
            'kitchens' => $data['kitchens'] ?? 1,
            'parking' => $data['parking'] ?? 0,
            'rent_price' => $data['rent_price'] ?? null,
            'status' => $data['status'] ?? 'active',
            'amenities' => !empty($data['amenities']) ? json_encode($data['amenities']) : null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle images if provided
        if (isset($data['images'])) {
            $updateData['images'] = json_encode($data['images']);
        }

        $this->db->update('properties', $updateData, 'id = ?', [$id]);

        // Log activity
        $this->logActivity($admin['id'], 'update', "Updated property: {$data['name']}", 'property', $id);

        $this->json(['message' => 'Property updated successfully']);
    }

    public function delete($id) {
        $admin = $this->jwtMiddleware->getCurrentUser();
        
        // Check if property exists and belongs to admin
        $sql = "SELECT name FROM properties WHERE id = ? AND admin_id = ? AND deleted_at IS NULL";
        $property = $this->db->fetch($sql, [$id, $admin['id']]);
        
        if (!$property) {
            $this->json(['error' => 'Property not found'], 404);
        }

        // Soft delete
        $this->db->update('properties', ['deleted_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);

        // Log activity
        $this->logActivity($admin['id'], 'delete', "Deleted property: {$property['name']}", 'property', $id);

        $this->json(['message' => 'Property deleted successfully']);
    }
}
