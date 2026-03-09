<?php
// Test script to simulate form submission
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/controllers/BaseController.php';
require_once __DIR__ . '/app/controllers/PropertyController.php';

use Config\Database;
use App\Controllers\PropertyController;

echo "<h1>Property Form Submission Test</h1>";

// Simulate POST data similar to what the JavaScript sends
$_POST = [
    'name' => 'Test Property from Form',
    'address' => '456 Form Test Avenue, Test City',
    'type' => 'residential',
    'rent_price' => '15000.00',
    'year_built' => '2023',
    'bedrooms' => '3',
    'bathrooms' => '2',
    'kitchens' => '1',
    'parking' => 'yes',
    'water_availability' => 'in_building',
    'description' => 'This is a test property submitted via simulated form',
    'category' => 'apartment',
    'status' => 'active',
    'amenities' => ''  // Empty string to test without amenities
];

// Simulate file upload (empty for this test)
$_FILES = [
    'images' => [
        'name' => [],
        'type' => [],
        'tmp_name' => [],
        'error' => [],
        'size' => []
    ]
];

// Set server variables to simulate AJAX request
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/properties';

// Mock session
@session_start();

try {
    // Create controller instance
    $controller = new PropertyController();
    
    echo "<h2>1. Controller Created Successfully</h2>";
    
    // Call the store method
    ob_start(); // Capture output
    $controller->store();
    $output = ob_get_clean();
    
    echo "<h2>2. Store Method Executed</h2>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    // Check if property was created
    $db = Database::getInstance();
    $latestProperty = $db->fetchAll("SELECT * FROM properties WHERE name = 'Test Property from Form' AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 1");
    
    if (!empty($latestProperty)) {
        $property = $latestProperty[0];
        echo "<h2 style='color: green;'>3. Property Created Successfully!</h2>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        echo "<tr><td>ID</td><td>{$property['id']}</td></tr>";
        echo "<tr><td>Name</td><td>" . htmlspecialchars($property['name']) . "</td></tr>";
        echo "<tr><td>Address</td><td>" . htmlspecialchars($property['address']) . "</td></tr>";
        echo "<tr><td>Type</td><td>{$property['type']}</td></tr>";
        echo "<tr><td>Bedrooms</td><td>{$property['bedrooms']}</td></tr>";
        echo "<tr><td>Bathrooms</td><td>{$property['bathrooms']}</td></tr>";
        echo "<tr><td>Kitchens</td><td>{$property['kitchens']}</td></tr>";
        echo "<tr><td>Parking</td><td>{$property['parking']}</td></tr>";
        echo "<tr><td>Rent Price</td><td>{$property['rent_price']}</td></tr>";
        echo "<tr><td>Status</td><td>{$property['status']}</td></tr>";
        echo "<tr><td>Created At</td><td>{$property['created_at']}</td></tr>";
        echo "</table>";
        
        // Test the index method to see if it shows up
        echo "<h2>4. Testing Index Method</h2>";
        $_GET = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        
        ob_start();
        $controller->index();
        $indexOutput = ob_get_clean();
        
        if (strpos($indexOutput, 'Test Property from Form') !== false) {
            echo "<p style='color: green;'>✓ Property appears in index view</p>";
        } else {
            echo "<p style='color: red;'>✗ Property not found in index view</p>";
        }
        
        // Clean up - delete test property
        $db->update('properties', ['deleted_at' => date('Y-m-d H:i:s')], 'id = ?', [$property['id']]);
        echo "<p style='color: blue;'>♻ Test property cleaned up</p>";
        
    } else {
        echo "<h2 style='color: red;'>3. Property Creation Failed</h2>";
        echo "<p>No property found with the expected name in database.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error Occurred</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h2>Test Summary</h2>";
echo "<p><a href='/properties/create'>Try the actual form</a></p>";
echo "<p><a href='/properties'>View properties list</a></p>";
?>
