<?php
// Simple test to check navbar functionality
require_once __DIR__ . '/config/bootstrap.php';

// Test if routes are working
echo "<h1>Navbar Routes Test</h1>";

// Check if controllers exist
$controllers = [
    'UnitController' => '/admin/units',
    'CommunicationController' => '/admin/communications', 
    'DocumentController' => '/admin/documents',
    'FinanceController' => '/admin/finances',
    'MaintenanceController' => '/admin/maintenance',
    'ReportController' => '/admin/reports',
    'SettingsController' => '/admin/settings',
    'ProfileController' => '/admin/profile'
];

echo "<h2>Controller Status:</h2>";
foreach ($controllers as $controller => $route) {
    $file = __DIR__ . "/app/controllers/{$controller}.php";
    $status = file_exists($file) ? "✅ EXISTS" : "❌ MISSING";
    echo "<p>{$controller}: {$status} -> {$route}</p>";
}

// Check if UIComponents is working
echo "<h2>UIComponents Test:</h2>";
try {
    $avatar = UIComponents::avatar('Test User', '', 'small');
    echo "<p>✅ UIComponents working: {$avatar}</p>";
} catch (Exception $e) {
    echo "<p>❌ UIComponents error: {$e->getMessage()}</p>";
}

// Check current session
echo "<h2>Session Status:</h2>";
session_start();
if (isset($_SESSION['admin'])) {
    echo "<p>✅ Admin session active</p>";
} else {
    echo "<p>❌ No admin session</p>";
}

echo "<h2>Test Links:</h2>";
echo "<p><a href='/admin/dashboard'>Dashboard</a></p>";
echo "<p><a href='/admin/units'>Units</a></p>";
echo "<p><a href='/admin/communications'>Communications</a></p>";
echo "<p><a href='/admin/documents'>Documents</a></p>";
echo "<p><a href='/admin/finances'>Finances</a></p>";
echo "<p><a href='/admin/maintenance'>Maintenance</a></p>";
echo "<p><a href='/admin/reports'>Reports</a></p>";
echo "<p><a href='/admin/settings'>Settings</a></p>";
echo "<p><a href='/admin/profile'>Profile</a></p>";
?>
