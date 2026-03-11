<?php
require_once 'config/database.php';

try {
    $pdo = Config\Database::getInstance()->getConnection();
    
    // Check if payments table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'payments'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✅ Payments table exists\n\n";
        
        // Show table structure
        $stmt = $pdo->query("DESCRIBE payments");
        $columns = $stmt->fetchAll();
        
        echo "Columns in payments table:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Check if payment_status column exists
        $hasPaymentStatus = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'payment_status') {
                $hasPaymentStatus = true;
                break;
            }
        }
        
        if ($hasPaymentStatus) {
            echo "\n✅ payment_status column exists\n";
        } else {
            echo "\n❌ payment_status column is MISSING\n";
        }
        
    } else {
        echo "❌ Payments table does NOT exist\n";
        
        // List all tables
        echo "\nAvailable tables:\n";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        foreach ($tables as $table) {
            echo "- {$table[0]}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
