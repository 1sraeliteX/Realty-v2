<?php

namespace App\Models;

// Manually require database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

/**
 * Payment Model
 * 
 * Handles payment data operations for the Cornerstone Realty application.
 * Uses MySQL database with PDO for secure database operations.
 * 
 * Assumed Database Schema for payments table:
 * - id (INT, PRIMARY KEY, AUTO_INCREMENT)
 * - tenant_id (INT, FOREIGN KEY to tenants.id)
 * - property_id (INT, FOREIGN KEY to properties.id)
 * - unit_id (INT, FOREIGN KEY to units.id)
 * - amount (DECIMAL(10,2))
 * - due_date (DATE)
 * - payment_date (DATE, NULLABLE)
 * - status (ENUM: 'paid', 'pending', 'overdue', 'failed')
 * - payment_method (VARCHAR(50), NULLABLE)
 * - receipt_reference (VARCHAR(100), NULLABLE)
 * - admin_id (INT, FOREIGN KEY to admins.id)
 * - created_at (TIMESTAMP)
 * - updated_at (TIMESTAMP)
 * - deleted_at (TIMESTAMP, NULLABLE)
 */
class PaymentModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensurePaymentsSchema();
    }
    
    /**
     * Ensure payments table has required schema
     */
    private function ensurePaymentsSchema(): void {
        try {
            $pdo = $this->db;

            // Add deleted_at for soft deletes
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME   = 'payments'
                  AND COLUMN_NAME  = 'deleted_at'
            ");
            $stmt->execute();
            if ((int)$stmt->fetchColumn() === 0) {
                $pdo->exec("ALTER TABLE payments
                            ADD COLUMN deleted_at TIMESTAMP NULL
                            DEFAULT NULL");
            }

            // Add unit_id
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME   = 'payments'
                  AND COLUMN_NAME  = 'unit_id'
            ");
            $stmt->execute();
            if ((int)$stmt->fetchColumn() === 0) {
                $pdo->exec("ALTER TABLE payments
                            ADD COLUMN unit_id INT NULL
                            AFTER property_id");
            }

        } catch (\Throwable $e) {
            error_log('ensurePaymentsSchema: ' . $e->getMessage());
        }
    }
    
    /**
     * Get paginated payments with filters
     * 
     * @param array $filters - Filter criteria (status, date_range, search)
     * @param int $page - Current page number
     * @param int $perPage - Items per page
     * @param int|null $userId - Admin user ID (null for superadmin to see all)
     * @return array - Paginated results with pagination info
     */
    public function getAllPaginated($filters = [], $page = 1, $perPage = 10, $userId = null) {
        try {
            $offset = ($page - 1) * $perPage;
            $params = [];
            $whereConditions = [];
            
            // Base query with joins to get related data
            $baseQuery = "
                SELECT 
                    p.id,
                    p.admin_id,
                    p.tenant_id,
                    p.property_id,
                    p.unit_id,
                    p.amount,
                    p.payment_type,
                    p.payment_method,
                    p.due_date,
                    p.payment_date,
                    p.status,
                    p.receipt_reference,
                    p.notes,
                    p.created_at,
                    p.updated_at,
                    p.deleted_at,
                    t.name as tenant_name,
                    t.email as tenant_email,
                    t.phone as tenant_phone,
                    prop.name as property_name,
                    u.unit_number,
                    a.name as admin_name
                FROM payments p
                LEFT JOIN tenants t ON p.tenant_id = t.id
                LEFT JOIN properties prop ON p.property_id = prop.id
                LEFT JOIN units u ON p.unit_id = u.id
                LEFT JOIN admins a ON p.admin_id = a.id
                WHERE p.deleted_at IS NULL
            ";
            
            // Add admin filter (superadmin sees all, regular admin sees only their own)
            if ($userId !== null) {
                $whereConditions[] = "p.admin_id = ?";
                $params[] = $userId;
            }
            
            // Add status filter
            if (!empty($filters['status'])) {
                $whereConditions[] = "p.status = ?";
                $params[] = $filters['status'];
            }
            
            // Add date range filter
            if (!empty($filters['date_from'])) {
                $whereConditions[] = "p.payment_date >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $whereConditions[] = "p.payment_date <= ?";
                $params[] = $filters['date_to'];
            }
            
            // Add search filter (tenant name or reference)
            if (!empty($filters['search'])) {
                $searchTerm = '%' . $filters['search'] . '%';
                $whereConditions[] = "(t.name LIKE ? OR p.receipt_reference LIKE ? OR t.email LIKE ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            // Combine WHERE conditions
            if (!empty($whereConditions)) {
                $baseQuery .= " AND " . implode(" AND ", $whereConditions);
            }
            
            // Get total count
            $countQuery = str_replace("SELECT p.id, p.admin_id, p.tenant_id, p.property_id, p.unit_id, p.amount, p.payment_type, p.payment_method, p.due_date, p.payment_date, p.status, p.receipt_reference, p.notes, p.created_at, p.updated_at, p.deleted_at, t.name as tenant_name, t.email as tenant_email, t.phone as tenant_phone, prop.name as property_name, u.unit_number, a.name as admin_name", "SELECT COUNT(*)", $baseQuery);
            $countStmt = $this->db->prepare($countQuery);
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            
            // Get paginated results
            $query = $baseQuery . " ORDER BY p.created_at DESC LIMIT {$perPage} OFFSET {$offset}";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $payments = $stmt->fetchAll();
            
            return [
                'data' => $payments,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => ceil($total / $perPage),
                    'has_next' => $page < ceil($total / $perPage),
                    'has_prev' => $page > 1
                ]
            ];
            
        } catch (\Exception $e) {
            error_log("PaymentModel::getAllPaginated error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get summary statistics for payments
     * 
     * @param int|null $userId - Admin user ID (null for superadmin to see all)
     * @return array - Summary statistics
     */
    public function getSummaryStats($userId = null) {
        try {
            $params = [];
            $whereCondition = "";
            
            // Add admin filter if specified
            if ($userId !== null) {
                $whereCondition = "WHERE admin_id = ?";
                $params[] = $userId;
            }
            
            $stats = [];
            
            // Total revenue (sum of paid amounts)
            $query = "SELECT COALESCE(SUM(amount), 0) as total FROM payments {$whereCondition} AND status = 'paid' AND deleted_at IS NULL";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $stats['total_revenue'] = (float) $stmt->fetchColumn();
            
            // Pending amount
            $query = "SELECT COALESCE(SUM(amount), 0) as total FROM payments {$whereCondition} AND status = 'pending' AND deleted_at IS NULL";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $stats['pending_amount'] = (float) $stmt->fetchColumn();
            
            // Overdue count
            $query = "SELECT COUNT(*) as count FROM payments {$whereCondition} AND status = 'overdue' AND deleted_at IS NULL";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $stats['overdue_count'] = (int) $stmt->fetchColumn();
            
            // This month's collections
            $monthStart = date('Y-m-01');
            $monthEnd = date('Y-m-t');
            $query = "SELECT COALESCE(SUM(amount), 0) as total FROM payments {$whereCondition} AND status = 'paid' AND payment_date >= ? AND payment_date <= ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($query);
            $monthParams = array_merge($params, [$monthStart, $monthEnd]);
            $stmt->execute($monthParams);
            $stats['this_month_collections'] = (float) $stmt->fetchColumn();
            
            return $stats;
            
        } catch (\Exception $e) {
            error_log("PaymentModel::getSummaryStats error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get payment by ID
     * 
     * @param int $id - Payment ID
     * @param int|null $userId - Admin user ID (null for superadmin to see all)
     * @return array|null - Payment data or null if not found
     */
    public function getById($id, $userId = null) {
        try {
            $params = [$id];
            $whereCondition = "WHERE p.id = ? AND p.deleted_at IS NULL";
            
            // Add admin filter if specified
            if ($userId !== null) {
                $whereCondition .= " AND p.admin_id = ?";
                $params[] = $userId;
            }
            
            $query = "
                SELECT 
                    p.*,
                    t.name as tenant_name,
                    t.email as tenant_email,
                    t.phone as tenant_phone,
                    prop.name as property_name,
                    u.unit_number,
                    a.name as admin_name
                FROM payments p
                LEFT JOIN tenants t ON p.tenant_id = t.id
                LEFT JOIN properties prop ON p.property_id = prop.id
                LEFT JOIN units u ON p.unit_id = u.id
                LEFT JOIN admins a ON p.admin_id = a.id
                {$whereCondition}
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $payment = $stmt->fetch();
            
            return $payment ?: null;
            
        } catch (\Exception $e) {
            error_log("PaymentModel::getById error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Create a new payment record
     * 
     * @param array $data - Payment data
     * @return int - New payment ID
     */
    public function create($data) {
        try {
            $query = "
                INSERT INTO payments (
                    tenant_id, property_id, unit_id, amount, due_date, 
                    payment_date, status, payment_method, receipt_reference, 
                    admin_id, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['tenant_id'],
                $data['property_id'],
                $data['unit_id'] ?? null,
                $data['amount'],
                $data['due_date'],
                $data['payment_date'] ?? null,
                $data['status'],
                $data['payment_method'] ?? null,
                $data['receipt_reference'] ?? null,
                $data['admin_id']
            ]);
            
            return $this->db->lastInsertId();
            
        } catch (\Exception $e) {
            error_log("PaymentModel::create error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update payment status
     * 
     * @param int $id - Payment ID
     * @param string $status - New status
     * @param array $additionalData - Additional data to update (payment_date, payment_method, etc.)
     * @param int|null $userId - Admin user ID for authorization
     * @return bool - Success status
     */
    public function updateStatus($id, $status, $additionalData = [], $userId = null) {
        try {
            $params = [$status, $id];
            $setClauses = ["status = ?", "updated_at = NOW()"];
            
            // Add additional fields to update
            if (!empty($additionalData['payment_date'])) {
                $setClauses[] = "payment_date = ?";
                $params = array_merge($params, [$additionalData['payment_date']]);
            }
            
            if (!empty($additionalData['payment_method'])) {
                $setClauses[] = "payment_method = ?";
                $params = array_merge($params, [$additionalData['payment_method']]);
            }
            
            if (!empty($additionalData['receipt_reference'])) {
                $setClauses[] = "receipt_reference = ?";
                $params = array_merge($params, [$additionalData['receipt_reference']]);
            }
            
            // Add admin filter if specified
            $whereCondition = "WHERE id = ?";
            if ($userId !== null) {
                $whereCondition .= " AND admin_id = ?";
                $params[] = $userId;
            }
            
            $query = "UPDATE payments SET " . implode(", ", $setClauses) . " {$whereCondition}";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute($params);
            
        } catch (\Exception $e) {
            error_log("PaymentModel::updateStatus error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Search payments by keyword
     * 
     * @param string $q - Search query
     * @param int|null $userId - Admin user ID (null for superadmin to see all)
     * @return array - Search results
     */
    public function searchByKeyword($q, $userId = null) {
        try {
            $params = [];
            $whereConditions = [];
            
            // Base query with joins to get related data
            $query = "
                SELECT 
                    p.id,
                    p.amount,
                    p.status,
                    p.payment_date,
                    p.receipt_reference,
                    t.name as tenant_name,
                    t.email as tenant_email,
                    prop.name as property_name,
                    u.unit_number
                FROM payments p
                LEFT JOIN tenants t ON p.tenant_id = t.id
                LEFT JOIN properties prop ON p.property_id = prop.id
                LEFT JOIN units u ON p.unit_id = u.id
                WHERE p.deleted_at IS NULL
            ";
            
            // Add admin filter (superadmin sees all, regular admin sees only their own)
            if ($userId !== null) {
                $whereConditions[] = "p.admin_id = ?";
                $params[] = $userId;
            }
            
            // Add search condition
            $whereConditions[] = "(p.receipt_reference LIKE ? OR t.name LIKE ? OR t.email LIKE ? OR prop.name LIKE ? OR u.unit_number LIKE ?)";
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
            $query .= " ORDER BY p.payment_date DESC LIMIT 5";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (\Exception $e) {
            error_log("PaymentModel::searchByKeyword error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Soft delete a payment
     * 
     * @param int $id - Payment ID
     * @param int|null $userId - Admin user ID for authorization
     * @return bool - Success status
     */
    public function delete($id, $userId = null) {
        try {
            $params = [$id];
            $whereCondition = "WHERE id = ?";
            
            // Add admin filter if specified
            if ($userId !== null) {
                $whereCondition .= " AND admin_id = ?";
                $params[] = $userId;
            }
            
            $query = "UPDATE payments SET deleted_at = NOW(), updated_at = NOW() {$whereCondition}";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute($params);
            
        } catch (\Exception $e) {
            error_log("PaymentModel::delete error: " . $e->getMessage());
            throw $e;
        }
    }
}
