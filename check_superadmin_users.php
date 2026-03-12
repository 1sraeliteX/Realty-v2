<?php
// Check superadmin users in database
require_once __DIR__ . '/config/database.php';

$db = \Config\Database::getInstance();
$connection = $db->getConnection();

echo "<h1>SuperAdmin Users Check</h1>";

$stmt = $connection->prepare("SELECT id, name, email, password, role, created_at FROM admins WHERE role = 'super_admin' AND deleted_at IS NULL");
$stmt->execute();
$admins = $stmt->fetchAll();

if (count($admins) > 0) {
    echo "<h2>Found " . count($admins) . " SuperAdmin Users:</h2>";
    foreach ($admins as $admin) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
        echo "<strong>ID:</strong> " . $admin['id'] . "<br>";
        echo "<strong>Name:</strong> " . htmlspecialchars($admin['name']) . "<br>";
        echo "<strong>Email:</strong> " . htmlspecialchars($admin['email']) . "<br>";
        echo "<strong>Role:</strong> " . htmlspecialchars($admin['role']) . "<br>";
        echo "<strong>Created:</strong> " . $admin['created_at'] . "<br>";
        
        // Check password hash
        if (password_verify('admin123', $admin['password'])) {
            echo "<strong>Password:</strong> ✅ admin123 works<br>";
        } else {
            echo "<strong>Password:</strong> ❌ admin123 failed<br>";
        }
        echo "</div>";
    }
} else {
    echo "<h2>No SuperAdmin Users Found</h2>";
    echo "<p>Creating a test superadmin user...</p>";
    
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $connection->prepare("INSERT INTO admins (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute(['Super Admin', 'superadmin@cornerstone.com', $hashedPassword, 'super_admin']);
    
    echo "<p>✅ Created superadmin user: superadmin@cornerstone.com / admin123</p>";
}
?>
