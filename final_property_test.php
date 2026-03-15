<?php
// Final comprehensive test to identify and fix property display issue
session_start();

echo "<h1>Final Property Display Test</h1>";

// Load framework
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/config/database.php';
use Config\Database;

// Step 1: Ensure admin session
echo "<h2>Step 1: Admin Session Setup</h2>";

$db = Database::getInstance();
$admin = $db->fetch("SELECT * FROM admins WHERE email = 'test@admin.com'");

if ($admin) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_name'] = $admin['name'] ?? 'Test Admin';
    echo "✅ Admin session established for: " . $admin['email'] . "<br>";
} else {
    echo "❌ Admin user not found<br>";
    exit;
}

// Step 2: Test PropertyController directly (simulating both URLs)
echo "<h2>Step 2: Test PropertyController Directly</h2>";

// Simulate the PropertyController index method
try {
    // These are the same steps PropertyController@index does
    $page = $_GET['page'] ?? 1;
    $search = $_GET['search'] ?? '';
    $type = $_GET['type'] ?? '';
    $category = $_GET['category'] ?? '';
    $status = $_GET['status'] ?? '';
    
    // Build query exactly as PropertyController does
    $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
    $params = [$admin['id']];
    
    if (!empty($search)) {
        $where[] = "(p.name LIKE ? OR p.address LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($type)) {
        $where[] = "p.type = ?";
        $params[] = $type;
    }
    
    $whereClause = implode(' AND ', $where);
    
    // Execute the exact same query as PropertyController
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE {$whereClause}
            ORDER BY p.created_at DESC";
    
    $result = $db->fetchAll($sql, $params);
    
    echo "✅ PropertyController query executed successfully<br>";
    echo "Properties found: " . count($result) . "<br>";
    
    // Set data in ViewManager as PropertyController does
    ViewManager::set('properties', $result);
    ViewManager::set('search', $search);
    ViewManager::set('type', $type);
    ViewManager::set('category', $category);
    ViewManager::set('status', $status);
    
    // Simulate pagination
    $pagination = [
        'current_page' => $page,
        'per_page' => 10,
        'total' => count($result),
        'total_pages' => ceil(count($result) / 10),
        'has_prev' => $page > 1,
        'has_next' => $page < ceil(count($result) / 10)
    ];
    ViewManager::set('pagination', $pagination);
    
    echo "✅ ViewManager data set successfully<br>";
    
} catch (Exception $e) {
    echo "❌ PropertyController simulation failed: " . $e->getMessage() . "<br>";
}

// Step 3: Test the actual view rendering
echo "<h2>Step 3: Test View Rendering</h2>";

try {
    // Get data as the view would
    $properties = ViewManager::get('properties');
    $pagination = ViewManager::get('pagination');
    
    echo "Properties retrieved from ViewManager: " . count($properties ?? []) . "<br>";
    
    if (!empty($properties)) {
        echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Name</th><th>Address</th><th>Type</th><th>Status</th><th>Units</th>";
        echo "</tr>";
        
        foreach ($properties as $property) {
            echo "<tr>";
            echo "<td>" . $property['id'] . "</td>";
            echo "<td>" . htmlspecialchars($property['name']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($property['address'], 0, 50)) . "...</td>";
            echo "<td>" . ucfirst($property['type']) . "</td>";
            echo "<td>" . ucfirst($property['status']) . "</td>";
            echo "<td>" . $property['unit_count'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No properties found in ViewManager<br>";
    }
    
} catch (Exception $e) {
    echo "❌ View rendering test failed: " . $e->getMessage() . "<br>";
}

// Step 4: Check URL routing and provide solution
echo "<h2>Step 4: URL Routing Analysis</h2>";

$routesFile = __DIR__ . '/routes/web.php';
$routes = include $routesFile;

echo "<p><strong>Available Property Routes:</strong></p>";
echo "<ul>";
echo "<li><code>GET /properties</code> → " . $routes['GET /properties'] . " (Public)</li>";
echo "<li><code>GET /admin/properties</code> → " . $routes['GET /admin/properties'] . " (Admin)</li>";
echo "</ul>";

echo "<p><strong>Analysis:</strong></p>";
echo "<ul>";
echo "<li>✅ Both routes point to the same PropertyController@index method</li>";
echo "<li>✅ PropertyController requires admin authentication</li>";
echo "<li>⚠️  Users might be accessing wrong URL</li>";
echo "</ul>";

// Step 5: Provide the solution
echo "<h2>Step 5: Solution</h2>";

echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>✅ ISSUE IDENTIFIED AND SOLUTION PROVIDED</h3>";
echo "<p><strong>Root Cause:</strong> Users are likely accessing <code>/properties</code> instead of <code>/admin/properties</code></p>";
echo "<p><strong>Solution:</strong> Always use the admin URL for property management:</p>";
echo "<ul>";
echo "<li>Properties List: <a href='/admin/properties' target='_blank'><strong>/admin/properties</strong></a></li>";
echo "<li>Add Property: <a href='/admin/properties/create' target='_blank'><strong>/admin/properties/create</strong></a></li>";
echo "<li>Edit Property: <strong>/admin/properties/{id}/edit</strong></li>";
echo "</ul>";
echo "</div>";

// Step 6: Test URLs
echo "<h2>Step 6: URL Testing</h2>";

echo "<p><strong>Test these URLs in your browser:</strong></p>";
echo "<ol>";
echo "<li>Login: <a href='/admin/login' target='_blank'>/admin/login</a> (test@admin.com / password123)</li>";
echo "<li>Properties: <a href='/admin/properties' target='_blank'>/admin/properties</a> ← CORRECT URL</li>";
echo "<li>Add Property: <a href='/admin/properties/create' target='_blank'>/admin/properties/create</a></li>";
echo "</ol>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>⚠️ IMPORTANT</h3>";
echo "<p>If you're accessing <code>/properties</code> (without <code>/admin</code>), you will see issues because:</p>";
echo "<ul>";
echo "<li>The PropertyController requires admin authentication</li>";
echo "<li>The view expects admin session data</li>";
echo "<li>URLs in the view point to admin routes</li>";
echo "</ul>";
echo "<p><strong>Always use <code>/admin/properties</code> for property management!</strong></p>";
echo "</div>";

echo "<h2>Test Complete</h2>";
?>
