<?php

// Test route matching
echo "=== Testing Route Matching ===\n";

// Load routes
$webRoutes = [
    'GET /' => 'AuthController@showLogin',
    'GET /login' => 'AuthController@showLogin',
    'POST /login' => 'AuthController@login',
    'GET /register' => 'AuthController@showRegister',
    'POST /register' => 'AuthController@register',
    'POST /logout' => 'AuthController@logout',
    'GET /dashboard' => 'DashboardController@index',
    'GET /superadmin' => 'SuperAdminController@index',
    'GET /superadmin/admins' => 'SuperAdminController@admins',
    'GET /superadmin/export' => 'SuperAdminController@exportData',
];

function matchesPattern($pattern, $routeKey, &$params) {
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

// Test dashboard route
$routeKey = 'GET /dashboard';
echo "Testing route: $routeKey\n";

// Check for exact route match
if (isset($webRoutes[$routeKey])) {
    echo "✅ Exact route match found: " . $webRoutes[$routeKey] . "\n";
} else {
    echo "❌ No exact match\n";
    
    // Check for parameterized routes
    foreach ($webRoutes as $pattern => $handler) {
        if (matchesPattern($pattern, $routeKey, $params)) {
            echo "✅ Pattern match found: $pattern -> $handler\n";
            echo "Params: " . json_encode($params) . "\n";
            break;
        }
    }
}

// Test superadmin route
$routeKey = 'GET /superadmin';
echo "\nTesting route: $routeKey\n";

if (isset($webRoutes[$routeKey])) {
    echo "✅ Exact route match found: " . $webRoutes[$routeKey] . "\n";
} else {
    echo "❌ No exact match\n";
}

echo "\n=== Test Complete ===\n";
?>
