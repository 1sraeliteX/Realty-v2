<?php
// Simple test to check if tenant views work
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Tenant Views Path Test</h1>";

// Test the actual include paths like in the tenant views
$currentDir = __DIR__;
echo "<p>Current directory: " . $currentDir . "</p>";

// Test UIComponents path (from admin/tenants/ perspective)
$uiPath = $currentDir . '/views/admin/tenants/../../../components/UIComponents.php';
echo "<p>UIComponents path: " . $uiPath . "</p>";
echo "<p>UIComponents exists: " . (file_exists($uiPath) ? "✅ YES" : "❌ NO") . "</p>";

// Test Layout path (from admin/tenants/ perspective)
$layoutPath = $currentDir . '/views/admin/dashboard_layout.php';
echo "<p>Layout path: " . $layoutPath . "</p>";
echo "<p>Layout exists: " . (file_exists($layoutPath) ? "✅ YES" : "❌ NO") . "</p>";

// Try to include UIComponents
echo "<h2>Testing UIComponents inclusion:</h2>";
try {
    require_once $uiPath;
    echo "<p>✅ UIComponents included successfully</p>";
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// Try to include layout
echo "<h2>Testing Layout inclusion:</h2>";
try {
    ob_start();
    echo "<div style='padding: 20px; background: #f0f0f0; margin: 10px 0;'>";
    echo "<h3>Test Content</h3>";
    echo "<p>This is test content for the layout.</p>";
    echo "</div>";
    $content = ob_get_clean();
    
    $title = "Test Page";
    $pageTitle = "Test Tenant Page";
    
    include $layoutPath;
    echo "<p>✅ Layout included successfully</p>";
} catch (Exception $e) {
    echo "<p>❌ Exception: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Complete Test Results:</h2>";
echo "<p>If you see this message and the layout rendered above, the paths are working correctly.</p>";
echo "<p>The issue might be with the web server configuration or PHP include path.</p>";
?>
