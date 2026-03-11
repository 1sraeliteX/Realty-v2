<?php
// Test the namespace fix
require_once __DIR__ . '/config/init_framework.php';

// Mock session for authentication
session_start();
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'admin@test.com';

// Test DataProvider access with namespace
echo "=== Testing DataProvider Namespace Fix ===\n";

try {
    $financeStats = \DataProvider::get('finance_stats');
    echo "✅ \DataProvider::get('finance_stats') works\n";
    echo "   Total Revenue: " . $financeStats['total_revenue'] . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

try {
    $payments = \DataProvider::get('payments');
    echo "✅ \DataProvider::get('payments') works\n";
    echo "   Payments count: " . count($payments) . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Namespace Fix Complete ===\n";
?>
