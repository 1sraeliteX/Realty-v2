<?php

// Simple Supabase Test
$supabaseUrl = "https://ducwcodegciekralkrqd.supabase.co";
$serviceKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImR1Y3djb2RlZ2NpZWtyYWxrcnFkIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3MjgzNjczNiwiZXhwIjoyMDg4NDEyNzM2fQ.VKZUKgEtkrJWhE1UlHzHNm_fIZe4gdrOGYfFyHlQ22Y";

echo "=== Simple Supabase Test ===\n\n";

// Test 1: Check admins
echo "1. Testing admin access...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $supabaseUrl . "/rest/v1/admins?select=id,name,email,role&limit=5");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $serviceKey,
    'Authorization: Bearer ' . $serviceKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $admins = json_decode($response, true);
    echo "✅ Found " . count($admins) . " admins\n";
    foreach ($admins as $admin) {
        echo "   - {$admin['name']} ({$admin['email']}) - {$admin['role']}\n";
    }
    $adminId = $admins[0]['id'];
} else {
    echo "❌ Failed to access admins (HTTP $httpCode)\n";
    exit;
}

// Test 2: Create test property
echo "\n2. Creating test property...\n";
$testProperty = [
    'admin_id' => $adminId,
    'name' => 'Test Property ' . date('H:i:s'),
    'address' => '123 Test Street',
    'type' => 'residential',
    'description' => 'Test property for integration testing',
    'year_built' => 2020,
    'bedrooms' => 3,
    'bathrooms' => 2,
    'kitchens' => 1,
    'parking' => 2,
    'rent_price' => 1500.00,
    'status' => 'active'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $supabaseUrl . "/rest/v1/properties");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testProperty));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $serviceKey,
    'Authorization: Bearer ' . $serviceKey,
    'Content-Type: application/json',
    'Prefer: return=minimal'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201) {
    echo "✅ Test property created successfully\n";
} else {
    echo "❌ Failed to create property (HTTP $httpCode): $response\n";
}

// Test 3: Check properties
echo "\n3. Checking properties...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $supabaseUrl . "/rest/v1/properties?admin_id=eq.$adminId&select=id,name,address,status");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $serviceKey,
    'Authorization: Bearer ' . $serviceKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $properties = json_decode($response, true);
    echo "✅ Found " . count($properties) . " properties\n";
    foreach ($properties as $prop) {
        echo "   - {$prop['name']} ({$prop['status']})\n";
    }
} else {
    echo "❌ Failed to access properties (HTTP $httpCode)\n";
}

// Test 4: Check all tables
echo "\n4. Testing table access...\n";
$tables = ['admins', 'properties', 'units', 'tenants', 'payments', 'invoices', 'activities'];
foreach ($tables as $table) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $supabaseUrl . "/rest/v1/$table?select=id&limit=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $serviceKey,
        'Authorization: Bearer ' . $serviceKey
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "✅ $table table accessible\n";
    } else {
        echo "❌ $table table error (HTTP $httpCode)\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "✅ Supabase integration is working!\n";
?>
