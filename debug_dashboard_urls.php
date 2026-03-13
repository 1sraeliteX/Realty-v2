<?php
// Debug script to understand dashboard URL loading issue
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>🔍 Dashboard URL Debug Analysis</h1>";

// Check current request
echo "<h2>📊 Current Request Information</h2>";
echo "<p><strong>Current URL:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";
echo "<p><strong>HTTP Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</p>";
echo "<p><strong>Request Method:</strong> " . ($_SERVER['REQUEST_METHOD'] ?? 'Not set') . "</p>";
echo "<p><strong>Server Port:</strong> " . ($_SERVER['SERVER_PORT'] ?? 'Not set') . "</p>";

// Check session
echo "<h2>🔐 Session Status</h2>";
session_start();
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Admin ID:</strong> " . ($_SESSION['admin_id'] ?? 'Not set') . "</p>";
echo "<p><strong>Admin Email:</strong> " . ($_SESSION['admin_email'] ?? 'Not set') . "</p>";

// Check navigation links in dashboard
echo "<h2>🔗 Dashboard Navigation Links Analysis</h2>";

$dashboardLayoutPath = __DIR__ . '/views/admin/dashboard_layout.php';
if (file_exists($dashboardLayoutPath)) {
    $content = file_get_contents($dashboardLayoutPath);
    
    // Find all property-related links
    preg_match_all('/href="([^"]*properties[^"]*)"/', $content, $matches);
    if (!empty($matches[1])) {
        echo "<h3>✅ Property Links Found in dashboard_layout.php:</h3>";
        foreach ($matches[1] as $link) {
            echo "<p>• " . htmlspecialchars($link) . "</p>";
        }
    } else {
        echo "<p>❌ No property links found in dashboard_layout.php</p>";
    }
    
    // Check for any window.location redirects
    preg_match_all('/window\.location\.href\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $matches);
    if (!empty($matches[1])) {
        echo "<h3>⚠️ JavaScript Redirects Found:</h3>";
        foreach ($matches[1] as $redirect) {
            echo "<p>• " . htmlspecialchars($redirect) . "</p>";
        }
    }
}

$dashboardEnhancedPath = __DIR__ . '/views/admin/dashboard_enhanced.php';
if (file_exists($dashboardEnhancedPath)) {
    $content = file_get_contents($dashboardEnhancedPath);
    
    // Find all property-related links
    preg_match_all('/href="([^"]*properties[^"]*)"/', $content, $matches);
    if (!empty($matches[1])) {
        echo "<h3>✅ Property Links Found in dashboard_enhanced.php:</h3>";
        foreach ($matches[1] as $link) {
            echo "<p>• " . htmlspecialchars($link) . "</p>";
        }
    }
    
    // Check for window.location redirects
    preg_match_all('/window\.location\.href\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $matches);
    if (!empty($matches[1])) {
        echo "<h3>⚠️ JavaScript Redirects Found:</h3>";
        foreach ($matches[1] as $redirect) {
            echo "<p>• " . htmlspecialchars($redirect) . "</p>";
        }
    }
}

// Check browser cache and JavaScript issues
echo "<h2>🌐 Browser-Side Issues Check</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>";
echo "<h3>🔍 Why Your Browser Might Load Wrong URL:</h3>";
echo "<ol>";
echo "<li><strong>Browser Cache:</strong> Your browser may have cached the old URL</li>";
echo "<li><strong>JavaScript Redirect:</strong> Some JavaScript might be redirecting you</li>";
echo "<li><strong>Bookmark:</strong> You might be using an old bookmark</li>";
echo "<li><strong>Type-in:</strong> You might be manually typing the wrong URL</li>";
echo "<li><strong>History:</strong> Browser history might be redirecting</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🛠️ Solutions to Try</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>";
echo "<h3>✅ Step-by-Step Fix:</h3>";
echo "<ol>";
echo "<li><strong>Clear Browser Cache:</strong> Press Ctrl+F5 or Cmd+Shift+R</li>";
echo "<li><strong>Use Correct URL:</strong> <a href='/admin/dashboard' target='_blank'>http://127.0.0.1:54542/admin/dashboard</a></li>";
echo "<li><strong>Check Navigation:</strong> Click the \"Properties\" link in the sidebar</li>";
echo "<li><strong>Verify Links:</strong> Hover over links to see they point to /admin/properties</li>";
echo "<li><strong>Check Developer Tools:</strong> F12 → Network tab to see redirects</li>";
echo "</ol>";
echo "</div>";

// Test the actual URLs
echo "<h2>🧪 URL Testing</h2>";
echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff;'>";
echo "<h3>Test These Links:</h3>";
echo "<p><a href='/admin/dashboard' target='_blank'>✅ Admin Dashboard (Correct)</a></p>";
echo "<p><a href='/admin/properties' target='_blank'>✅ Admin Properties (Correct)</a></p>";
echo "<p><a href='/properties' target='_blank'>❌ Public Properties (Wrong for admin)</a></p>";
echo "</div>";

echo "<h2>🔍 What Should Happen:</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #6c757d;'>";
echo "<ol>";
echo "<li>When you click <strong>Properties</strong> in the admin dashboard sidebar → Should go to <code>/admin/properties</code></li>";
echo "<li>When you click <strong>Add Property</strong> → Should go to <code>/admin/properties/create</code></li>";
echo "<li>When you click on a recent property → Should go to <code>/admin/properties/{id}</code></li>";
echo "</ol>";
echo "</div>";

echo "<h2>📝 Debug Report:</h2>";
echo "<p><strong>If you're still seeing the wrong URL:</strong></p>";
echo "<ul>";
echo "<li>Open browser developer tools (F12)</li>";
echo "<li>Go to Network tab</li>";
echo "<li>Clear the log and refresh the page</li>";
echo "<li>Click on the Properties link</li>";
echo "<li>Look for any 302 redirects or unexpected requests</li>";
echo "<li>Check the Console tab for JavaScript errors</li>";
echo "</ul>";

echo "<script>
// Add JavaScript to detect any automatic redirects
let currentUrl = window.location.href;
console.log('Current page URL:', currentUrl);

// Monitor for navigation changes
window.addEventListener('beforeunload', function() {
    console.log('Leaving page:', window.location.href);
});

// Check if there are any redirects happening
setTimeout(function() {
    if (window.location.href !== currentUrl) {
        console.log('Page was redirected from', currentUrl, 'to', window.location.href);
        document.body.innerHTML += '<div style=\"background: red; color: white; padding: 10px; position: fixed; top: 0; left: 0; right: 0; z-index: 9999;\">⚠️ Page was redirected! Check console for details.</div>';
    }
}, 1000);
</script>";
?>
