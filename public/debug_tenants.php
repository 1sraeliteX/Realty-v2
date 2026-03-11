<?php
// Debug tenants page
echo "<h1>Tenants Page Debug</h1>";

// Test the controller
require_once __DIR__ . '/../app/controllers/BaseController.php';
require_once __DIR__ . '/../app/controllers/TenantController.php';

// Mock session
if (!headers_sent()) {
    session_start();
}

use App\Controllers\TenantController;

// Create controller instance
$tenantController = new TenantController();

echo "<h2>Testing Tenant Controller Index Method</h2>";

try {
    // Capture output
    ob_start();
    $tenantController->index();
    $output = ob_get_clean();
    
    echo "<h3>Output Length: " . strlen($output) . " characters</h3>";
    
    // Check if output contains expected elements
    $checks = [
        'contains HTML' => strpos($output, '<html') !== false,
        'contains tenants table' => strpos($output, 'Tenants') !== false,
        'contains stats cards' => strpos($output, 'Active Tenants') !== false,
        'contains tenant data' => strpos($output, 'John Smith') !== false || strpos($output, 'Sarah Johnson') !== false,
        'contains UI components' => strpos($output, 'stats-card') !== false
    ];
    
    echo "<h3>Output Checks:</h3>";
    foreach ($checks as $check => $result) {
        echo "<p style='color: " . ($result ? 'green' : 'red') . ";'>" . ($result ? '✅' : '❌') . " $check</p>";
    }
    
    // Show first 1000 characters of output
    echo "<h3>First 1000 characters of output:</h3>";
    echo "<pre style='background: #f0f0f0; padding: 10px; max-height: 300px; overflow: auto;'>" . htmlspecialchars(substr($output, 0, 1000)) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Fatal Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>Direct View Test</h2>";

// Test the view directly with mock data
$mockTenants = [
    [
        'id' => 1,
        'first_name' => 'John',
        'last_name' => 'Smith',
        'email' => 'john.smith@email.com',
        'phone' => '(555) 123-4567',
        'property_name' => 'Sunset Apartments',
        'unit_number' => '1A',
        'lease_status' => 'active',
        'payment_status' => 'current',
        'rent_amount' => 1200,
        'lease_start' => '2023-01-01',
        'lease_end' => '2024-01-01',
        'move_in_date' => '2023-01-01',
        'emergency_contact' => 'Jane Smith - (555) 987-6543',
        'created_at' => '2022-12-15'
    ]
];

echo "<p>Mock tenants count: " . count($mockTenants) . "</p>";
echo "<p>Mock tenant name: " . $mockTenants[0]['first_name'] . ' ' . $mockTenants[0]['last_name'] . "</p>";

echo "<h2>Test Complete</h2>";
?>
