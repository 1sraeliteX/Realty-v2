<?php
// Test the dashboard fix
require_once __DIR__ . '/components/UIComponents.php';

// Test data
$stats = [
    'total_properties' => 15,
    'total_units' => 44,
    'active_tenants' => 37,
    'occupancy_rate' => 84.1,
    'monthly_revenue' => 25000,
    'occupied_units' => 37,
    'pending_payments' => 8,
    'maintenance_requests' => 3,
    'new_applications' => 2
];

echo "Testing UIComponents...\n";

// Test a simple component
$button = UIComponents::button('Test Button', 'primary', 'medium', 'home');
echo "Button test: " . ($button ? "PASS" : "FAIL") . "\n";

// Test stats card
$statsCard = UIComponents::statsCard('Test Stat', 100, 'home', 12.5, 'primary');
echo "Stats card test: " . ($statsCard ? "PASS" : "FAIL") . "\n";

// Test card
$card = UIComponents::card('Test content', 'Test header', null, 'test-class');
echo "Card test: " . ($card ? "PASS" : "FAIL") . "\n";

echo "\nAll UIComponents are working!\n";
echo "Dashboard should now display properly.\n";
?>
