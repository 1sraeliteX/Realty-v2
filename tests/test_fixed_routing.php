<?php
// Test the fixed routing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate a web request to the admin dashboard
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/admin/dashboard';
$_SERVER['HTTP_HOST'] = '127.0.0.1:8080';
$_SERVER['SCRIPT_NAME'] = '/app.php';

session_start();

// Set up admin session for testing
$_SESSION['admin_id'] = 7;
$_SESSION['admin_email'] = 'test@admin.com';
$_SESSION['admin_role'] = 'admin';

echo "Testing fixed routing for /admin/dashboard...\n";

// Load the application
try {
    require_once __DIR__ . '/public/app.php';
    echo "✅ Routing test completed successfully!\n";
} catch (Exception $e) {
    echo "❌ Routing test failed: " . $e->getMessage() . "\n";
}
?>
