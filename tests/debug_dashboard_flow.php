<?php
// Debug the complete dashboard flow
session_start();

echo "<h1>Dashboard Flow Debug</h1>";

// Step 1: Check if we can access the route
echo "<h2>Step 1: Route Check</h2>";
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    $routes = require $routesFile;
    echo "✅ Routes loaded<br>";
    echo "Admin dashboard route: " . (isset($routes['GET /admin/dashboard']) ? $routes['GET /admin/dashboard'] : 'NOT FOUND') . "<br>";
} else {
    echo "❌ Routes file not found<br>";
}

// Step 2: Check controller
echo "<h2>Step 2: Controller Check</h2>";
$controllerFile = __DIR__ . '/app/controllers/AdminDashboardController.php';
if (file_exists($controllerFile)) {
    echo "✅ Controller file exists<br>";
    
    // Try to load the controller
    try {
        require_once __DIR__ . '/config/database.php';
        require_once __DIR__ . '/app/controllers/BaseController.php';
        require_once $controllerFile;
        echo "✅ Controller loaded successfully<br>";
        
        // Create controller instance
        $controller = new \App\Controllers\AdminDashboardController();
        echo "✅ Controller instantiated<br>";
        
    } catch (Exception $e) {
        echo "❌ Controller error: " . $e->getMessage() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "❌ Controller file not found<br>";
}

// Step 3: Check authentication
echo "<h2>Step 3: Authentication Check</h2>";
if (isset($_SESSION['admin_id'])) {
    echo "✅ Admin session exists: " . $_SESSION['admin_id'] . "<br>";
} else {
    echo "❌ No admin session - creating test session<br>";
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_email'] = 'admin@cornerstone.com';
    $_SESSION['admin_role'] = 'admin';
    echo "✅ Test admin session created<br>";
}

// Step 4: Test controller method directly
echo "<h2>Step 4: Controller Method Test</h2>";
try {
    // Capture output
    ob_start();
    $controller = new \App\Controllers\AdminDashboardController();
    $controller->index();
    $output = ob_get_clean();
    
    if (!empty($output)) {
        echo "✅ Controller produced output: " . strlen($output) . " bytes<br>";
        echo "<h3>Output Preview:</h3>";
        echo "<div style='border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow-y: auto;'>";
        echo htmlspecialchars(substr($output, 0, 1000)) . "...";
        echo "</div>";
    } else {
        echo "❌ Controller produced no output<br>";
    }
} catch (Exception $e) {
    echo "❌ Controller method error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Step 5: Test view rendering
echo "<h2>Step 5: View Rendering Test</h2>";
$viewFile = __DIR__ . '/views/admin/dashboard_enhanced.php';
if (file_exists($viewFile)) {
    echo "✅ View file exists<br>";
    
    try {
        // Test view rendering with mock data
        require_once __DIR__ . '/config/init_framework.php';
        
        // Mock some data
        $stats = DataProvider::get('dashboard_stats');
        $recentProperties = DataProvider::get('recent_properties');
        $activities = DataProvider::get('activities');
        
        echo "✅ Mock data loaded<br>";
        
        // Capture view output
        ob_start();
        include $viewFile;
        $viewOutput = ob_get_clean();
        
        if (!empty($viewOutput)) {
            echo "✅ View produced output: " . strlen($viewOutput) . " bytes<br>";
        } else {
            echo "❌ View produced no output<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ View rendering error: " . $e->getMessage() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "❌ View file not found<br>";
}

// Step 6: Check error logs
echo "<h2>Step 6: Error Log Check</h2>";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    echo "✅ Error log found: " . $errorLog . "<br>";
    $recentErrors = file_get_contents($errorLog);
    if (!empty($recentErrors)) {
        echo "<h3>Recent Errors:</h3>";
        echo "<pre style='background: #ffe6e6; padding: 10px;'>";
        echo htmlspecialchars(substr($recentErrors, -2000)); // Last 2000 chars
        echo "</pre>";
    } else {
        echo "No recent errors in log<br>";
    }
} else {
    echo "❌ No error log found<br>";
}

echo "<h2>Quick Actions</h2>";
echo "<a href='/admin/login'>Go to Login</a><br>";
echo "<a href='/debug_dashboard_auth.php'>Debug Auth</a><br>";
echo "<a href='/test_dashboard_display.php'>Test Display</a><br>";
?>
