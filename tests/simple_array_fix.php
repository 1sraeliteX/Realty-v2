<?php

/**
 * Simple script to demonstrate the ArrayHelper usage
 * This shows how to prevent "Undefined array key" errors
 */

require_once __DIR__ . '/config/ArrayHelper.php';

echo "=== ArrayHelper Usage Examples ===\n\n";

// Example 1: Safe array access
$stats = [
    'total_units' => 150,
    'occupied_units' => 120
    // Note: 'vacant_units' is missing
];

echo "Unsafe way (causes error):\n";
// echo $stats['vacant_units']; // This would cause "Undefined array key"

echo "\nSafe way with ArrayHelper:\n";
echo "Vacant units: " . ArrayHelper::get($stats, 'vacant_units', 0) . "\n";
echo "Total units: " . ArrayHelper::get($stats, 'total_units', 0) . "\n";

// Example 2: Using helper functions
echo "\nUsing helper functions:\n";
echo "Total units: " . arr_get($stats, 'total_units', 0) . "\n";
echo "Formatted: " . arr_format($stats, 'total_units') . "\n";

// Example 3: Superglobal access
$_POST['username'] = 'john_doe';
// $_POST['email'] is missing

echo "\nSuperglobal access:\n";
echo "Username: " . ArrayHelper::post('username', 'guest') . "\n";
echo "Email: " . ArrayHelper::post('email', 'no-email@example.com') . "\n";

// Example 4: HTML escaping
$user = ['name' => '<script>alert("xss")</script>'];
echo "\nHTML escaping:\n";
echo "Unsafe: " . $user['name'] . "\n";
echo "Safe: " . arr_escape($user, 'name') . "\n";

echo "\n=== All examples completed safely! ===\n";
?>
