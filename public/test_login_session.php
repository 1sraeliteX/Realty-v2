<?php
session_start();

// Simulate successful admin login
$_SESSION['admin_id'] = 7;
$_SESSION['admin_email'] = 'test@admin.com';
$_SESSION['admin_name'] = 'Test Admin';
$_SESSION['admin_role'] = 'admin';
$_SESSION['logged_in'] = true;
$_SESSION['login_time'] = time();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Login Session Created</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .info { color: blue; }
        a { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; margin: 10px; }
    </style>
</head>
<body>
    <h1 class='success'>✅ Login Session Created Successfully!</h1>
    <p class='info'>Admin session has been established with the following credentials:</p>
    <ul>
        <li><strong>ID:</strong> 7</li>
        <li><strong>Name:</strong> Test Admin</li>
        <li><strong>Email:</strong> test@admin.com</li>
        <li><strong>Role:</strong> admin</li>
    </ul>
    
    <div style='margin-top: 30px;'>
        <a href='/admin/dashboard'>🎯 Go to Dashboard</a>
        <a href='/admin/login'>🔐 Go to Login</a>
    </div>
    
    <h3>Session Data:</h3>
    <pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
var_dump($_SESSION);
echo "</pre>
</body>
</html>";
?>
