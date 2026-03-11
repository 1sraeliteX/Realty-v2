<?php
// Simple test to check login status without interfering with headers
echo "<h2>Login Status Check</h2>";

// Test database connection
try {
    require_once __DIR__ . '/config/config_simple.php';
    require_once __DIR__ . '/config/database.php';
    
    $db = Config\Database::getInstance();
    echo "<p style='color: green;'>✓ Database connected</p>";
    
    // Check admin users
    $stmt = $db->getConnection()->prepare("SELECT id, name, email, role FROM admins WHERE email = ? AND deleted_at IS NULL");
    $stmt->execute(['admin@cornerstone.com']);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p style='color: green;'>✓ Admin user found: {$admin['name']} (ID: {$admin['id']})</p>";
        
        // Test password
        if (password_verify('admin123', $admin['password'])) {
            echo "<p style='color: green;'>✓ Password verification successful</p>";
        } else {
            echo "<p style='color: red;'>✗ Password verification failed</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Admin user not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test server status
echo "<h3>Server Status</h3>";
$serverUrl = 'http://localhost:8000';
$testUrl = $serverUrl . '/admin/login';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 5
    ]
]);

$response = @file_get_contents($testUrl, false, $context);
if ($response !== false) {
    echo "<p style='color: green;'>✓ Web server accessible at $serverUrl</p>";
    echo "<p style='color: green;'>✓ Login page accessible</p>";
} else {
    echo "<p style='color: red;'>✗ Web server not accessible at $serverUrl</p>";
    echo "<p>Make sure the PHP development server is running: <code>php -S localhost:8000 -t public</code></p>";
}

// Instructions
echo "<h3>Login Instructions</h3>";
echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #0066cc;'>";
echo "<p><strong>Steps to login:</strong></p>";
echo "<ol>";
echo "<li>Make sure server is running: <code>php -S localhost:8000 -t public</code></li>";
echo "<li>Open browser and go to: <a href='http://localhost:8000/admin/login' target='_blank'>http://localhost:8000/admin/login</a></li>";
echo "<li>Use credentials: admin@cornerstone.com / admin123</li>";
echo "<li>Or click the quick login buttons on the page</li>";
echo "</ol>";
echo "</div>";

// Quick test links
echo "<h3>Quick Test Links</h3>";
echo "<ul>";
echo "<li><a href='http://localhost:8000/admin/login' target='_blank'>Admin Login</a></li>";
echo "<li><a href='http://localhost:8000/test_login_simple.php' target='_blank'>Database Test</a></li>";
echo "<li><a href='http://localhost:8000/check_admins.php' target='_blank'>Check Admins</a></li>";
echo "</ul>";
?>
