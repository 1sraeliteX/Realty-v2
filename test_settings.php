<?php
// Test script to create admin session and test settings page
session_start();

// Simulate admin login
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'admin@cornerstone.com';
$_SESSION['admin_name'] = 'Admin User';
$_SESSION['admin_role'] = 'admin';

echo "Session created. Admin ID: " . $_SESSION['admin_id'] . "\n";
echo "Now testing settings page...\n";

// Test the settings controller
require_once __DIR__ . '/app/controllers/SettingsController.php';

$controller = new \App\Controllers\SettingsController();
$controller->index();
