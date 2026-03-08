<?php

// Fix missing rent_expiry_date column
require_once __DIR__ . '/config/database.php';

use Config\Database;

echo "<h2>Fixing rent_expiry_date Column</h2>";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Check if column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM tenants LIKE 'rent_expiry_date'");
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if ($columnExists) {
        echo "<p style='color: green;'>✓ Column 'rent_expiry_date' already exists in tenants table</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Column 'rent_expiry_date' missing from tenants table</p>";
        
        // Add the column
        $sql = "ALTER TABLE tenants ADD COLUMN rent_expiry_date date NOT NULL AFTER rent_start_date";
        $pdo->exec($sql);
        
        echo "<p style='color: green;'>✓ Column 'rent_expiry_date' added successfully</p>";
    }
    
    // Check if rent_start_date exists (needed for rent_expiry_date)
    $stmt = $pdo->prepare("SHOW COLUMNS FROM tenants LIKE 'rent_start_date'");
    $stmt->execute();
    $rentStartExists = $stmt->fetch();
    
    if (!$rentStartExists) {
        echo "<p style='color: orange;'>⚠ Column 'rent_start_date' missing, adding it...</p>";
        
        $sql = "ALTER TABLE tenants ADD COLUMN rent_start_date date NOT NULL";
        $pdo->exec($sql);
        
        echo "<p style='color: green;'>✓ Column 'rent_start_date' added successfully</p>";
        
        // Set default values for existing records
        $sql = "UPDATE tenants SET rent_start_date = CURDATE(), rent_expiry_date = DATE_ADD(CURDATE(), INTERVAL 1 YEAR) WHERE rent_start_date IS NULL OR rent_expiry_date IS NULL";
        $pdo->exec($sql);
        
        echo "<p style='color: green;'>✓ Default dates set for existing records</p>";
    }
    
    echo "<h3 style='color: green;'>✓ Database fix completed!</h3>";
    echo "<p><a href='/admin/dashboard'>Go to Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

?>
