<?php

namespace App\Controllers;

// Manually require the SupabaseClient to ensure it's loaded
require_once __DIR__ . '/../../config/supabase.php';

use App\Middleware\JwtMiddleware;
use Config\SupabaseClient;

class ApiPropertyController extends BaseController {
    private $jwtMiddleware;

    public function __construct() {
        parent::__construct();
        $this->jwtMiddleware = new JwtMiddleware();
    }

    public function index() {
        $admin = $this->jwtMiddleware->authenticate();
        
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
        
        // Simple query without subqueries for now
        $sql = "SELECT p.* FROM properties p WHERE {$whereClause} ORDER BY p.created_at DESC";
        
        $result = $this->paginate($sql, $page, 10);
        
        $this->json($result);
    }

    public function show($id) {
        $admin = $this->jwtMiddleware->authenticate();
        
        // Get property with units
        $property = $this->supabase->select('properties', '*', ['id' => $id, 'admin_id' => $admin['id']]);
        
        if (!$property) {
            $this->json(['error' => 'Property not found'], 404);
        }
        
        $property = $property[0]; // Get first result

        // Get units for this property
        $units = $this->supabase->select('units', '*', ['property_id' => $id]);

        // Get tenants for this property
        $tenants = $this->supabase->select('tenants', '*', ['property_id' => $id]);

        $this->json([
            'property' => $property,
            'units' => $units,
            'tenants' => $tenants
        ]);
    }

    public function store() {
        $admin = $this->jwtMiddleware->authenticate();
        
        $data = $this->getPostData();
        
        // Validate required fields
        $required = ['name', 'address', 'type'];
        $errors = $this->validateRequired($data, $required);
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
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

        $propertyId = $this->supabase->insert('properties', $propertyData);

        // Log activity
        $this->logActivity($admin['id'], 'create', "Created property: {$data['name']}", 'property', $propertyId);

        $this->json([
            'message' => 'Property created successfully',
            'property_id' => $propertyId
        ], 201);
    }

    public function update($id) {
        $admin = $this->jwtMiddleware->authenticate();
        $data = $this->getPostData();
        
        // Check if property exists and belongs to admin
        $property = $this->supabase->select('properties', 'id', ['id' => $id, 'admin_id' => $admin['id']]);
        
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

        $this->supabase->update('properties', $updateData, ['id' => $id]);

        // Log activity
        $this->logActivity($admin['id'], 'update', "Updated property: {$data['name']}", 'property', $id);

        $this->json(['message' => 'Property updated successfully']);
    }

    public function delete($id) {
        $admin = $this->jwtMiddleware->authenticate();
        
        // Check if property exists and belongs to admin
        $property = $this->supabase->select('properties', 'name', ['id' => $id, 'admin_id' => $admin['id']]);
        
        if (!$property) {
            $this->json(['error' => 'Property not found'], 404);
        }
        
        $propertyName = $property[0]['name'];

        // Soft delete
        $this->supabase->update('properties', ['deleted_at' => date('Y-m-d H:i:s')], ['id' => $id]);

        // Log activity
        $this->logActivity($admin['id'], 'delete', "Deleted property: {$propertyName}", 'property', $id);

        $this->json(['message' => 'Property deleted successfully']);
    }
}
