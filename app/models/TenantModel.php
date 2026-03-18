<?php

namespace App\Models;

// Manually require database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

/**
 * Tenant Model
 * 
 * Handles tenant data operations for the Cornerstone Realty application.
 * Uses MySQL database with PDO for secure database operations.
 */
class TenantModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Search tenants by keyword
     * 
     * @param string $q - Search query
     * @param int|null $adminId - Admin user ID (null for superadmin to see all)
     * @return array - Search results
     */
    public function searchByKeyword($q, $adminId = null) {
        try {
            $params = [];
            $whereConditions = [];
            
            // Base query with property join for address context
            $query = "
                SELECT t.id, t.name, t.email, t.phone, p.name as property_name, p.address as property_address
                FROM tenants t
                LEFT JOIN properties p ON t.property_id = p.id
                WHERE t.deleted_at IS NULL
            ";
            
            // Add admin filter if specified
            if ($adminId !== null) {
                $whereConditions[] = "t.admin_id = ?";
                $params[] = $adminId;
            }
            
            // Add search condition
            $whereConditions[] = "(t.name LIKE ? OR t.email LIKE ? OR t.phone LIKE ? OR p.name LIKE ? OR p.address LIKE ?)";
            $searchTerm = '%' . $q . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            
            // Combine WHERE conditions
            if (!empty($whereConditions)) {
                $query .= " AND " . implode(" AND ", $whereConditions);
            }
            
            // Add limit and order
            $query .= " ORDER BY t.name ASC LIMIT 5";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (\Exception $e) {
            error_log("TenantModel::searchByKeyword error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get tenant by ID
     * 
     * @param int $id - Tenant ID
     * @param int|null $adminId - Admin user ID for authorization
     * @return array|null - Tenant data or null if not found
     */
    public function getById($id, $adminId = null) {
        try {
            $params = [$id];
            $whereCondition = "WHERE t.id = ? AND t.deleted_at IS NULL";
            
            // Add admin filter if specified
            if ($adminId !== null) {
                $whereCondition .= " AND t.admin_id = ?";
                $params[] = $adminId;
            }
            
            $query = "
                SELECT t.*, p.name as property_name, p.address as property_address, u.unit_number
                FROM tenants t
                LEFT JOIN properties p ON t.property_id = p.id
                LEFT JOIN units u ON t.unit_id = u.id
                {$whereCondition} 
                LIMIT 1
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $tenant = $stmt->fetch();
            
            return $tenant ?: null;
            
        } catch (\Exception $e) {
            error_log("TenantModel::getById error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new tenant
     * 
     * @param array $data - Tenant data
     * @return int - New tenant ID
     */
    public function create($data) {
        try {
            $query = "
                INSERT INTO tenants (
                    admin_id, property_id, unit_id, name, email, phone, alternate_phone,
                    id_type, id_number, date_of_birth, nationality, occupation, emergency_contact_name,
                    emergency_contact_phone, emergency_contact_relationship, emergency_contact_email,
                    lease_start_date, lease_end_date, rent_amount, rent_frequency, security_deposit,
                    payment_method, status, notes, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['admin_id'],
                $data['property_id'] ?? null,
                $data['unit_id'] ?? null,
                $data['name'],
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['alternate_phone'] ?? null,
                $data['id_type'] ?? null,
                $data['id_number'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['nationality'] ?? null,
                $data['occupation'] ?? null,
                $data['emergency_contact_name'] ?? null,
                $data['emergency_contact_phone'] ?? null,
                $data['emergency_contact_relationship'] ?? null,
                $data['emergency_contact_email'] ?? null,
                $data['lease_start_date'] ?? null,
                $data['lease_end_date'] ?? null,
                $data['rent_amount'] ?? null,
                $data['rent_frequency'] ?? 'monthly',
                $data['security_deposit'] ?? null,
                $data['payment_method'] ?? null,
                $data['status'] ?? 'active',
                $data['notes'] ?? null
            ]);
            
            return $this->db->lastInsertId();
            
        } catch (\Exception $e) {
            error_log("TenantModel::create error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update tenant
     * 
     * @param int $id - Tenant ID
     * @param array $data - Tenant data to update
     * @param int|null $adminId - Admin user ID for authorization
     * @return bool - Success status
     */
    public function update($id, $data, $adminId = null) {
        try {
            $params = [];
            $setClauses = [];
            
            // Build SET clauses dynamically
            $allowedFields = ['name', 'email', 'phone', 'alternate_phone', 'id_type', 'id_number', 
                            'date_of_birth', 'nationality', 'occupation', 'emergency_contact_name',
                            'emergency_contact_phone', 'emergency_contact_relationship', 
                            'emergency_contact_email', 'lease_start_date', 'lease_end_date', 
                            'rent_amount', 'rent_frequency', 'security_deposit', 'payment_method', 
                            'status', 'notes', 'property_id', 'unit_id'];
            
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
            
            $query = "UPDATE tenants SET " . implode(", ", $setClauses) . " {$whereCondition}";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute($params);
            
        } catch (\Exception $e) {
            error_log("TenantModel::update error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Soft delete tenant
     * 
     * @param int $id - Tenant ID
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
            
            $query = "UPDATE tenants SET deleted_at = NOW(), updated_at = NOW() {$whereCondition}";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute($params);
            
        } catch (\Exception $e) {
            error_log("TenantModel::delete error: " . $e->getMessage());
            throw $e;
        }
    }
}
