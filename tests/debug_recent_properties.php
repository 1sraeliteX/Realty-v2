<?php
// Test recent property creation and display
require_once 'config/config_simple.php';
require_once 'config/database.php';

use Config\Database;

try {
    $db = Database::getInstance();
    
    echo "=== Recent Property Activity ===\n\n";
    
    // Get most recent properties
    $recentProps = $db->fetchAll(
        "SELECT * FROM properties WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5"
    );
    
    echo "Most recent properties:\n";
    foreach($recentProps as $prop) {
        echo "- " . $prop['name'] . " (ID: " . $prop['id'] . ") - Admin: " . $prop['admin_id'] . " - Created: " . $prop['created_at'] . "\n";
        echo "  Address: " . substr($prop['address'], 0, 50) . "...\n";
        echo "  Type: " . $prop['type'] . " | Status: " . $prop['status'] . "\n\n";
    }
    
    // Check properties by admin
    echo "Properties by admin:\n";
    $admins = $db->fetchAll("SELECT id, name, email FROM admins WHERE deleted_at IS NULL");
    
    foreach($admins as $admin) {
        $count = $db->fetch("SELECT COUNT(*) as count FROM properties WHERE admin_id = ? AND deleted_at IS NULL", [$admin['id']])['count'];
        echo "- " . $admin['name'] . " (ID: " . $admin['id'] . "): " . $count . " properties\n";
    }
    
    // Test the exact query used by PropertyController
    echo "\n=== Testing PropertyController Query ===\n";
    
    // Simulate admin ID 1 (Test Admin)
    $adminId = 1;
    $page = 1;
    $search = '';
    $type = '';
    $category = '';
    $status = '';
    
    $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
    $params = [$adminId];
    
    $whereClause = implode(' AND ', $where);
    
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE {$whereClause}
            ORDER BY p.created_at DESC";
    
    $result = $db->fetchAll($sql, $params);
    
    echo "Query results for Admin ID $adminId:\n";
    echo "Found " . count($result) . " properties\n\n";
    
    foreach($result as $property) {
        echo "- " . $property['name'] . " (ID: " . $property['id'] . ")\n";
        echo "  Units: " . $property['unit_count'] . " | Occupied: " . $property['occupied_units'] . "\n";
        echo "  Created: " . $property['created_at'] . "\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
