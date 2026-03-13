<?php
// Emergency debug for property display issue
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>🚨 Emergency Debug: Property Saved But Not Displaying</h1>";

session_start();
$adminId = $_SESSION['admin_id'] ?? null;

if (!$adminId) {
    echo "<p>❌ Not logged in as admin</p>";
    exit;
}

echo "<p>✅ Admin ID: $adminId</p>";

try {
    $db = \Config\Database::getConnection();
    
    // Check if property was actually saved
    echo "<h2>🔍 Database Check</h2>";
    
    // Check most recent property
    $recent = $db->query("SELECT id, name, admin_id, status, deleted_at, created_at FROM properties WHERE admin_id = $adminId ORDER BY created_at DESC LIMIT 1")->fetch_assoc();
    
    if ($recent) {
        echo "<p>✅ Most recent property found:</p>";
        echo "<ul>";
        echo "<li><strong>ID:</strong> " . $recent['id'] . "</li>";
        echo "<li><strong>Name:</strong> " . htmlspecialchars($recent['name']) . "</li>";
        echo "<li><strong>Status:</strong> " . $recent['status'] . "</li>";
        echo "<li><strong>Deleted:</strong> " . ($recent['deleted_at'] ?? 'NULL') . "</li>";
        echo "<li><strong>Created:</strong> " . $recent['created_at'] . "</li>";
        echo "</ul>";
        
        // Check if it should be displayed
        if ($recent['deleted_at'] === null) {
            echo "<p>✅ Property should be displayed (not deleted)</p>";
        } else {
            echo "<p>❌ Property is marked as deleted - won't display</p>";
        }
    } else {
        echo "<p>❌ No properties found for admin</p>";
    }
    
    // Test the exact PropertyController query
    echo "<h2>🎮 PropertyController Query Test</h2>";
    
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE p.admin_id = ? AND p.deleted_at IS NULL
            ORDER BY p.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $adminId);
    $stmt->execute();
    $properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo "<p><strong>PropertyController query results:</strong> " . count($properties) . " properties</p>";
    
    if (!empty($properties)) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Units</th></tr>";
        foreach ($properties as $prop) {
            echo "<tr>";
            echo "<td>" . $prop['id'] . "</td>";
            echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
            echo "<td>" . $prop['status'] . "</td>";
            echo "<td>" . $prop['unit_count'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>📤 Data That Should Be Sent to View:</h3>";
        echo "<div style='background: #d4edda; padding: 10px; border-left: 4px solid #28a745;'>";
        echo "<code>\$properties = [" . count($properties) . " items];</code><br>";
        echo "<code>\$pagination = ['total' => " . count($properties) . "];</code><br>";
        echo "</div>";
        
    } else {
        echo "<p>❌ PropertyController query returned no results</p>";
    }
    
    // Check what the view is actually receiving
    echo "<h2>👁️ View Simulation</h2>";
    echo "<p>Simulating what the view should receive...</p>";
    
    // This simulates the renderView call
    $viewData = [
        'properties' => $properties,
        'pagination' => [
            'current_page' => 1,
            'per_page' => 10,
            'total' => count($properties),
            'total_pages' => ceil(count($properties) / 10)
        ],
        'search' => '',
        'type' => '',
        'category' => '',
        'status' => ''
    ];
    
    echo "<div style='background: #e7f3ff; padding: 10px; border-left: 4px solid #007bff;'>";
    echo "<strong>View receives:</strong><br>";
    foreach ($viewData as $key => $value) {
        if (is_array($value)) {
            echo "<code>\$$key = [" . count($value) . " items];</code><br>";
        } else {
            echo "<code>\$$key = '" . htmlspecialchars($value) . "';</code><br>";
        }
    }
    echo "</div>";
    
    // Diagnosis
    echo "<h2>🎯 Diagnosis</h2>";
    
    if (!empty($properties)) {
        echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";
        echo "<h3>⚠️ ISSUE IDENTIFIED:</h3>";
        echo "<p>Properties exist in database and PropertyController query works, but view is not receiving the data.</p>";
        echo "<p><strong>Possible causes:</strong></p>";
        echo "<ol>";
        echo "<li>PropertyController renderView call is not passing data correctly</li>";
        echo "<li>View is not using the passed data</li>";
        echo "<li>There's a caching issue</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<h3>🛠️ IMMEDIATE FIX:</h3>";
        echo "<ol>";
        echo "<li>Clear browser cache: Ctrl+F5</li>";
        echo "<li>Check PropertyController renderView call</li>";
        echo "<li>Verify view is using passed parameters</li>";
        echo "</ol>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545;'>";
        echo "<h3>❌ CRITICAL ISSUE:</h3>";
        echo "<p>PropertyController query is not returning results even though property exists.</p>";
        echo "<p>This suggests a problem with the WHERE clause or query logic.</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<script>";
echo "console.log('Emergency debug completed');";
echo "console.log('Admin ID:', '$adminId');";
echo "</script>";
?>
