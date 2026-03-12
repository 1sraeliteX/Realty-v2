<?php
session_start();

// Simulate login
$_SESSION['admin_id'] = 7;
$_SESSION['admin_email'] = 'test@admin.com';
$_SESSION['admin_name'] = 'Test Admin';
$_SESSION['admin_role'] = 'admin';
$_SESSION['logged_in'] = true;
$_SESSION['login_time'] = time();

echo "<h1>Login Session Created</h1>";
echo "<p>Admin session created successfully!</p>";
echo "<p><a href='/admin/dashboard'>Go to Dashboard</a></p>";
echo "<p>Session data:</p><pre>";
var_dump($_SESSION);
echo "</pre>";
?>
