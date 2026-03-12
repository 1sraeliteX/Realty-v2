<?php
session_start();
require_once __DIR__ . '/config/bootstrap.php';

// Check authentication status
echo "<h1>Navbar Authentication Test</h1>";

if (isset($_SESSION['admin_id'])) {
    echo "<p>✅ Logged in: {$_SESSION['admin_name']} ({$_SESSION['admin_email']})</p>";
    echo "<p>✅ Role: {$_SESSION['admin_role']}</p>";
    echo "<p>✅ Session ID: " . session_id() . "</p>";
    
    // Test navbar links
    echo "<h2>Navbar Links Test:</h2>";
    $links = [
        '/admin/dashboard' => 'Dashboard',
        '/admin/properties' => 'Properties', 
        '/admin/units' => 'Units',
        '/admin/tenants' => 'Tenants',
        '/admin/tenants-occupants' => 'Tenants & Occupants',
        '/admin/payments' => 'Payments',
        '/admin/invoices' => 'Invoices',
        '/admin/finances' => 'Finances',
        '/admin/maintenance' => 'Maintenance',
        '/admin/communications' => 'Communications',
        '/admin/documents' => 'Documents',
        '/admin/reports' => 'Reports',
        '/admin/dashboard/reports' => 'Dashboard Reports',
        '/admin/settings' => 'Settings',
        '/admin/profile' => 'Profile'
    ];
    
    foreach ($links as $url => $name) {
        echo "<p><a href='$url' target='_blank'>$name</a> - $url</p>";
    }
    
    echo "<h2>Quick Actions:</h2>";
    echo "<p><a href='/admin/login' target='_blank'>Go to Login</a></p>";
    echo "<p><a href='/admin/logout' target='_blank'>Logout</a></p>";
    
} else {
    echo "<p>❌ Not logged in</p>";
    echo "<p><a href='/admin/login'>Please login first</a></p>";
    
    // Show session info
    echo "<h2>Session Info:</h2>";
    echo "<p>Session ID: " . session_id() . "</p>";
    echo "<p>Session data: " . json_encode($_SESSION) . "</p>";
}

echo "<h2>Test Login Credentials:</h2>";
echo "<p>Email: test@admin.com</p>";
echo "<p>Password: password123</p>";
?>
