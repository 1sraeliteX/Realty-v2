<?php
// Simple test to access superadmin login through the web router
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/superadmin/login';

// Simulate the app.php routing
require_once __DIR__ . '/public/app.php';
?>
