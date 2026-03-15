<?php
// Debug script to identify property display issues
session_start();

require_once __DIR__ . '/config/bootstrap.php';

// Load database connection
require_once __DIR__ . '/config/database.php';

use Config\Database;

echo "<h1>Property Display Debug</h1>";

// 1. Check database connection
echo "<h2>1. Database Connection</h2>";
try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// 2. Check admin session
echo "<h2>2. Admin Session</h2>";
if (isset($_SESSION['admin_id'])) {
    echo "✅ Admin session found: " . $_SESSION['admin_id'] . "<br>";
    
    // Get admin details
    $admin = $db->fetch("SELECT * FROM admins WHERE id = ?", [$_SESSION['admin_id']]);
    if ($admin) {
        echo "✅ Admin user found: " . $admin['email'] . "<br>";
    } else {
        echo "❌ Admin user not found in database<br>";
    }
} else {
    echo "❌ No admin session found<br>";
    echo "<a href='/admin/login'>Please login first</a><br>";
}

// 3. Check properties table structure
echo "<h2>3. Properties Table Structure</h2>";
try {
    $columns = $db->fetchAll("SHOW COLUMNS FROM properties");
    echo "<table border='1'><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "❌ Error checking table structure: " . $e->getMessage() . "<br>";
}

// 4. Check total properties in database
echo "<h2>4. Total Properties in Database</h2>";
try {
    $totalProperties = $db->fetch("SELECT COUNT(*) as count FROM properties WHERE deleted_at IS NULL");
    echo "Total properties: " . $totalProperties['count'] . "<br>";
    
    // Check properties for current admin
    if (isset($_SESSION['admin_id'])) {
        $adminProperties = $db->fetch("SELECT COUNT(*) as count FROM properties WHERE admin_id = ? AND deleted_at IS NULL", [$_SESSION['admin_id']]);
        echo "Properties for current admin: " . $adminProperties['count'] . "<br>";
        
        // Show actual properties for admin
        $properties = $db->fetchAll("SELECT * FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 5", [$_SESSION['admin_id']]);
        echo "<h3>Recent Properties for Admin:</h3>";
        if ($properties) {
            echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Address</th><th>Type</th><th>Status</th><th>Created</th></tr>";
            foreach ($properties as $prop) {
                echo "<tr><td>{$prop['id']}</td><td>{$prop['name']}</td><td>{$prop['address']}</td><td>{$prop['type']}</td><td>{$prop['status']}</td><td>{$prop['created_at']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No properties found for admin<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking properties: " . $e->getMessage() . "<br>";
}

// 5. Test the exact query from PropertyController
echo "<h2>5. Test PropertyController Query</h2>";
if (isset($_SESSION['admin_id'])) {
    try {
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                FROM properties p 
                WHERE p.admin_id = ? AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        
        $result = $db->fetchAll($sql, [$_SESSION['admin_id']]);
        echo "Query executed successfully<br>";
        echo "Results count: " . count($result) . "<br>";
        
        if ($result) {
            echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Units</th><th>Occupied</th></tr>";
            foreach ($result as $prop) {
                echo "<tr><td>{$prop['id']}</td><td>{$prop['name']}</td><td>{$prop['unit_count']}</td><td>{$prop['occupied_units']}</td></tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "❌ Error executing query: " . $e->getMessage() . "<br>";
    }
}

// 6. Check routing
echo "<h2>6. Routing Check</h2>";
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    echo "✅ Routes file exists<br>";
    $routes = include $routesFile;
    echo "Total routes: " . count($routes) . "<br>";
    
    // Check for properties route
    $propertyRoutes = array_filter($routes, function($route, $key) {
        return strpos($key, 'properties') !== false;
    }, ARRAY_FILTER_USE_BOTH);
    
    echo "Property-related routes:<br>";
    echo "<pre>";
    print_r(array_keys($propertyRoutes));
    echo "</pre>";
} else {
    echo "❌ Routes file not found<br>";
}

// 7. Check view files
echo "<h2>7. View Files Check</h2>";
$viewFiles = [
    'views/properties/index.php',
    'views/admin/properties/list.php',
    'views/admin/properties/add.php'
];

foreach ($viewFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h2>Debug Complete</h2>";
echo "<p>Check the results above to identify the issue.</p>";
?>
