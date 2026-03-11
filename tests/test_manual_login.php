<?php
// Manual login test to simulate the exact form submission
session_start();

echo "<h2>Manual Login Test</h2>";

// Step 1: Clear session
session_destroy();
session_start();

// Step 2: Simulate form submission
$_POST['email'] = 'admin@cornerstone.com';
$_POST['password'] = 'admin123';
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "<h3>Simulating POST to /admin/login</h3>";

// Load the controller and test the login method directly
require_once __DIR__ . '/config/config_simple.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/controllers/BaseController.php';
require_once __DIR__ . '/app/middleware/JwtMiddleware.php';
require_once __DIR__ . '/app/controllers/AdminAuthController.php';

try {
    $controller = new App\Controllers\AdminAuthController();
    
    echo "<p>Controller created successfully</p>";
    
    // Capture any output/redirects
    ob_start();
    $controller->login();
    $output = ob_get_clean();
    
    echo "<h3>Login Result:</h3>";
    echo "<p>Output: " . htmlspecialchars($output) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Check session after login attempt
echo "<h3>Session After Login:</h3>";
if (empty($_SESSION)) {
    echo "<p style='color: red;'>No session data</p>";
} else {
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
}

// Test if we can access dashboard
echo "<h3>Testing Dashboard Access:</h3>";
if (isset($_SESSION['admin_id'])) {
    echo "<p style='color: green;'>✓ Admin session found</p>";
    
    // Try to access dashboard controller
    try {
        require_once __DIR__ . '/app/controllers/AdminDashboardController.php';
        $dashboard = new App\Controllers\AdminDashboardController();
        
        echo "<p>Dashboard controller created</p>";
        
        // This should work if auth is working
        ob_start();
        $dashboard->index();
        $dashboardOutput = ob_get_clean();
        
        echo "<p>Dashboard access: " . (empty($dashboardOutput) ? 'Redirected (likely successful)' : 'Content rendered') . "</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Dashboard error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ No admin session</p>";
}
?>
