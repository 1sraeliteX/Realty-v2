<?php

// Database fix script
try {
    $pdo = new PDO('mysql:host=localhost;dbname=real_estate_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database!\n";
    
    // Add admin_id column to tenants table
    $pdo->exec("ALTER TABLE tenants ADD COLUMN admin_id INT NOT NULL DEFAULT 1 AFTER id");
    echo "Added admin_id column to tenants table\n";
    
    // Create payments table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            tenant_id INT NOT NULL,
            property_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_type ENUM('rent','deposit','utility','maintenance','other') DEFAULT 'rent',
            payment_method ENUM('cash','bank_transfer','check','online','mobile') DEFAULT 'cash',
            due_date DATE NOT NULL,
            payment_date DATE,
            status ENUM('pending','paid','overdue','cancelled') DEFAULT 'pending',
            receipt_reference VARCHAR(255),
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_id) REFERENCES admins(id),
            FOREIGN KEY (tenant_id) REFERENCES tenants(id),
            FOREIGN KEY (property_id) REFERENCES properties(id)
        )
    ");
    echo "Created payments table\n";
    
    // Create invoices table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS invoices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            tenant_id INT NOT NULL,
            property_id INT NOT NULL,
            invoice_number VARCHAR(50) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            due_date DATE NOT NULL,
            status ENUM('draft','sent','paid','overdue','cancelled') DEFAULT 'draft',
            items JSON,
            notes TEXT,
            reminder_sent TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_id) REFERENCES admins(id),
            FOREIGN KEY (tenant_id) REFERENCES tenants(id),
            FOREIGN KEY (property_id) REFERENCES properties(id)
        )
    ");
    echo "Created invoices table\n";
    
    // Create sessions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            token VARCHAR(255) NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_id) REFERENCES admins(id)
        )
    ");
    echo "Created sessions table\n";
    
    echo "Database fix completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
