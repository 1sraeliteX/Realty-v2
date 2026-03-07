<?php

// Start session
session_start();

// Simple autoloader for our classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Simple autoloader for Config classes
spl_autoload_register(function ($class) {
    $prefix = 'Config\\';
    $base_dir = __DIR__ . '/../config/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Simple router that uses the actual MVC controllers
class ApplicationRouter {
    private $webRoutes;
    private $apiRoutes;

    public function __construct() {
        // Load routes
        $this->webRoutes = require __DIR__ . '/../routes/web.php';
        $this->apiRoutes = require __DIR__ . '/../routes/api.php';
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
        
        // Handle API routes
        if (strpos($uri, '/api') === 0) {
            $this->handleApiRoute($requestMethod, $uri);
            return;
        }
        
        // Handle web routes
        $this->handleWebRoute($requestMethod, $uri);
    }

    private function handleWebRoute($method, $uri) {
        $routeKey = "{$method} {$uri}";
        
        // Check for exact route match
        if (isset($this->webRoutes[$routeKey])) {
            $this->executeRoute($this->webRoutes[$routeKey]);
            return;
        }
        
        // Check for parameterized routes
        foreach ($this->webRoutes as $pattern => $handler) {
            if ($this->matchesPattern($pattern, $routeKey, $params)) {
                $this->executeRoute($handler, $params);
                return;
            }
        }
        
        // 404 - Route not found
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>Route: $routeKey</p>";
    }

    private function handleApiRoute($method, $uri) {
        $routeKey = "{$method} {$uri}";
        
        if (isset($this->apiRoutes[$routeKey])) {
            $this->executeRoute($this->apiRoutes[$routeKey]);
            return;
        }
        
        // API route not found
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'API endpoint not found']);
    }

    private function matchesPattern($pattern, $routeKey, &$params) {
        // Convert route pattern to regex
        $regex = str_replace(['{id}', '{action}'], ['(\d+)', '(\w+)'], $pattern);
        $regex = '/^' . str_replace('/', '\/', $regex) . '$/';
        
        if (preg_match($regex, $routeKey, $matches)) {
            array_shift($matches);
            $params = $matches;
            return true;
        }
        
        return false;
    }

    private function executeRoute($handler, $params = []) {
        try {
            list($controllerName, $method) = explode('@', $handler);
            $controllerClass = "App\\Controllers\\{$controllerName}";
            
            if (!class_exists($controllerClass)) {
                throw new Exception("Controller not found: $controllerClass");
            }
            
            $controller = new $controllerClass();
            
            if (!method_exists($controller, $method)) {
                throw new Exception("Method not found: $method on $controllerClass");
            }
            
            call_user_func_array([$controller, $method], $params);
        } catch (Exception $e) {
            if (strpos($_SERVER['REQUEST_URI'], '/api') === 0) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            } else {
                http_response_code(500);
                echo "<h1>Application Error</h1>";
                echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            }
        }
    }
}

// Dispatch the router
$router = new ApplicationRouter();
$router->dispatch();
?>
