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
    
    public function createOccupant() {
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set page metadata
        \ViewManager::set('title', 'Add New Occupant');
        \ViewManager::set('user', ['name' => 'Admin User', 'email' => 'admin@cornerstone.com']);
        \ViewManager::set('notifications', []);
        
        // Set mock data through DataProvider
        \DataProvider::set('properties', [
            ['id' => 1, 'name' => 'Sunset Apartments'],
            ['id' => 2, 'name' => 'Ocean View Condos'],
            ['id' => 3, 'name' => 'Mountain Heights']
        ]);
        
        \DataProvider::set('units', [
            ['id' => 1, 'property_id' => 1, 'number' => 'A-101', 'type' => '1 Bedroom'],
            ['id' => 2, 'property_id' => 1, 'number' => 'A-102', 'type' => '2 Bedroom'],
            ['id' => 3, 'property_id' => 2, 'number' => 'B-201', 'type' => 'Studio'],
            ['id' => 4, 'property_id' => 2, 'number' => 'B-202', 'type' => '1 Bedroom'],
            ['id' => 5, 'property_id' => 3, 'number' => 'C-301', 'type' => '3 Bedroom']
        ]);
        
        \DataProvider::set('tenants', [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['id' => 3, 'name' => 'Mike Johnson', 'email' => 'mike@example.com']
        ]);
        
        // Render the occupants create view directly
        include __DIR__ . '/../../views/admin/occupants/create.php';
    }
    
    public function storeOccupant() {
        // Store new occupant
        // Implementation would go here
        header('Location: /admin/tenants-occupants');
        exit;
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
