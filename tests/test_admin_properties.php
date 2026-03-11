<?php
// Test to check which properties show for different admin IDs
require_once 'config/config_simple.php';
require_once 'config/database.php';

use Config\Database;

try {
    $db = Database::getInstance();
    
    // Get all admins
    $admins = $db->fetchAll('SELECT id, name, email FROM admins WHERE deleted_at IS NULL');
    
    echo "Checking properties for each admin:\n\n";
    
    foreach($admins as $admin) {
        $props = $db->fetchAll(
            "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE p.admin_id = ? AND p.deleted_at IS NULL 
            ORDER BY p.created_at DESC", 
            [$admin['id']]
        );
        
        echo "Admin: " . $admin['name'] . " (ID: " . $admin['id'] . ") - " . count($props) . " properties\n";
        foreach($props as $p) {
            echo "  - " . $p['name'] . "\n";
        }
        echo "\n";
    }
    
    // Check all properties without admin filter
    $allProps = $db->fetchAll("SELECT * FROM properties WHERE deleted_at IS NULL ORDER BY created_at DESC");
    echo "Total properties in database: " . count($allProps) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
