<?php
// Simple test script to verify super admin functionality
// This is for development/testing purposes only

require_once 'app/controllers/BaseController.php';
require_once 'app/controllers/SuperAdminController.php';
require_once 'app/controllers/DashboardController.php';

use App\Controllers\BaseController;
use App\Controllers\SuperAdminController;
use App\Controllers\DashboardController;

echo "<h1>Super Admin Implementation Test</h1>";

// Test 1: Check if required files exist
echo "<h2>File Structure Test</h2>";
$requiredFiles = [
    'app/controllers/SuperAdminController.php',
    'views/superadmin/dashboard.php',
    'views/superadmin/admins.php',
    'views/superadmin/superadmin_layout.php',
    'database/create_test_superadmin.sql'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file missing</p>";
    }
}

// Test 2: Check if routes are configured
echo "<h2>Routes Test</h2>";
$routesFile = 'routes/web.php';
if (file_exists($routesFile)) {
    $routesContent = file_get_contents($routesFile);
    if (strpos($routesContent, 'SuperAdminController') !== false) {
        echo "<p style='color: green;'>✓ Super admin routes found</p>";
    } else {
        echo "<p style='color: red;'>✗ Super admin routes missing</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Routes file not found</p>";
}

// Test 3: Check database schema
echo "<h2>Database Schema Test</h2>";
$schemaFile = 'database/supabase_schema.sql';
if (file_exists($schemaFile)) {
    $schemaContent = file_get_contents($schemaFile);
    if (strpos($schemaContent, "role TEXT DEFAULT 'admin' CHECK (role IN ('admin', 'super_admin'))") !== false) {
        echo "<p style='color: green;'>✓ Admin role column properly configured</p>";
    } else {
        echo "<p style='color: red;'>✗ Admin role column issue</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Schema file not found</p>";
}

// Test 4: Check view files for required elements
echo "<h2>View Components Test</h2>";
$dashboardView = 'views/superadmin/dashboard.php';
if (file_exists($dashboardView)) {
    $dashboardContent = file_get_contents($dashboardView);
    $requiredElements = [
        'Platform Overview',
        'Total Admins',
        'Export Data',
        'DotBot Assistant',
        'Recent Admins'
    ];
    
    foreach ($requiredElements as $element) {
        if (strpos($dashboardContent, $element) !== false) {
            echo "<p style='color: green;'>✓ $element found in dashboard</p>";
        } else {
            echo "<p style='color: red;'>✗ $element missing from dashboard</p>";
        }
    }
}

// Test 5: Instructions
echo "<h2>Setup Instructions</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px;'>";
echo "<h3>To complete the setup:</h3>";
echo "<ol>";
echo "<li>Run the SQL script in Supabase: <code>database/create_test_superadmin.sql</code></li>";
echo "<li>Log in as super admin: <strong>superadmin@cornerstone.com</strong> / <strong>admin123</strong></li>";
echo "<li>Access the super admin dashboard at: <code>/superadmin</code></li>";
echo "<li>Test admin management at: <code>/superadmin/admins</code></li>";
echo "</ol>";
echo "</div>";

echo "<h2>Test Accounts</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Role</th><th>Email</th><th>Password</th><th>Dashboard URL</th></tr>";
echo "<tr><td>Super Admin</td><td>superadmin@cornerstone.com</td><td>admin123</td><td>/superadmin</td></tr>";
echo "<tr><td>Regular Admin</td><td>admin@cornerstone.com</td><td>admin123</td><td>/dashboard</td></tr>";
echo "</table>";

echo "<p><strong>Important:</strong> Regular admins will be redirected to their property-focused dashboard, while super admins will see the platform overview.</p>";
?>
