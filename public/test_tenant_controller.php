<?php
// Test the tenant views with the new BaseController logic
echo "<h1>Tenant Views Test</h1>";

// Simulate what the TenantController does
require_once 'app/controllers/BaseController.php';
require_once 'app/controllers/TenantController.php';

// Mock session
session_start();

// Create controller instance
$tenantController = new TenantController();

echo "<h2>Testing Tenant Controller Methods</h2>";

// Test each method
$methods = ['index', 'create', 'show', 'edit'];
foreach ($methods as $method) {
    echo "<h3>Testing $method method:</h3>";
    try {
        if ($method === 'show' || $method === 'edit') {
            // These methods need an ID parameter
            $tenantController->$method(1);
        } else {
            $tenantController->$method();
        }
        echo "<p style='color: green;'>✅ $method method executed successfully</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error in $method: " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p style='color: red;'>❌ Fatal error in $method: " . $e->getMessage() . "</p>";
    }
    echo "<hr>";
}

echo "<h2>Test Complete</h2>";
echo "<p>If you see rendered content above with proper layout, the tenant views are working correctly!</p>";
?>
