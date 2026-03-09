<?php

namespace Config;

use PDO;
use PDOException;

class SupabaseDatabase {
    private static $instance = null;
    private $client;
    private $authToken;

    private function __construct() {
        $this->client = SupabaseClient::getInstance();
        $this->authToken = $_SESSION['supabase_token'] ?? null;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        // Return mock connection for compatibility
        return null;
    }

    public function query($sql, $params = []) {
        // Convert SQL to Supabase REST calls
        return $this->executeSupabaseQuery($sql, $params);
    }

    public function fetch($sql, $params = []) {
        $result = $this->executeSupabaseQuery($sql, $params);
        return $result[0] ?? null;
    }

    public function fetchAll($sql, $params = []) {
        return $this->executeSupabaseQuery($sql, $params);
    }

    public function insert($table, $data) {
        try {
            // Add created_at if not present
            if (!isset($data['created_at'])) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
            
            $id = $this->client->insert($table, $data);
            return $id;
        } catch (\Exception $e) {
            if (ConfigSimple::getInstance()->get('app.debug')) {
                die("Insert failed: " . $e->getMessage());
            } else {
                die("Database insert failed.");
            }
        }
    }

    public function update($table, $data, $where, $whereParams = []) {
        try {
            // Add updated_at if not present
            if (!isset($data['updated_at'])) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }
            
            // Parse where clause to extract filters
            $filters = $this->parseWhereClause($where, $whereParams);
            return $this->client->update($table, $data, $filters);
        } catch (\Exception $e) {
            if (ConfigSimple::getInstance()->get('app.debug')) {
                die("Update failed: " . $e->getMessage());
            } else {
                die("Database update failed.");
            }
        }
    }

    public function delete($table, $where, $params = []) {
        try {
            $filters = $this->parseWhereClause($where, $params);
            return $this->client->delete($table, $filters);
        } catch (\Exception $e) {
            if (ConfigSimple::getInstance()->get('app.debug')) {
                die("Delete failed: " . $e->getMessage());
            } else {
                die("Database delete failed.");
            }
        }
    }

    private function executeSupabaseQuery($sql, $params = []) {
        try {
            // Parse basic SQL queries and convert to Supabase REST calls
            $sql = trim($sql);
            
            // Handle SELECT queries
            if (preg_match('/^SELECT\s+(.*?)\s+FROM\s+(\w+)/i', $sql, $matches)) {
                $columns = $matches[1];
                $table = $matches[2];
                
                // Parse WHERE clause
                $filters = [];
                $options = [];
                
                if (preg_match('/WHERE\s+(.+?)(?:\s+(ORDER BY|GROUP BY|LIMIT|HAVING)\s+|$)/i', $sql, $whereMatch)) {
                    $whereClause = $whereMatch[1];
                    $filters = $this->parseWhereClause($whereClause, $params);
                }
                
                // Parse ORDER BY
                if (preg_match('/ORDER BY\s+(.+?)(?:\s+(LIMIT|GROUP BY|HAVING)\s+|$)/i', $sql, $orderMatch)) {
                    $options['order'] = $orderMatch[1];
                }
                
                // Parse LIMIT
                if (preg_match('/LIMIT\s+(\d+)/i', $sql, $limitMatch)) {
                    $options['limit'] = (int)$limitMatch[1];
                }
                
                return $this->client->select($table, $columns, $filters, $options);
            }
            
            // Handle subqueries and complex JOINs
            if (strpos($sql, 'SELECT') === 0 && strpos($sql, '(') !== false) {
                return $this->handleComplexQuery($sql, $params);
            }
            
            throw new \Exception("Unsupported query type: $sql");
        } catch (\Exception $e) {
            if (ConfigSimple::getInstance()->get('app.debug')) {
                die("Query failed: " . $e->getMessage());
            } else {
                die("Database query failed.");
            }
        }
    }

    private function parseWhereClause($whereClause, $params = []) {
        $filters = [];
        
        // Simple WHERE parsing for basic cases
        if (preg_match_all('/(\w+)\s*=\s*\?/i', $whereClause, $matches, PREG_SET_ORDER)) {
            $paramIndex = 0;
            foreach ($matches as $match) {
                $column = $match[1];
                if (isset($params[$paramIndex])) {
                    $filters[$column] = $params[$paramIndex];
                    $paramIndex++;
                }
            }
        }
        
        // Handle specific patterns
        if (strpos($whereClause, 'admin_id = ?') !== false && isset($params[0])) {
            $filters['admin_id'] = $params[0];
        }
        
        if (strpos($whereClause, 'deleted_at IS NULL') !== false) {
            $filters['deleted_at'] = 'is.null';
        }
        
        return $filters;
    }

    private function handleComplexQuery($sql, $params = []) {
        // Handle specific complex queries for properties with unit counts
        if (strpos($sql, 'properties p') !== false && strpos($sql, 'units u') !== false) {
            // Get properties first
            $properties = $this->client->select('properties', '*', [
                'deleted_at' => 'is.null'
            ]);
            
            // Add unit counts for each property
            foreach ($properties as &$property) {
                $units = $this->client->select('units', 'id', [
                    'property_id' => $property['id'],
                    'deleted_at' => 'is.null'
                ]);
                $property['unit_count'] = count($units);
                
                $occupiedUnits = $this->client->select('units', 'id', [
                    'property_id' => $property['id'],
                    'status' => 'occupied',
                    'deleted_at' => 'is.null'
                ]);
                $property['occupied_units'] = count($occupiedUnits);
            }
            
            return $properties;
        }
        
        throw new \Exception("Complex query not supported: $sql");
    }

    public function beginTransaction() {
        // Supabase doesn't support transactions via REST API
        return true;
    }

    public function commit() {
        return true;
    }

    public function rollback() {
        return true;
    }

    public function tableExists($table) {
        try {
            $this->client->select($table, 'id', [], ['limit' => 1]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function lastInsertId() {
        // Supabase uses UUIDs, so this method isn't applicable
        return null;
    }
}
