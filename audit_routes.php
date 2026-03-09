<?php

// Simple route audit script
$webRoutes = require 'routes/web.php';

echo "=== ROUTE AUDIT ===\n\n";

$missingViews = [];
$missingControllers = [];
$existingViews = [];

foreach ($webRoutes as $route => $handler) {
    if (strpos($route, 'GET') !== 0) continue; // Only check GET routes
    
    list($method, $uri) = explode(' ', $route, 2);
    list($controller, $action) = explode('@', $handler);
    
    // Check controller exists
    $controllerFile = "app/controllers/{$controller}.php";
    if (!file_exists($controllerFile)) {
        $missingControllers[] = $controllerFile;
        continue;
    }
    
    // Try to determine view file based on controller and action
    $viewFile = null;
    
    // Map common controller patterns to view files
    if ($controller === 'AdminDashboardController' && $action === 'index') {
        $viewFile = 'views/dashboard/index.php';
    } elseif ($controller === 'AdminAuthController') {
        if ($action === 'showLogin') {
            $viewFile = 'views/admin/login.php';
        } elseif ($action === 'showRegister') {
            $viewFile = 'views/admin/signup.php';
        }
    } elseif ($controller === 'PropertyController') {
        if ($action === 'index') {
            $viewFile = 'views/admin/properties/list.php';
        } elseif ($action === 'create') {
            $viewFile = 'views/admin/properties/add.php';
        } elseif ($action === 'show') {
            $viewFile = 'views/admin/properties/details.php';
        }
    } elseif ($controller === 'TenantController') {
        if ($action === 'index') {
            $viewFile = 'views/admin/tenants/list.php';
        } elseif ($action === 'create') {
            $viewFile = 'views/admin/tenants/create.php';
        }
    } elseif ($controller === 'SuperAdminController') {
        if ($action === 'index') {
            $viewFile = 'views/superadmin/dashboard.php';
        }
    }
    
    if ($viewFile) {
        if (file_exists($viewFile)) {
            $existingViews[] = $viewFile;
        } else {
            $missingViews[] = "$route -> $viewFile";
        }
    }
}

echo "MISSING CONTROLLERS:\n";
foreach ($missingControllers as $controller) {
    echo "- $controller\n";
}

echo "\nMISSING VIEWS:\n";
foreach ($missingViews as $missing) {
    echo "- $missing\n";
}

echo "\nEXISTING VIEWS:\n";
foreach ($existingViews as $view) {
    echo "- $view\n";
}

echo "\nSUMMARY:\n";
echo "Missing Controllers: " . count($missingControllers) . "\n";
echo "Missing Views: " . count($missingViews) . "\n";
echo "Existing Views: " . count($existingViews) . "\n";

?>
