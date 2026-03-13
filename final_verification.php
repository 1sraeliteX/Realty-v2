<?php
// Final verification that all property display fixes are applied
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>✅ Final Verification: Property Display Fix</h1>";

echo "<h2>🔧 Fix Status Check</h2>";

$checks = [
    'PropertyController data passing' => false,
    'View data receiving' => false, 
    'URL redirects fixed' => false,
    'JavaScript fetch fixed' => false,
    'Anti-scattering compliance' => false
];

// Check 1: PropertyController data passing
$controllerContent = file_get_contents(__DIR__ . '/app/controllers/PropertyController.php');
if (strpos($controllerContent, "'properties' => \$result['data']") !== false) {
    $checks['PropertyController data passing'] = true;
}

// Check 2: View data receiving  
$viewContent = file_get_contents(__DIR__ . '/views/properties/index.php');
if (strpos($viewContent, "\$properties = \$properties ?? [];") !== false) {
    $checks['View data receiving'] = true;
}

// Check 3: URL redirects fixed
$createContent = file_get_contents(__DIR__ . '/views/properties/create.php');
if (strpos($createContent, 'href="/admin/properties"') !== false &&
    strpos($createContent, "window.location.href='/admin/properties'") !== false) {
    $checks['URL redirects fixed'] = true;
}

// Check 4: JavaScript fetch fixed
if (strpos($createContent, "fetch('/admin/properties',") !== false) {
    $checks['JavaScript fetch fixed'] = true;
}

// Check 5: Anti-scattering compliance
if (strpos($viewContent, "ComponentRegistry::load") !== false &&
    strpos($viewContent, "ViewManager::set") !== false) {
    $checks['Anti-scattering compliance'] = true;
}

echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;'>";

foreach ($checks as $check => $status) {
    $color = $status ? '#d4edda' : '#f8d7da';
    $icon = $status ? '✅' : '❌';
    echo "<div style='background: $color; padding: 15px; border-left: 4px solid " . ($status ? '#28a745' : '#dc3545') . ";'>";
    echo "<strong>$icon $check</strong><br>";
    echo "<small>Status: " . ($status ? 'APPLIED' : 'MISSING') . "</small>";
    echo "</div>";
}

echo "</div>";

$allFixed = array_reduce($checks, function($carry, $item) { return $carry && $item; }, true);

if ($allFixed) {
    echo "<div style='background: #d4edda; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0;'>";
    echo "<h2>🎉 ALL FIXES SUCCESSFULLY APPLIED!</h2>";
    echo "<p>The property display feature should now work correctly.</p>";
    echo "</div>";
    
    echo "<h2>🧪 Test the Complete Flow</h2>";
    echo "<ol>";
    echo "<li><a href='/admin/properties/create'>1. Add New Property</a></li>";
    echo "<li>Fill out the form and submit</li>";
    echo "<li>Verify redirect to /admin/properties</li>";
    echo "<li>Check that new property appears in list</li>";
    echo "</ol>";
    
} else {
    echo "<div style='background: #f8d7da; padding: 20px; border-left: 4px solid #dc3545; margin: 20px 0;'>";
    echo "<h2>❌ SOME FIXES MISSING</h2>";
    echo "<p>Not all fixes have been applied correctly.</p>";
    echo "</div>";
}

echo "<h2>📋 Quick Test</h2>";
session_start();
if (isset($_SESSION['admin_id'])) {
    try {
        $db = \Config\Database::getConnection();
        $count = $db->query("SELECT COUNT(*) as count FROM properties WHERE admin_id = " . $_SESSION['admin_id'] . " AND deleted_at IS NULL")->fetch_assoc();
        echo "<p>Current properties in database: <strong>" . $count['count'] . "</strong></p>";
        
        if ($count['count'] > 0) {
            echo "<p>✅ Properties exist - they should display in the list</p>";
        } else {
            echo "<p>ℹ️ No properties yet - try adding one</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Please <a href='/admin/login'>login as admin</a> first</p>";
}

echo "<script>";
echo "console.log('Final verification completed');";
echo "if (" . ($allFixed ? 'true' : 'false') . ") {";
echo "    console.log('✅ All fixes applied successfully');";
echo "} else {";
echo "    console.log('❌ Some fixes missing');";
echo "}";
echo "</script>";
?>
