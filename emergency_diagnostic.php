<?php
// Emergency diagnostic to find why property display isn't working
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>🚨 EMERGENCY DIAGNOSTIC</h1>";

session_start();
$adminId = $_SESSION['admin_id'] ?? null;

if (!$adminId) {
    echo "<p>❌ Not logged in as admin</p>";
    exit;
}

echo "<p>✅ Admin ID: $adminId</p>";

try {
    $db = \Config\Database::getConnection();
    
    echo "<h2>🔍 STEP 1: Check if property was actually saved</h2>";
    
    // Check most recent property
    $recent = $db->query("SELECT id, name, admin_id, status, deleted_at, created_at FROM properties WHERE admin_id = $adminId ORDER BY created_at DESC LIMIT 1")->fetch_assoc();
    
    if ($recent) {
        echo "<p>✅ Found property in database:</p>";
        echo "<ul>";
        echo "<li>ID: " . $recent['id'] . "</li>";
        echo "<li>Name: " . htmlspecialchars($recent['name']) . "</li>";
        echo "<li>Status: " . $recent['status'] . "</li>";
        echo "<li>Deleted: " . ($recent['deleted_at'] ?? 'NULL') . "</li>";
        echo "<li>Created: " . $recent['created_at'] . "</li>";
        echo "</ul>";
        
        if ($recent['deleted_at'] !== null) {
            echo "<p>❌ PROPERTY IS MARKED AS DELETED - This is the problem!</p>";
        }
    } else {
        echo "<p>❌ No properties found in database</p>";
        echo "<p>Property creation is failing despite success message</p>";
    }
    
    echo "<h2>🎮 STEP 2: Test PropertyController query exactly</h2>";
    
    // Exact same query as PropertyController::index()
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE p.admin_id = ? AND p.deleted_at IS NULL
            ORDER BY p.created_at DESC";
    
    echo "<p>SQL: " . htmlspecialchars($sql) . "</p>";
    echo "<p>Params: [$adminId]</p>";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $adminId);
    $stmt->execute();
    $properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo "<p>Query results: " . count($properties) . " properties</p>";
    
    if (!empty($properties)) {
        echo "<p>✅ Controller query finds properties</p>";
        
        // Show what should be displayed
        echo "<h3>Properties that SHOULD be displayed:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Status</th></tr>";
        foreach ($properties as $prop) {
            echo "<tr>";
            echo "<td>" . $prop['id'] . "</td>";
            echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
            echo "<td>" . $prop['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h2>🔍 STEP 3: Check what view is actually receiving</h2>";
        
        // Simulate ViewManager data
        \ViewManager::set('properties', $properties);
        \ViewManager::set('pagination', ['total' => count($properties)]);
        
        // Check what view gets
        $viewData = ViewManager::get('properties');
        echo "<p>ViewManager has: " . count($viewData ?? []) . " properties</p>";
        
        if (empty($viewData)) {
            echo "<p>❌ ViewManager is empty - data not being set correctly</p>";
        } else {
            echo "<p>✅ ViewManager has data</p>";
        }
        
        echo "<h2>🎯 DIAGNOSIS:</h2>";
        echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";
        echo "<p><strong>Properties exist in database and controller query works.</strong></p>";
        echo "<p><strong>The issue is in the data flow from controller to view.</strong></p>";
        echo "</div>";
        
    } else {
        echo "<p>❌ Controller query finds no properties</p>";
        echo "<p>This means either:</p>";
        echo "<ul>";
        echo "<li>Properties are marked as deleted</li>";
        echo "<li>Wrong admin_id in properties</li>";
        echo "<li>Query logic issue</li>";
        echo "</ul>";
    }
    
    echo "<h2>🛠️ IMMEDIATE FIXES TO TRY:</h2>";
    echo "<ol>";
    echo "<li><strong>Clear Browser Cache:</strong> Ctrl+F5</li>";
    echo "<li><strong>Check URL:</strong> Must be /admin/properties not /properties</li>";
    echo "<li><strong>Check Session:</strong> Make sure admin_id is correct</li>";
    echo "<li><strong>Manual URL:</strong> Type /admin/properties directly</li>";
    echo "<li><strong>Check Error Logs:</strong> Look for PHP errors</li>";
    echo "</ol>";
    
    // Check if there's a routing issue
    echo "<h2>🌐 URL Routing Check</h2>";
    echo "<p>Current URL: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>";
    echo "<p>Should be: /admin/properties</p>";
    
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/properties') === 0) {
        echo "<p>✅ URL is correct</p>";
    } else {
        echo "<p>❌ URL is wrong - this could be the issue!</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<script>";
echo "console.log('Emergency diagnostic completed');";
echo "console.log('Admin ID:', '$adminId');";
echo "console.log('Properties found:', " . count($properties ?? []) . ");";
echo "</script>";
?>
