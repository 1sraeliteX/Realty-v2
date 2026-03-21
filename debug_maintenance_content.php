<?php
// Debug maintenance page content issue
require_once __DIR__ . '/config/bootstrap.php';

// Include BaseController
require_once __DIR__ . '/app/controllers/BaseController.php';

// Mock admin session
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'test@admin.com';

// Load the controller
require_once __DIR__ . '/app/controllers/MaintenanceController.php';

// Create controller instance
$controller = new App\Controllers\MaintenanceController();

echo "=== Debugging Maintenance Page Content ===\n\n";

// Test 1: Check if view file exists and is readable
$viewFile = __DIR__ . '/views/admin/maintenance/index.php';
if (file_exists($viewFile) && is_readable($viewFile)) {
    echo "✅ Maintenance view file exists and is readable\n";
} else {
    echo "❌ Maintenance view file issue\n";
}

// Test 2: Test view content generation directly
echo "\n=== Testing View Content Directly ===\n";

// Set some test data
ViewManager::set('requests', [
    ['id' => 1, 'title' => 'Test Request', 'priority' => 'high', 'status' => 'pending']
]);
ViewManager::set('stats', [
    'total_requests' => 1,
    'urgent_count' => 1,
    'pending_count' => 1,
    'in_progress_count' => 0
]);
ViewManager::set('properties', [
    ['id' => 1, 'name' => 'Test Property']
]);
ViewManager::set('filters', []);

// Capture view output
ob_start();
include $viewFile;
$directViewContent = ob_get_clean();

echo "Direct view content length: " . strlen($directViewContent) . " characters\n";
echo "Direct view content preview:\n";
echo substr($directViewContent, 0, 500) . "...\n";

// Test 3: Test full controller method
echo "\n=== Testing Full Controller Method ===\n";
ob_start();
try {
    $controller->index();
    $fullOutput = ob_get_clean();
    
    echo "Full controller output length: " . strlen($fullOutput) . " characters\n";
    
    // Check for maintenance-specific content
    if (strpos($fullOutput, 'Total Requests') !== false) {
        echo "✅ Maintenance stats found in output\n";
    } else {
        echo "❌ Maintenance stats NOT found in output\n";
    }
    
    if (strpos($fullOutput, 'Welcome to the admin dashboard') !== false) {
        echo "❌ Generic dashboard content found (this is the problem!)\n";
    } else {
        echo "✅ Generic dashboard content not found\n";
    }
    
    // Look for the content section
    if (preg_match('/<main[^>]*>.*?<\/main>/s', $fullOutput, $matches)) {
        $mainContent = $matches[0];
        echo "Main content section length: " . strlen($mainContent) . " characters\n";
        
        if (strpos($mainContent, 'Total Requests') !== false) {
            echo "✅ Maintenance content is in main section\n";
        } else {
            echo "❌ Maintenance content NOT in main section\n";
            echo "Main content preview:\n";
            echo substr($mainContent, 0, 300) . "...\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error in controller: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
?>
