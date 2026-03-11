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
        
        $this->view('admin.reports.dashboard', [
            'admin' => $admin,
            'title' => 'Reports & Maintenance',
            'pageTitle' => 'Reports & Maintenance',
            'pageDescription' => 'Generate reports and manage maintenance requests',
            'maintenanceRequests' => $maintenanceRequests,
            'maintenanceStats' => $maintenanceStats,
            'properties' => $properties,
            'tenants' => $tenants,
            'categories' => $categories,
            'priorities' => $priorities
        ]);
    }
    
    public function index() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Reports',
            'message' => 'Report generation module is coming soon.'
        ]);
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
