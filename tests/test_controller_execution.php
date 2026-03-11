<?php

// Test controller execution
echo "=== Testing Controller Execution ===\n";

// Manually load the autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    
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

// Test DashboardController instantiation
echo "Testing DashboardController instantiation...\n";
try {
    $controller = new App\Controllers\DashboardController();
    echo "✅ DashboardController instantiated\n";
    
    // Test index method
    echo "Testing index method...\n";
    
    // Mock session data
    $_SESSION['admin_id'] = '65c031c7-d47a-435c-80fa-e113515afaf9';
    $_SESSION['admin_name'] = 'Super Admin';
    $_SESSION['admin_email'] = 'superadmin@cornerstone.com';
    $_SESSION['admin_role'] = 'super_admin';
    
    $controller->index();
    echo "✅ Index method executed\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
