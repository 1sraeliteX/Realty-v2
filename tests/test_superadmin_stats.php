<?php
// Test SuperAdminController getPlatformStats method
require_once 'app/controllers/SuperAdminController.php';

use App\Controllers\SuperAdminController;

try {
    $controller = new SuperAdminController();
    
    // Use reflection to access private method for testing
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('getPlatformStats');
    $method->setAccessible(true);
    
    $stats = $method->invoke($controller);
    
    echo "<h2>Platform Stats Test</h2>";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
