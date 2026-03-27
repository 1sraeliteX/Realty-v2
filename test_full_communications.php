<?php
// Start session and set proper admin session data
session_start();

// Set session exactly like AdminAuthController does
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Test Admin';
$_SESSION['admin_email'] = 'admin@test.com';
$_SESSION['admin_role'] = 'admin';

echo "=== Setting up proper admin session ===" . PHP_EOL;
echo "Session data set:" . PHP_EOL;
print_r($_SESSION);

// Now test the communications controller
require_once 'config/bootstrap.php';
require_once 'app/controllers/BaseController.php';
require_once 'app/controllers/CommunicationController.php';

use App\Controllers\CommunicationController;

echo PHP_EOL . "=== Testing Communications Controller ===" . PHP_EOL;

try {
    $controller = new CommunicationController();
    
    // Capture output
    ob_start();
    $controller->index();
    $output = ob_get_clean();
    
    echo "Output length: " . strlen($output) . PHP_EOL;
    
    if (strlen($output) > 0) {
        echo "SUCCESS: Communications page working!" . PHP_EOL;
        echo "First 300 characters:" . PHP_EOL;
        echo substr($output, 0, 300) . PHP_EOL;
    } else {
        echo "ERROR: Still no output" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
}
?>
