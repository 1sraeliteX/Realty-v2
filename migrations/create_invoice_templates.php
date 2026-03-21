<?php
// Migration script to create invoice_templates table
try {
    // Include database configuration
    require_once __DIR__ . '/../config/database.php';
    
    // Get database connection
    $db = \Config\Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "Creating invoice_templates table...\n";
    
    // Read and execute the SQL migration
    $sql = file_get_contents(__DIR__ . '/create_invoice_templates.sql');
    $pdo->exec($sql);
    
    echo "✅ invoice_templates table created successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
