<?php

namespace App\Controllers;

use DataProvider;
use ViewManager;

// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../config/init_framework.php';

class MaintenanceController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Load components through registry (anti-scattering compliant)
        \ComponentRegistry::load('ui-components');
        
        // Get data from centralized provider (anti-scattering compliant)
        $maintenanceRequests = DataProvider::get('maintenance');
        
        // Calculate stats from data
        $maintenanceStats = [
            'total' => count($maintenanceRequests),
            'pending' => count(array_filter($maintenanceRequests, fn($r) => $r['status'] === 'pending')),
            'in_progress' => count(array_filter($maintenanceRequests, fn($r) => $r['status'] === 'in_progress')),
            'completed' => count(array_filter($maintenanceRequests, fn($r) => $r['status'] === 'completed'))
        ];
        
        // Get data from centralized provider (anti-scattering compliant)
        $user = DataProvider::get('user');
        $notifications = DataProvider::get('notifications');
        
        // Set data through ViewManager (anti-scattering compliant)
        ViewManager::set('user', $user);
        ViewManager::set('notifications', $notifications);
        ViewManager::set('title', 'Maintenance Management');
        ViewManager::set('pageTitle', 'Maintenance Management');
        ViewManager::set('pageDescription', 'Track and manage maintenance requests');
        ViewManager::set('maintenanceStats', $maintenanceStats);
        ViewManager::set('maintenanceRequests', $maintenanceRequests);
        ViewManager::set('admin', $admin);
        
        // Include the maintenance list view (which includes dashboard layout)
        include __DIR__ . '/../../views/admin/maintenance/list.php';
    }
    
    public function create() {
        $admin = $this->requireAuth();
        
        // Get data from centralized provider (anti-scattering compliant)
        $properties = DataProvider::get('properties');
        $tenants = DataProvider::get('tenants');
        
        // Mock data for form dropdowns (anti-scattering compliant)
        $contractors = [
            ['id' => 'john-handy', 'name' => 'John Handyman'],
            ['id' => 'mike-plumbing', 'name' => 'Mike Plumbing'],
            ['id' => 'sparky-electric', 'name' => 'Sparky Electric'],
            ['id' => 'hvac-pro', 'name' => 'HVAC Pro Services']
        ];
        
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
        
        $statuses = [
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'in-progress', 'label' => 'In Progress'],
            ['value' => 'completed', 'label' => 'Completed'],
            ['value' => 'cancelled', 'label' => 'Cancelled']
        ];
        
        $this->view('admin.maintenance.create', [
            'admin' => $admin,
            'title' => 'Create Maintenance Request',
            'pageTitle' => 'Create Maintenance Request',
            'pageDescription' => 'Create a new maintenance request or work order',
            'properties' => $properties,
            'tenants' => $tenants,
            'contractors' => $contractors,
            'categories' => $categories,
            'priorities' => $priorities,
            'statuses' => $statuses
        ]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Maintenance request creation is not yet implemented.';
        $this->redirect('/admin/maintenance');
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Edit Maintenance Request',
            'message' => "Maintenance request edit form for ID: $id is coming soon."
        ]);
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Maintenance request update is not yet implemented.';
        $this->redirect('/admin/maintenance');
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Maintenance request deletion is not yet implemented.';
        $this->redirect('/admin/maintenance');
    }
}
