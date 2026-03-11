<?php
// Create admin user for testing
session_start();

// Simple admin user creation
$_SESSION['admin'] = [
    'id' => 1,
    'name' => 'Test Admin',
    'email' => 'admin@test.com',
    'role' => 'admin'
];

echo "Admin user created in session. You can now access: <a href='/admin/tenants'>Tenants Page</a>";
echo "<br><br>Current session data:<pre>";
print_r($_SESSION);
echo "</pre>";

// Test link
echo "<p><a href='/admin/tenants' target='_blank'>📋 Open Tenants Page</a></p>";
?>
