<?php
// Debug content variable specifically
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

echo "=== Debugging Content Variable ===\n\n";

// Test setting content directly in ViewManager
$testContent = '<div class="test-content"><h1>Test Maintenance Content</h1><p>This should appear instead of dashboard</p></div>';
ViewManager::set('content', $testContent);
ViewManager::set('title', 'Test Maintenance');

// Check if content is properly stored
$storedContent = ViewManager::get('content');
echo "Content stored in ViewManager: " . (strlen($storedContent) . " characters") . "\n";
echo "Content preview: " . substr($storedContent, 0, 100) . "...\n\n";

// Now test the layout with this content
echo "=== Testing Layout Directly ===\n";
ob_start();
include __DIR__ . '/views/admin/dashboard_layout.php';
$layoutOutput = ob_get_clean();

echo "Layout output length: " . strlen($layoutOutput) . " characters\n";

if (strpos($layoutOutput, 'Test Maintenance Content') !== false) {
    echo "✅ Test content found in layout\n";
} else {
    echo "❌ Test content NOT found in layout\n";
}

if (strpos($layoutOutput, 'Welcome to the admin dashboard') !== false) {
    echo "❌ Generic dashboard content still showing\n";
} else {
    echo "✅ Generic dashboard content removed\n";
}

// Now test the full controller
echo "\n=== Testing Full Controller Again ===\n";
// Clear any previous content by setting it to empty
ViewManager::set('content', null);

ob_start();
try {
    $controller->index();
    $fullOutput = ob_get_clean();
    
    // Check what content was set
    $finalContent = ViewManager::get('content');
    echo "Final content in ViewManager: " . (strlen($finalContent) . " characters") . "\n";
    
    if (strpos($fullOutput, 'Total Requests') !== false) {
        echo "✅ Maintenance content in final output\n";
    }
    
    if (strpos($fullOutput, 'Welcome to the admin dashboard') !== false) {
        echo "❌ Generic dashboard content still in final output\n";
        
        // Find where the generic content is coming from
        $genericPos = strpos($fullOutput, 'Welcome to the admin dashboard');
        $contextStart = max(0, $genericPos - 200);
        $contextEnd = min(strlen($fullOutput), $genericPos + 200);
        $context = substr($fullOutput, $contextStart, $contextEnd - $contextStart);
        echo "Context around generic message:\n";
        echo $context . "\n";
    } else {
        echo "✅ Generic dashboard content removed from final output\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
?>
