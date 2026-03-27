<?php
// Debug the exact error in AdminDashboardController reports method
require_once __DIR__ . '/config/bootstrap.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate admin session
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'test@admin.com';
$_SESSION['admin_role'] = 'admin';

echo "Debugging AdminDashboardController reports() method...\n";
echo "====================================================\n\n";

try {
    // Include the controller
    require_once __DIR__ . '/app/controllers/BaseController.php';
    require_once __DIR__ . '/app/controllers/AdminDashboardController.php';
    
    $controller = new \App\Controllers\AdminDashboardController();
    
    echo "✅ Controller created successfully\n";
    
    // Test each method individually
    echo "\n9. Testing full reports() method...\n";
    try {
        // Capture output and any errors
        ob_start();
        $controller->reports();
        $output = ob_get_clean();
        echo "✅ reports() method SUCCESS\n";
        echo "Output length: " . strlen($output) . " characters\n";
    } catch (Exception $e) {
        echo "❌ reports() method FAILED: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    } catch (Error $e) {
        echo "❌ reports() method ERROR: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nDebug completed.\n";
?>
