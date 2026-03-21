<?php
require_once __DIR__ . '/config/database.php';

echo "<h1>Admin User Check</h1>";

try {
    $db = Config\Database::getInstance();
    $conn = $db->getConnection();
    
    // Check if test admin exists
    $stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM admins WHERE email = ? AND deleted_at IS NULL");
    $stmt->execute(['test@admin.com']);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p>✅ Test admin found:</p>";
        echo "<ul>";
        echo "<li>ID: {$admin['id']}</li>";
        echo "<li>Name: {$admin['name']}</li>";
        echo "<li>Email: {$admin['email']}</li>";
        echo "<li>Role: {$admin['role']}</li>";
        echo "<li>Created: {$admin['created_at']}</li>";
        echo "</ul>";
    } else {
        echo "<p>❌ Test admin not found. Creating one...</p>";
        
        // Create test admin
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO admins (name, email, password, role, created_at, updated_at) 
            VALUES (?, ?, ?, 'admin', NOW(), NOW())
        ");
        
        if ($stmt->execute(['Test Admin', 'test@admin.com', $hashedPassword])) {
            echo "<p>✅ Test admin created successfully!</p>";
        } else {
            echo "<p>❌ Failed to create test admin</p>";
        }
    }
    
    // Show all admins
    echo "<h2>All Admin Users:</h2>";
    $stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM admins WHERE deleted_at IS NULL ORDER BY created_at DESC");
    $stmt->execute();
    $admins = $stmt->fetchAll();
    
    if ($admins) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>{$admin['id']}</td>";
            echo "<td>{$admin['name']}</td>";
            echo "<td>{$admin['email']}</td>";
            echo "<td>{$admin['role']}</td>";
            echo "<td>{$admin['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No admin users found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: {$e->getMessage()}</p>";
}

echo "<p><a href='/admin/login'>Go to Login</a></p>";
?>
