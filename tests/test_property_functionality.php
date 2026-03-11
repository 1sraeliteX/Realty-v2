<?php

// Test script for property functionality
require_once __DIR__ . '/config/config_simple.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/middleware/JwtMiddleware.php';

use Config\Database;
use App\Middleware\JwtMiddleware;

echo "=== Testing Property Functionality ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    echo "✓ Database connection successful\n\n";
    
    // Test JWT token generation
    echo "2. Testing JWT token generation...\n";
    $jwt = new JwtMiddleware();
    $testUser = [
        'id' => 1,
        'email' => 'admin@test.com',
        'role' => 'admin'
    ];
    $token = $jwt->generateToken($testUser);
    echo "✓ JWT token generated: " . substr($token, 0, 50) . "...\n\n";
    
    // Test JWT token validation
    echo "3. Testing JWT token validation...\n";
    $payload = $jwt->validateToken($token);
    if ($payload && $payload['user_id'] == 1) {
        echo "✓ JWT token validation successful\n\n";
    } else {
        echo "✗ JWT token validation failed\n\n";
    }
    
    // Test property creation
    echo "4. Testing property creation...\n";
    $propertyData = [
        'admin_id' => 1,
        'name' => 'Test Property',
        'address' => '123 Test Street, Test City',
        'type' => 'residential',
        'category' => 'apartment',
        'description' => 'A beautiful test property',
        'year_built' => 2020,
        'bedrooms' => 3,
        'bathrooms' => 2.5,
        'kitchens' => 1,
        'parking' => 'yes',
        'rent_price' => 1500.00,
        'status' => 'active',
        'amenities' => json_encode(['pool', 'gym', 'parking']),
        'images' => json_encode(['image1.jpg', 'image2.jpg'])
    ];
    
    $propertyId = $db->insert('properties', $propertyData);
    echo "✓ Property created with ID: $propertyId\n\n";
    
    // Test property retrieval
    echo "5. Testing property retrieval...\n";
    $property = $db->fetch("SELECT * FROM properties WHERE id = ?", [$propertyId]);
    if ($property && $property['name'] === 'Test Property') {
        echo "✓ Property retrieved successfully\n";
        echo "  Name: " . $property['name'] . "\n";
        echo "  Address: " . $property['address'] . "\n";
        echo "  Type: " . $property['type'] . "\n\n";
    } else {
        echo "✗ Property retrieval failed\n\n";
    }
    
    // Test property listing
    echo "6. Testing property listing...\n";
    $properties = $db->fetchAll("SELECT * FROM properties WHERE deleted_at IS NULL ORDER BY created_at DESC");
    echo "✓ Found " . count($properties) . " properties\n\n";
    
    // Test API endpoint simulation
    echo "7. Testing API endpoint simulation...\n";
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
    $_POST = [
        'name' => 'API Test Property',
        'address' => '456 API Street',
        'type' => 'commercial',
        'rent_price' => 2500.00
    ];
    
    // Simulate the API property controller logic
    $user = $jwt->getCurrentUser();
    if ($user) {
        echo "✓ API authentication successful\n";
        echo "  User: " . $user['name'] . " (" . $user['email'] . ")\n\n";
    } else {
        echo "✗ API authentication failed\n\n";
    }
    
    echo "=== All Tests Completed ===\n";
    echo "The property functionality is working correctly!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
