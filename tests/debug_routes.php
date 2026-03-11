<?php
// Test the exact routing and URL access
require_once 'config/config_simple.php';
require_once 'config/database.php';
require_once 'app/controllers/BaseController.php';

use App\Controllers\BaseController;

echo "=== Route and URL Debug ===\n\n";

// Check the routes configuration
$routes = require 'routes/web.php';

echo "Available property routes:\n";
foreach ($routes as $route => $handler) {
    if (strpos($route, 'properties') !== false || strpos($handler, 'Property') !== false) {
        echo "- $route => $handler\n";
    }
}

echo "\nTesting URL access simulation:\n";

// Simulate different URLs
$testUrls = [
    '/properties',
    '/admin/properties', 
    '/properties/',
    '/admin/properties/'
];

foreach ($testUrls as $url) {
    echo "\nTesting URL: $url\n";
    
    // Parse URL like the router does
    $uri = rtrim($url, '/');
    if ($uri === '') {
        $uri = '/';
    }
    
    $routeKey = "GET $uri";
    echo "Route key: $routeKey\n";
    
    if (isset($routes[$routeKey])) {
        echo "✓ Route found: " . $routes[$routeKey] . "\n";
    } else {
        echo "✗ Route not found\n";
        
        // Check for pattern matches
        foreach ($routes as $pattern => $handler) {
            if (strpos($pattern, 'properties') !== false) {
                echo "  Similar route: $pattern => $handler\n";
            }
        }
    }
}

echo "\n=== Recommendation ===\n";
echo "Based on the route configuration, try these URLs:\n";
echo "1. http://127.0.0.1:49677/admin/properties (recommended)\n";
echo "2. http://127.0.0.1:49677/properties\n\n";

echo "If properties still don't show, the issue might be:\n";
echo "- Browser cache (try Ctrl+F5)\n";
echo "- Session/cookie issues\n";
echo "- Different port or virtual host configuration\n";
?>
