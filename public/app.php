<?php

// Start session
session_start();

// Load Composer autoloader if available, otherwise continue
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // Load environment variables
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
    }
}

// Simple router that uses the actual MVC controllers
class ApplicationRouter {
    private $routes = [];
    private $container = [];

    public function __construct() {
        // Handle static files first
        $this->handleStaticFiles();
        
        // Load required config files
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../config/database.php';
        
        // Load base controller first
        require_once __DIR__ . '/../app/controllers/BaseController.php';
        
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
    
    private function handleStaticFiles() {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestUri = parse_url($requestUri, PHP_URL_PATH);
        
        // Check if it's a static file request
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/', $requestUri)) {
            $filePath = __DIR__ . $requestUri;
            
            if (file_exists($filePath)) {
                // Set appropriate content type
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $contentTypes = [
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml',
                    'ico' => 'image/x-icon',
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2',
                    'ttf' => 'font/ttf',
                    'eot' => 'application/vnd.ms-fontobject'
                ];
                
                if (isset($contentTypes[$extension])) {
                    header('Content-Type: ' . $contentTypes[$extension]);
                }
                
                // Enable caching for static files
                header('Cache-Control: public, max-age=31536000');
                header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
                
                readfile($filePath);
                exit;
            }
        }
    }

    private function handleRoute($handler, $params = []) {
        // Parse handler (e.g., "PropertyController@store")
        list($controllerName, $method) = explode('@', $handler);
        
        // Build controller class name
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        // Load controller file if it exists
        $controllerFile = __DIR__ . "/../app/controllers/{$controllerName}.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        }
        
        if (!class_exists($controllerClass)) {
            echo "<h1>Controller Not Found</h1>";
            echo "<p>Controller: $controllerClass</p>";
            echo "<p>File: $controllerFile</p>";
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
