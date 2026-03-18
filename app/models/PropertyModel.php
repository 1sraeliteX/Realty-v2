<?php

namespace App\Models;

// Manually require database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

/**
 * Property Model
 * 
 * Handles property data operations for the Cornerstone Realty application.
 * Uses MySQL database with PDO for secure database operations.
 */
class PropertyModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Search properties by keyword
     * 
     * @param string $q - Search query
     * @param int|null $adminId - Admin user ID (null for superadmin to see all)
     * @return array - Search results
     */
    public function searchByKeyword($q, $adminId = null) {
        try {
            $params = [];
            $whereConditions = [];
            
            // Base query
            $query = "
                SELECT id, name, address, type, status
                FROM properties 
                WHERE deleted_at IS NULL
            ";
            
            // Add admin filter if specified
            if ($adminId !== null) {
                $whereConditions[] = "admin_id = ?";
                $params[] = $adminId;
            }
            
            // Add search condition
            $whereConditions[] = "(name LIKE ? OR address LIKE ? OR type LIKE ?)";
            $searchTerm = '%' . $q . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            
            // Combine WHERE conditions
            if (!empty($whereConditions)) {
                $query .= " AND " . implode(" AND ", $whereConditions);
            }
            
            // Add limit and order
            $query .= " ORDER BY name ASC LIMIT 5";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (\Exception $e) {
            error_log("PropertyModel::searchByKeyword error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get property by ID
     * 
     * @param int $id - Property ID
     * @param int|null $adminId - Admin user ID for authorization
     * @return array|null - Property data or null if not found
     */
    public function getById($id, $adminId = null) {
        try {
            $params = [$id];
            $whereCondition = "WHERE id = ? AND deleted_at IS NULL";
            
            // Add admin filter if specified
            if ($adminId !== null) {
                $whereCondition .= " AND admin_id = ?";
                $params[] = $adminId;
            }
            
            $query = "SELECT * FROM properties {$whereCondition} LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $property = $stmt->fetch();
            
            return $property ?: null;
            
        } catch (\Exception $e) {
            error_log("PropertyModel::getById error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new property
     * 
     * @param array $data - Property data
     * @return int - New property ID
     */
    public function create($data) {
        try {
            $query = "
                INSERT INTO properties (
                    admin_id, name, address, type, category, description,
                    year_built, bedrooms, bathrooms, kitchens, parking,
                    rent_price, status, amenities, images, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['admin_id'],
                $data['name'],
                $data['address'],
                $data['type'],
                $data['category'] ?? null,
                $data['description'] ?? null,
                $data['year_built'] ?? null,
                $data['bedrooms'] ?? null,
                $data['bathrooms'] ?? null,
                $data['kitchens'] ?? 1,
                $data['parking'] ?? 0,
                $data['rent_price'] ?? null,
                $data['status'] ?? 'active',
                $data['amenities'] ?? null,
                $data['images'] ?? null
            ]);
            
            return $this->db->lastInsertId();
            
        } catch (\Exception $e) {
            error_log("PropertyModel::create error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update property
     * 
     * @param int $id - Property ID
     * @param array $data - Property data to update
     * @param int|null $adminId - Admin user ID for authorization
     * @return bool - Success status
     */
    public function update($id, $data, $adminId = null) {
        try {
            $params = [];
            $setClauses = [];
            
            // Build SET clauses dynamically
            $allowedFields = ['name', 'address', 'type', 'category', 'description', 'year_built', 
                            'bedrooms', 'bathrooms', 'kitchens', 'parking', 'rent_price', 'status', 
                            'amenities', 'images'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $setClauses[] = "{$field} = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($setClauses)) {
                return false; // No fields to update
            }
            
            $setClauses[] = "updated_at = NOW()";
            $params[] = $id;
            
            // Add admin filter if specified
            $whereCondition = "WHERE id = ?";
            if ($adminId !== null) {
                $whereCondition .= " AND admin_id = ?";
                $params[] = $adminId;
            }
            
            $query = "UPDATE properties SET " . implode(", ", $setClauses) . " {$whereCondition}";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute($params);
            
        } catch (\Exception $e) {
            error_log("PropertyModel::update error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Soft delete property
     * 
     * @param int $id - Property ID
     * @param int|null $adminId - Admin user ID for authorization
     * @return bool - Success status
     */
    public function delete($id, $adminId = null) {
        try {
            $params = [$id];
            $whereCondition = "WHERE id = ?";
            
            // Add admin filter if specified
            if ($adminId !== null) {
                $whereCondition .= " AND admin_id = ?";
                $params[] = $adminId;
            }
            
            $query = "UPDATE properties SET deleted_at = NOW(), updated_at = NOW() {$whereCondition}";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute($params);
            
        } catch (\Exception $e) {
            error_log("PropertyModel::delete error: " . $e->getMessage());
            throw $e;
        }
    }
}
