<?php
require_once 'config/config_simple.php';
require_once 'config/database.php';

$config = Config\ConfigSimple::getInstance();
$db = Config\Database::getInstance();

echo "=== UPDATING DOCUMENTS TABLE STRUCTURE ===\n";

// Check current structure
echo "Current structure:\n";
$stmt = $db->query('SHOW COLUMNS FROM documents');
while ($row = $stmt->fetch()) {
    echo "- {$row['Field']} ({$row['Type']})\n";
}

echo "\nApplying updates...\n";

try {
    $db->beginTransaction();
    
    // 1. Add missing columns if they don't exist
    $updates = [
        // Add file_name column if it doesn't exist
        "ALTER TABLE documents ADD COLUMN file_name VARCHAR(255) AFTER title" => 
            "SELECT COUNT(*) as count FROM information_schema.columns WHERE table_name = 'documents' AND column_name = 'file_name' AND table_schema = DATABASE()",
            
        // Add file_size column if it doesn't exist  
        "ALTER TABLE documents ADD COLUMN file_size INT AFTER file_path" =>
            "SELECT COUNT(*) as count FROM information_schema.columns WHERE table_name = 'documents' AND column_name = 'file_size' AND table_schema = DATABASE()",
            
        // Add description column if it doesn't exist
        "ALTER TABLE documents ADD COLUMN description TEXT AFTER title" =>
            "SELECT COUNT(*) as count FROM information_schema.columns WHERE table_name = 'documents' AND column_name = 'description' AND table_schema = DATABASE()",
            
        // Add related_to_type ENUM if it doesn't exist
        "ALTER TABLE documents ADD COLUMN related_to_type ENUM('property','tenant','lease') AFTER tenant_id" =>
            "SELECT COUNT(*) as count FROM information_schema.columns WHERE table_name = 'documents' AND column_name = 'related_to_type' AND table_schema = DATABASE()",
            
        // Add related_to_id column if it doesn't exist
        "ALTER TABLE documents ADD COLUMN related_to_id INT AFTER related_to_type" =>
            "SELECT COUNT(*) as count FROM information_schema.columns WHERE table_name = 'documents' AND column_name = 'related_to_id' AND table_schema = DATABASE()"
    ];
    
    foreach ($updates as $alterSql => $checkSql) {
        $result = $db->fetch($checkSql);
        if ($result['count'] == 0) {
            echo "Executing: $alterSql\n";
            $db->query($alterSql);
            echo "✅ Done\n";
        } else {
            echo "⏭️  Column already exists, skipping\n";
        }
    }
    
    // 2. Rename 'type' to 'file_type' if needed
    $typeCheck = $db->fetch("SELECT COUNT(*) as count FROM information_schema.columns WHERE table_name = 'documents' AND column_name = 'type' AND table_schema = DATABASE()");
    $fileTypeCheck = $db->fetch("SELECT COUNT(*) as count FROM information_schema.columns WHERE table_name = 'documents' AND column_name = 'file_type' AND table_schema = DATABASE()");
    
    if ($typeCheck['count'] > 0 && $fileTypeCheck['count'] == 0) {
        echo "Renaming 'type' to 'file_type'\n";
        $db->query("ALTER TABLE documents CHANGE COLUMN type file_type VARCHAR(100)");
        echo "✅ Done\n";
    } elseif ($typeCheck['count'] == 0 && $fileTypeCheck['count'] > 0) {
        echo "✅ 'file_type' already exists\n";
    }
    
    $db->commit();
    
    echo "\n=== UPDATED STRUCTURE ===\n";
    $stmt = $db->query('SHOW COLUMNS FROM documents');
    while ($row = $stmt->fetch()) {
        echo "- {$row['Field']} ({$row['Type']}) | NULL: {$row['Null']} | KEY: {$row['Key']}\n";
    }
    
    echo "\n✅ Database schema updated successfully!\n";
    
} catch (Exception $e) {
    $db->rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
