<?php

namespace App\Controllers;

use DataProvider;
use ViewManager;

// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../config/init_framework.php';

class ReportController extends BaseController {
    public function dashboardReports() {
        $admin = $this->requireAuth();
        
        // Get data from centralized provider (anti-scattering compliant)
        $maintenanceRequests = DataProvider::get('maintenance');
        $properties = DataProvider::get('properties');
        $tenants = DataProvider::get('tenants');
        
        // Calculate maintenance stats
        $maintenanceStats = [
            'total' => count($maintenanceRequests),
            'pending' => count(array_filter($maintenanceRequests, fn($r) => $r['status'] === 'pending')),
            'in_progress' => count(array_filter($maintenanceRequests, fn($r) => $r['status'] === 'in_progress')),
            'completed' => count(array_filter($maintenanceRequests, fn($r) => $r['status'] === 'completed')),
            'high_priority' => count(array_filter($maintenanceRequests, fn($r) => $r['priority'] === 'high'))
        ];
        
        // Prepare form data for creating maintenance requests
        $categories = [
            ['value' => 'plumbing', 'label' => 'Plumbing'],
            ['value' => 'electrical', 'label' => 'Electrical'],
            ['value' => 'hvac', 'label' => 'HVAC'],
            ['value' => 'appliance', 'label' => 'Appliance'],
            ['value' => 'structural', 'label' => 'Structural'],
            ['value' => 'other', 'label' => 'Other']
        ];
        
        $priorities = [
            ['value' => 'low', 'label' => 'Low'],
            ['value' => 'medium', 'label' => 'Medium'],
            ['value' => 'high', 'label' => 'High'],
            ['value' => 'urgent', 'label' => 'Urgent']
        ];
        
        // Prepare report data for export
        $reportData = [
            'properties' => $properties,
            'tenants' => $tenants,
            'maintenanceRequests' => $maintenanceRequests,
            'maintenanceStats' => $maintenanceStats,
            'revenueData' => [
                'monthly' => [450000, 520000, 480000, 610000, 580000, 670000, 720000, 690000, 750000, 810000, 780000, 850000],
                'expenses' => [120000, 135000, 125000, 145000, 140000, 155000, 165000, 160000, 170000, 180000, 175000, 190000],
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            ],
            'occupancyData' => [
                'occupied' => 45,
                'vacant' => 12,
                'maintenance' => 3
            ],
            'paymentStats' => [
                'on_time' => 38,
                'late' => 5,
                'overdue' => 2
            ]
        ];

        // Set data through ViewManager (anti-scattering compliant)
        ViewManager::set('reportData', $reportData);
        ViewManager::set('maintenanceStats', $maintenanceStats);
        ViewManager::set('categories', $categories);
        ViewManager::set('priorities', $priorities);
        
        // Render using the standard layout pattern (anti-scattering compliant)
        ViewManager::set('title', 'Dashboard Reports');
        ViewManager::set('content', 'admin/reports/dashboard_reports');
        echo ViewManager::render('admin.dashboard_layout');
    }
    
    public function index() {
        $admin = $this->requireAuth();
        
        // Get data from centralized provider (anti-scattering compliant)
        $stats = DataProvider::get('dashboard_stats', [
            'total_properties' => 24,
            'total_units' => 156,
            'active_tenants' => 142,
            'occupancy_rate' => 91,
            'monthly_revenue' => 2450000,
            'occupied_units' => 142,
            'pending_payments' => 8,
            'maintenanceRequests' => 12,
            'newApplications' => 5
        ]);
        
        $recentProperties = DataProvider::get('properties', []);
        $activities = DataProvider::get('recent_activities', []);
        $revenueData = DataProvider::get('revenue_data', [
            '2024-01' => 2100000,
            '2024-02' => 2250000,
            '2024-03' => 2150000,
            '2024-04' => 2300000,
            '2024-05' => 2450000,
            '2024-06' => 2380000,
            '2024-07' => 2520000,
            '2024-08' => 2480000,
            '2024-09' => 2410000,
            '2024-10' => 2550000,
            '2024-11' => 2490000,
            '2024-12' => 2620000
        ]);
        $maintenanceRequests = DataProvider::get('maintenance_requests', []);
        $newApplications = DataProvider::get('new_applications', []);
        
        // Set data through ViewManager (anti-scattering compliant)
        ViewManager::set('stats', $stats);
        ViewManager::set('recentProperties', $recentProperties);
        ViewManager::set('activities', $activities);
        ViewManager::set('revenueData', $revenueData);
        ViewManager::set('maintenanceRequests', $maintenanceRequests);
        ViewManager::set('newApplications', $newApplications);
        
        // Render using the standard layout pattern (anti-scattering compliant)
        ViewManager::set('title', 'Reports & Analytics');
        ViewManager::set('content', 'admin/reports/index');
        echo ViewManager::render('admin.dashboard_layout');
    }
    
    public function create() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Generate Report',
            'message' => 'Report generation form is coming soon.'
        ]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Report generation is not yet implemented.';
        $this->redirect('/admin/reports');
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Edit Report',
            'message' => "Report edit form for ID: $id is coming soon."
        ]);
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Report update is not yet implemented.';
        $this->redirect('/admin/reports');
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Report deletion is not yet implemented.';
        $this->redirect('/admin/reports');
    }
}
