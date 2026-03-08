<?php
session_start();

// Include necessary files
require_once 'config/config_simple.php';
require_once 'config/database.php';

use Config\Database;

echo "=== Login Debug Test ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    echo "✅ Database connection successful\n\n";

    // Test admin user lookup
    echo "2. Testing admin user lookup...\n";
    
    // Test regular admin
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND deleted_at IS NULL");
    $stmt->execute(['admin@cornerstone.com']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ Found admin user:\n";
        echo "   ID: {$admin['id']}\n";
        echo "   Name: {$admin['name']}\n";
        echo "   Email: {$admin['email']}\n";
        echo "   Role: {$admin['role']}\n";
        echo "   Password Hash: " . substr($admin['password'], 0, 20) . "...\n";
        
        // Test password verification
        if (password_verify('admin123', $admin['password'])) {
            echo "   ✅ Password verification successful\n";
        } else {
            echo "   ❌ Password verification failed\n";
        }
    } else {
        echo "❌ Admin user not found\n";
    }
    
    echo "\n";
    
    // Test super admin
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND deleted_at IS NULL");
    $stmt->execute(['superadmin@cornerstone.com']);
    $superAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($superAdmin) {
        echo "✅ Found super admin user:\n";
        echo "   ID: {$superAdmin['id']}\n";
        echo "   Name: {$superAdmin['name']}\n";
        echo "   Email: {$superAdmin['email']}\n";
        echo "   Role: {$superAdmin['role']}\n";
        echo "   Password Hash: " . substr($superAdmin['password'], 0, 20) . "...\n";
        
        // Test password verification
        if (password_verify('admin123', $superAdmin['password'])) {
            echo "   ✅ Password verification successful\n";
        } else {
            echo "   ❌ Password verification failed\n";
        }
    } else {
        echo "❌ Super admin user not found\n";
    }
    
    echo "\n";
    
    // Test session simulation
    echo "3. Testing session simulation...\n";
    
    // Simulate successful login for admin
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_role'] = $admin['role'];
    
    echo "✅ Session set for admin:\n";
    echo "   Admin ID: " . $_SESSION['admin_id'] . "\n";
    echo "   Admin Role: " . $_SESSION['admin_role'] . "\n";
    
    // Test redirect logic
    if ($_SESSION['admin_role'] === 'super_admin') {
        echo "   Would redirect to: /superadmin\n";
    } else {
        echo "   Would redirect to: /dashboard\n";
    }
    
    echo "\n";
    
    // Clear session and test super admin
    session_destroy();
    session_start();
    
    $_SESSION['admin_id'] = $superAdmin['id'];
    $_SESSION['admin_name'] = $superAdmin['name'];
    $_SESSION['admin_email'] = $superAdmin['email'];
    $_SESSION['admin_role'] = $superAdmin['role'];
    
    echo "✅ Session set for super admin:\n";
    echo "   Admin ID: " . $_SESSION['admin_id'] . "\n";
    echo "   Admin Role: " . $_SESSION['admin_role'] . "\n";
    
    // Test redirect logic
    if ($_SESSION['admin_role'] === 'super_admin') {
        echo "   Would redirect to: /superadmin\n";
    } else {
        echo "   Would redirect to: /dashboard\n";
    }
    
    echo "\n=== Debug Complete ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
