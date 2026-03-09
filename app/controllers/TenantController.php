<?php

namespace App\Controllers;

class TenantController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Show a simple placeholder page for now
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Tenants',
            'message' => 'Tenant management module is coming soon.'
        ]);
    }
    
    public function create() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Add Tenant',
            'message' => 'Tenant creation form is coming soon.'
        ]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Tenant creation is not yet implemented.';
        $this->redirect('/admin/tenants');
    }
    
    public function show($id) {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Tenant Details',
            'message' => "Tenant details for ID: $id are coming soon."
        ]);
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Edit Tenant',
            'message' => "Tenant edit form for ID: $id is coming soon."
        ]);
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Tenant update is not yet implemented.';
        $this->redirect('/admin/tenants');
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Tenant deletion is not yet implemented.';
        $this->redirect('/admin/tenants');
    }
}
