<?php
// Simple test to verify routes are working
echo "Testing routes...\n";

// Test if routes file exists and is readable
if (file_exists('routes/web.php')) {
    echo "✓ routes/web.php exists\n";
    $routes = include 'routes/web.php';
    echo "✓ " . count($routes) . " routes loaded\n";
    
    // Check for admin dashboard route
    if (isset($routes['GET /admin/dashboard'])) {
        echo "✓ GET /admin/dashboard route found: " . $routes['GET /admin/dashboard'] . "\n";
    } else {
        echo "✗ GET /admin/dashboard route NOT found\n";
    }
} else {
    echo "✗ routes/web.php not found\n";
}

// Test UIComponents
if (file_exists('components/UIComponents.php')) {
    echo "✓ UIComponents.php exists\n";
} else {
    echo "✗ UIComponents.php not found\n";
}

// Test view files
$viewFiles = [
    'views/admin/login.php',
    'views/admin/dashboard_layout.php', 
    'views/admin/dashboard_enhanced.php',
    'views/admin/properties/list.php',
    'views/admin/tenants/list.php'
];

foreach ($viewFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file NOT found\n";
    }
}

echo "\nRoute testing complete!\n";
?>
