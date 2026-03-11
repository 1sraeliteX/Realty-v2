<?php
// Test anti-scattering system initialization
require_once __DIR__ . '/config/bootstrap.php';

echo "=== Anti-Scattering System Test ===\n";
echo "ComponentRegistry loaded: " . (class_exists('ComponentRegistry') ? "YES" : "NO") . "\n";
echo "ViewManager loaded: " . (class_exists('ViewManager') ? "YES" : "NO") . "\n";
echo "DataProvider loaded: " . (class_exists('DataProvider') ? "YES" : "NO") . "\n";
echo "UIComponents loaded: " . (class_exists('UIComponents') ? "YES" : "NO") . "\n";

echo "\n=== Data Test ===\n";
$user = ViewManager::get('user');
echo "User data: " . ($user ? "FOUND" : "NOT FOUND") . "\n";
if ($user) {
    echo "User name: " . $user['name'] . "\n";
}

$notifications = ViewManager::get('notifications');
echo "Notifications: " . (is_array($notifications) ? count($notifications) . " items" : "NOT FOUND") . "\n";

echo "\n=== Testing Dashboard Layout Directly ===\n";
// Test the dashboard layout directly
ob_start();
include __DIR__ . '/views/admin/dashboard_layout.php';
$output = ob_get_clean();

echo "Dashboard layout generated: " . (strlen($output) > 0 ? "YES (" . strlen($output) . " chars)" : "NO") . "\n";

// Check for JavaScript
if (strpos($output, 'open-sidebar') !== false) {
    echo "✅ Hamburger menu JavaScript found\n";
} else {
    echo "❌ Hamburger menu JavaScript NOT found\n";
}

if (strpos($output, 'fa-bars') !== false) {
    echo "✅ Hamburger icon found\n";
} else {
    echo "❌ Hamburger icon NOT found\n";
}

// Save output for manual testing
file_put_contents(__DIR__ . '/hamburger_test_output.html', $output);
echo "Saved test output to: hamburger_test_output.html\n";
?>
