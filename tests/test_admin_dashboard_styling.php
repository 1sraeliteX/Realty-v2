<?php
// Test script to check admin dashboard styling
session_start();

// Mock admin authentication
$_SESSION['admin'] = [
    'id' => 1,
    'name' => 'Test Admin',
    'email' => 'admin@test.com',
    'role' => 'admin'
];

// Initialize framework
require_once __DIR__ . '/config/init_framework.php';

// Mock controller logic
class TestAdminDashboardController {
    public function index() {
        // Load components through registry (anti-scattering compliant)
        \ComponentRegistry::load('ui-components');
        
        // Mock dashboard statistics
        $stats = [
            'total_properties' => 15,
            'total_units' => 44,
            'active_tenants' => 37,
            'occupancy_rate' => 84.1,
            'monthly_revenue' => 25000,
            'occupied_units' => 37,
            'pending_payments' => 8,
            'maintenance_requests' => 3,
            'new_applications' => 2
        ];
        
        // Mock recent properties
        $recentProperties = [
            [
                'id' => 1,
                'name' => 'Sunset Apartments',
                'address' => '123 Main St, City, State 12345',
                'type' => 'Apartment Complex',
                'status' => 'occupied',
                'unit_count' => 8,
                'occupied_units' => 7,
                'image' => '/assets/images/property1.jpg'
            ],
            [
                'id' => 2,
                'name' => 'Oak Villa Complex',
                'address' => '456 Oak Ave, City, State 67890',
                'type' => 'Luxury Villa',
                'status' => 'available',
                'unit_count' => 6,
                'occupied_units' => 5,
                'image' => '/assets/images/property2.jpg'
            ]
        ];
        
        // Mock activities
        $activities = [
            [
                'id' => 1,
                'action' => 'payment',
                'description' => 'Rent payment received from Alice Johnson',
                'property_name' => 'Sunset Apartments',
                'created_at' => '2024-03-15 10:30:00'
            ],
            [
                'id' => 2,
                'action' => 'maintenance',
                'description' => 'Maintenance request completed for Unit 2B',
                'property_name' => 'Oak Villa Complex',
                'created_at' => '2024-03-15 09:15:00'
            ]
        ];
        
        // Mock revenue data
        $revenueData = [
            '2023-04' => 22000,
            '2023-05' => 23500,
            '2023-06' => 24100,
            '2023-07' => 23800,
            '2023-08' => 24500,
            '2023-09' => 25200,
            '2023-10' => 24900,
            '2023-11' => 25600,
            '2023-12' => 26300,
            '2024-01' => 24800,
            '2024-02' => 25400,
            '2024-03' => 25000
        ];
        
        // Mock maintenance requests
        $maintenanceRequests = [
            [
                'id' => 1,
                'property_name' => 'Sunset Apartments',
                'unit' => 'Unit 5A',
                'issue' => 'HVAC Repair',
                'priority' => 'urgent',
                'status' => 'pending',
                'created_at' => '2024-03-15 08:00:00'
            ]
        ];
        
        // Mock new applications
        $newApplications = [
            [
                'id' => 1,
                'name' => 'Sarah Johnson',
                'property_name' => 'Sunset Apartments',
                'unit' => 'Unit 3C',
                'status' => 'pending',
                'created_at' => '2024-03-15 11:30:00'
            ]
        ];
        
        // Set data through ViewManager (anti-scattering compliant)
        \ViewManager::set('title', 'Admin Dashboard');
        \ViewManager::set('stats', $stats);
        \ViewManager::set('recentActivities', $activities);
        \ViewManager::set('recentProperties', $recentProperties);
        \ViewManager::set('revenueData', $revenueData);
        \ViewManager::set('maintenanceRequests', $maintenanceRequests);
        \ViewManager::set('newApplications', $newApplications);
        
        // Set user data
        \ViewManager::set('user', [
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'avatar' => null,
            'role' => 'Administrator'
        ]);
        
        // Set notifications
        \ViewManager::set('notifications', [
            ['id' => 1, 'type' => 'info', 'message' => 'New tenant application received', 'time' => '5 min ago', 'read' => false],
            ['id' => 2, 'type' => 'warning', 'message' => 'Rent payment overdue for Unit 3A', 'time' => '1 hour ago', 'read' => false]
        ]);
        
        // Render using ViewManager with admin dashboard layout (anti-scattering compliant)
        echo \ViewManager::render('admin.dashboard_enhanced', [], 'admin.dashboard_layout');
    }
}

// Run the test
$controller = new TestAdminDashboardController();
$controller->index();
?>
