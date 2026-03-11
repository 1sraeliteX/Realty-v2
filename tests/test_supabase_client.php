<?php

require_once 'config/supabase.php';

use Config\SupabaseClient;

echo "=== Testing Supabase Client ===\n";

try {
    $client = SupabaseClient::getInstance();
    
    // Test select without filters first
    echo "Testing without filters...\n";
    $admins = $client->select('admins', 'id,name,email');
    echo "✅ Found " . count($admins) . " admins\n";
    
    if (!empty($admins)) {
        foreach ($admins as $admin) {
            echo "   - " . $admin['name'] . " (" . $admin['email'] . ") - " . $admin['id'] . "\n";
        }
    }
    
    // Test with limit
    echo "\nTesting with limit...\n";
    $limitedAdmins = $client->select('admins', 'id,name,email', [], ['limit' => 1]);
    echo "✅ Limited to " . count($limitedAdmins) . " admins\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
