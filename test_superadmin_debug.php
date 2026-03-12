<?php
// Test script to debug superadmin login and dashboard issues
session_start();

echo "<h1>SuperAdmin Debug Test</h1>";

// Test 1: Check if routes are defined
echo "<h2>1. Routes Check</h2>";
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    $routes = include $routesFile;
    echo "✅ Routes file loaded<br>";
    
    // Check superadmin routes
    $superadminRoutes = [
        '/superadmin' => 'SuperAdminAuthController@showLogin',
        '/superadmin/login' => 'SuperAdminAuthController@showLogin',
        'POST /superadmin/login' => 'SuperAdminAuthController@login',
        '/superadmin/dashboard' => 'SuperAdminController@index'
    ];
    
    foreach ($superadminRoutes as $route => $controller) {
        if (array_key_exists($route, $routes) || array_key_exists(str_replace('POST ', '', $route), $routes)) {
            echo "✅ Route {$route} → {$controller}<br>";
        } else {
            echo "❌ Missing route: {$route}<br>";
        }
    }
} else {
    echo "❌ Routes file not found<br>";
}

// Test 2: Check if controllers exist
echo "<h2>2. Controllers Check</h2>";
$controllers = [
    'SuperAdminAuthController' => '/app/controllers/SuperAdminAuthController.php',
    'SuperAdminController' => '/app/controllers/SuperAdminController.php',
    'BaseController' => '/app/controllers/BaseController.php'
];

foreach ($controllers as $controller => $file) {
    $fullPath = __DIR__ . $file;
    if (file_exists($fullPath)) {
        echo "✅ {$controller} exists<br>";
    } else {
        echo "❌ {$controller} missing: {$file}<br>";
    }
}

// Test 3: Check if views exist
echo "<h2>3. Views Check</h2>";
$views = [
    'superadmin.login' => '/views/superadmin/login.php',
    'superadmin.dashboard' => '/views/superadmin/dashboard.php',
    'superadmin.superadmin_layout' => '/views/superadmin/superadmin_layout.php'
];

foreach ($views as $view => $file) {
    $fullPath = __DIR__ . $file;
    if (file_exists($fullPath)) {
        echo "✅ {$view} exists<br>";
    } else {
        echo "❌ {$view} missing: {$file}<br>";
    }
}

// Test 4: Check database connection
echo "<h2>4. Database Check</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    $db = \Config\Database::getInstance();
    $connection = $db->getConnection();
    echo "✅ Database connection successful<br>";
    
    // Check if admins table exists
    $stmt = $connection->query("SHOW TABLES LIKE 'admins'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Admins table exists<br>";
        
        // Check for superadmin users
        $stmt = $connection->prepare("SELECT COUNT(*) as count FROM admins WHERE role = 'super_admin' AND deleted_at IS NULL");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        echo "📊 Found {$count} superadmin users<br>";
        
        if ($count > 0) {
            $stmt = $connection->prepare("SELECT id, name, email FROM admins WHERE role = 'super_admin' AND deleted_at IS NULL LIMIT 1");
            $stmt->execute();
            $admin = $stmt->fetch();
            echo "📋 Sample superadmin: {$admin['name']} ({$admin['email']})<br>";
        }
    } else {
        echo "❌ Admins table missing<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 5: Check current session
echo "<h2>5. Session Check</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session is active<br>";
    echo "📊 Session ID: " . session_id() . "<br>";
    
    if (isset($_SESSION['admin_id'])) {
        echo "📋 Logged in admin ID: " . $_SESSION['admin_id'] . "<br>";
        echo "📋 Admin role: " . ($_SESSION['admin_role'] ?? 'not set') . "<br>";
    } else {
        echo "📋 No admin logged in<br>";
    }
} else {
    echo "❌ Session not active<br>";
}

// Test 6: Try to instantiate controllers
echo "<h2>6. Controller Instantiation Test</h2>";
try {
    require_once __DIR__ . '/app/controllers/SuperAdminAuthController.php';
    $authController = new \App\Controllers\SuperAdminAuthController();
    echo "✅ SuperAdminAuthController instantiated<br>";
} catch (Exception $e) {
    echo "❌ SuperAdminAuthController error: " . $e->getMessage() . "<br>";
}

try {
    require_once __DIR__ . '/app/controllers/SuperAdminController.php';
    $dashboardController = new \App\Controllers\SuperAdminController();
    echo "✅ SuperAdminController instantiated<br>";
} catch (Exception $e) {
    echo "❌ SuperAdminController error: " . $e->getMessage() . "<br>";
}

// Test 7: Check bootstrap and anti-scattering components
echo "<h2>7. Anti-Scattering Components Check</h2>";
$components = [
    '/config/bootstrap.php',
    '/config/components_registry.php',
    '/config/view_manager.php',
    '/config/data_provider.php'
];

foreach ($components as $component) {
    $fullPath = __DIR__ . $component;
    if (file_exists($fullPath)) {
        echo "✅ " . basename($component) . " exists<br>";
    } else {
        echo "❌ " . basename($component) . " missing<br>";
    }
}

echo "<h2>8. Current Request Info</h2>";
echo "📊 Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "<br>";
echo "📊 Request method: " . ($_SERVER['REQUEST_METHOD'] ?? 'not set') . "<br>";
echo "📊 Script name: " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "<br>";

echo "<h2>Test Complete</h2>";
echo "<p><a href='/superadmin/login'>Test SuperAdmin Login</a></p>";
echo "<p><a href='/admin/login'>Test Regular Admin Login</a></p>";
?>
