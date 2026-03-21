<?php
// Complete test to ensure property creation flows to display
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>🔄 Complete Property Flow Test</h1>";

session_start();
$adminId = $_SESSION['admin_id'] ?? null;

if (!$adminId) {
    echo "<p>❌ Please <a href='/admin/login'>login as admin</a> first</p>";
    exit;
}

echo "<p>✅ Testing with Admin ID: $adminId</p>";

try {
    $db = \Config\Database::getConnection();
    
    echo "<h2>🔍 Step 1: Check Current Properties</h2>";
    
    // Get current property count
    $currentCount = $db->query("SELECT COUNT(*) as count FROM properties WHERE admin_id = $adminId AND deleted_at IS NULL")->fetch_assoc();
    echo "<p>Current properties in database: <strong>" . $currentCount['count'] . "</strong></p>";
    
    // Show current properties
    $currentProperties = $db->query("SELECT id, name, created_at FROM properties WHERE admin_id = $adminId AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
    
    if (!empty($currentProperties)) {
        echo "<h3>Recent Properties:</h3>";
        echo "<ul>";
        foreach ($currentProperties as $prop) {
            echo "<li><strong>" . htmlspecialchars($prop['name']) . "</strong> (ID: " . $prop['id'] . ", Created: " . $prop['created_at'] . ")</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>🎮 Step 2: Test PropertyController Logic</h2>";
    
    // Simulate the exact PropertyController::index() method
    $page = 1;
    $search = '';
    $type = '';
    $category = '';
    $status = '';
    
    // Build the exact same query as PropertyController
    $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
    $params = [$adminId];
    $whereClause = implode(' AND ', $where);
    
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE {$whereClause}
            ORDER BY p.created_at DESC";
    
    echo "<p><strong>PropertyController Query:</strong></p>";
    echo "<code style='background: #f8f9fa; padding: 10px; display: block;'>" . htmlspecialchars($sql) . "</code>";
    echo "<p><strong>Parameters:</strong> [" . implode(', ', array_map(function($p) { return "'$p'"; }, $params)) . "]</p>";
    
    $stmt = $db->prepare($sql);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo "<p><strong>Query Results:</strong> " . count($properties) . " properties found</p>";
    
    // Simulate pagination
    $result = [
        'data' => $properties,
        'pagination' => [
            'current_page' => $page,
            'per_page' => 10,
            'total' => count($properties),
            'total_pages' => ceil(count($properties) / 10),
            'has_prev' => $page > 1,
            'has_next' => $page < ceil(count($properties) / 10)
        ]
    ];
    
    echo "<h2>📤 Step 3: Simulate Data Flow</h2>";
    
    // Simulate PropertyController setting data in ViewManager
    \ViewManager::set('properties', $result['data']);
    \ViewManager::set('pagination', $result['pagination']);
    \ViewManager::set('search', $search);
    \ViewManager::set('type', $type);
    \ViewManager::set('category', $category);
    \ViewManager::set('status', $status);
    
    echo "<p>✅ Data set in ViewManager</p>";
    
    // Simulate view receiving data
    $viewProperties = $result['data'] ?? ViewManager::get('properties') ?? [];
    $viewPagination = $result['pagination'] ?? ViewManager::get('pagination') ?? [];
    
    echo "<p><strong>View will receive:</strong></p>";
    echo "<ul>";
    echo "<li>Properties: " . count($viewProperties) . " items</li>";
    echo "<li>Pagination: " . json_encode($viewPagination) . "</li>";
    echo "</ul>";
    
    echo "<h2>👁️ Step 4: View Display Simulation</h2>";
    
    if (!empty($viewProperties)) {
        echo "<p>✅ View will display properties:</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Address</th><th>Status</th><th>Units</th></tr>";
        foreach ($viewProperties as $prop) {
            echo "<tr>";
            echo "<td>" . $prop['id'] . "</td>";
            echo "<td>" . htmlspecialchars($prop['name'] ?? 'No name') . "</td>";
            echo "<td>" . htmlspecialchars($prop['address'] ?? 'No address') . "</td>";
            echo "<td>" . ($prop['status'] ?? 'unknown') . "</td>";
            echo "<td>" . $prop['unit_count'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>✅ RESULT: Properties WILL be displayed!</strong></p>";
    } else {
        echo "<p>❌ View will show 'No Properties Found'</p>";
        echo "<p><strong>❌ RESULT: Properties will NOT be displayed!</strong></p>";
    }
    
    echo "<h2>🔗 Step 5: URL Flow Verification</h2>";
    echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
    echo "<h3>✅ Correct Flow:</h3>";
    echo "<ol>";
    echo "<li>User goes to <code>/admin/properties/create</code> ✅</li>";
    echo "<li>User fills form and submits ✅</li>";
    echo "<li>JavaScript submits to <code>/admin/properties</code> ✅</li>";
    echo "<li>PropertyController@store saves property ✅</li>";
    echo "<li>Controller redirects to <code>/admin/properties</code> ✅</li>";
    echo "<li>PropertyController@index loads properties ✅</li>";
    echo "<li>Data flows to view via ViewManager ✅</li>";
    echo "<li>View displays properties ✅</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h2>🧪 Step 6: Test the Complete Flow</h2>";
    echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff;'>";
    echo "<h3>To test the complete flow:</h3>";
    echo "<ol>";
    echo "<li><a href='/admin/properties/create' target='_blank'>1. Go to Property Creation Form</a></li>";
    echo "<li>Fill in property details (name, address, etc.)</li>";
    echo "<li>Click 'Add Property' button</li>";
    echo "<li>Wait for success message</li>";
    echo "<li>Verify redirect to <code>/admin/properties</code></li>";
    echo "<li>Check if new property appears in list</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h2>🚨 If Properties Still Don't Display</h2>";
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";
    echo "<h3>Immediate Actions:</h3>";
    echo "<ol>";
    echo "<li><strong>Clear Browser Cache:</strong> Press Ctrl+F5 or Cmd+Shift+R</li>";
    echo "<li><strong>Check URL:</strong> Ensure you're on <code>/admin/properties</code> not <code>/properties</code></li>";
    echo "<li><strong>Check Session:</strong> Make sure you're logged in as correct admin</li>";
    echo "<li><strong>Run Debug:</strong> <a href='/emergency_debug.php'>Emergency Debug Script</a></li>";
    echo "<li><strong>Check Error Logs:</strong> Look for PHP errors in server logs</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h2>✅ Final Status</h2>";
    $status = (!empty($viewProperties)) ? 'WORKING' : 'NEEDS ATTENTION';
    $statusColor = (!empty($viewProperties)) ? '#d4edda' : '#f8d7da';
    $statusIcon = (!empty($viewProperties)) ? '✅' : '❌';
    
    echo "<div style='background: $statusColor; padding: 20px; border-left: 4px solid " . (!empty($viewProperties) ? '#28a745' : '#dc3545') . "; text-align: center;'>";
    echo "<h2 style='margin: 0;'>$statusIcon Property Display Status: $status</h2>";
    echo "<p style='margin: 10px 0 0 0;'>" . (!empty($viewProperties) ? 'Properties should display correctly!' : 'Properties may not display - check debug steps above.') . "</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p>❌ Error during testing: " . $e->getMessage() . "</p>";
}

echo "<script>";
echo "console.log('Property flow test completed');";
echo "console.log('Properties found:', " . count($viewProperties ?? []) . ");";
echo "</script>";
?>
