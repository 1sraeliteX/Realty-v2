<?php
echo "<h2>Dashboard Access Test</h2>";

// Test Admin Dashboard
echo "<h3>Testing Admin Dashboard</h3>";
$adminUrl = 'http://localhost:8000/admin/dashboard';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 5
    ]
]);

$response = @file_get_contents($adminUrl, false, $context);
if ($response !== false) {
    echo "<p style='color: green;'>✓ Admin Dashboard accessible</p>";
    if (strpos($response, 'dashboard') !== false) {
        echo "<p style='color: green;'>✓ Dashboard content loaded</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Admin Dashboard not accessible</p>";
}

// Test Super Admin Dashboard
echo "<h3>Testing Super Admin Dashboard</h3>";
$superAdminUrl = 'http://localhost:8000/superadmin/dashboard';
$response = @file_get_contents($superAdminUrl, false, $context);
if ($response !== false) {
    echo "<p style='color: green;'>✓ Super Admin Dashboard accessible</p>";
    if (strpos($response, 'dashboard') !== false) {
        echo "<p style='color: green;'>✓ Dashboard content loaded</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Super Admin Dashboard not accessible</p>";
}

// Test root URL
echo "<h3>Testing Root URL</h3>";
$rootUrl = 'http://localhost:8000/';
$response = @file_get_contents($rootUrl, false, $context);
if ($response !== false) {
    echo "<p style='color: green;'>✓ Root URL accessible (should redirect to admin dashboard)</p>";
} else {
    echo "<p style='color: red;'>✗ Root URL not accessible</p>";
}

echo "<h3>Direct Access Links</h3>";
echo "<ul>";
echo "<li><a href='http://localhost:8000/' target='_blank'>🏠 Root (Admin Dashboard)</a></li>";
echo "<li><a href='http://localhost:8000/admin/dashboard' target='_blank'>👤 Admin Dashboard</a></li>";
echo "<li><a href='http://localhost:8000/superadmin/dashboard' target='_blank'>👑 Super Admin Dashboard</a></li>";
echo "<li><a href='http://localhost:8000/superadmin/admins' target='_blank'>👥 Super Admin - Admins List</a></li>";
echo "<li><a href='http://localhost:8000/admin-direct' target='_blank'>⚡ Admin Direct</a></li>";
echo "<li><a href='http://localhost:8000/superadmin-direct' target='_blank'>⚡ Super Admin Direct</a></li>";
echo "</ul>";

echo "<h3>Changes Made</h3>";
echo "<div style='background: #e8f5e8; padding: 15px; border-left: 4px solid #4caf50;'>";
echo "<p><strong>Authentication bypassed for development:</strong></p>";
echo "<ul>";
echo "<li>✅ AdminDashboardController - Authentication commented out</li>";
echo "<li>✅ SuperAdminController - Authentication commented out</li>";
echo "<li>✅ Login routes - Commented out</li>";
echo "<li>✅ Legacy routes - Redirected to dashboards</li>";
echo "</ul>";
echo "<p><strong>Mock users:</strong></p>";
echo "<ul>";
echo "<li>Admin: Test Admin (admin@cornerstone.com)</li>";
echo "<li>Super Admin: Super Administrator (superadmin@cornerstone.com)</li>";
echo "</ul>";
echo "</div>";
?>
