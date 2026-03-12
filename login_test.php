<?php
session_start();

// Simulate admin session
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'admin@cornerstone.com';
$_SESSION['admin_name'] = 'Admin User';
$_SESSION['admin_role'] = 'admin';

// Redirect to settings page
header('Location: http://127.0.0.1:8080/admin/settings');
exit();
?>
