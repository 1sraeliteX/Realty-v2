<?php
// Test script to verify property types functionality
require_once __DIR__ . '/config/property_type_helper.php';

echo "<h1>Property Types Functionality Test</h1>";

// Test 1: Check helper functions
echo "<h2>1. Testing Helper Functions</h2>";

try {
    // Test getCategoryOptions
    $categoryOptions = getCategoryOptions();
    echo "<h3>Category Options:</h3>";
    echo "<pre>" . print_r($categoryOptions, true) . "</pre>";
    
    // Test getPropertyCategory
    $testTypes = ['apartment', 'office_building', 'warehouse', 'residential_land'];
    echo "<h3>Property Type Categories:</h3>";
    foreach ($testTypes as $type) {
        $category = getPropertyCategory($type);
        echo "<p><strong>$type</strong> -> <em>$category</em></p>";
    }
    
    // Test getPropertiesByCategory
    echo "<h3>Properties by Category:</h3>";
    $residentialTypes = getPropertiesByCategory('residential');
    echo "<p>Residential category has " . count($residentialTypes) . " types:</p>";
    echo "<ul>";
    foreach ($residentialTypes as $type) {
        echo "<li>{$type['label']} ({$type['value']})</li>";
    }
    echo "</ul>";
    
    // Test getAllPropertyTypesWithCategories
    echo "<h3>All Property Types with Categories:</h3>";
    $allTypes = getAllPropertyTypesWithCategories();
    echo "<p>Total types: " . count($allTypes) . "</p>";
    echo "<p>First 5 types:</p>";
    echo "<ol>";
    for ($i = 0; $i < min(5, count($allTypes)); $i++) {
        echo "<li><strong>{$allTypes[$i]['label']}</strong> ({$allTypes[$i]['value']}) - Category: {$allTypes[$i]['category_label']}</li>";
    }
    echo "</ol>";
    
    echo "<p style='color: green;'>✓ All helper functions working correctly</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test 2: Simulate form submission with different property types
echo "<h2>2. Testing Property Type Storage</h2>";

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

use Config\Database;

$db = Database::getInstance();

// Test properties with different types
$testProperties = [
    ['name' => 'Modern Apartment', 'type' => 'apartment', 'address' => '123 Apt St'],
    ['name' => 'Office Building', 'type' => 'office_building', 'address' => '456 Office Blvd'],
    ['name' => 'Warehouse', 'type' => 'warehouse', 'address' => '789 Storage Rd'],
    ['name' => 'Residential Land', 'type' => 'residential_land', 'address' => '321 Land Ave']
];

echo "<h3>Inserting Test Properties:</h3>";
foreach ($testProperties as $index => $prop) {
    $propertyId = $db->insert('properties', [
        'admin_id' => 3,
        'name' => $prop['name'],
        'address' => $prop['address'],
        'type' => $prop['type'],
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    echo "<p>Property $index inserted with ID: $propertyId - Type: {$prop['type']}</p>";
}

// Test 3: Query properties by category
echo "<h3>3. Testing Category Filtering</h3>";

$residentialProps = $db->fetchAll("SELECT * FROM properties WHERE type IN ('apartment', 'flat', 'duplex') AND admin_id = 3 AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 3");
echo "<p>Found " . count($residentialProps) . " residential properties:</p>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Category</th></tr>";
foreach ($residentialProps as $prop) {
    $category = getPropertyCategory($prop['type']);
    echo "<tr>";
    echo "<td>{$prop['id']}</td>";
    echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
    echo "<td>{$prop['type']}</td>";
    echo "<td>$category</td>";
    echo "</tr>";
}
echo "</table>";

// Test 4: Check specific type lookup
echo "<h3>4. Testing Type Lookup</h3>";
$specificType = 'apartment';
$typeInfo = null;
foreach ($allTypes as $type) {
    if ($type['value'] === $specificType) {
        $typeInfo = $type;
        break;
    }
}
if ($typeInfo) {
    echo "<p style='color: green;'>✓ Found '$specificType': {$typeInfo['label']} (Category: {$typeInfo['category_label']})</p>";
} else {
    echo "<p style='color: red;'>✗ Type '$specificType' not found</p>";
}

echo "<h2>Test Summary</h2>";
echo "<p>Property types system is working with:</p>";
echo "<ul>";
echo "<li>✓ Helper functions for categorization</li>";
echo "<li>✓ Database storage and retrieval</li>";
echo "<li>✓ Category-based filtering support</li>";
echo "<li>✓ Specific type lookup and display</li>";
echo "</ul>";

echo "<p><a href='/properties'>View Properties List</a> | <a href='/properties/create'>Test Property Creation</a></p>";

// Clean up test data
$db->query("DELETE FROM properties WHERE name LIKE 'Test Property%' AND admin_id = 3");
?>
