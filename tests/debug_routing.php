<?php
// Debug routing to find the issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== ROUTING DEBUG ===\n";

// Test 1: Check if routes file loads
echo "\n1. Testing routes file...\n";
$routes = require __DIR__ . '/routes/web.php';
if (isset($routes['GET /admin/dashboard'])) {
    echo "   ✅ Route 'GET /admin/dashboard' found: " . $routes['GET /admin/dashboard'] . "\n";
} else {
    echo "   ❌ Route 'GET /admin/dashboard' NOT FOUND\n";
    echo "   Available routes:\n";
    foreach ($routes as $route => $handler) {
        if (strpos($route, 'admin/dashboard') !== false) {
            echo "      - $route => $handler\n";
        }
    }
}

// Test 2: Check AdminDashboardController
echo "\n2. Testing AdminDashboardController...\n";
if (file_exists(__DIR__ . '/app/controllers/AdminDashboardController.php')) {
    echo "   ✅ AdminDashboardController.php exists\n";
    require_once __DIR__ . '/app/controllers/BaseController.php';
    require_once __DIR__ . '/app/controllers/AdminDashboardController.php';
    if (class_exists('App\Controllers\AdminDashboardController')) {
        echo "   ✅ AdminDashboardController class exists\n";
        $controller = new \App\Controllers\AdminDashboardController();
        if (method_exists($controller, 'index')) {
            echo "   ✅ index method exists\n";
        } else {
            echo "   ❌ index method NOT FOUND\n";
        }
    } else {
        echo "   ❌ AdminDashboardController class NOT FOUND\n";
    }
} else {
    echo "   ❌ AdminDashboardController.php NOT FOUND\n";
}

// Test 3: Simulate router dispatch
echo "\n3. Testing router simulation...\n";
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/admin/dashboard';

class TestRouter {
    private $routes = [];
    
    public function __construct() {
        $this->routes = require __DIR__ . '/routes/web.php';
    }
    
    public function testDispatch() {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Remove query string from URI
        $uri = parse_url($requestUri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        
        echo "   Request URI: $requestUri\n";
        echo "   Parsed URI: $uri\n";
        echo "   Method: $requestMethod\n";
        
        // Handle root route
        if ($uri === '') {
            $uri = '/';
        }
        
        $routeKey = "{$requestMethod} {$uri}";
        echo "   Route Key: $routeKey\n";
        
        // Check for exact route match
        if (isset($this->routes[$routeKey])) {
            echo "   ✅ Route found: " . $this->routes[$routeKey] . "\n";
            return $this->routes[$routeKey];
        }
        
        echo "   ❌ Route NOT FOUND\n";
        echo "   Available admin routes:\n";
        foreach ($this->routes as $route => $handler) {
            if (strpos($route, '/admin/') === 0) {
                echo "      - $route => $handler\n";
            }
        }
        
        return null;
    }
}

$router = new TestRouter();
$handler = $router->testDispatch();

// Test 4: Check server configuration
echo "\n4. Testing server configuration...\n";
echo "   DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . "\n";
echo "   SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "   PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'NOT SET') . "\n";
echo "   REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";

// Test 5: Check if .htaccess exists
echo "\n5. Testing .htaccess...\n";
if (file_exists(__DIR__ . '/public/.htaccess')) {
    echo "   ✅ public/.htaccess exists\n";
    $htaccess = file_get_contents(__DIR__ . '/public/.htaccess');
    echo "   Content:\n" . substr($htaccess, 0, 200) . "...\n";
} else {
    echo "   ❌ public/.htaccess NOT FOUND\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
?>
