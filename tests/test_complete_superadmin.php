<?php
echo "<h1>SuperAdmin Login & Dashboard Test</h1>";

// Test 1: Login page loads
echo "<h2>1. Testing Login Page Load</h2>";
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/superadmin/login';
ob_start();
require_once __DIR__ . '/public/app.php';
$loginOutput = ob_get_clean();

if (strpos($loginOutput, 'Super Admin Portal') !== false) {
    echo "✅ Login page loads correctly<br>";
} else {
    echo "❌ Login page failed to load<br>";
}

// Test 2: Login submission
echo "<h2>2. Testing Login Submission</h2>";
session_start();
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/superadmin/login';
$_POST = [
    'email' => 'superadmin@cornerstone.com',
    'password' => 'admin123'
];

// Capture headers to check for redirect
$headers = [];
ob_start();
require_once __DIR__ . '/public/app.php';
ob_end_clean();

// Check if session was set
if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'super_admin') {
    echo "✅ Login successful - session created<br>";
    echo "📊 Admin ID: " . $_SESSION['admin_id'] . "<br>";
    echo "📊 Admin Name: " . $_SESSION['admin_name'] . "<br>";
    echo "📊 Admin Role: " . $_SESSION['admin_role'] . "<br>";
} else {
    echo "❌ Login failed - no session created<br>";
}

// Test 3: Dashboard access
echo "<h2>3. Testing Dashboard Access</h3>";
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/superadmin/dashboard';

ob_start();
require_once __DIR__ . '/public/app.php';
$dashboardOutput = ob_get_clean();

if (strpos($dashboardOutput, 'Platform Overview') !== false) {
    echo "✅ Dashboard loads correctly<br>";
} else {
    echo "❌ Dashboard failed to load<br>";
}

// Test 4: Check dashboard components
echo "<h2>4. Testing Dashboard Components</h2>";
$components = [
    'Total Admins' => 'Total Admins',
    'Total Properties' => 'Total Properties', 
    'Platform Revenue' => 'Platform Revenue',
    'Recent Admins' => 'Recent Admins',
    'Recent Activity' => 'Recent Activity'
];

foreach ($components as $component => $text) {
    if (strpos($dashboardOutput, $text) !== false) {
        echo "✅ {$component} component found<br>";
    } else {
        echo "❌ {$component} component missing<br>";
    }
}

echo "<h2>5. Summary</h2>";
echo "<p><a href='/superadmin/login' target='_blank'>Test SuperAdmin Login in Browser</a></p>";
echo "<p><strong>Demo Credentials:</strong> superadmin@cornerstone.com / admin123</p>";
?>
