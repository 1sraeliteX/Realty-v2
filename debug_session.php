<?php
session_start();

echo "<h2>Session Debug</h2>";

// Show current session data
echo "<h3>Current Session Data:</h3>";
if (empty($_SESSION)) {
    echo "<p style='color: orange;'>No session data found</p>";
} else {
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
}

// Test setting session data
echo "<h3>Testing Session Set:</h3>";
$_SESSION['test'] = 'Hello World';
$_SESSION['admin_id'] = 3;
$_SESSION['admin_name'] = 'Test Admin';
$_SESSION['admin_email'] = 'admin@cornerstone.com';
$_SESSION['admin_role'] = 'admin';

echo "<p>Session data set. <a href='debug_session.php'>Refresh to check persistence</a></p>";

// Check session configuration
echo "<h3>Session Configuration:</h3>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session status: " . session_status() . "</p>";
echo "<p>Session save path: " . session_save_path() . "</p>";
echo "<p>Session cookie params: " . print_r(session_get_cookie_params(), true) . "</p>";

// Test database connection for admin
echo "<h3>Test Admin Lookup:</h3>";
try {
    require_once __DIR__ . '/config/config_simple.php';
    require_once __DIR__ . '/config/database.php';
    
    $db = Config\Database::getInstance();
    $stmt = $db->getConnection()->prepare("SELECT * FROM admins WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([3]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p style='color: green;'>✓ Admin found: " . $admin['name'] . " (" . $admin['email'] . ")</p>";
    } else {
        echo "<p style='color: red;'>✗ Admin not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}

// Clear test session
if (isset($_GET['clear'])) {
    session_destroy();
    echo "<p>Session cleared. <a href='debug_session.php'>Start over</a></p>";
} else {
    echo "<p><a href='debug_session.php?clear=1'>Clear session</a></p>";
}
?>
