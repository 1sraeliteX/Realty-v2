<?php
// Start session
session_start();

// Mock admin user for testing
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Test Admin';
$_SESSION['admin_role'] = 'admin';

// Include required files
require_once 'app/controllers/BaseController.php';

// Create a mock controller to test the getDashboardStats method
require_once 'app/controllers/AdminDashboardController.php';

class TestDashboardController extends App\Controllers\AdminDashboardController {
    public function testGetDashboardStats() {
        return $this->getDashboardStats(1);
    }
}

try {
    $controller = new TestDashboardController();
    $stats = $controller->testGetDashboardStats();
    
    echo "✅ Dashboard stats loaded successfully!\n\n";
    echo "Stats:\n";
    foreach ($stats as $key => $value) {
        echo "- $key: $value\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
