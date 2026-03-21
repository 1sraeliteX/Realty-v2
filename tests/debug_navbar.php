<?php
session_start();
echo "<h1>Navbar Debug Information</h1>";

echo "<h2>Authentication Status:</h2>";
if (isset($_SESSION['admin_id'])) {
    echo "<p>✅ Logged in: {$_SESSION['admin_name']} ({$_SESSION['admin_email']})</p>";
    echo "<p>✅ Role: {$_SESSION['admin_role']}</p>";
    echo "<p>✅ Admin ID: {$_SESSION['admin_id']}</p>";
} else {
    echo "<p>❌ NOT LOGGED IN</p>";
    echo "<p>You need to login to access navbar functions</p>";
    echo "<p><a href='/admin/login'>Click here to login</a></p>";
}

echo "<h2>Session Data:</h2>";
echo "<pre>" . json_encode($_SESSION, JSON_PRETTY_PRINT) . "</pre>";

echo "<h2>Browser Info:</h2>";
echo "<p>User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "</p>";
echo "<p>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>";

echo "<h2>Test Links:</h2>";
echo "<p><a href='/admin/dashboard' target='_blank'>Dashboard</a></p>";
echo "<p><a href='/admin/properties' target='_blank'>Properties</a></p>";
echo "<p><a href='/admin/tenants' target='_blank'>Tenants</a></p>";
echo "<p><a href='/admin/login' target='_blank'>Login Page</a></p>";

echo "<h2>Login Credentials:</h2>";
echo "<p>Email: test@admin.com</p>";
echo "<p>Password: password123</p>";
?>
