<?php
// Check all tables mentioned in dashboard queries
require_once __DIR__ . '/config/database.php';

try {
    $pdo = \Config\Database::getInstance()->getConnection();
    
    $tables = ['properties', 'units', 'tenants', 'maintenance_requests', 'tenant_applications', 'payments'];
    
    foreach ($tables as $table) {
        echo "\n=== Checking table: $table ===\n";
        
        try {
            $stmt = $pdo->prepare("DESCRIBE $table");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "Columns:\n";
            foreach ($columns as $column) {
                echo "- {$column['Field']} ({$column['Type']})\n";
            }
            
            // Check for admin_id or related columns
            $hasAdminId = false;
            $adminColumns = [];
            foreach ($columns as $column) {
                if ($column['Field'] === 'admin_id') {
                    $hasAdminId = true;
                }
                if (strpos(strtolower($column['Field']), 'admin') !== false) {
                    $adminColumns[] = $column['Field'];
                }
            }
            
            if ($hasAdminId) {
                echo "✅ Has admin_id column\n";
            } else {
                echo "❌ No admin_id column\n";
                if (!empty($adminColumns)) {
                    echo "Admin-related columns: " . implode(', ', $adminColumns) . "\n";
                }
            }
            
            // Check row count
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table");
            $stmt->execute();
            $count = $stmt->fetchColumn();
            echo "Row count: $count\n";
            
        } catch (Exception $e) {
            echo "Error checking table $table: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
}
?>
