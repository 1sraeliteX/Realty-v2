<?php
// Final comprehensive debugging for property display issue
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>🔍 Final Property Display Debug</h1>";

// Check admin authentication
echo "<h2>🔐 Authentication Check</h2>";
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo "<p>❌ No admin session. Please <a href='/admin/login'>login first</a></p>";
    exit;
}

$adminId = $_SESSION['admin_id'];
$adminEmail = $_SESSION['admin_email'] ?? 'Unknown';
echo "<p>✅ Admin: $adminEmail (ID: $adminId)</p>";

// Check database connection
echo "<h2>🗄️ Database Connection</h2>";
try {
    $db = \Config\Database::getConnection();
    echo "<p>✅ Database connected</p>";
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    exit;
}

// Check if any properties exist at all
echo "<h2>📊 Database Properties Check</h2>";
$allSql = "SELECT id, name, admin_id, status, deleted_at, created_at FROM properties ORDER BY created_at DESC LIMIT 10";
$allProperties = $db->query($allSql)->fetch_all(MYSQLI_ASSOC);

echo "<p><strong>Total properties in database:</strong> " . count($allProperties) . "</p>";

if (!empty($allProperties)) {
    echo "<table border='1' style='width: 100%; border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Admin ID</th><th>Status</th><th>Deleted</th><th>Created</th></tr>";
    foreach ($allProperties as $prop) {
        $isYours = ($prop['admin_id'] == $adminId);
        $isDeleted = !is_null($prop['deleted_at']);
        $rowStyle = $isYours && !$isDeleted ? 'background-color: #d4edda;' : 
                   ($isDeleted ? 'background-color: #f8d7da;' : 'background-color: #fff3cd;');
        echo "<tr style='$rowStyle'>";
        echo "<td>" . $prop['id'] . "</td>";
        echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
        echo "<td>" . $prop['admin_id'] . " " . ($isYours ? "(YOU)" : "(OTHER)") . "</td>";
        echo "<td>" . $prop['status'] . "</td>";
        echo "<td>" . ($isDeleted ? "YES" : "NO") . "</td>";
        echo "<td>" . $prop['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><em>Green = Your active properties | Red = Deleted | Yellow = Other admins' properties</em></p>";
} else {
    echo "<p>❌ No properties found in database at all!</p>";
}

// Check properties specifically for this admin (using PropertyController query)
echo "<h2>🎯 Your Properties (PropertyController Query)</h2>";
$adminSql = "SELECT p.*, 
             (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
             (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
      FROM properties p 
      WHERE p.admin_id = ? AND p.deleted_at IS NULL
      ORDER BY p.created_at DESC";

$stmt = $db->prepare($adminSql);
$stmt->bind_param('i', $adminId);
$stmt->execute();
$adminProperties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo "<p><strong>Your active properties:</strong> " . count($adminProperties) . "</p>";

if (!empty($adminProperties)) {
    echo "<table border='1' style='width: 100%; border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Address</th><th>Status</th><th>Units</th><th>Created</th></tr>";
    foreach ($adminProperties as $prop) {
        echo "<tr style='background-color: #d4edda;'>";
        echo "<td>" . $prop['id'] . "</td>";
        echo "<td>" . htmlspecialchars($prop['name']) . "</td>";
        echo "<td>" . htmlspecialchars($prop['address'] ?? 'No address') . "</td>";
        echo "<td>" . $prop['status'] . "</td>";
        echo "<td>" . $prop['unit_count'] . "</td>";
        echo "<td>" . $prop['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ No active properties found for your admin account!</p>";
    
    // Check if there are deleted properties
    $deletedSql = "SELECT id, name, deleted_at FROM properties WHERE admin_id = ? AND deleted_at IS NOT NULL";
    $deletedStmt = $db->prepare($deletedSql);
    $deletedStmt->bind_param('i', $adminId);
    $deletedStmt->execute();
    $deletedProperties = $deletedStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    if (!empty($deletedProperties)) {
        echo "<p>⚠️ Found " . count($deletedProperties) . " deleted properties:</p>";
        echo "<ul>";
        foreach ($deletedProperties as $prop) {
            echo "<li>❌ " . htmlspecialchars($prop['name']) . " (deleted: " . $prop['deleted_at'] . ")</li>";
        }
        echo "</ul>";
    }
}

// Simulate the exact PropertyController index method
echo "<h2>🎮 PropertyController Simulation</h2>";
try {
    // Mock the exact same logic as PropertyController::index()
    $page = $_GET['page'] ?? 1;
    $search = $_GET['search'] ?? '';
    $type = $_GET['type'] ?? '';
    $category = $_GET['category'] ?? '';
    $status = $_GET['status'] ?? '';
    
    echo "<p><strong>Parameters:</strong> page=$page, search='$search', type='$type', category='$category', status='$status'</p>";
    
    // Build query (exact same as PropertyController)
    $where = ["p.admin_id = ?", "p.deleted_at IS NULL"];
    $params = [$adminId];
    
    if (!empty($search)) {
        $where[] = "(p.name LIKE ? OR p.address LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($type)) {
        $where[] = "p.type = ?";
        $params[] = $type;
    }
    
    if (!empty($category)) {
        $where[] = "p.category = ?";
        $params[] = $category;
    }
    
    if (!empty($status)) {
        $where[] = "p.status = ?";
        $params[] = $status;
    }
    
    $whereClause = implode(' AND ', $where);
    
    // Get properties with unit counts (exact same query)
    $sql = "SELECT p.*, 
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
            FROM properties p 
            WHERE {$whereClause}
            ORDER BY p.created_at DESC";
    
    echo "<p><strong>SQL Query:</strong> " . htmlspecialchars($sql) . "</p>";
    echo "<p><strong>Parameters:</strong> [" . implode(', ', array_map(function($p) { return "'$p'"; }, $params)) . "]</p>";
    
    $stmt = $db->prepare($sql);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo "<p><strong>Query Results:</strong> " . count($properties) . " properties found</p>";
    
    // Simulate pagination
    $total = count($properties);
    $perPage = 10;
    $currentPage = $page;
    $totalPages = ceil($total / $perPage);
    $hasPrev = $currentPage > 1;
    $hasNext = $currentPage < $totalPages;
    
    $pagination = [
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'total' => $total,
        'total_pages' => $totalPages,
        'has_prev' => $hasPrev,
        'has_next' => $hasNext
    ];
    
    echo "<p><strong>Pagination:</strong> " . json_encode($pagination) . "</p>";
    
    // This is what should be passed to the view
    echo "<h3>📤 Data Sent to View:</h3>";
    echo "<div style='background: #f8f9fa; padding: 10px; border-left: 4px solid #6c757d;'>";
    echo "<strong>properties:</strong> " . count($properties) . " items<br>";
    echo "<strong>pagination:</strong> " . json_encode($pagination) . "<br>";
    echo "<strong>search:</strong> '$search'<br>";
    echo "<strong>type:</strong> '$type'<br>";
    echo "<strong>category:</strong> '$category'<br>";
    echo "<strong>status:</strong> '$status'<br>";
    echo "</div>";
    
    // Show what the view should receive
    echo "<h3>👁️ View Data Simulation:</h3>";
    echo "<div style='background: #e7f3ff; padding: 10px; border-left: 4px solid #007bff;'>";
    echo "<code>\$properties = " . (empty($properties) ? '[] (EMPTY!)' : '[' . count($properties) . ' items]') . ";</code><br>";
    echo "<code>\$pagination = " . json_encode($pagination) . ";</code><br>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p>❌ Controller simulation error: " . $e->getMessage() . "</p>";
}

// Check recent property creation attempts
echo "<h2>🕐 Recent Activity (Last 10 Minutes)</h2>";
$recentSql = "SELECT id, name, admin_id, created_at FROM properties WHERE created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) ORDER BY created_at DESC";
$recentProperties = $db->query($recentSql)->fetch_all(MYSQLI_ASSOC);

if (!empty($recentProperties)) {
    echo "<p>Found " . count($recentProperties) . " recently created properties:</p>";
    echo "<ul>";
    foreach ($recentProperties as $prop) {
        $isYours = ($prop['admin_id'] == $adminId);
        echo "<li><strong>" . htmlspecialchars($prop['name']) . "</strong> by Admin " . $prop['admin_id'] . " " . ($isYours ? "✅(YOURS)" : "❌(OTHER)") . " at " . $prop['created_at'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No properties created in the last 10 minutes.</p>";
}

echo "<h2>🚨 Diagnosis & Solution</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>";

if (empty($adminProperties)) {
    echo "<h3>❌ DIAGNOSIS: No properties found for your admin account</h3>";
    echo "<p><strong>Possible causes:</strong></p>";
    echo "<ol>";
    echo "<li>Property creation is failing but showing success message</li>";
    echo "<li>Property is being saved with wrong admin_id</li>";
    echo "<li>Property is being marked as deleted immediately</li>";
    echo "<li>Database transaction is being rolled back</li>";
    echo "</ol>";
    
    echo "<h3>🛠️ SOLUTION:</h3>";
    echo "<ol>";
    echo "<li>Check the PropertyController@store method for errors</li>";
    echo "<li>Look at PHP error logs for database errors</li>";
    echo "<li>Test property creation with <a href='/test_property_creation.php'>test script</a></li>";
    echo "<li>Check if admin session is correct during property creation</li>";
    echo "</ol>";
    
} else {
    echo "<h3>⚠️ DIAGNOSIS: Properties exist but not displaying</h3>";
    echo "<p><strong>Possible causes:</strong></p>";
    echo "<ol>";
    echo "<li>Data not being passed from controller to view correctly</li>";
    echo "<li>View not reading the passed data</li>";
    echo "<li>URL routing to wrong controller method</li>";
    echo "<li>Browser cache issue</li>";
    echo "</ol>";
    
    echo "<h3>🛠️ SOLUTION:</h3>";
    echo "<ol>";
    echo "<li>Clear browser cache: Ctrl+F5 or Cmd+Shift+R</li>";
    echo "<li>Ensure you're on /admin/properties not /properties</li>";
    echo "<li>Check browser developer tools for JavaScript errors</li>";
    echo "<li>Verify the PropertyController fixes are applied</li>";
    echo "</ol>";
}

echo "</div>";

echo "<h2>🧪 Immediate Tests</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
echo "<ol>";
echo "<li><strong>Test Property Creation:</strong> <a href='/test_property_creation.php'>Run Test Script</a></li>";
echo "<li><strong>Check Current URL:</strong> Ensure you're on <code>/admin/properties</code></li>";
echo "<li><strong>Try Adding Property:</strong> <a href='/admin/properties/create'>Add New Property</a></li>";
echo "<li><strong>Check Error Logs:</strong> Look for PHP errors in server logs</li>";
echo "<li><strong>Clear Browser Cache:</strong> Hard refresh the page</li>";
echo "</ol>";
echo "</div>";

echo "<script>";
echo "console.log('Property display debug completed');";
echo "console.log('Current URL:', window.location.href);";
echo "console.log('Admin ID:', '$adminId');";
echo "</script>";
?>
