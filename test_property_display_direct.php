<?php
// Direct test of property display without routing
require_once 'config/config_simple.php';
require_once 'config/database.php';
require_once 'app/controllers/BaseController.php';
require_once 'app/controllers/PropertyController.php';

use App\Controllers\PropertyController;

// Start session to avoid headers issues
session_start();

try {
    echo "<!DOCTYPE html>\n";
    echo "<html>\n";
    echo "<head>\n";
    echo "    <title>Property Display Test</title>\n";
    echo "    <script src='https://cdn.tailwindcss.com'></script>\n";
    echo "</head>\n";
    echo "<body class='bg-gray-100 p-8'>\n";
    echo "    <div class='max-w-6xl mx-auto'>\n";
    echo "        <h1 class='text-3xl font-bold mb-6'>Property Display Test</h1>\n";
    echo "        <div class='bg-white rounded-lg shadow p-6'>\n";
    
    // Test the database directly first
    $db = \Config\Database::getInstance();
    $properties = $db->fetchAll(
        "SELECT p.*, 
                (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
         FROM properties p 
         WHERE p.admin_id = 1 AND p.deleted_at IS NULL 
         ORDER BY p.created_at DESC 
         LIMIT 5"
    );
    
    echo "<h2 class='text-xl font-semibold mb-4'>Direct Database Query Results:</h2>\n";
    echo "<div class='mb-6'>\n";
    echo "<p class='text-sm text-gray-600 mb-2'>Found " . count($properties) . " properties for Admin ID 1</p>\n";
    
    if (!empty($properties)) {
        echo "<table class='min-w-full divide-y divide-gray-200'>\n";
        echo "<thead class='bg-gray-50'>\n";
        echo "<tr><th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase'>Property</th><th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase'>Type</th><th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase'>Units</th></tr>\n";
        echo "</thead>\n";
        echo "<tbody class='bg-white divide-y divide-gray-200'>\n";
        
        foreach ($properties as $property) {
            echo "<tr>\n";
            echo "<td class='px-6 py-4'>\n";
            echo "<div class='text-sm font-medium text-gray-900'>" . htmlspecialchars($property['name']) . "</div>\n";
            echo "<div class='text-sm text-gray-500'>" . htmlspecialchars(substr($property['address'], 0, 50)) . "...</div>\n";
            echo "</td>\n";
            echo "<td class='px-6 py-4'>\n";
            echo "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800'>" . ucfirst($property['type']) . "</span>\n";
            echo "</td>\n";
            echo "<td class='px-6 py-4 text-sm text-gray-900'>\n";
            echo $property['unit_count'] . " units (" . $property['occupied_units'] . " occupied)\n";
            echo "</td>\n";
            echo "</tr>\n";
        }
        
        echo "</tbody>\n</table>\n";
    } else {
        echo "<p class='text-gray-500'>No properties found</p>\n";
    }
    
    echo "</div>\n";
    
    // Now test the PropertyController
    echo "<h2 class='text-xl font-semibold mb-4 mt-8'>PropertyController Output:</h2>\n";
    echo "<div class='border-t pt-4'>\n";
    
    $controller = new PropertyController();
    
    // Capture the controller output
    ob_start();
    try {
        $controller->index();
        $controllerOutput = ob_get_clean();
        
        // Extract just the properties table from the full HTML
        $dom = new DOMDocument();
        @$dom->loadHTML($controllerOutput);
        $xpath = new DOMXPath($dom);
        
        // Find the properties table
        $tables = $xpath->query("//table[contains(@class, 'divide-y')]");
        if ($tables->length > 0) {
            $table = $tables->item(0);
            echo "<p class='text-sm text-green-600 mb-2'>✓ PropertyController generated table successfully</p>\n";
            echo $dom->saveHTML($table);
        } else {
            echo "<p class='text-sm text-red-600'>✗ No properties table found in controller output</p>\n";
        }
        
    } catch (Exception $e) {
        ob_get_clean();
        echo "<p class='text-sm text-red-600'>Controller error: " . $e->getMessage() . "</p>\n";
    }
    
    echo "</div>\n";
    
    echo "<h2 class='text-xl font-semibold mb-4 mt-8'>Troubleshooting Links:</h2>\n";
    echo "<div class='space-y-2'>\n";
    echo "<a href='/admin/properties' class='block text-blue-600 hover:underline'>→ /admin/properties</a>\n";
    echo "<a href='/properties' class='block text-blue-600 hover:underline'>→ /properties</a>\n";
    echo "<a href='/admin/login' class='block text-blue-600 hover:underline'>→ /admin/login (if needed)</a>\n";
    echo "</div>\n";
    
    echo "</div>\n";
    echo "</div>\n";
    echo "</body>\n";
    echo "</html>\n";
    
} catch (Exception $e) {
    echo "<h1 class='text-red-600'>Error: " . $e->getMessage() . "</h1>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}
?>
