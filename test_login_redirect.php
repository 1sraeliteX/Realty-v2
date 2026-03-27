<?php
// Test login redirect for reports page
require_once __DIR__ . '/config/bootstrap.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "Testing login redirect...\n";
echo "========================\n\n";

// Simulate accessing reports without login
echo "1. Testing without admin session:\n";
unset($_SESSION['admin_id']);

try {
    require_once __DIR__ . '/app/controllers/BaseController.php';
    require_once __DIR__ . '/app/controllers/AdminDashboardController.php';
    
    $controller = new \App\Controllers\AdminDashboardController();
    
    // This should redirect to login
    echo "Calling requireAuth()...\n";
    $admin = $controller->requireAuth();
    echo "Unexpected success: " . print_r($admin, true) . "\n";
    
} catch (Exception $e) {
    echo "Expected redirect exception: " . $e->getMessage() . "\n";
}

echo "\n2. Testing with admin session:\n";
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'test@admin.com';

try {
    $controller = new \App\Controllers\AdminDashboardController();
    
    echo "Calling requireAuth()...\n";
    $admin = $controller->requireAuth();
    echo "✅ Auth success: " . $admin['name'] . " (ID: " . $admin['id'] . ")\n";
    
} catch (Exception $e) {
    echo "❌ Unexpected error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
?>
