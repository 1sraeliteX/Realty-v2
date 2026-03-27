<?php
// Simulate web access to communications page
session_start();

// Simulate admin authentication
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'test@admin.com';
$_SESSION['admin_logged_in'] = true;

echo "=== Testing Web Access to Communications ===" . PHP_EOL;
echo "Session data set" . PHP_EOL;

// Load the bootstrap first
require_once 'config/bootstrap.php';

// Test accessing the communications route
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/admin/communications';
$_GET = [];

try {
    // Load required controllers
    require_once 'app/controllers/BaseController.php';
    require_once 'app/controllers/CommunicationController.php';
    
    $controller = new \App\Controllers\CommunicationController();
    
    echo "Controller created successfully" . PHP_EOL;
    
    // Capture output
    ob_start();
    $controller->index();
    $output = ob_get_clean();
    
    echo "Output length: " . strlen($output) . PHP_EOL;
    
    if (strlen($output) > 0) {
        echo "SUCCESS: Page generated content" . PHP_EOL;
        // Show first 200 chars
        echo "First 200 chars: " . substr($output, 0, 200) . PHP_EOL;
    } else {
        echo "ERROR: No content generated" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}
?>
