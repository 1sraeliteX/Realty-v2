<?php
// Database-only diagnostic (no session required)
require_once __DIR__ . '/config/database.php';

echo "<h1>🔍 Database Check</h1>";

try {
    $db = Config\Database::getInstance()->getConnection();
    
    echo "<h2>📊 All Properties in Database</h2>";
    
    $allProps = $db->query("SELECT id, name, admin_id, status, deleted_at, created_at FROM properties ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total properties: " . count($allProps) . "</p>";
    
    if (!empty($allProps)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Admin ID</th><th>Status</th><th>Deleted</th><th>Created</th></tr>";
        foreach ($allProps as $prop) {
            $rowStyle = $prop['deleted_at'] ? 'background-color: #f8d7da;' : 'background-color: #d4edda;';
            echo "<tr style='$rowStyle'>";
            echo "<td>" . $prop['id'] . "</td>";
            echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
            echo "<td>" . $prop['admin_id'] . "</td>";
            echo "<td>" . $prop['status'] . "</td>";
            echo "<td>" . ($prop['deleted_at'] ?? 'NULL') . "</td>";
            echo "<td>" . $prop['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><em>Green = Active, Red = Deleted</em></p>";
        
        // Check admins
        echo "<h2>👥 Admin Users</h2>";
        $admins = $db->query("SELECT id, email, role FROM admins WHERE role = 'admin'")->fetchAll(PDO::FETCH_ASSOC);
        echo "<p>Admin users: " . count($admins) . "</p>";
        
        if (!empty($admins)) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Email</th><th>Role</th></tr>";
            foreach ($admins as $admin) {
                $propCount = $db->query("SELECT COUNT(*) as count FROM properties WHERE admin_id = " . $admin['id'] . " AND deleted_at IS NULL")->fetch(PDO::FETCH_ASSOC);
                echo "<tr>";
                echo "<td>" . $admin['id'] . "</td>";
                echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
                echo "<td>" . $admin['role'] . "</td>";
                echo "<td>" . $propCount['count'] . " properties</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Test PropertyController query for first admin
        if (!empty($admins)) {
            $firstAdminId = $admins[0]['id'];
            echo "<h2>🎮 Test PropertyController Query (Admin ID: $firstAdminId)</h2>";
            
            $sql = "SELECT p.*, 
                           (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                           (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                    FROM properties p 
                    WHERE p.admin_id = ? AND p.deleted_at IS NULL
                    ORDER BY p.created_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->bind_param('i', $firstAdminId);
            $stmt->execute();
            $results = $stmt->get_result()->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<p>Controller query results: " . count($results) . " properties</p>";
            
            if (!empty($results)) {
                echo "<p>✅ Controller query finds properties - they should display!</p>";
                echo "<table border='1' style='border-collapse: collapse;'>";
                echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Units</th></tr>";
                foreach ($results as $prop) {
                    echo "<tr>";
                    echo "<td>" . $prop['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
                    echo "<td>" . $prop['status'] . "</td>";
                    echo "<td>" . $prop['unit_count'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>";
                echo "<h3>✅ DIAGNOSIS: Properties exist and query works</h3>";
                echo "<p>The issue is likely in the web interface, not the database.</p>";
                echo "<p><strong>Next steps:</strong></p>";
                echo "<ol>";
                echo "<li>Access <a href='/admin/properties'>/admin/properties</a> in browser</li>";
                echo "<li>Clear browser cache (Ctrl+F5)</li>";
                echo "<li>Check you're logged in as correct admin</li>";
                echo "<li>Check browser console for JavaScript errors</li>";
                echo "</ol>";
                echo "</div>";
                
            } else {
                echo "<p>❌ Controller query finds no properties</p>";
                echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0;'>";
                echo "<h3>❌ DIAGNOSIS: Query issue</h3>";
                echo "<p>Properties exist but controller query doesn't find them.</p>";
                echo "<p>This suggests an issue with the PropertyController logic.</p>";
                echo "</div>";
            }
        }
        
    } else {
        echo "<p>❌ No properties found in database at all!</p>";
        echo "<p>This means property creation is failing despite success messages.</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}
?>
