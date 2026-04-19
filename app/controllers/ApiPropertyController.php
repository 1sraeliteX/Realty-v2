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
        $admin = $this->jwtMiddleware->authenticate();

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $search = $_GET['search'] ?? '';
        $type   = $_GET['type'] ?? '';
        $status = $_GET['status'] ?? '';

        $where  = ['p.admin_id = ?', 'p.deleted_at IS NULL'];
        $params = [$admin['id']];

        if ($search) { $where[] = '(p.name LIKE ? OR p.address LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($type)   { $where[] = 'p.type = ?';   $params[] = $type; }
        if ($status) { $where[] = 'p.status = ?'; $params[] = $status; }

        $sql = 'SELECT p.* FROM properties p WHERE ' . implode(' AND ', $where) . ' ORDER BY p.created_at DESC';
        $this->json($this->paginate($sql, $page, 10, $params));
    }

    public function show($id) {
        $admin = $this->jwtMiddleware->authenticate();
        $pdo = $this->db->getConnection();

        $stmt = $pdo->prepare('SELECT * FROM properties WHERE id = ? AND admin_id = ? AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([$id, $admin['id']]);
        $property = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$property) { $this->json(['error' => 'Property not found'], 404); return; }

        $units = $pdo->prepare('SELECT * FROM units WHERE property_id = ? AND deleted_at IS NULL ORDER BY unit_number ASC');
        $units->execute([$id]);

        $tenants = $pdo->prepare('SELECT * FROM tenants WHERE property_id = ? AND deleted_at IS NULL ORDER BY name ASC');
        $tenants->execute([$id]);

        $this->json([
            'property' => $property,
            'units'    => $units->fetchAll(\PDO::FETCH_ASSOC),
            'tenants'  => $tenants->fetchAll(\PDO::FETCH_ASSOC),
        ]);
    }

    public function store() {
        $admin = $this->jwtMiddleware->authenticate();
        $data  = $this->getPostData();

        $errors = $this->validateRequired($data, ['name', 'address', 'type']);
        if ($errors) { $this->json(['errors' => $errors], 422); return; }

        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO properties (admin_id, name, address, type, category, description,
                                    year_built, bedrooms, bathrooms, kitchens, parking,
                                    rent_price, status, amenities, images, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            $admin['id'], $data['name'], $data['address'], $data['type'],
            $data['category'] ?? null, $data['description'] ?? null,
            $data['year_built'] ?? null, $data['bedrooms'] ?? null,
            $data['bathrooms'] ?? null, $data['kitchens'] ?? 1,
            $data['parking'] ?? 0, $data['rent_price'] ?? null,
            $data['status'] ?? 'active',
            !empty($data['amenities']) ? json_encode($data['amenities']) : null,
            !empty($data['images'])    ? json_encode($data['images'])    : null,
        ]);
        $propertyId = $pdo->lastInsertId();

        $this->logActivity($admin['id'], 'create', "Created property: {$data['name']}", 'property', $propertyId);
        $this->json(['message' => 'Property created successfully', 'property_id' => $propertyId], 201);
    }

    public function update($id) {
        $admin = $this->jwtMiddleware->authenticate();
        $data  = $this->getPostData();
        $pdo   = $this->db->getConnection();

        $check = $pdo->prepare('SELECT id, name FROM properties WHERE id = ? AND admin_id = ? AND deleted_at IS NULL LIMIT 1');
        $check->execute([$id, $admin['id']]);
        if (!$check->fetch()) { $this->json(['error' => 'Property not found'], 404); return; }

        $errors = $this->validateRequired($data, ['name', 'address', 'type']);
        if ($errors) { $this->json(['errors' => $errors], 422); return; }

        $stmt = $pdo->prepare("
            UPDATE properties SET name=?, address=?, type=?, category=?, description=?,
                year_built=?, bedrooms=?, bathrooms=?, kitchens=?, parking=?,
                rent_price=?, status=?, amenities=?, updated_at=CURRENT_TIMESTAMP
            WHERE id = ? AND admin_id = ?
        ");
        $stmt->execute([
            $data['name'], $data['address'], $data['type'],
            $data['category'] ?? null, $data['description'] ?? null,
            $data['year_built'] ?? null, $data['bedrooms'] ?? null,
            $data['bathrooms'] ?? null, $data['kitchens'] ?? 1,
            $data['parking'] ?? 0, $data['rent_price'] ?? null,
            $data['status'] ?? 'active',
            !empty($data['amenities']) ? json_encode($data['amenities']) : null,
            $id, $admin['id'],
        ]);

        $this->logActivity($admin['id'], 'update', "Updated property: {$data['name']}", 'property', $id);
        $this->json(['message' => 'Property updated successfully']);
    }

    public function delete($id) {
        $admin = $this->jwtMiddleware->authenticate();
        $pdo   = $this->db->getConnection();

        $check = $pdo->prepare('SELECT name FROM properties WHERE id = ? AND admin_id = ? AND deleted_at IS NULL LIMIT 1');
        $check->execute([$id, $admin['id']]);
        $property = $check->fetch(\PDO::FETCH_ASSOC);

        if (!$property) { $this->json(['error' => 'Property not found'], 404); return; }

        $pdo->prepare("UPDATE properties SET deleted_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
            ->execute([$id]);

        $this->logActivity($admin['id'], 'delete', "Deleted property: {$property['name']}", 'property', $id);
        $this->json(['message' => 'Property deleted successfully']);
    }
}
