<?php
// Simulate the full application flow like app.php does
session_start();

// Load required config files
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/controllers/BaseController.php';

// Initialize database
$db = Config\Database::getInstance();

// Load SuperAdminController
require_once __DIR__ . '/app/controllers/SuperAdminController.php';

try {
    $controller = new App\Controllers\SuperAdminController();
    echo 'SuperAdminController instantiated successfully' . PHP_EOL;
    
    // Test a simple query
    $result = $controller->getRecentAdmins(1);
    echo 'getRecentAdmins method works' . PHP_EOL;
    echo 'Result: ' . print_r($result, true) . PHP_EOL;
    
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo 'File: ' . $e->getFile() . PHP_EOL;
    echo 'Line: ' . $e->getLine() . PHP_EOL;
}
?>
