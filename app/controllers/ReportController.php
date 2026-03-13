<?php

namespace App\Controllers;

class ReportController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set page data through ViewManager (anti-scattering compliant)
        ViewManager::set('title', 'Reports Dashboard');
        ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        
        // Generate mock reports data
        $reports = [
            'financial' => [
                'total_revenue' => 2500000,
                'total_expenses' => 850000,
                'net_profit' => 1650000,
                'monthly_growth' => 12.5,
                'revenue_by_property' => [
                    ['name' => 'Sunset Apartments', 'revenue' => 850000],
                    ['name' => 'Oak Villa Complex', 'revenue' => 650000],
                    ['name' => 'Downtown Office', 'revenue' => 1000000]
                ]
            ],
            'occupancy' => [
                'total_units' => 150,
                'occupied_units' => 135,
                'occupancy_rate' => 90.0,
                'vacancy_rate' => 10.0,
                'properties' => [
                    ['name' => 'Sunset Apartments', 'occupied' => 45, 'total' => 50],
                    ['name' => 'Oak Villa Complex', 'occupied' => 38, 'total' => 40],
                    ['name' => 'Downtown Office', 'occupied' => 52, 'total' => 60]
                ]
            ],
            'maintenance' => [
                'total_requests' => 45,
                'completed' => 38,
                'pending' => 5,
                'overdue' => 2,
                'average_completion_time' => 3.2,
                'cost_this_month' => 125000
            ],
            'tenants' => [
                'total_tenants' => 135,
                'new_this_month' => 8,
                'expiring_this_month' => 12,
                'average_tenancy_duration' => 18,
                'payment_delinquency_rate' => 5.5
            ]
        ];
        
        ViewManager::set('reports', $reports);
        
        // Include the dashboard layout with reports content
        include __DIR__ . '/../../views/admin/dashboard_reports.php';
    }
    
    public function create() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        ViewManager::set('title', 'Generate Report');
        ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        
        // Include the dashboard layout with report creation form
        include __DIR__ . '/../../views/admin/reports/create.php';
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Handle report generation logic here
        $_SESSION['success'] = 'Report generated successfully!';
        $this->redirect('/admin/dashboard/reports');
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        ViewManager::set('title', 'Edit Report');
        ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        ViewManager::set('report_id', $id);
        
        // Include the dashboard layout with report edit form
        include __DIR__ . '/../../views/admin/reports/edit.php';
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Handle report update logic here
        $_SESSION['success'] = 'Report updated successfully!';
        $this->redirect('/admin/dashboard/reports');
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Handle report deletion logic here
        $_SESSION['success'] = 'Report deleted successfully!';
        $this->redirect('/admin/dashboard/reports');
    }
}
