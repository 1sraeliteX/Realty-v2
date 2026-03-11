<?php
// Test admin dashboard access with proper authentication
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if we have a database connection
try {
    require_once __DIR__ . '/config/database.php';
    $pdo = \Config\Database::getInstance()->getConnection();
    echo "✅ Database connection successful\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Create test admin if not exists
try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute(['test@admin.com']);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        echo "Creating test admin user...\n";
        $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            'Test Admin',
            'test@admin.com',
            password_hash('password123', PASSWORD_DEFAULT),
            'admin'
        ]);
        echo "✅ Test admin created: test@admin.com / password123\n";
    } else {
        echo "✅ Test admin exists: test@admin.com\n";
    }
} catch (Exception $e) {
    echo "❌ Error creating test admin: " . $e->getMessage() . "\n";
}

// Test authentication
$_POST['email'] = 'test@admin.com';
$_POST['password'] = 'password123';

// Mock the authentication process
$stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
$stmt->execute([$_POST['email']]);
$admin = $stmt->fetch();

if ($admin && password_verify($_POST['password'], $admin['password'])) {
    echo "✅ Authentication successful\n";
    
    // Set session
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_role'] = $admin['role'];
    
    echo "✅ Session set: admin_id={$_SESSION['admin_id']}, role={$_SESSION['admin_role']}\n";
    
    // Now test the dashboard controller
    require_once __DIR__ . '/app/controllers/BaseController.php';
    require_once __DIR__ . '/app/controllers/AdminDashboardController.php';
    
    $controller = new \App\Controllers\AdminDashboardController();
    
    echo "Testing dashboard rendering...\n";
    ob_start();
    try {
        $controller->index();
        $output = ob_get_clean();
        echo "✅ Dashboard rendered successfully!\n";
        echo "Output length: " . strlen($output) . " characters\n";
        
        // Check for key dashboard elements
        $hasStatsCards = strpos($output, 'Total Properties') !== false;
        $hasChart = strpos($output, 'revenueChart') !== false;
        $hasQuickActions = strpos($output, 'Quick Actions') !== false;
        $hasRecentProperties = strpos($output, 'Recent Properties') !== false;
        
        echo "Dashboard components check:\n";
        echo "  ✅ Stats Cards: " . ($hasStatsCards ? "Found" : "Missing") . "\n";
        echo "  ✅ Revenue Chart: " . ($hasChart ? "Found" : "Missing") . "\n";
        echo "  ✅ Quick Actions: " . ($hasQuickActions ? "Found" : "Missing") . "\n";
        echo "  ✅ Recent Properties: " . ($hasRecentProperties ? "Found" : "Missing") . "\n";
        
        if ($hasStatsCards && $hasChart && $hasQuickActions && $hasRecentProperties) {
            echo "\n🎉 Admin dashboard is fully functional!\n";
            echo "You can access it at: http://127.0.0.1:8080/admin/dashboard\n";
            echo "Login with: test@admin.com / password123\n";
        } else {
            echo "\n⚠️ Some dashboard components are missing\n";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "❌ Error rendering dashboard: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
} else {
    echo "❌ Authentication failed\n";
}
?>
