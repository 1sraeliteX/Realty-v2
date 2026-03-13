<?php
// Quick debug for property display issue
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>🔍 Quick Property Display Debug</h1>";

session_start();
$adminId = $_SESSION['admin_id'] ?? null;

if (!$adminId) {
    echo "<p>❌ Not logged in as admin</p>";
    exit;
}

echo "<p>✅ Admin ID: $adminId</p>";

try {
    $db = Config\Database::getInstance()->getConnection();
    
    echo "<h2>🗄️ Database Check</h2>";
    
    // Check most recent property for this admin
    $recent = $db->prepare("SELECT id, name, admin_id, status, deleted_at, created_at FROM properties WHERE admin_id = ? ORDER BY created_at DESC LIMIT 1");
    $recent->execute([$adminId]);
    $property = $recent->fetch(PDO::FETCH_ASSOC);
    
    if ($property) {
        echo "<p>✅ Most recent property found:</p>";
        echo "<ul>";
        echo "<li>ID: " . $property['id'] . "</li>";
        echo "<li>Name: " . htmlspecialchars($property['name']) . "</li>";
        echo "<li>Status: " . $property['status'] . "</li>";
        echo "<li>Deleted: " . ($property['deleted_at'] ?? 'NULL') . "</li>";
        echo "<li>Created: " . $property['created_at'] . "</li>";
        echo "</ul>";
        
        if ($property['deleted_at'] !== null) {
            echo "<p>❌ PROPERTY IS MARKED AS DELETED - This is the problem!</p>";
        } else {
            echo "<p>✅ Property should display (not deleted)</p>";
        }
    } else {
        echo "<p>❌ No properties found for this admin</p>";
        echo "<p>Property creation may be failing despite success message</p>";
    }
    
    // Test PropertyController query
    echo "<h2>🎮 PropertyController Query Test</h2>";
    
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count
            FROM properties p 
            WHERE p.admin_id = ? AND p.deleted_at IS NULL
            ORDER BY p.created_at DESC";
    
    echo "<p>SQL: " . htmlspecialchars($sql) . "</p>";
    echo "<p>Params: [$adminId]</p>";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$adminId]);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Query results: " . count($properties) . " properties</p>";
    
    if (!empty($properties)) {
        echo "<p>✅ Controller query finds properties</p>";
        
        // Test ViewManager data flow
        echo "<h2>📤 Data Flow Test</h2>";
        
        \ViewManager::set('properties', $properties);
        \ViewManager::set('pagination', ['total' => count($properties)]);
        
        $viewData = ViewManager::get('properties');
        echo "<p>ViewManager has: " . count($viewData ?? []) . " properties</p>";
        
        if (!empty($viewData)) {
            echo "<p>✅ Data flow working - properties should display!</p>";
        } else {
            echo "<p>❌ Data flow issue - ViewManager empty</p>";
        }
        
    } else {
        echo "<p>❌ Controller query finds no properties</p>";
    }
    
    echo "<h2>🛠️ Immediate Actions</h2>";
    echo "<ol>";
    echo "<li><strong>Clear Browser Cache:</strong> Ctrl+F5</li>";
    echo "<li><strong>Check URL:</strong> Must be /admin/properties not /properties</li>";
    echo "<li><strong>Check Session:</strong> Make sure admin_id is correct</li>";
    echo "<li><strong>Check Error Logs:</strong> Look for PHP errors</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<script>";
echo "console.log('Quick property debug completed');";
echo "console.log('Admin ID:', '$adminId');";
echo "console.log('Properties found:', " . count($properties ?? []) . ");";
echo "</script>";
?>
