<?php

// Start session
session_start();

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Simple router that uses the actual MVC controllers
class ApplicationRouter {
    private $routes = [];
    private $container = [];

    public function __construct() {
        // Load routes from web.php
        $this->routes = require __DIR__ . '/../routes/web.php';
        
        // Initialize database
        $this->container['db'] = Config\Database::getInstance();
    }

    public function dispatch() {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Remove query string from URI
        $uri = parse_url($requestUri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        
        // Handle root route
        if ($uri === '') {
            $uri = '/';
        }
        
        $routeKey = "{$requestMethod} {$uri}";
        
        // Check for exact route match
        if (isset($this->routes[$routeKey])) {
            $this->handleRoute($this->routes[$routeKey]);
            return;
        }
        
        // Check for parameterized routes (simple implementation)
        foreach ($this->routes as $pattern => $handler) {
            if ($this->matchesPattern($pattern, $routeKey, $params)) {
                $this->handleRoute($handler, $params);
                return;
            }
        }
        
        // 404 - Route not found
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>Route: $routeKey</p>";
    }

    private function matchesPattern($pattern, $routeKey, &$params) {
        // Convert route pattern to regex
        $regex = str_replace(['{id}', '{action}'], ['(\d+)', '(\w+)'], $pattern);
        $regex = '/^' . str_replace('/', '\/', $regex) . '$/';
        
        if (preg_match($regex, $routeKey, $matches)) {
            // Remove the full match and keep only parameters
            array_shift($matches);
            $params = $matches;
            return true;
        }
        
        return false;
    }

    private function handleRoute($handler, $params = []) {
        // Parse handler (e.g., "PropertyController@store")
        list($controllerName, $method) = explode('@', $handler);
        
        // Build controller class name
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            echo "<h1>Controller Not Found</h1>";
            echo "<p>Controller: $controllerClass</p>";
            return;
        }
        
        // Instantiate controller
        $controller = new $controllerClass();
        
        // Call method with parameters
        if (method_exists($controller, $method)) {
            call_user_func_array([$controller, $method], $params);
        } else {
            echo "<h1>Method Not Found</h1>";
            echo "<p>Method: $method on $controllerClass</p>";
        }
    }
}

// Handle API routes separately
if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
    handleApiRoute();
    return;
}

// Dispatch the router
try {
    $router = new ApplicationRouter();
    $router->dispatch();
} catch (Exception $e) {
    http_response_code(500);
    echo "<h1>Application Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

function handleApiRoute() {
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    // Remove query string from URI
    $uri = parse_url($requestUri, PHP_URL_PATH);
    $uri = rtrim($uri, '/');
    
    // Load API routes
    $apiRoutes = require __DIR__ . '/../routes/api.php';
    
    $routeKey = "{$requestMethod} {$uri}";
    
    if (isset($apiRoutes[$routeKey])) {
        $handler = $apiRoutes[$routeKey];
        list($controllerName, $method) = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            if (method_exists($controller, $method)) {
                $controller->$method();
                return;
            }
        }
    }
    
    // API route not found
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'API endpoint not found']);
}
?>
