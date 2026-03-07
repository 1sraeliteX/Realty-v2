<?php

// Create database and tables
try {
    // Connect to MySQL without specifying database
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS real_estate_db");
    echo "Database 'real_estate_db' created successfully!\n";
    
    // Switch to the new database
    $pdo->exec("USE real_estate_db");
    
    // Create admins table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'super_admin') DEFAULT 'admin',
            business_name VARCHAR(255),
            phone VARCHAR(50),
            email_verified_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL
        )
    ");
    
    // Create properties table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS properties (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            address TEXT NOT NULL,
            type VARCHAR(100) NOT NULL,
            category VARCHAR(100),
            description TEXT,
            year_built INT,
            bedrooms INT,
            bathrooms DECIMAL(3,1),
            kitchens INT DEFAULT 1,
            parking VARCHAR(20) DEFAULT 'no',
            rent_price DECIMAL(10,2),
            status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
            amenities JSON,
            images JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (admin_id) REFERENCES admins(id)
        )
    ");
    
    // Create units table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS units (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_id INT NOT NULL,
            unit_number VARCHAR(50) NOT NULL,
            type VARCHAR(100) DEFAULT 'residential',
            bedrooms INT DEFAULT 1,
            bathrooms DECIMAL(3,1) DEFAULT 1,
            kitchens INT DEFAULT 1,
            rent_price DECIMAL(10,2),
            status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
            tenant_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (property_id) REFERENCES properties(id)
        )
    ");
    
    // Create tenants table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tenants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_id INT NOT NULL,
            unit_id INT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            phone VARCHAR(50),
            rent_amount DECIMAL(10,2),
            deposit_amount DECIMAL(10,2),
            lease_start DATE,
            lease_end DATE,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (property_id) REFERENCES properties(id),
            FOREIGN KEY (unit_id) REFERENCES units(id)
        )
    ");
    
    // Create activities table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS activities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            action VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            entity_type VARCHAR(100),
            entity_id INT,
            metadata JSON,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_id) REFERENCES admins(id)
        )
    ");
    
    // Insert test admin users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins WHERE deleted_at IS NULL");
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO admins (name, email, password, role, business_name) 
            VALUES ('Test Admin', 'admin@test.com', '$password', 'admin', 'Test Properties')
        ");
        
        $password = password_hash('super123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO admins (name, email, password, role, business_name) 
            VALUES ('Super Admin', 'super@test.com', '$password', 'super_admin', 'Super Admin')
        ");
        
        echo "Test admin users created:\n";
        echo "Admin: admin@test.com / admin123\n";
        echo "Super Admin: super@test.com / super123\n";
    }
    
    echo "All tables created successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
