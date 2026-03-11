<?php
// Test login functionality
session_start();

// Include required files
require_once __DIR__ . '/config/config_simple.php';
require_once __DIR__ . '/config/database.php';

// Test database connection
$db = \Config\Database::getInstance();

// Check if admins exist
$admins = $db->fetchAll("SELECT id, name, email, role FROM admins WHERE deleted_at IS NULL LIMIT 5");

echo "<h2>Available Admin Accounts</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";

foreach ($admins as $admin) {
    echo "<tr>";
    echo "<td>{$admin['id']}</td>";
    echo "<td>{$admin['name']}</td>";
    echo "<td>{$admin['email']}</td>";
    echo "<td>{$admin['role']}</td>";
    echo "</tr>";
}

echo "</table>";

// Test session storage
echo "<h2>Session Test</h2>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active') . "</p>";

if (isset($_SESSION['admin_id'])) {
    echo "<p>Logged in Admin ID: " . $_SESSION['admin_id'] . "</p>";
    echo "<p>Logged in Admin Role: " . $_SESSION['admin_role'] . "</p>";
} else {
    echo "<p>No admin logged in</p>";
}

// Test password verification
echo "<h2>Password Verification Test</h2>";
$testPassword = 'admin123';
$hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
echo "<p>Test Password: $testPassword</p>";
echo "<p>Hashed Password: $hashedPassword</p>";
echo "<p>Verification Result: " . (password_verify($testPassword, $hashedPassword) ? 'PASS' : 'FAIL') . "</p>";

// Check if superadmin exists
$superadmin = $db->fetch("SELECT * FROM admins WHERE email = 'superadmin@cornerstone.com' AND deleted_at IS NULL");
if ($superadmin) {
    echo "<h2>Superadmin Account Found</h2>";
    echo "<p>Email: {$superadmin['email']}</p>";
    echo "<p>Role: {$superadmin['role']}</p>";
    echo "<p>Password Verify: " . (password_verify('admin123', $superadmin['password']) ? 'PASS' : 'FAIL') . "</p>";
} else {
    echo "<h2>Superadmin Account Not Found</h2>";
}

// Check if regular admin exists
$admin = $db->fetch("SELECT * FROM admins WHERE email = 'admin@cornerstone.com' AND deleted_at IS NULL");
if ($admin) {
    echo "<h2>Regular Admin Account Found</h2>";
    echo "<p>Email: {$admin['email']}</p>";
    echo "<p>Role: {$admin['role']}</p>";
    echo "<p>Password Verify: " . (password_verify('admin123', $admin['password']) ? 'PASS' : 'FAIL') . "</p>";
} else {
    echo "<h2>Regular Admin Account Not Found</h2>";
}
?>
