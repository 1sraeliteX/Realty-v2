<?php

// Test the login functionality
require_once __DIR__ . '/public/index.php';

// Simulate a login request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['email'] = 'admin@cornerstone.com';
$_POST['password'] = 'admin123';

echo "=== Testing Login Functionality ===\n";

try {
    // Create AuthController instance
    $authController = new App\Controllers\AuthController();
    
    echo "✅ AuthController created successfully\n";
    
    // Test login
    ob_start();
    $authController->login();
    $output = ob_get_clean();
    
    echo "✅ Login method executed\n";
    echo "Output: $output\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
