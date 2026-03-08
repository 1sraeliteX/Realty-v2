<?php

// Supabase configuration
$supabaseUrl = "https://ducwcodegciekralkrqd.supabase.co";
$serviceKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImR1Y3djb2RlZ2NpZWtyYWxrcnFkIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3MjgzNjczNiwiZXhwIjoyMDg4NDEyNzM2fQ.VKZUKgEtkrJWhE1UlHzHNm_fIZe4gdrOGYfFyHlQ22Y";

// Tables that should exist
$expectedTables = [
    'admins',
    'sessions', 
    'properties',
    'units',
    'tenants',
    'payments',
    'invoices',
    'activities'
];

echo "=== Checking Database Tables Status ===\n\n";

// Check each table
foreach ($expectedTables as $table) {
    echo "Checking table: $table\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $supabaseUrl . "/rest/v1/$table?select=count&limit=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . $serviceKey,
        'Authorization: Bearer ' . $serviceKey,
        'Prefer: count=exact'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $count = json_decode($response, true);
        $rowCount = $count[0]['count'] ?? 0;
        echo "✅ Table exists - $rowCount records\n";
    } else {
        echo "❌ Table missing or inaccessible (HTTP $httpCode)\n";
    }
    echo "\n";
}

// Check sample data in key tables
echo "=== Sample Data Check ===\n";

// Check properties
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $supabaseUrl . "/rest/v1/properties?select=id,name,address,type,status&limit=3");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $serviceKey,
    'Authorization: Bearer ' . $serviceKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\nProperties (HTTP $httpCode):\n";
if ($httpCode === 200) {
    $properties = json_decode($response, true);
    if ($properties) {
        foreach ($properties as $prop) {
            echo "- {$prop['name']} ({$prop['type']}) - {$prop['status']}\n";
        }
    } else {
        echo "No properties found\n";
    }
} else {
    echo "Error accessing properties\n";
}

// Check units
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $supabaseUrl . "/rest/v1/units?select=unit_number,unit_type,status,property_id&limit=5");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $serviceKey,
    'Authorization: Bearer ' . $serviceKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\nUnits (HTTP $httpCode):\n";
if ($httpCode === 200) {
    $units = json_decode($response, true);
    if ($units) {
        foreach ($units as $unit) {
            echo "- Unit {$unit['unit_number']} ({$unit['unit_type']}) - {$unit['status']}\n";
        }
    } else {
        echo "No units found\n";
    }
} else {
    echo "Error accessing units\n";
}

echo "\n=== Database Check Complete ===\n";
?>
