<?php
// Comprehensive debugging script for property display issue
session_start();

echo "<h2>Property Display Issue Debug</h2>";

// Check admin session
echo "<h3>Admin Session Check</h3>";
if (isset($_SESSION['admin_id'])) {
    echo "<p>✅ Admin session found</p>";
    echo "<p>Admin ID: {$_SESSION['admin_id']}</p>";
    echo "<p>Admin Name: {$_SESSION['admin_name']}</p>";
    echo "<p>Admin Email: {$_SESSION['admin_email']}</p>";
    echo "<p>Admin Role: {$_SESSION['admin_role']}</p>";
} else {
    echo "<p>❌ No admin session found</p>";
    echo "<p>Please <a href='/admin/login'>login as admin</a> first</p>";
    exit;
}

// Check database connection
require_once __DIR__ . '/config/database.php';
use Config\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    echo "<p>✅ Database connection successful</p>";
    
    // Verify admin exists in database
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p>✅ Admin found in database</p>";
        echo "<pre>" . htmlspecialchars(json_encode($admin, JSON_PRETTY_PRINT)) . "</pre>";
    } else {
        echo "<p>❌ Admin not found in database</p>";
        exit;
    }
    
    // Check properties table
    echo "<h3>Properties Analysis</h3>";
    
    // Total properties
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM properties");
    $totalProperties = $stmt->fetchColumn();
    echo "<p>Total properties in database: {$totalProperties}</p>";
    
    // Properties for this admin
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM properties WHERE admin_id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $adminProperties = $stmt->fetchColumn();
    echo "<p>Properties for current admin: {$adminProperties}</p>";
    
    // Properties for this admin (not deleted)
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM properties WHERE admin_id = ? AND deleted_at IS NULL");
    $stmt->execute([$_SESSION['admin_id']]);
    $adminActiveProperties = $stmt->fetchColumn();
    echo "<p>Active properties for current admin: {$adminActiveProperties}</p>";
    
    // Show all properties for this admin
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE admin_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['admin_id']]);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h4>All Properties for Current Admin:</h4>";
    if (empty($properties)) {
        echo "<p>No properties found for this admin</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Deleted</th><th>Created</th></tr>";
        foreach ($properties as $property) {
            $deletedAt = $property['deleted_at'] ? 'Yes' : 'No';
            echo "<tr>";
            echo "<td>{$property['id']}</td>";
            echo "<td>" . htmlspecialchars($property['name']) . "</td>";
            echo "<td>{$property['status']}</td>";
            echo "<td>{$deletedAt}</td>";
            echo "<td>{$property['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test the exact query used by PropertyController
    echo "<h3>Query Test</h3>";
    
    $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
    $params = [$_SESSION['admin_id']];
    $whereClause = implode(' AND ', $where);
    
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE {$whereClause}
            ORDER BY p.created_at DESC";
    
    echo "<p>SQL Query:</p>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    echo "<p>Parameters:</p>";
    echo "<pre>" . htmlspecialchars(json_encode($params)) . "</pre>";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Query Results (" . count($results) . " rows):</p>";
    if (empty($results)) {
        echo "<p>❌ No results from query</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Units</th><th>Occupied</th><th>Status</th></tr>";
        foreach ($results as $property) {
            echo "<tr>";
            echo "<td>{$property['id']}</td>";
            echo "<td>" . htmlspecialchars($property['name']) . "</td>";
            echo "<td>{$property['unit_count']}</td>";
            echo "<td>{$property['occupied_units']}</td>";
            echo "<td>{$property['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test creating a sample property
    echo "<h3>Create Test Property</h3>";
    
    $testProperty = [
        'admin_id' => $_SESSION['admin_id'],
        'name' => 'Debug Test Property ' . date('Y-m-d H:i:s'),
        'address' => '123 Debug Street',
        'type' => 'residential',
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $columns = implode(', ', array_keys($testProperty));
    $placeholders = implode(', ', array_fill(0, count($testProperty), '?'));
    $values = array_values($testProperty);
    
    $insertSql = "INSERT INTO properties ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($insertSql);
    $result = $stmt->execute($values);
    
    if ($result) {
        $insertId = $pdo->lastInsertId();
        echo "<p>✅ Test property created with ID: $insertId</p>";
        
        // Test if it shows up in the main query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $newResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Query now returns " . count($newResults) . " results</p>";
        
        // Clean up - delete test property
        $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
        $stmt->execute([$insertId]);
        echo "<p>✅ Test property cleaned up</p>";
    } else {
        echo "<p>❌ Failed to create test property</p>";
        echo "<p>Error: " . $stmt->errorInfo()[2] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='/admin/properties'>Back to Properties</a> | <a href='/admin/dashboard'>Dashboard</a></p>";
?>
