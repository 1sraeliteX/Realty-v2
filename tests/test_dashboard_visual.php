<?php
// Visual dashboard test with mock data
require_once __DIR__ . '/config/init_framework.php';

// Mock dashboard data
$stats = [
    'total_properties' => 24,
    'total_units' => 156,
    'active_tenants' => 142,
    'occupancy_rate' => 91.2,
    'monthly_revenue' => 124500,
    'occupied_units' => 142,
    'pending_payments' => 8,
    'maintenance_requests' => 3,
    'new_applications' => 5
];

$recentProperties = [
    [
        'id' => 1,
        'name' => 'Sunset Apartments',
        'address' => '123 Main St, City',
        'unit_count' => 12,
        'occupied_units' => 11,
        'status' => 'occupied',
        'type' => 'Apartment',
        'image' => 'https://via.placeholder.com/150'
    ],
    [
        'id' => 2,
        'name' => 'Downtown Plaza',
        'address' => '456 Oak Ave, City',
        'unit_count' => 8,
        'occupied_units' => 7,
        'status' => 'available',
        'type' => 'Commercial',
        'image' => 'https://via.placeholder.com/150'
    ]
];

$activities = [
    [
        'action' => 'payment',
        'description' => 'Payment received for Unit 3A',
        'property_name' => 'Sunset Apartments',
        'created_at' => '2024-01-15 10:30:00'
    ],
    [
        'action' => 'maintenance',
        'description' => 'Maintenance request submitted',
        'property_name' => 'Downtown Plaza',
        'created_at' => '2024-01-15 09:15:00'
    ]
];

$revenueData = [
    '2024-01' => 120000,
    '2024-02' => 115000,
    '2024-03' => 125000,
    '2024-04' => 130000,
    '2024-05' => 124500,
    '2024-06' => 128000,
    '2024-07' => 132000,
    '2024-08' => 129000,
    '2024-09' => 126000,
    '2024-10' => 124500,
    '2024-11' => 127000,
    '2024-12' => 130000
];

// Set data for ViewManager
ViewManager::set('title', 'Admin Dashboard - Visual Test');
ViewManager::set('stats', $stats);
ViewManager::set('recentProperties', $recentProperties);
ViewManager::set('activities', $activities);
ViewManager::set('revenueData', $revenueData);
ViewManager::set('maintenanceRequests', []);
ViewManager::set('newApplications', []);

// Render the dashboard
echo ViewManager::render('admin.dashboard_enhanced', [], 'simple_layout');
?>
