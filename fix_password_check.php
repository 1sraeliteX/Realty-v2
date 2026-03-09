<?php
require_once __DIR__ . '/config/config_simple.php';
require_once __DIR__ . '/config/database.php';

$db = Config\Database::getInstance();

echo "<h2>Password Check and Fix</h2>";

// Check admin user with password
$stmt = $db->getConnection()->prepare("SELECT id, name, email, password, role FROM admins WHERE email = ? AND deleted_at IS NULL");
$stmt->execute(['admin@cornerstone.com']);
$admin = $stmt->fetch();

if ($admin) {
    echo "<h3>Admin User Found</h3>";
    echo "<p>ID: {$admin['id']}</p>";
    echo "<p>Name: {$admin['name']}</p>";
    echo "<p>Email: {$admin['email']}</p>";
    echo "<p>Role: {$admin['role']}</p>";
    echo "<p>Password Hash: {$admin['password']}</p>";
    
    // Test password verification
    echo "<h3>Password Verification Test</h3>";
    $testPassword = 'admin123';
    if (password_verify($testPassword, $admin['password'])) {
        echo "<p style='color: green;'>✓ Password '$testPassword' matches hash</p>";
    } else {
        echo "<p style='color: red;'>✗ Password '$testPassword' does not match hash</p>";
        
        // Check if it's using old MD5 or plain text
        if (strlen($admin['password']) === 32 && ctype_xdigit($admin['password'])) {
            echo "<p style='color: orange;'>⚠ Password appears to be MD5 hashed</p>";
            if (md5($testPassword) === $admin['password']) {
                echo "<p style='color: orange;'>⚠ MD5 verification successful - updating to secure hash</p>";
                $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
                $updateStmt = $db->getConnection()->prepare("UPDATE admins SET password = ? WHERE id = ?");
                if ($updateStmt->execute([$newHash, $admin['id']])) {
                    echo "<p style='color: green;'>✓ Password updated to secure hash</p>";
                }
            }
        } else {
            echo "<p style='color: orange;'>⚠ Password might be plain text</p>";
            if ($admin['password'] === $testPassword) {
                echo "<p style='color: orange;'>⚠ Plain text password found - updating to secure hash</p>";
                $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
                $updateStmt = $db->getConnection()->prepare("UPDATE admins SET password = ? WHERE id = ?");
                if ($updateStmt->execute([$newHash, $admin['id']])) {
                    echo "<p style='color: green;'>✓ Password updated to secure hash</p>";
                }
            }
        }
    }
} else {
    echo "<p style='color: red;'>Admin user not found</p>";
}

// Check super admin too
echo "<h3>Super Admin Check</h3>";
$stmt = $db->getConnection()->prepare("SELECT id, name, email, password, role FROM admins WHERE email = ? AND deleted_at IS NULL");
$stmt->execute(['superadmin@cornerstone.com']);
$superAdmin = $stmt->fetch();

if ($superAdmin) {
    echo "<p>Super Admin found: {$superAdmin['name']} (ID: {$superAdmin['id']})</p>";
    if (password_verify('admin123', $superAdmin['password'])) {
        echo "<p style='color: green;'>✓ Super Admin password verified</p>";
    } else {
        echo "<p style='color: red;'>✗ Super Admin password issue detected</p>";
    }
} else {
    echo "<p style='color: red;'>Super Admin not found</p>";
}

echo "<h3>Test Again</h3>";
echo "<p><a href='http://localhost:8000/admin/login'>Try Login Again</a></p>";
?>
