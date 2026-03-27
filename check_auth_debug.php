<?php
// Check authentication and database connection
require_once __DIR__ . '/config/bootstrap.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "Authentication and Database Debug\n";
echo "=================================\n\n";

// Check session
echo "Session status: " . session_status() . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Session data:\n";
print_r($_SESSION);

// Check if admin is logged in
if (isset($_SESSION['admin_id'])) {
    echo "\n✅ Admin ID found in session: " . $_SESSION['admin_id'] . "\n";
} else {
    echo "\n❌ No admin_id in session\n";
    echo "Available session keys: " . implode(', ', array_keys($_SESSION)) . "\n";
}

// Test database connection
echo "\nTesting database connection...\n";
try {
    $pdo = \Config\Database::getInstance()->getConnection();
    echo "✅ Database connection successful\n";
    
    // Test a simple query
    $stmt = $pdo->prepare("SELECT DATABASE() as db_name");
    $stmt->execute();
    $dbName = $stmt->fetchColumn();
    echo "Database name: $dbName\n";
    
    // Test properties table
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM properties WHERE admin_id = ?");
    $adminId = $_SESSION['admin_id'] ?? 1;
    $stmt->execute([$adminId]);
    $count = $stmt->fetchColumn();
    echo "Properties for admin $adminId: $count\n";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Test getCurrentAdmin method
echo "\nTesting getCurrentAdmin()...\n";
try {
    require_once __DIR__ . '/app/controllers/BaseController.php';
    $controller = new \App\Controllers\BaseController();
    $admin = $controller->getCurrentAdmin();
    
    if ($admin) {
        echo "✅ Admin found: " . $admin['name'] . " (ID: " . $admin['id'] . ")\n";
        echo "Role: " . ($admin['role'] ?? 'unknown') . "\n";
    } else {
        echo "❌ No admin found\n";
    }
} catch (Exception $e) {
    echo "❌ getCurrentAdmin error: " . $e->getMessage() . "\n";
}

echo "\nDebug completed.\n";
?>
