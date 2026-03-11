<?php
require_once __DIR__ . '/config/config_simple.php';
require_once __DIR__ . '/config/database.php';

$db = Config\Database::getInstance();
$stmt = $db->getConnection()->query('SELECT id, name, email, role FROM admins WHERE deleted_at IS NULL');
$admins = $stmt->fetchAll();

echo "<h2>Current Admin Users</h2>";
echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
foreach ($admins as $admin) {
    echo "<tr><td>{$admin['id']}</td><td>{$admin['name']}</td><td>{$admin['email']}</td><td>{$admin['role']}</td></tr>";
}
echo "</table>";

// Check which admin ID should be used for the mock
echo "<h3>Mock Admin Configuration</h3>";
echo "<p>The BaseController mock admin uses ID: 1</p>";
echo "<p>Available admin IDs: ";
$ids = [];
foreach ($admins as $admin) {
    $ids[] = $admin['id'];
}
echo implode(', ', $ids) . "</p>";

if (!in_array(1, $ids)) {
    echo "<p style='color: red;'>⚠️ Admin ID 1 doesn't exist! This will cause authentication issues.</p>";
    echo "<p>Fix: Update BaseController mock admin to use an existing admin ID.</p>";
} else {
    echo "<p style='color: green;'>✅ Admin ID 1 exists.</p>";
}

// Test login with each admin
echo "<h3>Testing Login Credentials</h3>";
$testCredentials = [
    ['email' => 'admin@cornerstone.com', 'password' => 'admin123'],
    ['email' => 'superadmin@cornerstone.com', 'password' => 'admin123']
];

foreach ($testCredentials as $cred) {
    echo "<h4>Testing: {$cred['email']}</h4>";
    $stmt = $db->getConnection()->prepare("SELECT * FROM admins WHERE email = ? AND deleted_at IS NULL");
    $stmt->execute([$cred['email']]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p style='color: green;'>✓ User found (ID: {$user['id']})</p>";
        if (password_verify($cred['password'], $user['password'])) {
            echo "<p style='color: green;'>✓ Password matches</p>";
        } else {
            echo "<p style='color: red;'>✗ Password doesn't match</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ User not found</p>";
    }
}
?>
