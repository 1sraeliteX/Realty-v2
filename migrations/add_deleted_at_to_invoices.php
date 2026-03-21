<?php
// Migration script to add deleted_at column to invoices table
try {
    // Include database configuration
    require_once __DIR__ . '/../config/database.php';
    
    // Get database connection
    $db = Config\Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "Connecting to database...\n";
    
    // Check if column already exists
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'invoices'
          AND COLUMN_NAME = 'deleted_at'
    ");
    $stmt->execute();
    $columnExists = (int)$stmt->fetchColumn() > 0;
    
    if ($columnExists) {
        echo "✅ deleted_at column already exists in invoices table\n";
        exit(0);
    }
    
    echo "Adding deleted_at column to invoices table...\n";
    
    // Add the column
    $pdo->exec("ALTER TABLE invoices ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
    
    echo "✅ deleted_at column added successfully to invoices table\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
