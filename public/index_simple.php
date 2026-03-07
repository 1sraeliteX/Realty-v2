<?php

// Start session
session_start();

// Simple router without dependencies
class SimpleRouter {
    private $routes = [];

    public function __construct() {
        // Define routes
        $this->routes = [
            'GET /' => 'login',
            'GET /login' => 'login',
            'POST /login' => 'login',
            'GET /register' => 'register',
            'POST /register' => 'register',
            'GET /dashboard' => 'dashboard',
            'GET /superadmin' => 'superadmin',
            'GET /logout' => 'logout'
        ];
    }

    public function dispatch() {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Remove query string from URI
        $uri = parse_url($requestUri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        
        // Handle root route
        if ($uri === '' && !isset($this->routes["{$requestMethod} /"])) {
            $uri = '/';
        }
        
        $routeKey = "{$requestMethod} {$uri}";
        
        if (isset($this->routes[$routeKey])) {
            $this->renderPage($this->routes[$routeKey]);
        } else {
            // Default to login for unknown routes
            $this->renderPage('login');
        }
    }

    private function renderPage($page) {
        switch($page) {
            case 'login':
                $this->renderView('auth/login_simple');
                break;
            case 'register':
                $this->renderView('auth/register');
                break;
            case 'dashboard':
                $this->renderView('dashboard/index');
                break;
            case 'superadmin':
                $this->renderView('superadmin/dashboard');
                break;
            case 'logout':
                session_destroy();
                header('Location: /login');
                exit;
            default:
                $this->renderView('auth/login_simple');
        }
    }

    private function renderView($view) {
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h1>View Not Found</h1>";
            echo "<p>View file: $viewPath</p>";
        }
    }
}

// Simple view renderer
function renderView($viewPath, $data = []) {
    $fullPath = __DIR__ . '/../views/' . $viewPath . '.php';
    
    if (file_exists($fullPath)) {
        // Extract data to make variables available in view
        extract($data);
        include $fullPath;
    } else {
        echo "<h1>View Not Found</h1>";
        echo "<p>Expected: $fullPath</p>";
    }
}

// Simple session helper
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['admin_id'] ?? 1,
            'name' => $_SESSION['admin_name'] ?? 'Test User',
            'email' => $_SESSION['admin_email'] ?? 'test@example.com',
            'role' => $_SESSION['admin_role'] ?? 'admin'
        ];
    }
    return null;
}

// Dispatch the router
$router = new SimpleRouter();
$router->dispatch();
?>
