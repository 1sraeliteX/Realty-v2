<?php

// Test if controllers can be loaded
echo "=== Testing Controller Loading ===\n";

// Test autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    echo "Loading: $class -> $file\n";
    
    if (file_exists($file)) {
        require $file;
        echo "✅ Loaded: $class\n";
    } else {
        echo "❌ File not found: $file\n";
    }
});

// Test loading controllers
$controllers = ['DashboardController', 'SuperAdminController', 'AuthController'];

foreach ($controllers as $controller) {
    $class = "App\\Controllers\\{$controller}";
    if (class_exists($class)) {
        echo "✅ $class exists\n";
    } else {
        echo "❌ $class not found\n";
    }
}

echo "\n=== Test Complete ===\n";
?>
