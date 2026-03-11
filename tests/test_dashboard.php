<?php
// Test admin dashboard functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Mock admin session for testing
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'admin@test.com';
$_SESSION['admin_role'] = 'admin';

// Initialize framework
require_once __DIR__ . '/config/init_framework.php';

// Mock the AdminDashboardController
class MockAdminDashboardController {
    public function requireAuth() {
        return [
            'id' => 1,
            'email' => 'admin@test.com',
            'role' => 'admin'
        ];
    }
    
    public function index() {
        // Initialize framework
        require_once __DIR__ . '/config/init_framework.php';
        
        // Mock dashboard data
        $stats = [
            'total_properties' => 12,
            'total_units' => 48,
            'active_tenants' => 42,
            'occupancy_rate' => 87.5,
            'monthly_revenue' => 45000,
            'occupied_units' => 42,
            'pending_payments' => 3,
            'maintenanceRequests' => 5,
            'newApplications' => 8
        ];
        
        $recentProperties = [
            [
                'id' => 1,
                'name' => 'Sunset Apartments',
                'address' => '123 Main St',
                'type' => 'Apartment',
                'status' => 'occupied',
                'image' => '/assets/images/property1.jpg',
                'unit_count' => 12,
                'occupied_units' => 10
            ],
            [
                'id' => 2,
                'name' => 'Downtown Plaza',
                'address' => '456 Oak Ave',
                'type' => 'Commercial',
                'status' => 'available',
                'image' => '/assets/images/property2.jpg',
                'unit_count' => 8,
                'occupied_units' => 6
            ]
        ];
        
        $activities = [
            [
                'action' => 'payment',
                'description' => 'Payment received from John Doe',
                'property_name' => 'Sunset Apartments',
                'created_at' => '2026-03-11 10:30:00'
            ],
            [
                'action' => 'maintenance',
                'description' => 'Maintenance request submitted',
                'property_name' => 'Downtown Plaza',
                'created_at' => '2026-03-11 09:15:00'
            ]
        ];
        
        $revenueData = [
            '2025-04' => 42000,
            '2025-05' => 43500,
            '2025-06' => 44100,
            '2025-07' => 43800,
            '2025-08' => 44500,
            '2025-09' => 45200,
            '2025-10' => 44900,
            '2025-11' => 45600,
            '2025-12' => 46300,
            '2026-01' => 45000,
            '2026-02' => 44700,
            '2026-03' => 45000
        ];
        
        // Set data through ViewManager
        ViewManager::set('title', 'Admin Dashboard');
        ViewManager::set('stats', $stats);
        ViewManager::set('recentActivities', $activities);
        ViewManager::set('recentProperties', $recentProperties);
        ViewManager::set('revenueData', $revenueData);
        ViewManager::set('maintenanceRequests', []);
        ViewManager::set('newApplications', []);
        
        return ViewManager::render('admin.dashboard_enhanced', [], 'admin.dashboard_layout');
    }
}

// Test the dashboard
$controller = new MockAdminDashboardController();
try {
    $output = $controller->index();
    echo "Dashboard rendered successfully!\n";
    echo "Output length: " . strlen($output) . " characters\n";
    echo "First 500 characters:\n";
    echo substr($output, 0, 500) . "...\n";
} catch (Exception $e) {
    echo "Error rendering dashboard: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
