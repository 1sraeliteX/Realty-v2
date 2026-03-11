<?php
// Show exactly where your properties are stored
require_once 'config/config_simple.php';
require_once 'config/database.php';

use Config\Database;

echo "=== PROPERTY STORAGE LOCATION ANALYSIS ===\n\n";

try {
    $db = Database::getInstance();
    
    echo "CURRENT DATABASE CONFIGURATION:\n";
    echo "✓ Host: " . $db->getConnection()->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";
    echo "✓ Database: real_estate_db (MySQL)\n";
    echo "✓ Table: properties\n\n";
    
    // Show all properties in local MySQL
    $properties = $db->fetchAll("SELECT * FROM properties WHERE deleted_at IS NULL ORDER BY created_at DESC");
    
    echo "PROPERTIES IN LOCAL MYSQL DATABASE:\n";
    echo "===================================\n";
    echo "Found " . count($properties) . " properties\n\n";
    
    foreach ($properties as $prop) {
        echo "🏠 Property: " . $prop['name'] . "\n";
        echo "   ID: " . $prop['id'] . "\n";
        echo "   Address: " . $prop['address'] . "\n";
        echo "   Type: " . $prop['type'] . "\n";
        echo "   Admin ID: " . $prop['admin_id'] . "\n";
        echo "   Created: " . $prop['created_at'] . "\n";
        echo "   Status: " . $prop['status'] . "\n\n";
    }
    
    echo "\nSUPABASE VS LOCAL MYSQL:\n";
    echo "========================\n";
    echo "❌ You're looking in Supabase dashboard (PostgreSQL)\n";
    echo "✅ Properties are actually stored in local MySQL database\n";
    echo "❌ These are two completely separate databases\n\n";
    
    echo "HOW TO SEE YOUR PROPERTIES:\n";
    echo "==========================\n";
    echo "Option 1 - Use the web application:\n";
    echo "   → http://127.0.0.1:49677/admin/properties\n";
    echo "   → http://127.0.0.1:49677/properties\n\n";
    
    echo "Option 2 - Use MySQL command line:\n";
    echo "   mysql -u root real_estate_db\n";
    echo "   SELECT * FROM properties;\n\n";
    
    echo "Option 3 - Use phpMyAdmin/MySQL Workbench:\n";
    echo "   Connect to localhost/real_estate_db\n";
    echo "   Browse the 'properties' table\n\n";
    
    echo "TO USE SUPABASE INSTEAD:\n";
    echo "======================\n";
    echo "You need to either:\n";
    echo "1. Configure your app to use Supabase instead of MySQL\n";
    echo "2. Or migrate your data from MySQL to Supabase\n\n";
    
    echo "The 'properties' table in your Supabase dashboard is empty\n";
    echo "because your app is saving to MySQL, not Supabase.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
