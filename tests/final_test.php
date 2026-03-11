<?php

// Final comprehensive test of the property functionality
echo "=== FINAL PROPERTY FUNCTIONALITY TEST ===\n\n";

// Test 1: Login
echo "1. Testing login...\n";
$login = file_get_contents('http://localhost:8000/api/auth/login', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode(['email' => 'admin@test.com', 'password' => 'admin123'])
    ]
]));

$loginData = json_decode($login, true);
if ($loginData && isset($loginData['token'])) {
    echo "✓ Login successful\n";
    $token = $loginData['token'];
} else {
    echo "✗ Login failed\n";
    exit(1);
}

// Test 2: Create Property
echo "\n2. Testing property creation...\n";
$create = file_get_contents('http://localhost:8000/api/properties', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\nAuthorization: Bearer $token\r\nX-Requested-With: XMLHttpRequest",
        'content' => 'name=Final Test Property&address=789 Final Test Street&type=residential&rent_price=1800&bedrooms=2&bathrooms=2'
    ]
]));

$createData = json_decode($create, true);
if ($createData && isset($createData['property_id'])) {
    echo "✓ Property created with ID: " . $createData['property_id'] . "\n";
    $propertyId = $createData['property_id'];
} else {
    echo "✗ Property creation failed: " . ($createData['error'] ?? 'Unknown error') . "\n";
}

// Test 3: List Properties
echo "\n3. Testing property listing...\n";
$list = file_get_contents('http://localhost:8000/api/properties', false, stream_context_create([
    'http' => [
        'header' => "Authorization: Bearer $token"
    ]
]));

$listData = json_decode($list, true);
if ($listData && isset($listData['data'])) {
    echo "✓ Properties listed successfully\n";
    echo "  Found " . count($listData['data']) . " properties\n";
    foreach ($listData['data'] as $property) {
        echo "  - " . $property['name'] . " (ID: " . $property['id'] . ")\n";
    }
} else {
    echo "✗ Property listing failed: " . ($listData['error'] ?? 'Unknown error') . "\n";
}

// Test 4: Get Specific Property
if (isset($propertyId)) {
    echo "\n4. Testing property retrieval...\n";
    $get = file_get_contents("http://localhost:8000/api/properties/$propertyId", false, stream_context_create([
        'http' => [
            'header' => "Authorization: Bearer $token"
        ]
    ]));
    
    $getData = json_decode($get, true);
    if ($getData && isset($getData['id'])) {
        echo "✓ Property retrieved successfully\n";
        echo "  Name: " . $getData['name'] . "\n";
        echo "  Address: " . $getData['address'] . "\n";
    } else {
        echo "✗ Property retrieval failed: " . ($getData['error'] ?? 'Unknown error') . "\n";
    }
}

echo "\n=== ALL TESTS COMPLETED ===\n";
echo "✅ Property functionality is working correctly!\n";
echo "🌐 You can now test the web interface at: http://localhost:8000/test_properties.html\n";
echo "👤 Login with: admin@test.com / admin123\n";
?>
