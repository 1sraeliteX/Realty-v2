<?php
// Test admin session and property access
session_start();

echo "<h2>Admin Session Test</h2>";

// Check if admin is logged in
if (isset($_SESSION['admin_id'])) {
    echo "<p>✅ Admin session found</p>";
    echo "<p>Admin ID: {$_SESSION['admin_id']}</p>";
    echo "<p>Admin Name: {$_SESSION['admin_name']}</p>";
    echo "<p>Admin Email: {$_SESSION['admin_email']}</p>";
    echo "<p>Admin Role: {$_SESSION['admin_role']}</p>";
    
    // Check properties for this admin
    require_once __DIR__ . '/config/database.php';
    
    try {
        $pdo = Config\Database::getInstance()->getConnection();
        
        // Properties for this admin
        $stmt = $pdo->prepare("SELECT * FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['admin_id']]);
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Properties for Current Admin:</h3>";
        echo "<p>Found " . count($properties) . " properties</p>";
        
        if (!empty($properties)) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Status</th><th>Created</th></tr>";
            foreach ($properties as $property) {
                echo "<tr>";
                echo "<td>{$property['id']}</td>";
                echo "<td>" . htmlspecialchars($property['name']) . "</td>";
                echo "<td>{$property['type']}</td>";
                echo "<td>{$property['status']}</td>";
                echo "<td>{$property['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No properties found for this admin</p>";
        }
        
        // Test the exact query from PropertyController
        echo "<h3>PropertyController Query Test:</h3>";
        
        $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
        $params = [$_SESSION['admin_id']];
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                FROM properties p 
                WHERE {$whereClause}
                ORDER BY p.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Query Results: " . count($results) . " properties</p>";
        
        if (!empty($results)) {
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
        
    } catch (Exception $e) {
        echo "<p>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} else {
    echo "<p>❌ No admin session found</p>";
    echo "<p>You need to <a href='/admin/login'>login as admin</a> first</p>";
    
    // Show available admin users for testing
    echo "<h3>Available Admin Users:</h3>";
    require_once __DIR__ . '/config/database.php';
    
    try {
        $pdo = Config\Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT id, name, email, role FROM admins WHERE deleted_at IS NULL");
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>{$admin['id']}</td>";
            echo "<td>" . htmlspecialchars($admin['name']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td>{$admin['role']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Login Links for Testing:</h3>";
        foreach ($admins as $admin) {
            echo "<p><a href='/admin/login?email=" . urlencode($admin['email']) . "'>Login as {$admin['name']} ({$admin['email']})</a></p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='/admin/login'>Admin Login</a> | <a href='/admin/properties'>Properties</a> | <a href='/admin/dashboard'>Dashboard</a></p>";
?>
