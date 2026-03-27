<?php

/**
 * Create message_templates table
 */

require_once 'config/bootstrap.php';

try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_NAME'] ?? 'real_estate_db';
    $username = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';

    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Creating message_templates table...\n";

    // Drop table if exists (for testing)
    $db->exec("DROP TABLE IF EXISTS message_templates");

    // Create message_templates table
    $sql = "CREATE TABLE message_templates (
        id int(10) unsigned NOT NULL AUTO_INCREMENT,
        admin_id int(10) unsigned NOT NULL,
        name varchar(255) NOT NULL,
        type enum('email','sms','whatsapp','broadcast') NOT NULL,
        subject varchar(255) DEFAULT NULL,
        message text NOT NULL,
        variables json DEFAULT NULL,
        is_default tinyint(1) DEFAULT 0,
        usage_count int(11) DEFAULT 0,
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at timestamp NULL DEFAULT NULL,
        PRIMARY KEY (id),
        KEY idx_admin_type (admin_id, type),
        KEY idx_deleted_at (deleted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql);
    echo "✓ message_templates table created successfully\n";

    // Add phone column to tenants table for WhatsApp
    echo "Adding phone column to tenants table...\n";
    $db->exec("ALTER TABLE tenants ADD COLUMN phone varchar(20) DEFAULT NULL AFTER email");
    echo "✓ phone column added to tenants table\n";

    // Add whatsapp_status to communications table
    echo "Adding whatsapp_status to communications table...\n";
    $db->exec("ALTER TABLE communications ADD COLUMN whatsapp_message_id varchar(100) DEFAULT NULL AFTER status");
    echo "✓ whatsapp_status column added to communications table\n";

    echo "\nDatabase migration completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
