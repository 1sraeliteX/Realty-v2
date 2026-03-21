<?php
// Comprehensive debugging script for property display issue
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>🔍 Property Display Issue Debug</h1>";

// Check admin authentication
echo "<h2>🔐 Authentication Status</h2>";
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo "<p>❌ No admin session found. Please <a href='/admin/login'>login first</a></p>";
    exit;
}

$adminId = $_SESSION['admin_id'];
$adminEmail = $_SESSION['admin_email'] ?? 'Unknown';
echo "<p>✅ Admin logged in: ID = $adminId, Email = $adminEmail</p>";

// Check database connection
echo "<h2>🗄️ Database Connection</h2>";
try {
    $db = \Config\Database::getConnection();
    echo "<p>✅ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    exit;
}

// Check all properties in database
echo "<h2>📊 All Properties in Database</h2>";
$allPropertiesSql = "SELECT id, name, address, admin_id, status, deleted_at, created_at FROM properties ORDER BY created_at DESC";
$allProperties = $db->query($allPropertiesSql)->fetch_all(MYSQLI_ASSOC);

echo "<p><strong>Total properties in database:</strong> " . count($allProperties) . "</p>";

if (!empty($allProperties)) {
    echo "<table border='1' style='width: 100%; border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Address</th><th>Admin ID</th><th>Status</th><th>Deleted At</th><th>Created At</th></tr>";
    foreach ($allProperties as $prop) {
        $rowClass = ($prop['admin_id'] == $adminId) ? 'background-color: #d4edda;' : 'background-color: #f8d7da;';
        echo "<tr style='$rowClass'>";
        echo "<td>" . $prop['id'] . "</td>";
        echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
        echo "<td>" . htmlspecialchars($prop['address']) . "</td>";
        echo "<td>" . $prop['admin_id'] . " " . (($prop['admin_id'] == $adminId) ? "✅(YOU)" : "❌(OTHER)") . "</td>";
        echo "<td>" . $prop['status'] . "</td>";
        echo "<td>" . ($prop['deleted_at'] ?? 'NULL') . "</td>";
        echo "<td>" . $prop['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><strong>Green rows = Your properties, Red rows = Other admins' properties</strong></p>";
} else {
    echo "<p>❌ No properties found in database at all!</p>";
}

// Check properties for current admin (using the exact same query as PropertyController)
echo "<h2>🎯 Your Properties (PropertyController Query)</h2>";
$adminPropertiesSql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                FROM properties p 
                WHERE p.admin_id = ? AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";

$stmt = $db->prepare($adminPropertiesSql);
$stmt->bind_param('i', $adminId);
$stmt->execute();
$adminProperties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo "<p><strong>Your properties found:</strong> " . count($adminProperties) . "</p>";

if (!empty($adminProperties)) {
    echo "<table border='1' style='width: 100%; border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Address</th><th>Status</th><th>Units</th><th>Occupied</th><th>Created</th></tr>";
    foreach ($adminProperties as $prop) {
        echo "<tr style='background-color: #d4edda;'>";
        echo "<td>" . $prop['id'] . "</td>";
        echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
        echo "<td>" . htmlspecialchars($prop['address']) . "</td>";
        echo "<td>" . $prop['status'] . "</td>";
        echo "<td>" . $prop['unit_count'] . "</td>";
        echo "<td>" . $prop['occupied_units'] . "</td>";
        echo "<td>" . $prop['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ No properties found for your admin account!</p>";
    
    // Check if there are properties with deleted_at not NULL
    $deletedSql = "SELECT id, name, address, deleted_at FROM properties WHERE admin_id = ? AND deleted_at IS NOT NULL";
    $deletedStmt = $db->prepare($deletedSql);
    $deletedStmt->bind_param('i', $adminId);
    $deletedStmt->execute();
    $deletedProperties = $deletedStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    if (!empty($deletedProperties)) {
        echo "<p>⚠️ Found " . count($deletedProperties) . " deleted properties for your account:</p>";
        echo "<ul>";
        foreach ($deletedProperties as $prop) {
            echo "<li>❌ " . htmlspecialchars($prop['name']) . " (deleted on: " . $prop['deleted_at'] . ")</li>";
        }
        echo "</ul>";
    }
}

// Check recent property creation (last 5 minutes)
echo "<h2>🕐 Recently Created Properties (Last 5 Minutes)</h2>";
$recentSql = "SELECT id, name, address, admin_id, created_at FROM properties WHERE created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) ORDER BY created_at DESC";
$recentProperties = $db->query($recentSql)->fetch_all(MYSQLI_ASSOC);

if (!empty($recentProperties)) {
    echo "<p><strong>Recently created properties:</strong> " . count($recentProperties) . "</p>";
    echo "<ul>";
    foreach ($recentProperties as $prop) {
        $isYours = ($prop['admin_id'] == $adminId) ? "✅ YOURS" : "❌ Other admin";
        echo "<li><strong>" . htmlspecialchars($prop['name']) . "</strong> by Admin ID: " . $prop['admin_id'] . " $isYours (Created: " . $prop['created_at'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No properties created in the last 5 minutes.</p>";
}

// Simulate the PropertyController index method
echo "<h2>🎮 PropertyController Simulation</h2>";
try {
    // Mock the PropertyController logic
    $page = 1;
    $search = '';
    $type = '';
    $category = '';
    $status = '';
    
    // Build WHERE clause (same as PropertyController)
    $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
    $params = [$adminId];
    
    $whereClause = implode(' AND ', $where);
    
    // Get properties with unit counts (same query as PropertyController)
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE {$whereClause}
            ORDER BY p.created_at DESC";
    
    echo "<p><strong>Simulated SQL:</strong> " . htmlspecialchars($sql) . "</p>";
    echo "<p><strong>Parameters:</strong> [" . implode(', ', $params) . "]</p>";
    
    $result = $db->query($sql);
    $properties = $result->fetch_all(MYSQLI_ASSOC);
    
    echo "<p><strong>Simulation result:</strong> " . count($properties) . " properties found</p>";
    
    // Simulate pagination
    $pagination = [
        'current_page' => $page,
        'per_page' => 10,
        'total' => count($properties),
        'total_pages' => ceil(count($properties) / 10),
        'has_prev' => $page > 1,
        'has_next' => $page < ceil(count($properties) / 10)
    ];
    
    echo "<p><strong>Pagination data:</strong> " . json_encode($pagination) . "</p>";
    
    // This is what should be passed to ViewManager
    echo "<h3>📤 Data That Should Be Sent to ViewManager:</h3>";
    echo "<pre>";
    echo "ViewManager::set('properties', " . json_encode($properties, JSON_PRETTY_PRINT) . ");\n";
    echo "ViewManager::set('pagination', " . json_encode($pagination, JSON_PRETTY_PRINT) . ");\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Controller simulation error: " . $e->getMessage() . "</p>";
}

// Check for potential issues
echo "<h2>🚨 Potential Issues & Solutions</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>";

if (empty($adminProperties)) {
    echo "<h3>❌ ISSUE: No properties found for your admin account</h3>";
    echo "<p><strong>Possible causes:</strong></p>";
    echo "<ol>";
    echo "<li>Property was saved to a different admin account</li>";
    echo "<li>Property was marked as deleted (deleted_at is not NULL)</li>";
    echo "<li>Database transaction was not committed</li>";
    echo "<li>Property creation failed but showed success message</li>";
    echo "</ol>";
} else {
    echo "<h3>✅ Properties found in database but not displaying</h3>";
    echo "<p><strong>Possible causes:</strong></p>";
    echo "<ol>";
    echo "<li>ViewManager data not being set correctly</li>";
    echo "<li>View file not reading from ViewManager</li>";
    echo "<li>Caching issue in browser</li>";
    echo "<li>URL routing to wrong controller method</li>";
    echo "</ol>";
}

echo "</div>";

echo "<h2>🛠️ Recommended Actions</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>";
echo "<ol>";
echo "<li><strong>Check above:</strong> See if your property appears in the database tables</li>";
echo "<li><strong>If not found:</strong> Property creation failed - check PropertyController@store method</li>";
echo "<li><strong>If found but not displaying:</strong> Clear browser cache and reload</li>";
echo "<li><strong>Check URL:</strong> Ensure you're on /admin/properties not /properties</li>";
echo "<li><strong>Check logs:</strong> Look for any PHP errors in error logs</li>";
echo "</ol>";
echo "</div>";

// Add JavaScript to check current page state
echo "<script>";
echo "console.log('Current URL:', window.location.href);";
echo "console.log('Current path:', window.location.pathname);";
echo "</script>";
?>
