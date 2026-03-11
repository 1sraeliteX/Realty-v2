<?php
// Tenants Debug Script
echo "<!DOCTYPE html>";
echo "<html><head><title>Tenants Debug</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; }
    .error { background: #ffe0e0; padding: 10px; margin: 10px 0; }
    .success { background: #e0ffe0; padding: 10px; margin: 10px 0; }
</style>";
echo "</head><body>";
echo "<h1>Tenants Page Debug</h1>";

// Test the controller directly
require_once __DIR__ . '/../app/controllers/BaseController.php';
require_once __DIR__ . '/../app/controllers/TenantController.php';

use App\Controllers\TenantController;

echo "<div class='debug'>";
echo "<h2>Testing Tenants Controller</h2>";

try {
    $controller = new TenantController();
    
    // Capture the output
    ob_start();
    $controller->index();
    $output = ob_get_clean();
    
    echo "<div class='success'>✅ Controller executed successfully</div>";
    echo "<div class='debug'>Output length: " . strlen($output) . " characters</div>";
    
    // Check for expected content
    $checks = [
        'HTML structure' => strpos($output, '<html') !== false,
        'Tenants heading' => strpos($output, 'Tenants') !== false,
        'Stats cards' => strpos($output, 'Active Tenants') !== false,
        'Tenant data' => strpos($output, 'John Smith') !== false,
        'Table structure' => strpos($output, '<table') !== false,
    ];
    
    echo "<h3>Content Checks:</h3>";
    foreach ($checks as $check => $result) {
        $status = $result ? '✅' : '❌';
        $color = $result ? 'green' : 'red';
        echo "<div style='color: $color;'>$status $check</div>";
    }
    
    // Look for debug comments
    if (strpos($output, 'Debug:') !== false) {
        echo "<div class='success'>✅ Debug comments found</div>";
        // Extract debug info
        preg_match('/Debug: Tenant count = (\d+)/', $output, $matches);
        if (isset($matches[1])) {
            echo "<div class='debug'>Tenant count from debug: {$matches[1]}</div>";
        }
    } else {
        echo "<div class='error'>❌ No debug comments found</div>";
    }
    
    // Show a snippet of the output
    echo "<h3>Output Preview (first 2000 characters):</h3>";
    echo "<div class='debug'><pre>" . htmlspecialchars(substr($output, 0, 2000)) . "</pre></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Exception: " . $e->getMessage() . "</div>";
} catch (Error $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "</div>";

echo "<div class='debug'>";
echo "<h2>Direct View Test</h2>";

// Test the view directly
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

echo "<div class='debug'>Mock tenants prepared: " . count($mockTenants) . " tenants</div>";
echo "<div class='debug'>First tenant: " . $mockTenants[0]['first_name'] . ' ' . $mockTenants[0]['last_name'] . "</div>";

echo "</div>";

echo "<div class='debug'>";
echo "<h2>Quick Links</h2>";
echo "<p><a href='/admin/tenants' target='_blank'>📋 Open Tenants Page</a></p>";
echo "<p><a href='/admin/tenants/create' target='_blank'>➕ Create Tenant</a></p>";
echo "<p><a href='/admin/dashboard' target='_blank'>🏠 Admin Dashboard</a></p>";
echo "</div>";

echo "</body></html>";
?>
