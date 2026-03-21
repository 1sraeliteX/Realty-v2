<?php
// Simple test to check what's happening with app.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Direct app.php Test</h1>";

// Test if we can access the router directly
try {
    // Simulate what happens when someone visits /
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['HTTP_HOST'] = '127.0.0.1:8080';
    
    echo "<h2>Testing Route Dispatch</h2>";
    
    // Load the router
    require_once __DIR__ . '/app.php';
    
} catch (Exception $e) {
    echo "<h2>Error Caught</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
} catch (Error $e) {
    echo "<h2>Fatal Error Caught</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>Test Complete</h2>";
?>
