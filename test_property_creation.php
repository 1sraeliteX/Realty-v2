<?php
// Test script to verify property creation and display
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>🧪 Test Property Creation & Display</h1>";

// Get admin session
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo "<p>❌ Please <a href='/admin/login'>login first</a></p>";
    exit;
}

$adminId = $_SESSION['admin_id'];
echo "<p>✅ Testing with Admin ID: $adminId</p>";

// Test database connection
try {
    $db = \Config\Database::getConnection();
    echo "<p>✅ Database connection OK</p>";
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    exit;
}

// Check properties table structure
echo "<h2>🏗️ Properties Table Structure</h2>";
$structure = $db->query("DESCRIBE properties")->fetch_all(MYSQLI_ASSOC);
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
foreach ($structure as $field) {
    echo "<tr>";
    echo "<td>" . $field['Field'] . "</td>";
    echo "<td>" . $field['Type'] . "</td>";
    echo "<td>" . $field['Null'] . "</td>";
    echo "<td>" . $field['Key'] . "</td>";
    echo "<td>" . ($field['Default'] ?: 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test creating a sample property
echo "<h2>🏗️ Test Property Creation</h2>";
$testPropertyData = [
    'admin_id' => $adminId,
    'name' => 'Test Property ' . date('Y-m-d H:i:s'),
    'address' => '123 Test Street, Test City',
    'type' => 'residential',
    'category' => 'apartment',
    'description' => 'This is a test property created for debugging',
    'status' => 'active',
    'bedrooms' => 2,
    'bathrooms' => 1,
    'kitchens' => 1,
    'parking' => 1,
    'rent_price' => 1500.00,
    'year_built' => 2020,
    'created_at' => date('Y-m-d H:i:s')
];

echo "<h3>📝 Test Property Data:</h3>";
echo "<pre>" . json_encode($testPropertyData, JSON_PRETTY_PRINT) . "</pre>";

try {
    // Insert test property
    $columns = implode(', ', array_keys($testPropertyData));
    $placeholders = str_repeat('?,', count($testPropertyData) - 1) . '?';
    $values = array_values($testPropertyData);
    
    $sql = "INSERT INTO properties ($columns) VALUES ($placeholders)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($values)), ...$values);
    
    echo "<p><strong>SQL:</strong> " . htmlspecialchars($sql) . "</p>";
    echo "<p><strong>Values:</strong> " . json_encode($values) . "</p>";
    
    if ($stmt->execute()) {
        $testPropertyId = $db->insert_id;
        echo "<p>✅ Test property created successfully! ID: $testPropertyId</p>";
        
        // Verify the property was inserted
        $verifySql = "SELECT * FROM properties WHERE id = ?";
        $verifyStmt = $db->prepare($verifySql);
        $verifyStmt->bind_param('i', $testPropertyId);
        $verifyStmt->execute();
        $insertedProperty = $verifyStmt->get_result()->fetch_assoc();
        
        if ($insertedProperty) {
            echo "<h3>✅ Verification - Property Found in Database:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            foreach ($insertedProperty as $key => $value) {
                echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ Property not found after insertion!</p>";
        }
        
        // Test the PropertyController query
        echo "<h2>🎮 Test PropertyController Query</h2>";
        $controllerSql = "SELECT p.*, 
                          (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                          (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                   FROM properties p 
                   WHERE p.admin_id = ? AND p.deleted_at IS NULL
                   ORDER BY p.created_at DESC";
        
        $controllerStmt = $db->prepare($controllerSql);
        $controllerStmt->bind_param('i', $adminId);
        $controllerStmt->execute();
        $controllerResults = $controllerStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo "<p><strong>PropertyController query results:</strong> " . count($controllerResults) . " properties</p>";
        
        if (!empty($controllerResults)) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Address</th><th>Status</th><th>Created</th></tr>";
            foreach ($controllerResults as $prop) {
                $isNew = ($prop['id'] == $testPropertyId) ? 'background-color: #90EE90;' : '';
                echo "<tr style='$isNew'>";
                echo "<td>" . $prop['id'] . ($prop['id'] == $testPropertyId ? ' 🆕' : '') . "</td>";
                echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
                echo "<td>" . htmlspecialchars($prop['address']) . "</td>";
                echo "<td>" . $prop['status'] . "</td>";
                echo "<td>" . $prop['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No properties found with PropertyController query!</p>";
        }
        
        // Clean up - delete test property
        echo "<h2>🧹 Cleanup</h2>";
        $deleteSql = "DELETE FROM properties WHERE id = ?";
        $deleteStmt = $db->prepare($deleteSql);
        $deleteStmt->bind_param('i', $testPropertyId);
        if ($deleteStmt->execute()) {
            echo "<p>✅ Test property deleted</p>";
        } else {
            echo "<p>⚠️ Could not delete test property: " . $deleteStmt->error . "</p>";
        }
        
    } else {
        echo "<p>❌ Failed to create test property: " . $stmt->error . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error during test: " . $e->getMessage() . "</p>";
}

// Check if there are any recent properties that might be the "missing" one
echo "<h2>🔍 Check for Recent Properties (Last 10 Minutes)</h2>";
$recentSql = "SELECT id, name, address, admin_id, status, created_at FROM properties 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) 
             ORDER BY created_at DESC";
$recentResults = $db->query($recentSql)->fetch_all(MYSQLI_ASSOC);

if (!empty($recentResults)) {
    echo "<p>Found " . count($recentResults) . " recent properties:</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Admin ID</th><th>Status</th><th>Created</th></tr>";
    foreach ($recentResults as $prop) {
        $isYours = ($prop['admin_id'] == $adminId) ? 'background-color: #d4edda;' : 'background-color: #f8d7da;';
        echo "<tr style='$isYours'>";
        echo "<td>" . $prop['id'] . "</td>";
        echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
        echo "<td>" . $prop['admin_id'] . " " . (($prop['admin_id'] == $adminId) ? '(YOU)' : '(OTHER)') . "</td>";
        echo "<td>" . $prop['status'] . "</td>";
        echo "<td>" . $prop['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No recent properties found.</p>";
}

echo "<h2>🎯 Next Steps</h2>";
echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;'>";
echo "<p>Based on this test:</p>";
echo "<ol>";
echo "<li><strong>If test property works:</strong> The issue is with the property creation form/data</li>";
echo "<li><strong>If test property fails:</strong> There's a database or connection issue</li>";
echo "<li><strong>If properties exist but don't show:</strong> It's a view/display issue</li>";
echo "<li><strong>Check the debug script:</strong> <a href='/debug_property_display_issue.php'>Run full debug analysis</a></li>";
echo "</ol>";
echo "</div>";
?>
