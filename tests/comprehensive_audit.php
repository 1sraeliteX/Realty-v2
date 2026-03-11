<?php

// Comprehensive route and view audit
$webRoutes = require 'routes/web.php';

echo "=== COMPREHENSIVE ROUTE & VIEW AUDIT ===\n\n";

$missingViews = [];
$missingControllers = [];
$allRoutes = [];
$expectedViews = [];

foreach ($webRoutes as $route => $handler) {
    if (strpos($route, 'GET') !== 0) continue; // Only check GET routes
    
    list($method, $uri) = explode(' ', $route, 2);
    list($controller, $action) = explode('@', $handler);
    
    $allRoutes[] = $route;
    
    // Check controller exists
    $controllerFile = "app/controllers/{$controller}.php";
    if (!file_exists($controllerFile)) {
        $missingControllers[] = $controllerFile;
        continue;
    }
    
    // Determine expected view file based on controller and action
    $viewFile = null;
    
    switch ($controller) {
        case 'AdminDashboardController':
            if ($action === 'index') $viewFile = 'views/dashboard/index.php';
            break;
            
        case 'AdminAuthController':
            if ($action === 'showLogin') $viewFile = 'views/admin/login.php';
            elseif ($action === 'showRegister') $viewFile = 'views/admin/signup.php';
            break;
            
        case 'PropertyController':
            if ($action === 'index') $viewFile = 'views/admin/properties/list.php';
            elseif ($action === 'create') $viewFile = 'views/admin/properties/add.php';
            elseif ($action === 'show') $viewFile = 'views/admin/properties/details.php';
            elseif ($action === 'edit') $viewFile = 'views/admin/properties/edit.php';
            break;
            
        case 'TenantController':
            if ($action === 'index') $viewFile = 'views/admin/tenants/list.php';
            elseif ($action === 'create') $viewFile = 'views/admin/tenants/create.php';
            elseif ($action === 'show') $viewFile = 'views/admin/tenants/details.php';
            elseif ($action === 'edit') $viewFile = 'views/admin/tenants/edit.php';
            break;
            
        case 'UnitController':
            if ($action === 'index') $viewFile = 'views/admin/units/list.php';
            elseif ($action === 'create') $viewFile = 'views/admin/units/create.php';
            elseif ($action === 'edit') $viewFile = 'views/admin/units/edit.php';
            break;
            
        case 'PaymentController':
            if ($action === 'index') $viewFile = 'views/admin/payments/list.php';
            elseif ($action === 'create') $viewFile = 'views/admin/payments/create.php';
            elseif ($action === 'edit') $viewFile = 'views/admin/payments/edit.php';
            break;
            
        case 'InvoiceController':
            if ($action === 'index') $viewFile = 'views/admin/invoices/list.php';
            elseif ($action === 'create') $viewFile = 'views/admin/invoices/create.php';
            elseif ($action === 'show') $viewFile = 'views/admin/invoices/details.php';
            elseif ($action === 'edit') $viewFile = 'views/admin/invoices/edit.php';
            break;
            
        case 'FinanceController':
            if ($action === 'index') $viewFile = 'views/admin/finances/list.php';
            elseif ($action === 'create') $viewFile = 'views/admin/finances/create.php';
            elseif ($action === 'edit') $viewFile = 'views/admin/finances/edit.php';
            break;
            
        case 'MaintenanceController':
            if ($action === 'index') $viewFile = 'views/admin/maintenance/list.php';
            elseif ($action === 'create') $viewFile = 'views/admin/maintenance/create.php';
            elseif ($action === 'edit') $viewFile = 'views/admin/maintenance/edit.php';
            break;
            
        case 'CommunicationController':
            if ($action === 'index') $viewFile = 'views/admin/communications/list.php';
            elseif ($action === 'create') $viewFile = 'views/admin/communications/create.php';
            elseif ($action === 'edit') $viewFile = 'views/admin/communications/edit.php';
            break;
            
        case 'DocumentController':
            if ($action === 'index') $viewFile = 'views/admin/documents/list.php';
            elseif ($action === 'create') $viewFile = 'views/admin/documents/create.php';
            elseif ($action === 'edit') $viewFile = 'views/admin/documents/edit.php';
            break;
            
        case 'ReportController':
            if ($action === 'index') $viewFile = 'views/admin/reports/list.php';
            elseif ($action === 'create') $viewFile = 'views/admin/reports/create.php';
            elseif ($action === 'edit') $viewFile = 'views/admin/reports/edit.php';
            break;
            
        case 'SettingsController':
            if ($action === 'index') $viewFile = 'views/admin/settings/index.php';
            break;
            
        case 'ProfileController':
            if ($action === 'index') $viewFile = 'views/admin/profile/index.php';
            break;
            
        case 'SuperAdminController':
            if ($action === 'index') $viewFile = 'views/superadmin/dashboard.php';
            elseif ($action === 'admins') $viewFile = 'views/superadmin/admins.php';
            elseif ($action === 'exportData') $viewFile = 'views/superadmin/export.php';
            break;
    }
    
    if ($viewFile) {
        $expectedViews[] = $viewFile;
        if (!file_exists($viewFile)) {
            $missingViews[] = "$route -> $viewFile";
        }
    }
}

// Check for root route
$hasRootRoute = in_array('GET /', $allRoutes);
if (!$hasRootRoute) {
    echo "ROOT ROUTE ISSUE:\n";
    echo "- No root route (GET /) defined\n";
    echo "- Should redirect to landing page or login\n\n";
}

echo "MISSING CONTROLLERS:\n";
if (empty($missingControllers)) {
    echo "- None found\n";
} else {
    foreach ($missingControllers as $controller) {
        echo "- $controller\n";
    }
}

echo "\nMISSING VIEWS:\n";
if (empty($missingViews)) {
    echo "- None found\n";
} else {
    foreach ($missingViews as $missing) {
        echo "- $missing\n";
    }
}

echo "\nCHECKING FOR EMPTY DIRECTORIES:\n";
$directories = [
    'views/admin/payments',
    'views/admin/invoices',
    'views/admin/finances',
    'views/admin/maintenance',
    'views/admin/communications',
    'views/admin/documents',
    'views/admin/reports',
    'views/admin/settings',
    'views/admin/profile',
    'views/admin/units'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        $files = array_diff($files, ['.', '..']);
        if (empty($files)) {
            echo "- $dir (empty directory)\n";
        }
    } else {
        echo "- $dir (directory missing)\n";
    }
}

echo "\nSUMMARY:\n";
echo "Total GET routes: " . count($allRoutes) . "\n";
echo "Missing Controllers: " . count($missingControllers) . "\n";
echo "Missing Views: " . count($missingViews) . "\n";
echo "Has Root Route: " . ($hasRootRoute ? 'Yes' : 'No') . "\n";

?>
