<?php
// Final comprehensive login test
echo "<h2>Final Login Test</h2>";

// Test 1: Direct controller test (no browser)
echo "<h3>Test 1: Direct Controller Test</h3>";

// Start fresh session
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}
session_start();

try {
    // Load everything the app.php loads
    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/app/controllers/BaseController.php';
    require_once __DIR__ . '/app/middleware/JwtMiddleware.php';
    require_once __DIR__ . '/app/controllers/AdminAuthController.php';
    
    // Initialize database (like app.php does)
    $db = Config\Database::getInstance();
    echo "<p style='color: green;'>✓ Database initialized</p>";
    
    // Create controller
    $controller = new App\Controllers\AdminAuthController();
    echo "<p style='color: green;'>✓ Controller created</p>";
    
    // Simulate POST login
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['email'] = 'admin@cornerstone.com';
    $_POST['password'] = 'admin123';
    
    // Capture any redirect headers
    ob_start();
    $controller->login();
    $output = ob_get_clean();
    
    echo "<p>Login method executed</p>";
    
    // Check session after login
    if (isset($_SESSION['admin_id'])) {
        echo "<p style='color: green;'>✓ Login successful - Admin ID: {$_SESSION['admin_id']}</p>";
        echo "<p>Admin Email: " . ($_SESSION['admin_email'] ?? 'N/A') . "</p>";
        echo "<p>Admin Role: " . ($_SESSION['admin_role'] ?? 'N/A') . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Login failed - No session data</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test 2: Browser simulation
echo "<h3>Test 2: Browser Simulation</h3>";

$cookieFile = tempnam(sys_get_temp_dir(), 'cookie_');
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HEADER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

// Get login page
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/login');
curl_setopt($ch, CURLOPT_POST, false);
$response1 = curl_exec($ch);
$httpCode1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "<p>Login page HTTP Code: $httpCode1</p>";

// Submit login
$postData = http_build_query([
    'email' => 'admin@cornerstone.com',
    'password' => 'admin123'
]);

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

echo "<p>Login submission HTTP Code: $httpCode2</p>";
echo "<p>Final URL after login: $finalUrl</p>";

// Check if redirected to dashboard
if (strpos($finalUrl, 'dashboard') !== false) {
    echo "<p style='color: green;'>✓ Browser login successful - redirected to dashboard</p>";
} else {
    echo "<p style='color: red;'>✗ Browser login failed or not redirected</p>";
}

// Try to access dashboard
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/dashboard');
curl_setopt($ch, CURLOPT_POST, false);
$response3 = curl_exec($ch);
$httpCode3 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "<p>Dashboard access HTTP Code: $httpCode3</p>";
if ($httpCode3 === 200) {
    echo "<p style='color: green;'>✓ Dashboard accessible after login</p>";
} else {
    echo "<p style='color: red;'>✗ Dashboard not accessible</p>";
}

curl_close($ch);
unlink($cookieFile);

// Test 3: Check for common issues
echo "<h3>Test 3: Common Issues Check</h3>";

// Check session configuration
echo "<p>Session save path: " . session_save_path() . "</p>";
echo "<p>Session cookie params: " . print_r(session_get_cookie_params(), true) . "</p>";

// Check if session is working across requests
echo "<p>Testing session persistence...</p>";
$_SESSION['test_value'] = 'test_' . time();
echo "<p>Set test value: " . $_SESSION['test_value'] . "</p>";

// Final diagnosis
echo "<h3>Final Diagnosis</h3>";
echo "<div style='background: #f5f5f5; padding: 15px; border-left: 4px solid #333;'>";
echo "<p><strong>Based on the tests:</strong></p>";

if (isset($_SESSION['admin_id']) && strpos($finalUrl, 'dashboard') !== false) {
    echo "<p style='color: green;'>✅ Login system is working correctly!</p>";
    echo "<p>The issue might be:</p>";
    echo "<ul>";
    echo "<li>Browser cache/cookies - clear them and try again</li>";
    echo "<li>Using wrong URL - use: http://localhost:8000/admin/login</li>";
    echo "<li>Server not accessible - check if PHP server is running</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ There might be an issue with the login system</p>";
    echo "<p>Check the test results above for specific errors</p>";
}

echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Clear browser cache and cookies</li>";
echo "<li>Try incognito/private browsing mode</li>";
echo "<li>Use exact URL: http://localhost:8000/admin/login</li>";
echo "<li>Click the quick login buttons on the login page</li>";
echo "</ol>";
echo "</div>";
?>
