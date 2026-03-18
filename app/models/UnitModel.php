<?php

namespace App\Models;

// Manually require database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

/**
 * Unit Model
 * 
 * Handles unit data operations for the Cornerstone Realty application.
 * Uses MySQL database with PDO for secure database operations.
 */
class UnitModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Search units by keyword
     * 
     * @param string $q - Search query
     * @param int|null $adminId - Admin user ID (null for superadmin to see all)
     * @return array - Search results
     */
    public function searchByKeyword($q, $adminId = null) {
        try {
            $params = [];
            $whereConditions = [];
            
            // Base query with property join for context
            $query = "
                SELECT u.id, u.unit_number, u.property_id, u.status, u.rent_amount,
                       p.name as property_name, p.address as property_address
                FROM units u
                LEFT JOIN properties p ON u.property_id = p.id
                WHERE u.deleted_at IS NULL
            ";
            
            // Add admin filter if specified
            if ($adminId !== null) {
                $whereConditions[] = "u.admin_id = ?";
                $params[] = $adminId;
            }
            
            // Add search condition
            $whereConditions[] = "(u.unit_number LIKE ? OR p.name LIKE ? OR p.address LIKE ?)";
            $searchTerm = '%' . $q . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            
            // Combine WHERE conditions
            if (!empty($whereConditions)) {
                $query .= " AND " . implode(" AND ", $whereConditions);
            }
            
            // Add limit and order
            $query .= " ORDER BY u.unit_number ASC LIMIT 5";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (\Exception $e) {
            error_log("UnitModel::searchByKeyword error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get unit by ID
     * 
     * @param int $id - Unit ID
     * @param int|null $adminId - Admin user ID for authorization
     * @return array|null - Unit data or null if not found
     */
    public function getById($id, $adminId = null) {
        try {
            $params = [$id];
            $whereCondition = "WHERE u.id = ? AND u.deleted_at IS NULL";
            
            // Add admin filter if specified
            if ($adminId !== null) {
                $whereCondition .= " AND u.admin_id = ?";
                $params[] = $adminId;
            }
            
            $query = "
                SELECT u.*, p.name as property_name, p.address as property_address,
                       t.name as tenant_name, t.email as tenant_email
                FROM units u
                LEFT JOIN properties p ON u.property_id = p.id
                LEFT JOIN tenants t ON u.id = t.unit_id AND t.deleted_at IS NULL
                {$whereCondition} 
                LIMIT 1
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $unit = $stmt->fetch();
            
            return $unit ?: null;
            
        } catch (\Exception $e) {
            error_log("UnitModel::getById error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get units by property ID
     * 
     * @param int $propertyId - Property ID
     * @param int|null $adminId - Admin user ID for authorization
     * @return array - List of units
     */
    public function getByPropertyId($propertyId, $adminId = null) {
        try {
            $params = [$propertyId];
            $whereCondition = "WHERE property_id = ? AND deleted_at IS NULL";
            
            // Add admin filter if specified
            if ($adminId !== null) {
                $whereCondition .= " AND admin_id = ?";
                $params[] = $adminId;
            }
            
            $query = "
                SELECT u.*, t.name as tenant_name, t.email as tenant_email
                FROM units u
                LEFT JOIN tenants t ON u.id = t.unit_id AND t.deleted_at IS NULL
                {$whereCondition}
                ORDER BY unit_number ASC
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (\Exception $e) {
            error_log("UnitModel::getByPropertyId error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create a new unit
     * 
     * @param array $data - Unit data
     * @return int - New unit ID
     */
    public function create($data) {
        try {
            $query = "
                INSERT INTO units (
                    admin_id, property_id, unit_number, type, bedrooms, bathrooms,
                    size_sqft, rent_amount, status, amenities, description,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['admin_id'],
                $data['property_id'],
                $data['unit_number'],
                $data['type'] ?? 'residential',
                $data['bedrooms'] ?? 1,
                $data['bathrooms'] ?? 1,
                $data['size_sqft'] ?? null,
                $data['rent_amount'] ?? null,
                $data['status'] ?? 'available',
                $data['amenities'] ?? null,
                $data['description'] ?? null
            ]);
            
            return $this->db->lastInsertId();
            
        } catch (\Exception $e) {
            error_log("UnitModel::create error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update unit
     * 
     * @param int $id - Unit ID
     * @param array $data - Unit data to update
     * @param int|null $adminId - Admin user ID for authorization
     * @return bool - Success status
     */
    public function update($id, $data, $adminId = null) {
        try {
            $params = [];
            $setClauses = [];
            
            // Build SET clauses dynamically
            $allowedFields = ['unit_number', 'type', 'bedrooms', 'bathrooms', 'size_sqft', 
                            'rent_amount', 'status', 'amenities', 'description', 'property_id'];
            
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
            
            $query = "UPDATE units SET " . implode(", ", $setClauses) . " {$whereCondition}";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute($params);
            
        } catch (\Exception $e) {
            error_log("UnitModel::update error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Soft delete unit
     * 
     * @param int $id - Unit ID
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
            
            $query = "UPDATE units SET deleted_at = NOW(), updated_at = NOW() {$whereCondition}";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute($params);
            
        } catch (\Exception $e) {
            error_log("UnitModel::delete error: " . $e->getMessage());
            throw $e;
        }
    }
}
