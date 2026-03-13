<?php
// Debug script for property URL issue
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>Property URL Debug</h1>";

// Check if admin is logged in
session_start();
echo "<h2>Session Status:</h2>";
echo "<pre>";
echo "Admin ID: " . ($_SESSION['admin_id'] ?? 'Not set') . "\n";
echo "Admin Email: " . ($_SESSION['admin_email'] ?? 'Not set') . "\n";
echo "Session Data: " . json_encode($_SESSION, JSON_PRETTY_PRINT) . "\n";
echo "</pre>";

// Check database connection
echo "<h2>Database Connection:</h2>";
try {
    $db = \Config\Database::getConnection();
    echo "✅ Database connection successful\n";
    
    // Check if properties table exists
    $result = $db->query("SHOW TABLES LIKE 'properties'");
    if ($result->num_rows > 0) {
        echo "✅ Properties table exists\n";
        
        // Count properties
        $count = $db->query("SELECT COUNT(*) as count FROM properties WHERE deleted_at IS NULL")->fetch_assoc();
        echo "Total properties: " . $count['count'] . "\n";
        
        // Count properties for admin (if logged in)
        if (isset($_SESSION['admin_id'])) {
            $adminCount = $db->query("SELECT COUNT(*) as count FROM properties WHERE admin_id = " . $_SESSION['admin_id'] . " AND deleted_at IS NULL")->fetch_assoc();
            echo "Properties for current admin: " . $adminCount['count'] . "\n";
            
            // Show actual properties for admin
            $properties = $db->query("SELECT id, name, address, admin_id FROM properties WHERE admin_id = " . $_SESSION['admin_id'] . " AND deleted_at IS NULL LIMIT 5")->fetch_all(MYSQLI_ASSOC);
            echo "<h3>Recent Properties for Admin:</h3>";
            echo "<pre>";
            print_r($properties);
            echo "</pre>";
        }
    } else {
        echo "❌ Properties table does not exist\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Check routes
echo "<h2>Route Analysis:</h2>";
echo "<p>You are accessing: <strong>/properties</strong></p>";
echo "<p>This goes to: <strong>PropertyController@index</strong> (public route)</p>";
echo "<p>Admin route would be: <strong>/admin/properties</strong> → PropertyController@index</p>";
echo "<p>Both routes use the same controller method, but the difference is in authentication and data filtering.</p>";

// Check if admin authentication is working
echo "<h2>Admin Authentication Test:</h2>";
if (isset($_SESSION['admin_id'])) {
    echo "✅ Admin appears to be logged in with ID: " . $_SESSION['admin_id'] . "\n";
    
    // Verify admin exists in database
    $admin = $db->query("SELECT * FROM admins WHERE id = " . $_SESSION['admin_id'])->fetch_assoc();
    if ($admin) {
        echo "✅ Admin found in database: " . $admin['email'] . "\n";
    } else {
        echo "❌ Admin not found in database!\n";
    }
} else {
    echo "❌ No admin session found. You need to log in first.\n";
    echo "<p><a href='/admin/login'>Go to Admin Login</a></p>";
}

echo "<h2>Recommendations:</h2>";
echo "<ul>";
echo "<li>Use <strong>/admin/properties</strong> instead of <strong>/properties</strong> for admin property management</li>";
echo "<li>Ensure you are logged in as admin</li>";
echo "<li>Check that the PropertyController is properly filtering by admin_id</li>";
echo "</ul>";

// Test the actual PropertyController query
if (isset($_SESSION['admin_id'])) {
    echo "<h2>Property Controller Query Test:</h2>";
    
    $adminId = $_SESSION['admin_id'];
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE p.admin_id = $adminId AND p.deleted_at IS NULL
            ORDER BY p.created_at DESC";
    
    $result = $db->query($sql);
    if ($result) {
        $properties = $result->fetch_all(MYSQLI_ASSOC);
        echo "Query executed successfully. Found " . count($properties) . " properties.\n";
        echo "<pre>";
        print_r($properties);
        echo "</pre>";
    } else {
        echo "❌ Query failed: " . $db->error . "\n";
    }
}
?>
