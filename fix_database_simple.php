<?php

// Simple database fix without dependencies
echo "<h2>Fixing Database Schema</h2>";

try {
    // Direct database connection
    $host = 'localhost';
    $dbname = 'real_estate_db';
    $user = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Database connected successfully</p>";
    
    // Check if rent_start_date column exists first (it's required for rent_expiry_date)
    $stmt = $pdo->prepare("SHOW COLUMNS FROM tenants LIKE 'rent_start_date'");
    $stmt->execute();
    $rentStartExists = $stmt->fetch();
    
    if (!$rentStartExists) {
        echo "<p style='color: orange;'>⚠ Adding 'rent_start_date' column...</p>";
        
        $sql = "ALTER TABLE tenants ADD COLUMN rent_start_date date NOT NULL AFTER phone";
        $pdo->exec($sql);
        echo "<p style='color: green;'>✓ Column 'rent_start_date' added successfully</p>";
    } else {
        echo "<p style='color: green;'>✓ Column 'rent_start_date' already exists</p>";
    }
    
    // Check if rent_expiry_date column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM tenants LIKE 'rent_expiry_date'");
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        echo "<p style='color: orange;'>⚠ Adding 'rent_expiry_date' column...</p>";
        
        $sql = "ALTER TABLE tenants ADD COLUMN rent_expiry_date date NOT NULL AFTER rent_start_date";
        $pdo->exec($sql);
        echo "<p style='color: green;'>✓ Column 'rent_expiry_date' added successfully</p>";
    } else {
        echo "<p style='color: green;'>✓ Column 'rent_expiry_date' already exists</p>";
    }
    
    // Update existing records with default values if needed
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tenants WHERE rent_start_date IS NULL OR rent_expiry_date IS NULL");
    $stmt->execute();
    $nullCount = $stmt->fetch()['count'];
    
    if ($nullCount > 0) {
        echo "<p style='color: orange;'>⚠ Updating $nullCount records with default dates...</p>";
        
        $sql = "UPDATE tenants SET 
                rent_start_date = COALESCE(rent_start_date, CURDATE()),
                rent_expiry_date = COALESCE(rent_expiry_date, DATE_ADD(CURDATE(), INTERVAL 1 YEAR))
                WHERE rent_start_date IS NULL OR rent_expiry_date IS NULL";
        $pdo->exec($sql);
        echo "<p style='color: green;'>✓ Records updated successfully</p>";
    }
    
    echo "<h3 style='color: green;'>✓ Database schema fixed successfully!</h3>";
    echo "<p><a href='/admin/dashboard'>Go to Dashboard</a> | <a href='/'>Home</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}

?>
