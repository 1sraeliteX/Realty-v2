<?php
// Test web access to reports page
require_once __DIR__ . '/config/bootstrap.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulate admin session for web
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'test@admin.com';
$_SESSION['admin_role'] = 'admin';
$_SESSION['admin_name'] = 'Test Admin';

echo "Testing web access to dashboard reports...\n";
echo "==========================================\n\n";

try {
    // Include the controller
    require_once __DIR__ . '/app/controllers/BaseController.php';
    require_once __DIR__ . '/app/controllers/AdminDashboardController.php';
    
    $controller = new \App\Controllers\AdminDashboardController();
    
    echo "✅ Controller created successfully\n";
    
    // Test the reports method like web access
    echo "\nCalling reports() method...\n";
    
    // Capture any output/errors
    ob_start();
    $controller->reports();
    $output = ob_get_clean();
    
    echo "✅ reports() method completed\n";
    echo "Output length: " . strlen($output) . " characters\n";
    
    // Check for any error messages in output
    if (strpos($output, 'Query failed') !== false) {
        echo "❌ Found 'Query failed' in output!\n";
        // Extract the error part
        $errorPos = strpos($output, 'Query failed');
        $errorSnippet = substr($output, $errorPos, 200);
        echo "Error snippet: " . $errorSnippet . "\n";
    } else {
        echo "✅ No 'Query failed' found in output\n";
    }
    
    if (strpos($output, 'admin_id') !== false) {
        echo "✅ Found 'admin_id' references in output\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nWeb test completed.\n";
?>
