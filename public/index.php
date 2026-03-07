<?php

// Load autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Start session
session_start();

// Simple router
class Router {
    private $routes = [];
    private $apiRoutes = [];

    public function __construct() {
        $this->routes = include __DIR__ . '/../routes/web.php';
        $this->apiRoutes = include __DIR__ . '/../routes/api.php';
    }

    public function dispatch() {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Remove query string from URI
        $uri = parse_url($requestUri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        
        // Check if it's an API route
        if (strpos($uri, '/api/') === 0) {
            return $this->handleApiRoute($uri, $requestMethod);
        }
        
        return $this->handleWebRoute($uri, $requestMethod);
    }

    private function handleWebRoute($uri, $method) {
        $routeKey = "{$method} {$uri}";
        
        // Handle root route
        if ($uri === '' && !isset($this->routes[$routeKey])) {
            $routeKey = 'GET /';
        }
        
        if (isset($this->routes[$routeKey])) {
            return $this->callController($this->routes[$routeKey]);
        }
        
        // Handle dynamic routes with parameters
        foreach ($this->routes as $route => $controller) {
            $routeMethod = explode(' ', $route)[0];
            $routePath = explode(' ', $route)[1];
            
            if ($routeMethod !== $method) continue;
            
            // Convert route pattern to regex
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                return $this->callController($controller, $matches);
            }
        }
        
        // 404 - Not found
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        return;
    }

    private function handleApiRoute($uri, $method) {
        header('Content-Type: application/json');
        
        $routeKey = "{$method} {$uri}";
        
        if (isset($this->apiRoutes[$routeKey])) {
            try {
                $result = $this->callController($this->apiRoutes[$routeKey]);
                echo json_encode($result);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
        } else {
            // Handle dynamic API routes
            foreach ($this->apiRoutes as $route => $controller) {
                $routeMethod = explode(' ', $route)[0];
                $routePath = explode(' ', $route)[1];
                
                if ($routeMethod !== $method) continue;
                
                $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
                $pattern = '#^' . $pattern . '$#';
                
                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches);
                    try {
                        $result = $this->callController($controller, $matches);
                        echo json_encode($result);
                    } catch (Exception $e) {
                        http_response_code(500);
                        echo json_encode(['error' => $e->getMessage()]);
                    }
                    return;
                }
            }
            
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
        }
    }

    private function callController($controllerString, $params = []) {
        list($controllerName, $method) = explode('@', $controllerString);
        
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller {$controllerClass} not found");
        }
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $method)) {
            throw new Exception("Method {$method} not found in {$controllerClass}");
        }
        
        return call_user_func_array([$controller, $method], $params);
    }
}

// Handle CORS for API requests
if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }
}

// Dispatch the request
$router = new Router();
$router->dispatch();
