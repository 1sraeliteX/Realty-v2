<?php
// Check session status and admin authentication
session_start();

echo "=== Session Debug ===" . PHP_EOL;
echo "Session ID: " . session_id() . PHP_EOL;
echo "Session status: " . session_status() . PHP_EOL;

echo "Session data:" . PHP_EOL;
print_r($_SESSION);

echo PHP_EOL . "=== Testing Admin Authentication ===" . PHP_EOL;

// Load the authentication system
require_once 'config/bootstrap.php';
require_once 'app/controllers/BaseController.php';

$controller = new \App\Controllers\BaseController();

// Test getCurrentAdmin method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('getCurrentAdmin');
$method->setAccessible(true);

$admin = $method->invoke($controller);

if ($admin) {
    echo "Admin found: " . $admin['name'] . PHP_EOL;
} else {
    echo "No admin found in session" . PHP_EOL;
}

echo PHP_EOL . "=== Testing Database Connection ===" . PHP_EOL;

// Test database
$db = \Config\DatabaseFactory::create();
if ($db) {
    echo "Database connected successfully" . PHP_EOL;
    
    // Test communications query
    $result = $db->query('SELECT COUNT(*) FROM communications WHERE deleted_at IS NULL')->fetchColumn();
    echo "Communications in database: $result" . PHP_EOL;
} else {
    echo "Database connection failed" . PHP_EOL;
}
?>
