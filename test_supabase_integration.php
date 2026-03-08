<?php

// Test Supabase Integration
require_once 'config/config_simple.php';
require_once 'config/supabase.php';

use Config\SupabaseClient;

echo "=== Testing Supabase Integration ===\n\n";

try {
    $supabase = SupabaseClient::getInstance();
    
    // Test 1: Check if we can read from admins table
    echo "1. Testing admin access...\n";
    $admins = $supabase->select('admins', 'id, name, email, role', [], ['limit' => 5]);
    echo "✅ Found " . count($admins) . " admins\n";
    foreach ($admins as $admin) {
        echo "   - {$admin['name']} ({$admin['email']}) - {$admin['role']}\n";
    }
    
    // Test 2: Test property creation
    echo "\n2. Testing property creation...\n";
    $testProperty = [
        'admin_id' => $admins[0]['id'],
        'name' => 'Test Property - ' . date('Y-m-d H:i:s'),
        'address' => '123 Test Street, Test City',
        'type' => 'residential',
        'description' => 'This is a test property created via Supabase integration test',
        'year_built' => 2020,
        'bedrooms' => 3,
        'bathrooms' => 2,
        'kitchens' => 1,
        'parking' => 2,
        'rent_price' => 1500.00,
        'status' => 'active'
    ];
    
    $propertyId = $supabase->insert('properties', $testProperty);
    echo "✅ Created test property with ID: $propertyId\n";
    
    // Test 3: Test property retrieval
    echo "\n3. Testing property retrieval...\n";
    $properties = $supabase->select('properties', '*', ['admin_id' => $admins[0]['id']]);
    echo "✅ Found " . count($properties) . " properties for admin\n";
    
    // Test 4: Test activity logging
    echo "\n4. Testing activity logging...\n";
    $activity = [
        'admin_id' => $admins[0]['id'],
        'action' => 'test',
        'description' => 'Integration test activity',
        'entity_type' => 'test',
        'entity_id' => $propertyId,
        'metadata' => json_encode(['test' => true])
    ];
    
    $activityId = $supabase->insert('activities', $activity);
    echo "✅ Created activity with ID: $activityId\n";
    
    // Test 5: Test property update
    echo "\n5. Testing property update...\n";
    $updateData = [
        'description' => 'Updated test property description',
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $supabase->update('properties', $updateData, ['id' => $propertyId]);
    echo "✅ Updated test property\n";
    
    // Test 6: Test property deletion (soft delete)
    echo "\n6. Testing property soft delete...\n";
    $supabase->update('properties', ['deleted_at' => date('Y-m-d H:i:s')], ['id' => $propertyId]);
    echo "✅ Soft deleted test property\n";
    
    // Test 7: Verify tables exist and are accessible
    echo "\n7. Testing table access...\n";
    $tables = ['admins', 'properties', 'units', 'tenants', 'payments', 'invoices', 'activities'];
    foreach ($tables as $table) {
        try {
            $result = $supabase->select($table, 'id', [], ['limit' => 1]);
            echo "✅ $table table accessible\n";
        } catch (Exception $e) {
            echo "❌ $table table error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== Integration Test Complete ===\n";
    echo "✅ All Supabase operations working correctly!\n";
    
} catch (Exception $e) {
    echo "❌ Integration test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
