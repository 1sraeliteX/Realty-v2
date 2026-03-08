<?php
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'app/controllers/AuthController.php';
require_once 'app/controllers/BaseController.php';

use Config\Database;

echo "=== Testing Fixed Login System ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    echo "✅ Database connection successful\n\n";

    // Test AuthController instantiation
    echo "2. Testing AuthController instantiation...\n";
    $authController = new \App\Controllers\AuthController();
    echo "✅ AuthController instantiated successfully\n\n";

    // Test manual login process
    echo "3. Testing manual login process...\n";
    
    // Simulate POST data for admin login
    $_POST['email'] = 'admin@cornerstone.com';
    $_POST['password'] = 'admin123';
    
    // Mock server method
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    // Capture output
    ob_start();
    try {
        $authController->login();
    } catch (Exception $e) {
        echo "Login method error: " . $e->getMessage() . "\n";
    }
    $output = ob_get_clean();
    
    if (empty($output)) {
        echo "✅ Admin login process completed (no output means redirect happened)\n";
        echo "   Session data:\n";
        echo "   Admin ID: " . ($_SESSION['admin_id'] ?? 'Not set') . "\n";
        echo "   Admin Name: " . ($_SESSION['admin_name'] ?? 'Not set') . "\n";
        echo "   Admin Email: " . ($_SESSION['admin_email'] ?? 'Not set') . "\n";
        echo "   Admin Role: " . ($_SESSION['admin_role'] ?? 'Not set') . "\n";
    } else {
        echo "❌ Admin login failed: $output\n";
    }
    
    echo "\n";
    
    // Clear session and test super admin
    session_destroy();
    session_start();
    
    // Simulate POST data for super admin login
    $_POST['email'] = 'superadmin@cornerstone.com';
    $_POST['password'] = 'admin123';
    
    // Capture output
    ob_start();
    try {
        $authController->login();
    } catch (Exception $e) {
        echo "Login method error: " . $e->getMessage() . "\n";
    }
    $output = ob_get_clean();
    
    if (empty($output)) {
        echo "✅ Super Admin login process completed (no output means redirect happened)\n";
        echo "   Session data:\n";
        echo "   Admin ID: " . ($_SESSION['admin_id'] ?? 'Not set') . "\n";
        echo "   Admin Name: " . ($_SESSION['admin_name'] ?? 'Not set') . "\n";
        echo "   Admin Email: " . ($_SESSION['admin_email'] ?? 'Not set') . "\n";
        echo "   Admin Role: " . ($_SESSION['admin_role'] ?? 'Not set') . "\n";
    } else {
        echo "❌ Super Admin login failed: $output\n";
    }
    
    echo "\n";
    
    // Test dashboard access
    echo "4. Testing dashboard access...\n";
    
    // Set session as admin
    $_SESSION['admin_id'] = 3; // Test admin ID
    $_SESSION['admin_name'] = 'Test Admin';
    $_SESSION['admin_email'] = 'admin@cornerstone.com';
    $_SESSION['admin_role'] = 'admin';
    
    require_once 'app/controllers/DashboardController.php';
    $dashboardController = new \App\Controllers\DashboardController();
    
    echo "✅ DashboardController instantiated successfully\n";
    
    // Test super admin dashboard access
    session_destroy();
    session_start();
    
    $_SESSION['admin_id'] = 4; // Test super admin ID
    $_SESSION['admin_name'] = 'Super Administrator';
    $_SESSION['admin_email'] = 'superadmin@cornerstone.com';
    $_SESSION['admin_role'] = 'super_admin';
    
    require_once 'app/controllers/SuperAdminController.php';
    $superAdminController = new \App\Controllers\SuperAdminController();
    
    echo "✅ SuperAdminController instantiated successfully\n";
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
