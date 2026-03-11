<?php
// Debug dashboard authentication and data loading
session_start();

echo "<h1>Dashboard Debug</h1>";

// Check session
echo "<h2>Session Status</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Data: " . print_r($_SESSION, true) . "\n";
echo "</pre>";

// Check if admin is logged in
if (isset($_SESSION['admin_id'])) {
    echo "<h3>✅ Admin Session Found</h3>";
    echo "Admin ID: " . $_SESSION['admin_id'] . "<br>";
    
    // Try to load admin from database
    try {
        require_once __DIR__ . '/config/database.php';
        $pdo = Config\Database::getInstance()->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            echo "<h3>✅ Admin Found in Database</h3>";
            echo "<pre>" . print_r($admin, true) . "</pre>";
        } else {
            echo "<h3>❌ Admin Not Found in Database</h3>";
        }
    } catch (Exception $e) {
        echo "<h3>❌ Database Error: " . $e->getMessage() . "</h3>";
    }
} else {
    echo "<h3>❌ No Admin Session Found</h3>";
    echo "<p>You need to <a href='/admin/login'>login first</a></p>";
}

// Test framework initialization
echo "<h2>Framework Test</h2>";
try {
    require_once __DIR__ . '/config/init_framework.php';
    echo "<h3>✅ Framework Initialized</h3>";
    
    // Test ComponentRegistry
    ComponentRegistry::load('ui-components');
    echo "<h3>✅ UI Components Loaded</h3>";
    
    // Test DataProvider
    $stats = DataProvider::get('dashboard_stats');
    echo "<h3>✅ Data Provider Working</h3>";
    echo "<pre>Dashboard Stats: " . print_r($stats, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<h3>❌ Framework Error: " . $e->getMessage() . "</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test view file
echo "<h2>View File Test</h2>";
$viewPath = __DIR__ . '/views/admin/dashboard_enhanced.php';
if (file_exists($viewPath)) {
    echo "<h3>✅ View File Exists</h3>";
    echo "Path: " . $viewPath . "<br>";
    echo "Size: " . filesize($viewPath) . " bytes<br>";
} else {
    echo "<h3>❌ View File Not Found</h3>";
    echo "Expected Path: " . $viewPath . "<br>";
}

// Test simple layout
echo "<h2>Layout Test</h2>";
$layoutPath = __DIR__ . '/views/simple_layout.php';
if (file_exists($layoutPath)) {
    echo "<h3>✅ Simple Layout Exists</h3>";
} else {
    echo "<h3>❌ Simple Layout Not Found</h3>";
}

// Quick login form for testing
if (!isset($_SESSION['admin_id'])) {
    echo "<h2>Quick Login (for testing)</h2>";
    echo "<form method='post' action='/admin/login'>";
    echo "<input type='hidden' name='email' value='admin@cornerstone.com'>";
    echo "<input type='hidden' name='password' value='admin123'>";
    echo "<button type='submit'>Login as Admin</button>";
    echo "</form>";
}
?>
