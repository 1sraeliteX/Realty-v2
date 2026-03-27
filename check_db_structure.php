<?php
// Check database structure for properties table
require_once __DIR__ . '/config/database.php';

try {
    $pdo = \Config\Database::getInstance()->getConnection();
    
    echo "Checking properties table structure...\n";
    
    // Get table structure
    $stmt = $pdo->prepare("DESCRIBE properties");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nProperties table columns:\n";
    echo "========================\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
    // Check if admin_id exists
    $hasAdminId = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'admin_id') {
            $hasAdminId = true;
            break;
        }
    }
    
    if ($hasAdminId) {
        echo "\n✅ admin_id column exists in properties table\n";
    } else {
        echo "\n❌ admin_id column MISSING in properties table\n";
        
        // Check for other possible admin columns
        echo "\nChecking for alternative admin columns...\n";
        foreach ($columns as $column) {
            if (strpos(strtolower($column['Field']), 'admin') !== false) {
                echo "- Found: {$column['Field']}\n";
            }
        }
    }
    
    // Check sample data
    echo "\n\nChecking sample data in properties table...\n";
    $stmt = $pdo->prepare("SELECT * FROM properties LIMIT 3");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($rows)) {
        echo "Sample data found:\n";
        foreach ($rows as $row) {
            echo "- ID: {$row['id']}, Name: " . ($row['name'] ?? 'N/A') . "\n";
            if (isset($row['admin_id'])) {
                echo "  admin_id: {$row['admin_id']}\n";
            }
        }
    } else {
        echo "No data found in properties table\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
