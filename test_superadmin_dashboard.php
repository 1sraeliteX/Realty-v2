<?php
// Test superadmin dashboard access
session_start();

// First, simulate login
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Super Admin';
$_SESSION['admin_email'] = 'superadmin@cornerstone.com';
$_SESSION['admin_role'] = 'super_admin';

// Then test dashboard access
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/superadmin/dashboard';

// Simulate the app.php routing
require_once __DIR__ . '/public/app.php';
?>
