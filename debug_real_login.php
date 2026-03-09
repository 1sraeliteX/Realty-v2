<?php
// Debug the actual login process step by step
session_start();

echo "<h2>Real Login Debug</h2>";

// Step 1: Check if we can access the login controller directly
echo "<h3>Step 1: Testing AdminAuthController directly</h3>";

try {
    require_once __DIR__ . '/config/config_simple.php';
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/app/controllers/BaseController.php';
    require_once __DIR__ . '/app/middleware/JwtMiddleware.php';
    require_once __DIR__ . '/app/controllers/AdminAuthController.php';
    
    $controller = new App\Controllers\AdminAuthController();
    echo "<p style='color: green;'>✓ AdminAuthController loaded successfully</p>";
    
    // Test showLogin method
    ob_start();
    $controller->showLogin();
    $loginOutput = ob_get_clean();
    
    if (strpos($loginOutput, 'login') !== false || strpos($loginOutput, 'Sign in') !== false) {
        echo "<p style='color: green;'>✓ Login page renders correctly</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Login page output: " . htmlspecialchars(substr($loginOutput, 0, 200)) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Controller error: " . $e->getMessage() . "</p>";
}

// Step 2: Test the login method with correct credentials
echo "<h3>Step 2: Testing login method</h3>";

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'email' => 'admin@cornerstone.com',
    'password' => 'admin123'
];

// Clear session first
session_destroy();
session_start();

try {
    $controller = new App\Controllers\AdminAuthController();
    
    // Capture output and headers
    ob_start();
    $controller->login();
    $output = ob_get_clean();
    
    echo "<p>Login method executed</p>";
    echo "<p>Output: " . htmlspecialchars(substr($output, 0, 300)) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Login method error: " . $e->getMessage() . "</p>";
}

// Step 3: Check session after login
echo "<h3>Step 3: Session state after login</h3>";
if (empty($_SESSION)) {
    echo "<p style='color: red;'>✗ No session data</p>";
} else {
    echo "<p style='color: green;'>✓ Session data found:</p>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
}

// Step 4: Test dashboard access
echo "<h3>Step 4: Testing dashboard access</h3>";
if (isset($_SESSION['admin_id'])) {
    try {
        require_once __DIR__ . '/app/controllers/AdminDashboardController.php';
        $dashboard = new App\Controllers\AdminDashboardController();
        
        echo "<p>Dashboard controller created</p>";
        
        // Test requireAuth method
        $admin = $dashboard->requireAuth();
        if ($admin) {
            echo "<p style='color: green;'>✓ Auth successful - Admin: " . $admin['name'] . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Auth failed</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Dashboard error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<p style='color: red;'>✗ No admin session - cannot test dashboard</p>";
}

// Step 5: Check for any PHP errors
echo "<h3>Step 5: PHP Error Log</h3>";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $errors = file_get_contents($errorLog);
    if (!empty($errors)) {
        echo "<p style='color: red;'>Recent PHP errors:</p>";
        echo "<pre>" . htmlspecialchars(substr($errors, -1000)) . "</pre>";
    } else {
        echo "<p style='color: green;'>✓ No recent PHP errors</p>";
    }
} else {
    echo "<p>Could not access error log</p>";
}

echo "<h3>Debug Complete</h3>";
?>
