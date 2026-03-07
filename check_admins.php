<?php

require_once 'config/config_simple.php';
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
        } else {
            echo "📋 Found " . count($admins) . " admin users:\n";
            foreach ($admins as $admin) {
                echo "  - ID: {$admin['id']}, Name: {$admin['name']}, Email: {$admin['email']}, Role: {$admin['role']}\n";
            }
        }
        
        // Ensure test users exist
        $testAdmin = $pdo->prepare("SELECT id FROM admins WHERE email = 'admin@test.com' AND deleted_at IS NULL");
        $testAdmin->execute();
        $testAdmin = $testAdmin->fetch(PDO::FETCH_ASSOC);
        
        $testSuperAdmin = $pdo->prepare("SELECT id FROM admins WHERE email = 'super@test.com' AND deleted_at IS NULL");
        $testSuperAdmin->execute();
        $testSuperAdmin = $testSuperAdmin->fetch(PDO::FETCH_ASSOC);
        
        if (!$testAdmin) {
            echo "⚠️  Test admin user (admin@test.com) not found - adding...\n";
            $pdo->exec("INSERT INTO admins (name, email, password, role, business_name, created_at) VALUES (?, ?, ?, ?, ?)", 
                ['Test Admin', 'admin@test.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin', 'Test Properties', NOW()]);
        } else {
            echo "✅ Test admin user exists\n";
        }
        
        if (!$testSuperAdmin) {
            echo "⚠️  Test super admin user (super@test.com) not found - adding...\n";
            $pdo->exec("INSERT INTO admins (name, email, password, role, business_name, created_at) VALUES (?, ?, ?, ?, ?)", 
                ['Super Admin', 'super@test.com', password_hash('super123', PASSWORD_DEFAULT), 'super_admin', 'Super Admin Platform', NOW()]);
        } else {
            echo "✅ Test super admin user exists\n";
        }
        
        echo "\n=== Database Check Complete ===\n";
        
    }
    
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
?>
