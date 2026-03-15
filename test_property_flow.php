<?php
// Test the complete property creation and display flow
session_start();

echo "<h1>Property Flow Test</h1>";

// Step 1: Test admin login
echo "<h2>Step 1: Admin Login Test</h2>";

// Load database to check admin user
require_once __DIR__ . '/config/database.php';
use Config\Database;

$db = Database::getInstance();

// Check if admin user exists
$admin = $db->fetch("SELECT * FROM admins WHERE email = 'test@admin.com'");
if ($admin) {
    echo "✅ Test admin found: " . $admin['email'] . "<br>";
    
    // Simulate login session
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_name'] = $admin['name'] ?? 'Test Admin';
    echo "✅ Admin session created<br>";
} else {
    echo "❌ Test admin not found. Creating one...<br>";
    
    // Create test admin
    $adminData = [
        'name' => 'Test Admin',
        'email' => 'test@admin.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'admin',
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $adminId = $db->insert('admins', $adminData);
    echo "✅ Test admin created with ID: $adminId<br>";
    
    $_SESSION['admin_id'] = $adminId;
    $_SESSION['admin_email'] = 'test@admin.com';
    $_SESSION['admin_name'] = 'Test Admin';
}

// Step 2: Test property creation
echo "<h2>Step 2: Property Creation Test</h2>";

$testProperty = [
    'admin_id' => $_SESSION['admin_id'],
    'name' => 'Test Property ' . date('Y-m-d H:i:s'),
    'address' => '123 Test Street, Test City',
    'type' => 'residential',
    'category' => 'apartment',
    'description' => 'This is a test property created for debugging',
    'bedrooms' => 3,
    'bathrooms' => 2,
    'kitchens' => 1,
    'parking' => '2',
    'rent_price' => 150000.00,
    'status' => 'active',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

try {
    $propertyId = $db->insert('properties', $testProperty);
    echo "✅ Test property created with ID: $propertyId<br>";
    echo "Property name: " . $testProperty['name'] . "<br>";
} catch (Exception $e) {
    echo "❌ Property creation failed: " . $e->getMessage() . "<br>";
}

// Step 3: Test property retrieval (same as PropertyController query)
echo "<h2>Step 3: Property Retrieval Test</h2>";

try {
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE p.admin_id = ? AND p.deleted_at IS NULL
            ORDER BY p.created_at DESC";
    
    $properties = $db->fetchAll($sql, [$_SESSION['admin_id']]);
    echo "✅ Query executed successfully<br>";
    echo "Properties found: " . count($properties) . "<br>";
    
    if ($properties) {
        echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Type</th><th>Status</th><th>Units</th><th>Created</th></tr>";
        foreach ($properties as $prop) {
            echo "<tr>";
            echo "<td>{$prop['id']}</td>";
            echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
            echo "<td>{$prop['type']}</td>";
            echo "<td>{$prop['status']}</td>";
            echo "<td>{$prop['unit_count']}</td>";
            echo "<td>{$prop['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No properties found for admin<br>";
    }
} catch (Exception $e) {
    echo "❌ Query failed: " . $e->getMessage() . "<br>";
}

// Step 4: Test ViewManager data flow
echo "<h2>Step 4: ViewManager Data Flow Test</h2>";

require_once __DIR__ . '/config/bootstrap.php';

// Set data as PropertyController does
ViewManager::set('properties', $properties ?? []);
ViewManager::set('search', '');
ViewManager::set('type', '');
ViewManager::set('category', '');
ViewManager::set('status', '');

// Retrieve data as view does
$retrievedProperties = ViewManager::get('properties');
echo "✅ ViewManager data set and retrieved<br>";
echo "Properties in ViewManager: " . count($retrievedProperties ?? []) . "<br>";

// Step 5: Test URL routing
echo "<h2>Step 5: URL Routing Test</h2>";

$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    $routes = include $routesFile;
    
    // Check for admin properties route
    if (isset($routes['GET /admin/properties'])) {
        echo "✅ Admin properties route found: " . $routes['GET /admin/properties'] . "<br>";
    } else {
        echo "❌ Admin properties route not found<br>";
    }
    
    // Check for public properties route
    if (isset($routes['GET /properties'])) {
        echo "⚠️  Public properties route also exists: " . $routes['GET /properties'] . "<br>";
        echo "   This might cause conflicts - ensure you're using /admin/properties<br>";
    }
}

echo "<h2>Test Complete</h2>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>1. Login to admin: <a href='/admin/login' target='_blank'>/admin/login</a> (test@admin.com / password123)</li>";
echo "<li>2. Go to properties: <a href='/admin/properties' target='_blank'>/admin/properties</a></li>";
echo "<li>3. Create a new property and check if it appears</li>";
echo "</ul>";
?>
