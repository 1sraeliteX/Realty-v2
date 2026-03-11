<?php

require_once 'config/config.php';
require_once 'config/database.php';

use Config\Database;

echo "=== Checking Admin Users in Database ===\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Check if admins table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('admins', $tables)) {
        echo "✅ Admins table exists\n";
        
        // Check current admin users
        $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM admins WHERE deleted_at IS NULL ORDER BY created_at");
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($admins)) {
            echo "⚠️  No admin users found in database\n";
            
            // Create demo admin users
            echo "🔧 Creating demo admin users...\n";
            
            // Regular admin
            $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, role, business_name, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                'Admin User',
                'admin@cornerstone.com', 
                password_hash('admin123', PASSWORD_DEFAULT),
                'admin',
                'Cornerstone Properties'
            ]);
            
            // Super admin
            $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, role, business_name, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                'Super Admin',
                'superadmin@cornerstone.com',
                password_hash('admin123', PASSWORD_DEFAULT), 
                'super_admin',
                'Super Admin Platform'
            ]);
            
            echo "✅ Demo admin users created successfully\n";
            
            // Check again
            $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM admins WHERE deleted_at IS NULL ORDER BY created_at");
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "📋 Found " . count($admins) . " admin users:\n";
            foreach ($admins as $admin) {
                echo "  - ID: {$admin['id']}, Name: {$admin['name']}, Email: {$admin['email']}, Role: {$admin['role']}\n";
            }
            
        } else {
            echo "📋 Found " . count($admins) . " admin users:\n";
            foreach ($admins as $admin) {
                echo "  - ID: {$admin['id']}, Name: {$admin['name']}, Email: {$admin['email']}, Role: {$admin['role']}\n";
            }
        }
        
        echo "\n=== Database Check Complete ===\n";
        
    } else {
        echo "❌ Admins table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
