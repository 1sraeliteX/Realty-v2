<?php
require_once __DIR__ . '/../config/bootstrap.php';
session_start();

echo "<h2 style='font-family:monospace'>Realty-v2 Property Debug Report</h2>";

// 1. DB Connection
echo "<h3>1. Database Connection</h3>";
try {
    $db = \App\Config\Database::getInstance();
    echo "<p style='color:green'>✅ DB connected</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ DB failed: " . $e->getMessage() . "</p>";
}

// 2. Properties table exists and has data
echo "<h3>2. Properties Table</h3>";
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM properties");
    $count = $stmt->fetchColumn();
    echo "<p>Total properties in DB: <strong>{$count}</strong></p>";
    
    $stmt2 = $db->query("SELECT id, name, type, status, admin_id, created_at FROM properties ORDER BY created_at DESC LIMIT 5");
    $props = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    if (empty($props)) {
        echo "<p style='color:orange'>⚠️ No properties found in table</p>";
    } else {
        echo "<table border='1' cellpadding='4' style='font-family:monospace;font-size:12px'>";
        echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Status</th><th>Admin ID</th><th>Created</th></tr>";
        foreach ($props as $p) {
            echo "<tr>";
            foreach ($p as $v) echo "<td>" . htmlspecialchars($v ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Query failed: " . $e->getMessage() . "</p>";
}

// 3. Units table
echo "<h3>3. Units Table</h3>";
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM units");
    $count = $stmt->fetchColumn();
    echo "<p>Total units in DB: <strong>{$count}</strong></p>";
} catch (Exception $e) {
    echo "<p style='color:orange'>⚠️ Units table issue: " . $e->getMessage() . "</p>";
}

// 4. Session / Auth
echo "<h3>4. Session & Auth</h3>";
echo "<pre>";
echo "Session admin_id: " . ($_SESSION['admin_id'] ?? 'NOT SET') . "\n";
echo "Session admin: ";
var_dump($_SESSION['admin'] ?? null);
echo "</pre>";

// 5. PropertyController index() query simulation
echo "<h3>5. PropertyController index() Query Simulation</h3>";
try {
    $adminId = $_SESSION['admin']['id'] ?? $_SESSION['admin_id'] ?? null;
    if (!$adminId) {
        echo "<p style='color:orange'>⚠️ No admin_id in session — log in first then revisit this page</p>";
    } else {
        $sql = "
            SELECT p.*, 
                COUNT(u.id) AS unit_count,
                SUM(CASE WHEN u.status = 'occupied' THEN 1 ELSE 0 END) AS occupied_units
            FROM properties p
            LEFT JOIN units u ON u.property_id = p.id
            WHERE p.admin_id = :admin_id AND p.deleted_at IS NULL
            GROUP BY p.id
            ORDER BY p.created_at DESC
            LIMIT 20
        ";
        $stmt = $db->query($sql, ['admin_id' => $adminId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<p>Properties returned for admin_id={$adminId}: <strong>" . count($results) . "</strong></p>";
        if (!empty($results)) {
            echo "<table border='1' cellpadding='4' style='font-family:monospace;font-size:12px'>";
            echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Status</th><th>unit_count</th><th>occupied_units</th></tr>";
            foreach ($results as $r) {
                echo "<tr>
                    <td>{$r['id']}</td>
                    <td>" . htmlspecialchars($r['name']) . "</td>
                    <td>{$r['type']}</td>
                    <td>{$r['status']}</td>
                    <td>{$r['unit_count']}</td>
                    <td>{$r['occupied_units']}</td>
                </tr>";
            }
            echo "</table>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Query failed: " . $e->getMessage() . "</p>";
}

// 6. Check create form POST route
echo "<h3>6. Route Check</h3>";
echo "<p>POST /admin/properties → should hit PropertyController::store()</p>";
echo "<p>Check routes/web.php has: <code>'POST /admin/properties' => 'PropertyController@store'</code></p>";

// 7. PHP error log (last 20 lines)
echo "<h3>7. Recent PHP Errors</h3>";
$logPath = 'C:/xampp/logs/php_error_log';
if (file_exists($logPath)) {
    $lines = array_slice(file($logPath), -20);
    echo "<pre style='font-size:11px;background:#111;color:#f88;padding:10px'>";
    foreach ($lines as $line) echo htmlspecialchars($line);
    echo "</pre>";
} else {
    echo "<p style='color:orange'>⚠️ Log not found at {$logPath}</p>";
}

// 8. add.php section margin check
echo "<h3>8. add.php Section Wrapper Classes</h3>";
$addPhp = file_get_contents(__DIR__ . '/../views/admin/properties/add.php');
preg_match_all('/(Step \d[^>]*-->)\s*\n\s*(<div class="[^"]*")/', $addPhp, $matches);
echo "<pre>";
foreach ($matches[0] as $m) {
    echo htmlspecialchars(trim($m)) . "\n\n";
}
echo "</pre>";

echo "<hr><p style='font-family:monospace;color:#888'>End of debug report</p>";
?>
