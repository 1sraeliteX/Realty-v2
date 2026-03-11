<?php

// Test login with debugging
session_start();

// Simulate login form submission
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/login';
$_POST['email'] = 'admin@cornerstone.com';
$_POST['password'] = 'admin123';

echo "=== Testing Login with Debug ===\n";

// Load the application
require_once __DIR__ . '/public/index.php';

echo "Login test completed.\n";
echo "Check PHP error logs for debug information.\n";
?>
