<?php
// Simulate the exact PropertyController index method call
require_once 'config/config_simple.php';
require_once 'config/database.php';
require_once 'app/controllers/BaseController.php';
require_once 'app/controllers/PropertyController.php';

use App\Controllers\PropertyController;

try {
    echo "=== Simulating PropertyController@index ===\n\n";
    
    $controller = new PropertyController();
    
    // Test the index method directly
    echo "Calling PropertyController->index()...\n";
    
    // This should trigger the same logic as visiting /properties
    ob_start();
    $controller->index();
    $output = ob_get_clean();
    
    echo "Output length: " . strlen($output) . " characters\n";
    echo "First 500 characters:\n";
    echo substr($output, 0, 500) . "...\n\n";
    
    // Check if properties are in the output
    if (strpos($output, 'Test Property 13:17:24') !== false) {
        echo "✓ Most recent property found in output\n";
    } else {
        echo "✗ Most recent property NOT found in output\n";
    }
    
    if (strpos($output, 'No Properties Found') !== false) {
        echo "✗ Showing 'No Properties Found' message\n";
    } else {
        echo "✓ Not showing 'No Properties Found' message\n";
    }
    
    // Count property occurrences
    $propertyCount = substr_count($output, 'Test Property');
    echo "Property occurrences in output: $propertyCount\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
