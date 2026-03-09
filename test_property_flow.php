<?php
// Simple test script to check property creation flow
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

use Config\Database;

// Initialize database connection
$db = Database::getInstance();

echo "<h1>Property Creation Flow Test</h1>";

// Test 1: Check database connection
echo "<h2>1. Database Connection</h2>";
try {
    $result = $db->fetch("SELECT 1 as test");
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Check if properties table exists and has structure
echo "<h2>2. Properties Table Structure</h2>";
try {
    $columns = $db->fetchAll("SHOW COLUMNS FROM properties");
    echo "<p style='color: green;'>✓ Properties table exists with " . count($columns) . " columns</p>";
    echo "<ul>";
    foreach ($columns as $col) {
        echo "<li>{$col['Field']} ({$col['Type']})</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Properties table check failed: " . $e->getMessage() . "</p>";
}

// Test 3: Check existing properties
echo "<h2>3. Existing Properties</h2>";
try {
    $properties = $db->fetchAll("SELECT * FROM properties WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5");
    echo "<p>Found " . count($properties) . " properties:</p>";
    if (empty($properties)) {
        echo "<p style='color: orange;'>No properties found - this is expected for a new system</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Address</th><th>Type</th><th>Status</th><th>Created</th></tr>";
        foreach ($properties as $prop) {
            echo "<tr>";
            echo "<td>{$prop['id']}</td>";
            echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($prop['address'], 0, 50)) . "...</td>";
            echo "<td>{$prop['type']}</td>";
            echo "<td>{$prop['status']}</td>";
            echo "<td>{$prop['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Query failed: " . $e->getMessage() . "</p>";
}

// Test 4: Test property insertion
echo "<h2>4. Test Property Insertion</h2>";
try {
    $testProperty = [
        'admin_id' => 3, // Test admin ID from BaseController
        'name' => 'Test Property ' . date('Y-m-d H:i:s'),
        'address' => '123 Test Street, Test City, Test Country',
        'type' => 'residential',
        'category' => 'apartment',
        'description' => 'This is a test property created by the test script',
        'year_built' => 2023,
        'bedrooms' => 2,
        'bathrooms' => 1,
        'kitchens' => 1,
        'parking' => 1,
        'rent_price' => 12000.00,
        'status' => 'active',
        'amenities' => json_encode(['Security', 'Parking', 'Water']),
        'images' => json_encode(['test1.jpg', 'test2.jpg'])
    ];
    
    $propertyId = $db->insert('properties', $testProperty);
    echo "<p style='color: green;'>✓ Test property inserted with ID: $propertyId</p>";
    
    // Verify the property was inserted
    $insertedProperty = $db->fetch("SELECT * FROM properties WHERE id = ?", [$propertyId]);
    if ($insertedProperty) {
        echo "<p style='color: green;'>✓ Property verification successful</p>";
        echo "<p>Name: " . htmlspecialchars($insertedProperty['name']) . "</p>";
        echo "<p>Address: " . htmlspecialchars($insertedProperty['address']) . "</p>";
        echo "<p>Type: {$insertedProperty['type']}</p>";
        echo "<p>Status: {$insertedProperty['status']}</p>";
    } else {
        echo "<p style='color: red;'>✗ Property verification failed</p>";
    }
    
    // Clean up - delete the test property
    $db->update('properties', ['deleted_at' => date('Y-m-d H:i:s')], 'id = ?', [$propertyId]);
    echo "<p style='color: blue;'>♻ Test property cleaned up</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Property insertion failed: " . $e->getMessage() . "</p>";
}

// Test 5: Check admin user
echo "<h2>5. Test Admin User</h2>";
try {
    $admin = $db->fetch("SELECT * FROM admins WHERE id = 3 AND deleted_at IS NULL");
    if ($admin) {
        echo "<p style='color: green;'>✓ Test admin found: " . htmlspecialchars($admin['name']) . " ({$admin['email']})</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Test admin (ID: 3) not found. You may need to create an admin first.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Admin check failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Test Summary</h2>";
echo "<p>If all tests show green checkmarks, the property creation system should work correctly.</p>";
echo "<p><a href='/properties/create'>Go to Property Creation Form</a></p>";
echo "<p><a href='/properties'>Go to Properties List</a></p>";
?>
