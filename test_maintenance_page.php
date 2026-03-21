<?php
// Test maintenance page functionality
require_once __DIR__ . '/config/bootstrap.php';

// Include BaseController
require_once __DIR__ . '/app/controllers/BaseController.php';

// Mock admin session
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'test@admin.com';

// Load the controller
require_once __DIR__ . '/app/controllers/MaintenanceController.php';

// Create controller instance and test index method
$controller = new App\Controllers\MaintenanceController();

// Capture output
ob_start();
try {
    $controller->index();
    $output = ob_get_clean();
    
    echo "✅ Maintenance page loaded successfully!\n";
    echo "Output length: " . strlen($output) . " characters\n";
    
    // Check for key elements
    if (strpos($output, '<!DOCTYPE html') !== false) {
        echo "✅ Full HTML document structure found\n";
    } else {
        echo "❌ Full HTML document structure not found\n";
    }
    
    if (strpos($output, 'Maintenance Requests') !== false) {
        echo "✅ Page title found\n";
    } else {
        echo "❌ Page title not found\n";
    }
    
    if (strpos($output, 'Total Requests') !== false) {
        echo "✅ Stats cards found\n";
    } else {
        echo "❌ Stats cards not found\n";
    }
    
    if (strpos($output, 'sidebar') !== false || strpos($output, 'nav') !== false) {
        echo "✅ Navigation elements found\n";
    } else {
        echo "❌ Navigation elements not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
