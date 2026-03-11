<?php
// Test tenant view inclusion
echo "Testing tenant view paths...\n";

// Test 1: Check current directory
echo "Current directory: " . __DIR__ . "\n";

// Test 2: Check if UIComponents exists
$uiComponentsPath = __DIR__ . '/../../../components/UIComponents.php';
echo "UIComponents path: " . $uiComponentsPath . "\n";
echo "UIComponents exists: " . (file_exists($uiComponentsPath) ? "YES" : "NO") . "\n";

// Test 3: Check if dashboard layout exists
$layoutPath = __DIR__ . '/../dashboard_layout.php';
echo "Layout path: " . $layoutPath . "\n";
echo "Layout exists: " . (file_exists($layoutPath) ? "YES" : "NO") . "\n";

// Test 4: Try to include UIComponents
try {
    require_once $uiComponentsPath;
    echo "UIComponents included successfully\n";
} catch (Exception $e) {
    echo "Error including UIComponents: " . $e->getMessage() . "\n";
}

// Test 5: Try to include layout
try {
    ob_start();
    echo "<h1>Test Content</h1>";
    $content = ob_get_clean();
    include $layoutPath;
    echo "Layout included successfully\n";
} catch (Exception $e) {
    echo "Error including layout: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "Fatal error including layout: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
?>
