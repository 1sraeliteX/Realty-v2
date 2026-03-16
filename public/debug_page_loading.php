<?php
// Debug script to test page loading issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Page Loading Debug</h1>";

// Test 1: Check if server is running
echo "<h2>1. Server Status</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";

// Test 2: Check file structure
echo "<h2>2. File Structure</h2>";
$files_to_check = [
    'app.php',
    '.htaccess',
    '../routes/web.php',
    '../app/controllers/LandingController.php',
    '../app/controllers/BaseController.php',
    '../views/landing.php',
    '../views/layout.php'
];

foreach ($files_to_check as $file) {
    $exists = file_exists(__DIR__ . '/' . $file) ? '✅ EXISTS' : '❌ MISSING';
    echo "<p>{$file}: {$exists}</p>";
}

// Test 3: Check routes
echo "<h2>3. Routes Check</h2>";
if (file_exists(__DIR__ . '/../routes/web.php')) {
    $routes = include __DIR__ . '/../routes/web.php';
    echo "<p>Total routes: " . count($routes) . "</p>";
    echo "<p>Root route ('GET /'): " . (isset($routes['GET /']) ? '✅ Found -> ' . $routes['GET /'] : '❌ Missing') . "</p>";
} else {
    echo "<p>❌ Routes file not found</p>";
}

// Test 4: Test database connection
echo "<h2>4. Database Connection</h2>";
try {
    if (file_exists(__DIR__ . '/../config/database.php')) {
        require_once __DIR__ . '/../config/database.php';
        $db = Config\Database::getInstance();
        $connection = $db->getConnection();
        echo "<p>✅ Database connection successful</p>";
    } else {
        echo "<p>❌ Database config not found</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 5: Test landing page view
echo "<h2>5. Landing Page View</h2>";
if (file_exists(__DIR__ . '/../views/landing.php')) {
    echo "<p>✅ Landing view exists</p>";
    // Try to include it with error suppression to see if there are syntax errors
    try {
        ob_start();
        include __DIR__ . '/../views/landing.php';
        $content = ob_get_clean();
        echo "<p>✅ Landing view loads without errors</p>";
        echo "<p>Content length: " . strlen($content) . " characters</p>";
    } catch (Exception $e) {
        echo "<p>❌ Landing view error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>❌ Landing view not found</p>";
}

// Test 6: Test layout
echo "<h2>6. Layout Check</h2>";
if (file_exists(__DIR__ . '/../views/layout.php')) {
    echo "<p>✅ Layout exists</p>";
} else {
    echo "<p>❌ Layout not found</p>";
}

// Test 7: Check .htaccess
echo "<h2>7. .htaccess Check</h2>";
if (file_exists(__DIR__ . '/.htaccess')) {
    echo "<p>✅ .htaccess exists</p>";
    $htaccess = file_get_contents(__DIR__ . '/.htaccess');
    if (strpos($htaccess, 'app.php') !== false) {
        echo "<p>✅ .htaccess routes to app.php</p>";
    } else {
        echo "<p>❌ .htaccess doesn't route to app.php</p>";
    }
} else {
    echo "<p>❌ .htaccess not found</p>";
}

echo "<h2>8. Test Direct Route Access</h2>";
echo "<p><a href='/public/app.php' target='_blank'>Test direct access to app.php</a></p>";
echo "<p><a href='/' target='_blank'>Test root route</a></p>";

echo "<h2>Debug Complete</h2>";
?>
