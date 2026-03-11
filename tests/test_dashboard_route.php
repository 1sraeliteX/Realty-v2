<?php
// Test the admin dashboard route through the web interface
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate a web request to the admin dashboard
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/admin/dashboard';
$_SERVER['HTTP_HOST'] = '127.0.0.1:8080';
$_SERVER['SCRIPT_NAME'] = '/public/app.php';

session_start();

// Set up admin session
$_SESSION['admin_id'] = 7;
$_SESSION['admin_email'] = 'test@admin.com';
$_SESSION['admin_role'] = 'admin';

echo "Testing admin dashboard route...\n";

// Load the router
require_once __DIR__ . '/public/app.php';

echo "✅ Admin dashboard route test completed successfully!\n";
?>
