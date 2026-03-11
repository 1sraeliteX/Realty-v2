<?php

namespace App\Controllers;

require_once __DIR__ . '/BaseController.php';

class TenantOccupantController extends BaseController {
    
    public function index() {
        // For now, we'll use mock data since this is a UI demonstration
        $tenants = [];
        $occupants = [];
        
        // Pass data to the view
        require_once __DIR__ . '/../../views/admin/tenants_occupants/index.php';
    }
    
    public function create() {
        // Create new tenant/occupant page
        require_once __DIR__ . '/../../views/admin/tenants_occupants/create.php';
    }
    
    public function show($id) {
        // Show individual tenant/occupant details
        $tenant = []; // Mock data - would fetch from database
        require_once __DIR__ . '/../../views/admin/tenants_occupants/show.php';
    }
    
    public function edit($id) {
        // Edit tenant/occupant
        $tenant = []; // Mock data - would fetch from database
        require_once __DIR__ . '/../../views/admin/tenants_occupants/edit.php';
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
