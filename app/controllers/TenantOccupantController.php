<?php

namespace App\Controllers;

require_once __DIR__ . '/BaseController.php';

class TenantOccupantController extends BaseController {
    
    public function index() {
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set data through DataProvider (anti-scattering compliant)
        \DataProvider::set('tenants', []);
        \DataProvider::set('occupants', []);
        
        // Set page metadata
        \ViewManager::set('title', 'Tenants & Occupants');
        \ViewManager::set('user', ['name' => 'Admin User', 'email' => 'admin@example.com']);
        
        // Render using ViewManager with dashboard layout
        echo \ViewManager::render('admin.tenants_occupants.index', [], 'admin.dashboard_layout');
    }
    
    public function create() {
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set page metadata
        \ViewManager::set('title', 'Add New Tenant/Occupant');
        
        // Render using ViewManager
        echo \ViewManager::render('admin.tenants_occupants.create');
    }
    
    public function show($id) {
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set mock data through DataProvider
        \DataProvider::set('tenant', []);
        
        // Set page metadata
        \ViewManager::set('title', 'Tenant/Occupant Details');
        
        // Render using ViewManager
        echo \ViewManager::render('admin.tenants_occupants.show');
    }
    
    public function edit($id) {
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set mock data through DataProvider
        \DataProvider::set('tenant', []);
        
        // Set page metadata
        \ViewManager::set('title', 'Edit Tenant/Occupant');
        
        // Render using ViewManager
        echo \ViewManager::render('admin.tenants_occupants.edit');
    }
    
    public function store() {
        // Store new tenant/occupant
        // Implementation would go here
        header('Location: /admin/tenants-occupants');
        exit;
    }
    
    public function update($id) {
        // Update tenant/occupant
        // Implementation would go here
        header('Location: /admin/tenants-occupants');
        exit;
    }
    
    public function delete($id) {
        // Delete tenant/occupant
        // Implementation would go here
        header('Location: /admin/tenants-occupants');
        exit;
    }
}
