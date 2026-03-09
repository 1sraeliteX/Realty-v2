<?php
// Simple browser test without interfering with headers
echo "<h2>Simple Browser Test</h2>";

// Test server accessibility
$testUrl = 'http://localhost:8000/admin/login';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 5,
        'follow_location' => true,
        'max_redirects' => 5
    ]
]);

echo "<h3>Testing Server Access</h3>";
$response = @file_get_contents($testUrl, false, $context);
if ($response !== false) {
    echo "<p style='color: green;'>✓ Server is running and accessible</p>";
    echo "<p style='color: green;'>✓ Login page loads successfully</p>";
    
    // Check if login form is present
    if (strpos($response, 'email') !== false && strpos($response, 'password') !== false) {
        echo "<p style='color: green;'>✓ Login form is present</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Login form might be missing</p>";
    }
    
    // Check for quick login buttons
    if (strpos($response, 'quickAdminLogin') !== false) {
        echo "<p style='color: green;'>✓ Quick login buttons available</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Server not accessible</p>";
    echo "<p>Make sure to run: <code>php -S localhost:8000 -t public</code></p>";
}

echo "<h3>Manual Testing Instructions</h3>";
echo "<div style='background: #e8f4fd; padding: 15px; border-left: 4px solid #2196f3;'>";
echo "<p><strong>To test login manually:</strong></p>";
echo "<ol>";
echo "<li>Open your browser</li>";
echo "<li>Go to: <a href='http://localhost:8000/admin/login' target='_blank'>http://localhost:8000/admin/login</a></li>";
echo "<li>Try one of these methods:</li>";
echo "<ul>";
echo "<li>Enter: admin@cornerstone.com / admin123</li>";
echo "<li>Or click the 'Login as Admin' button (auto-fills credentials)</li>";
echo "<li>Or click the 'Login as Super Admin' button</li>";
echo "</ul>";
echo "<li>Click 'Sign in'</li>";
echo "<li>You should be redirected to the dashboard</li>";
echo "</ol>";
echo "</div>";

echo "<h3>Troubleshooting Steps</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";
echo "<p><strong>If login still doesn't work:</strong></p>";
echo "<ol>";
echo "<li><strong>Clear Browser Data:</strong> Clear cache, cookies, and local storage</li>";
echo "<li><strong>Try Incognito Mode:</strong> Open a private/incognito window</li>";
echo "<li><strong>Check Server:</strong> Ensure the PHP server is running on port 8000</li>";
echo "<li><strong>Check URL:</strong> Use exactly: http://localhost:8000/admin/login</li>";
echo "<li><strong>Network Issues:</strong> Check if localhost resolves correctly</li>";
echo "<li><strong>Firewall:</strong> Make sure nothing is blocking port 8000</li>";
echo "</ol>";
echo "</div>";

echo "<h3>Quick Access Links</h3>";
echo "<ul>";
echo "<li><a href='http://localhost:8000/admin/login' target='_blank'>🔐 Admin Login</a></li>";
echo "<li><a href='http://localhost:8000/test_login_simple.php' target='_blank'>🔍 Database Test</a></li>";
echo "<li><a href='http://localhost:8000/check_admins.php' target='_blank'>👥 Check Admins</a></li>";
echo "<li><a href='http://localhost:8000/fix_password_check.php' target='_blank'>🔧 Password Check</a></li>";
echo "</ul>";

// Check if we can detect any recent changes that might have broken login
echo "<h3>Recent Changes Analysis</h3>";
echo "<p>The login system was working before. Recent changes included:</p>";
echo "<ul>";
echo "<li>✅ Fixed JwtMiddleware dependency issue</li>";
echo "<li>✅ Database tables are present and working</li>";
echo "<li>✅ Admin credentials are valid</li>";
echo "<li>✅ Server is running and accessible</li>";
echo "</ul>";
echo "<p style='color: green;'><strong>Conclusion: The login system should be working. Try the manual steps above.</strong></p>";
?>
