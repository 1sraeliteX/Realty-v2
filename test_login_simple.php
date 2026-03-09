<?php
// Simple login test script
session_start();

// Load environment variables
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Database configuration
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'real_estate_db';
$user = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

echo "<h2>Login Debug Test</h2>";

// Test database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Check if admins table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p style='color: green;'>✓ Admins table exists</p>";
        
        // Check admin users
        $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM admins WHERE deleted_at IS NULL");
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Admin Users Found:</h3>";
        if (count($admins) > 0) {
            echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr>";
            foreach ($admins as $admin) {
                echo "<tr><td>{$admin['id']}</td><td>{$admin['name']}</td><td>{$admin['email']}</td><td>{$admin['role']}</td><td>{$admin['created_at']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠ No admin users found in database</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Admins table does not exist</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error checking admins table: " . $e->getMessage() . "</p>";
}

// Test login with demo credentials
echo "<h3>Testing Login Credentials:</h3>";

$testCredentials = [
    ['email' => 'admin@cornerstone.com', 'password' => 'admin123'],
    ['email' => 'superadmin@cornerstone.com', 'password' => 'admin123']
];

foreach ($testCredentials as $cred) {
    echo "<h4>Testing: {$cred['email']}</h4>";
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$cred['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p style='color: green;'>✓ User found in database</p>";
            echo "<p>Password verification: ";
            if (password_verify($cred['password'], $user['password'])) {
                echo "<span style='color: green;'>✓ Password matches</span>";
            } else {
                echo "<span style='color: red;'>✗ Password does not match</span>";
                echo "<br>Stored hash: " . $user['password'];
            }
            echo "</p>";
        } else {
            echo "<p style='color: red;'>✗ User not found in database</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    }
}

// Check session configuration
echo "<h3>Session Configuration:</h3>";
echo "<p>Session status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not active') . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";

if (isset($_SESSION)) {
    echo "<p>Session data: " . print_r($_SESSION, true) . "</p>";
}

echo "<h3>Quick Actions:</h3>";
echo "<a href='/'>Go to Login Page</a><br>";
echo "<a href='create_admin_now.php'>Create Admin User</a><br>";
echo "<a href='check_admins.php'>Check Admin Users</a>";
?>
