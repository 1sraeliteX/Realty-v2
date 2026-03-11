<?php
// Debug script to check dashboard rendering
session_start();

echo "<h1>Dashboard Debug</h1>";

// Check if user is logged in
echo "<h2>Session Status:</h2>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

// Check if admin is authenticated
echo "<h2>Authentication Check:</h2>";
if (isset($_SESSION['admin'])) {
    echo "<p style='color: green;'>✅ Admin is logged in</p>";
    echo "<pre>";
    var_dump($_SESSION['admin']);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ No admin session found</p>";
    echo "<p>You need to <a href='/admin/login'>login first</a></p>";
}

// Check required files
echo "<h2>Required Files Check:</h2>";
$files = [
    'config/init_framework.php',
    'config/components_registry.php', 
    'config/data_provider.php',
    'config/view_manager.php',
    'views/admin/dashboard_enhanced.php',
    'views/simple_layout.php',
    'components/UIComponents.php'
];

foreach ($files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "<p style='color: green;'>✅ $file exists</p>";
    } else {
        echo "<p style='color: red;'>❌ $file missing</p>";
    }
}

// Test framework loading
echo "<h2>Framework Test:</h2>";
try {
    require_once __DIR__ . '/config/init_framework.php';
    echo "<p style='color: green;'>✅ Framework loaded successfully</p>";
    
    // Test ComponentRegistry
    if (class_exists('ComponentRegistry')) {
        echo "<p style='color: green;'>✅ ComponentRegistry available</p>";
    } else {
        echo "<p style='color: red;'>❌ ComponentRegistry not found</p>";
    }
    
    // Test DataProvider
    if (class_exists('DataProvider')) {
        echo "<p style='color: green;'>✅ DataProvider available</p>";
    } else {
        echo "<p style='color: red;'>❌ DataProvider not found</p>";
    }
    
    // Test ViewManager
    if (class_exists('ViewManager')) {
        echo "<p style='color: green;'>✅ ViewManager available</p>";
    } else {
        echo "<p style='color: red;'>❌ ViewManager not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Framework error: " . $e->getMessage() . "</p>";
}

// Test database connection
echo "<h2>Database Test:</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    $pdo = \Config\Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/admin/login'>Go to Login</a> | <a href='/'>Go to Landing</a></p>";
?>
