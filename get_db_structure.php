<?php
// Load database configuration directly
require_once 'config/database.php';

use Config\Database;

$db = Database::getInstance()->getConnection();

echo "=== DOCUMENTS TABLE ===\n";
try {
    $result = $db->query('DESCRIBE documents')->fetchAll();
    foreach($result as $row) {
        printf("%-20s %-25s %-10s %-10s %-10s %-15s\n", 
            $row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Default'], $row['Extra']);
    }
} catch(Exception $e) {
    echo "Documents table not found: " . $e->getMessage() . "\n";
}

echo "\n=== COMMUNICATIONS TABLE ===\n";
try {
    $result = $db->query('DESCRIBE communications')->fetchAll();
    foreach($result as $row) {
        printf("%-20s %-25s %-10s %-10s %-10s %-15s\n", 
            $row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Default'], $row['Extra']);
    }
} catch(Exception $e) {
    echo "Communications table not found: " . $e->getMessage() . "\n";
}

echo "\n=== MAINTENANCE REQUESTS TABLE ===\n";
try {
    $result = $db->query('DESCRIBE maintenance_requests')->fetchAll();
    foreach($result as $row) {
        printf("%-20s %-25s %-10s %-10s %-10s %-15s\n", 
            $row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Default'], $row['Extra']);
    }
} catch(Exception $e) {
    echo "Maintenance requests table not found: " . $e->getMessage() . "\n";
}
?>
