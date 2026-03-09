<?php
require_once __DIR__ . '/config/config_simple.php';
require_once __DIR__ . '/config/database.php';

$db = Config\Database::getInstance();
$tables = ['properties', 'units', 'tenants', 'payments', 'activities'];

echo "<h2>Database Tables Check</h2>";

foreach ($tables as $table) {
    $stmt = $db->getConnection()->query("SHOW TABLES LIKE '$table'");
    $exists = $stmt->rowCount() > 0;
    echo "<p>" . $table . ": " . ($exists ? '<span style="color: green;">EXISTS</span>' : '<span style="color: red;">MISSING</span>') . "</p>";
}

// If tables are missing, create them
echo "<h3>Creating Missing Tables...</h3>";

// Simple properties table
try {
    $db->getConnection()->exec("
        CREATE TABLE IF NOT EXISTS properties (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            address TEXT,
            type VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p style='color: green;'>✓ Properties table created/verified</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Properties table error: " . $e->getMessage() . "</p>";
}

// Simple units table
try {
    $db->getConnection()->exec("
        CREATE TABLE IF NOT EXISTS units (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_id INT NOT NULL,
            unit_number VARCHAR(50),
            status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
            rent_amount DECIMAL(10,2),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p style='color: green;'>✓ Units table created/verified</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Units table error: " . $e->getMessage() . "</p>";
}

// Simple tenants table
try {
    $db->getConnection()->exec("
        CREATE TABLE IF NOT EXISTS tenants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            phone VARCHAR(50),
            rent_expiry_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p style='color: green;'>✓ Tenants table created/verified</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Tenants table error: " . $e->getMessage() . "</p>";
}

// Simple payments table
try {
    $db->getConnection()->exec("
        CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            tenant_id INT,
            amount DECIMAL(10,2) NOT NULL,
            status ENUM('pending', 'paid', 'overdue') DEFAULT 'pending',
            payment_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "<p style='color: green;'>✓ Payments table created/verified</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Payments table error: " . $e->getMessage() . "</p>";
}

// Simple activities table
try {
    $db->getConnection()->exec("
        CREATE TABLE IF NOT EXISTS activities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            action VARCHAR(100) NOT NULL,
            description TEXT,
            entity_type VARCHAR(100),
            entity_id INT,
            metadata JSON,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<p style='color: green;'>✓ Activities table created/verified</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Activities table error: " . $e->getMessage() . "</p>";
}

echo "<h3>Table Check Complete</h3>";
echo "<p><a href='/admin/login'>Try Login Again</a></p>";
?>
