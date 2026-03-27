<?php
// Check activities table structure
require_once __DIR__ . '/config/database.php';

try {
    $pdo = \Config\Database::getInstance()->getConnection();
    
    echo "Checking activities table structure...\n";
    
    // Check if table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'activities'");
    $stmt->execute();
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "✅ activities table exists\n";
        
        // Get table structure
        $stmt = $pdo->prepare("DESCRIBE activities");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nActivities table columns:\n";
        echo "==========================\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Check for admin_id column
        $hasAdminId = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'admin_id') {
                $hasAdminId = true;
                break;
            }
        }
        
        if ($hasAdminId) {
            echo "\n✅ admin_id column exists\n";
        } else {
            echo "\n❌ admin_id column MISSING\n";
            
            // Check for other admin columns
            echo "\nChecking for alternative admin columns...\n";
            foreach ($columns as $column) {
                if (strpos(strtolower($column['Field']), 'admin') !== false) {
                    echo "- Found: {$column['Field']}\n";
                }
            }
        }
        
        // Check row count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM activities");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "Row count: $count\n";
        
        // Test the exact query
        echo "\nTesting the exact query from getRecentActivities...\n";
        try {
            $adminId = 1;
            $limit = 10;
            $stmt = $pdo->prepare("SELECT a.*, p.name as property_name 
                                   FROM activities a
                                   LEFT JOIN properties p ON a.entity_id = p.id AND a.entity_type = 'property'
                                   WHERE a.admin_id = ? 
                                   ORDER BY a.created_at DESC 
                                   LIMIT ?");
            $stmt->execute([$adminId, $limit]);
            $results = $stmt->fetchAll();
            echo "✅ Query SUCCESS: " . count($results) . " activities found\n";
        } catch (Exception $e) {
            echo "❌ Query FAILED: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ activities table does NOT exist\n";
        
        // Show all tables
        echo "\nAll tables in database:\n";
        $stmt = $pdo->prepare("SHOW TABLES");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            echo "- $table\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
