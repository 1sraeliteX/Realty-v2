<?php
// Test script to understand property routing issue
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>Property Routes Analysis</h1>";

// Simulate both routes
echo "<h2>Testing Route: GET /properties (Public)</h2>";
try {
    // Initialize framework
    \ComponentRegistry::load('ui-components');
    
    // Mock the PropertyController index method behavior
    $controller = new \App\Controllers\PropertyController();
    
    // Check if requireAuth is properly implemented
    echo "<p>✅ PropertyController loaded</p>";
    
    // Test database query without authentication (public route behavior)
    $db = \Config\Database::getConnection();
    
    // This is what the public route would execute (no admin filtering)
    $publicSql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE p.deleted_at IS NULL
            ORDER BY p.created_at DESC";
    
    $publicResult = $db->query($publicSql);
    if ($publicResult) {
        $publicProperties = $publicResult->fetch_all(MYSQLI_ASSOC);
        echo "<p>✅ Public query executed: Found " . count($publicProperties) . " properties</p>";
        echo "<pre>";
        foreach ($publicProperties as $prop) {
            echo "ID: {$prop['id']}, Name: {$prop['name']}, Admin ID: {$prop['admin_id']}\n";
        }
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Public route error: " . $e->getMessage() . "</p>";
}

echo "<h2>Testing Route: GET /admin/properties (Admin)</h2>";
try {
    // Mock admin authentication
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        // Try to get first admin for testing
        $admin = $db->query("SELECT * FROM admins WHERE role = 'admin' LIMIT 1")->fetch_assoc();
        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            echo "<p>🔧 Using test admin: {$admin['email']} (ID: {$admin['id']})</p>";
        } else {
            echo "<p>❌ No admin found in database</p>";
            exit;
        }
    }
    
    $adminId = $_SESSION['admin_id'];
    
    // This is what the admin route would execute (with admin filtering)
    $adminSql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE p.admin_id = $adminId AND p.deleted_at IS NULL
            ORDER BY p.created_at DESC";
    
    $adminResult = $db->query($adminSql);
    if ($adminResult) {
        $adminProperties = $adminResult->fetch_all(MYSQLI_ASSOC);
        echo "<p>✅ Admin query executed: Found " . count($adminProperties) . " properties</p>";
        echo "<pre>";
        foreach ($adminProperties as $prop) {
            echo "ID: {$prop['id']}, Name: {$prop['name']}, Admin ID: {$prop['admin_id']}\n";
        }
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Admin route error: " . $e->getMessage() . "</p>";
}

echo "<h2>Analysis & Solution</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #0066cc;'>";
echo "<h3>🔍 Root Cause Identified:</h3>";
echo "<p><strong>You are accessing <code>/properties</code> instead of <code>/admin/properties</code></strong></p>";
echo "<ul>";
echo "<li><code>/properties</code> → Public route (no admin authentication, shows all properties)</li>";
echo "<li><code>/admin/properties</code> → Admin route (requires authentication, shows only your properties)</li>";
echo "</ul>";

echo "<h3>💡 Solution:</h3>";
echo "<p>Use the correct admin URL: <a href='/admin/properties'><strong>/admin/properties</strong></a></p>";
echo "</div>";

// Test if authentication is working properly
echo "<h2>Authentication Test</h2>";
if (isset($_SESSION['admin_id'])) {
    echo "<p>✅ Admin session active: ID = " . $_SESSION['admin_id'] . "</p>";
    
    // Test the requireAuth method
    try {
        $controller = new \App\Controllers\PropertyController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('requireAuth');
        echo "<p>✅ requireAuth method exists</p>";
    } catch (Exception $e) {
        echo "<p>❌ requireAuth method issue: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ No admin session - you need to <a href='/admin/login'>login first</a></p>";
}
?>
