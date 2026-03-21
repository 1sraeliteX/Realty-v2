<?php
// Simple script to check current properties without session requirement
require_once __DIR__ . '/config/database.php';
use Config\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    echo "<h2>Current Properties in Database</h2>";
    
    // Check all properties
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM properties");
    $totalProperties = $stmt->fetchColumn();
    echo "<p>Total properties in database: {$totalProperties}</p>";
    
    // Check recent properties (last 5)
    $stmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC LIMIT 5");
    $recentProperties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Recent Properties (Last 5):</h3>";
    if (empty($recentProperties)) {
        echo "<p>No properties found in database</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Admin ID</th><th>Status</th><th>Deleted</th><th>Created</th></tr>";
        foreach ($recentProperties as $property) {
            $deletedAt = $property['deleted_at'] ? 'Yes' : 'No';
            echo "<tr>";
            echo "<td>{$property['id']}</td>";
            echo "<td>" . htmlspecialchars($property['name']) . "</td>";
            echo "<td>{$property['admin_id']}</td>";
            echo "<td>{$property['status']}</td>";
            echo "<td>{$deletedAt}</td>";
            echo "<td>{$property['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check admins
    $stmt = $pdo->query("SELECT id, name, email, role FROM admins WHERE deleted_at IS NULL");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Admin Users:</h3>";
    if (empty($admins)) {
        echo "<p>No admin users found</p>";
    } else {
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
    }
    
    // Test property creation
    echo "<h3>Test Property Creation</h3>";
    
    // Get first admin ID for testing
    $firstAdmin = $admins[0] ?? null;
    if ($firstAdmin) {
        $testProperty = [
            'admin_id' => $firstAdmin['id'],
            'name' => 'Test Property ' . date('Y-m-d H:i:s'),
            'address' => '123 Test Street',
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
            
            // Clean up - delete test property
            $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
            $stmt->execute([$insertId]);
            echo "<p>✅ Test property cleaned up</p>";
        } else {
            echo "<p>❌ Failed to create test property</p>";
            echo "<p>Error: " . $stmt->errorInfo()[2] . "</p>";
        }
    } else {
        echo "<p>❌ No admin users available for testing</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='/admin/login'>Admin Login</a> | <a href='/admin/properties'>Properties</a></p>";
?>
