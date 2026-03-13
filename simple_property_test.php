<?php
// Simple test to bypass all filtering and test basic property display
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>🧪 Simple Property Test (No Filtering)</h1>";

session_start();
$adminId = $_SESSION['admin_id'] ?? null;

if (!$adminId) {
    echo "<p>❌ Please <a href='/admin/login'>login as admin</a></p>";
    exit;
}

echo "<p>✅ Admin ID: $adminId</p>";

try {
    $db = \Config\Database::getConnection();
    
    // Simple query - no filtering, no joins, just basic properties
    echo "<h2>🔍 Simple Database Query</h2>";
    
    $simpleSql = "SELECT id, name, address, status, created_at 
                  FROM properties 
                  WHERE admin_id = $adminId AND deleted_at IS NULL 
                  ORDER BY created_at DESC";
    
    echo "<p>Query: " . htmlspecialchars($simpleSql) . "</p>";
    
    $result = $db->query($simpleSql);
    $properties = $result->fetch_all(MYSQLI_ASSOC);
    
    echo "<p>Simple query results: " . count($properties) . " properties</p>";
    
    if (!empty($properties)) {
        echo "<h3>✅ Properties Found:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Address</th><th>Status</th><th>Created</th></tr>";
        foreach ($properties as $prop) {
            echo "<tr>";
            echo "<td>" . $prop['id'] . "</td>";
            echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
            echo "<td>" . htmlspecialchars($prop['address'] ?? 'No address') . "</td>";
            echo "<td>" . $prop['status'] . "</td>";
            echo "<td>" . $prop['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h2>🎯 Test Data Flow</h2>";
        
        // Set data exactly as PropertyController should
        $resultData = [
            'data' => $properties,
            'pagination' => [
                'current_page' => 1,
                'per_page' => 10,
                'total' => count($properties),
                'total_pages' => ceil(count($properties) / 10)
            ]
        ];
        
        \ViewManager::set('properties', $resultData['data']);
        \ViewManager::set('pagination', $resultData['pagination']);
        
        echo "<p>✅ Data set in ViewManager</p>";
        
        // Test what view gets
        $viewData = ViewManager::get('properties');
        echo "<p>View will get: " . count($viewData ?? []) . " properties</p>";
        
        if (!empty($viewData)) {
            echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
            echo "<h3>✅ BASIC DATA FLOW WORKS!</h3>";
            echo "<p>The issue is likely in the PropertyController filtering logic.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545;'>";
            echo "<h3>❌ Even basic data flow fails</h3>";
            echo "<p>There's a fundamental issue with ViewManager or data passing.</p>";
            echo "</div>";
        }
        
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545;'>";
        echo "<h3>❌ No Properties Found Even with Simple Query</h3>";
        echo "<p>This means either:</p>";
        echo "<ul>";
        echo "<li>No properties exist for this admin</li>";
        echo "<li>All properties are marked as deleted</li>";
        echo "<li>Wrong admin_id in session</li>";
        echo "</ul>";
        echo "</div>";
        
        // Check if properties exist for other admins
        $otherProps = $db->query("SELECT COUNT(*) as count FROM properties WHERE deleted_at IS NULL")->fetch_assoc();
        echo "<p>Properties in database (all admins): " . $otherProps['count'] . "</p>";
        
        // Check session admin
        $adminInfo = $db->query("SELECT email FROM admins WHERE id = $adminId")->fetch_assoc();
        echo "<p>Current admin email: " . ($adminInfo['email'] ?? 'Unknown') . "</p>";
    }
    
    echo "<h2>🛠️ Next Steps</h2>";
    echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff;'>";
    echo "<h3>If basic test works:</h3>";
    echo "<ol>";
    echo "<li>The issue is in PropertyController filtering logic</li>";
    echo "<li>Check category/type filtering that might be too restrictive</li>";
    echo "<li>Check if property-type-helper is loading correctly</li>";
    echo "</ol>";
    
    echo "<h3>If basic test fails:</h3>";
    echo "<ol>";
    echo "<li>No properties exist for this admin account</li>";
    echo "<li>Property creation is failing despite success message</li>";
    echo "<li>Session admin_id doesn't match property admin_id</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
