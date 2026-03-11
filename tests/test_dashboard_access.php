<?php

// Test dashboard access after login
session_start();

echo "=== Testing Dashboard Access ===\n";

// Simulate successful login session
$_SESSION['admin_id'] = '65c031c7-d47a-435c-80fa-e113515afaf9'; // Super Admin ID
$_SESSION['admin_name'] = 'Super Admin';
$_SESSION['admin_email'] = 'superadmin@cornerstone.com';
$_SESSION['admin_role'] = 'super_admin';

echo "Session set:\n";
echo "- admin_id: " . $_SESSION['admin_id'] . "\n";
echo "- admin_name: " . $_SESSION['admin_name'] . "\n";
echo "- admin_role: " . $_SESSION['admin_role'] . "\n";

// Load application
require_once __DIR__ . '/public/index.php';

echo "\nApplication loaded.\n";

// Test dashboard access
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/dashboard';

echo "Testing dashboard route...\n";

$router = new ApplicationRouter();
$result = $router->handleWebRoute('GET', '/dashboard');

echo "Dashboard test completed.\n";
?>
