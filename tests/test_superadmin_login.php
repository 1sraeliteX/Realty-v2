<?php
// Test superadmin login POST request
session_start();

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/superadmin/login';
$_POST = [
    'email' => 'superadmin@cornerstone.com',
    'password' => 'admin123'
];

// Simulate the app.php routing
require_once __DIR__ . '/public/app.php';
?>
