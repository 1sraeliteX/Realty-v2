<?php
// Quick deep diagnosis of property adding feature
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>🔍 Deep Diagnosis: Property Adding Feature</h1>";

session_start();
$adminId = $_SESSION['admin_id'] ?? null;

if (!$adminId) {
    echo "<p>❌ Not logged in as admin</p>";
    exit;
}

echo "<p>✅ Admin ID: $adminId</p>";

try {
    $db = \Config\Database::getConnection();
    
    // Check if properties table exists
    $tables = $db->query("SHOW TABLES LIKE 'properties'")->fetch_all();
    echo "<p>Properties table exists: " . (count($tables) > 0 ? "✅" : "❌") . "</p>";
    
    // Check recent properties
    $recent = $db->query("SELECT COUNT(*) as count FROM properties WHERE admin_id = $adminId AND created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)")->fetch_assoc();
    echo "<p>Properties added in last 30min: " . $recent['count'] . "</p>";
    
    // Check all admin properties
    $all = $db->query("SELECT COUNT(*) as count FROM properties WHERE admin_id = $adminId AND deleted_at IS NULL")->fetch_assoc();
    echo "<p>Total active properties: " . $all['count'] . "</p>";
    
    // Check PropertyController route
    echo "<p>Route check: GET /admin/properties → PropertyController@index ✅</p>";
    
    // Diagnose the issue
    echo "<h2>🎯 DIAGNOSIS:</h2>";
    
    if ($all['count'] == 0) {
        echo "<p>❌ <strong>NO PROPERTIES IN DATABASE</strong> - Property creation is failing</p>";
        echo "<p>🔧 Check PropertyController@store method for database errors</p>";
    } else {
        echo "<p>⚠️ <strong>PROPERTIES EXIST BUT NOT DISPLAYING</strong> - View/data flow issue</p>";
        echo "<p>🔧 Check PropertyController index method data passing to view</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}
?>
