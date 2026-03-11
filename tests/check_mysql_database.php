<?php

// MySQL database check and setup script
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';

use Config\Database;
use Config\Config;

echo "<h2>MySQL Database Status Check</h2>";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check if database exists
    $pdo->exec("USE real_estate_db");
    echo "<p style='color: green;'>✓ Database 'real_estate_db' exists and accessible</p>";
    
    // Check required tables
    $requiredTables = ['admins', 'properties', 'tenants', 'payments', 'activities', 'units', 'invoices', 'sessions'];
    $missingTables = [];
    
    foreach ($requiredTables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE '$table'");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            echo "<p style='color: green;'>✓ Table '$table' exists</p>";
            
            // Check if admin_id column exists in key tables
            if (in_array($table, ['properties', 'tenants', 'payments', 'activities'])) {
                $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE 'admin_id'");
                $stmt->execute();
                $columnExists = $stmt->fetch();
                
                if ($columnExists) {
                    echo "<p style='color: green;'>  ✓ Column 'admin_id' exists in '$table'</p>";
                } else {
                    echo "<p style='color: red;'>  ✗ Column 'admin_id' MISSING in '$table'</p>";
                    $missingTables[] = "$table (admin_id column)";
                }
            }
        } else {
            echo "<p style='color: red;'>✗ Table '$table' MISSING</p>";
            $missingTables[] = $table;
        }
    }
    
    // Check if there are admin users
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admins");
    $stmt->execute();
    $adminCount = $stmt->fetch()['count'];
    echo "<p>Admin users in database: $adminCount</p>";
    
    if ($adminCount == 0) {
        echo "<p style='color: orange;'>⚠ No admin users found. You may need to create admin accounts.</p>";
    }
    
    if (!empty($missingTables)) {
        echo "<h3 style='color: red;'>Missing Tables/Columns:</h3>";
        echo "<ul>";
        foreach ($missingTables as $missing) {
            echo "<li style='color: red;'>$missing</li>";
        }
        echo "</ul>";
        
        echo "<h3>Solution:</h3>";
        echo "<p>Run the database schema to create missing tables:</p>";
        echo "<code>mysql -u root real_estate_db < database/schema.sql</code>";
        echo "<br><br>";
        echo "<p>Or use the setup script:</p>";
        echo "<a href='setup_database.php'>Click here to run setup_database.php</a>";
    } else {
        echo "<h3 style='color: green;'>✓ All required tables and columns exist!</h3>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
    
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        echo "<h3>Solution:</h3>";
        echo "<p>Create the database first:</p>";
        echo "<code>CREATE DATABASE real_estate_db;</code>";
        echo "<br><br>";
        echo "<p>Then run the schema:</p>";
        echo "<code>mysql -u root real_estate_db < database/schema.sql</code>";
    }
}

?>
