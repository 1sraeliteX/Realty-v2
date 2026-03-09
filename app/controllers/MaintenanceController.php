<?php

namespace App\Controllers;

class MaintenanceController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Maintenance',
            'message' => 'Maintenance management module is coming soon.'
        ]);
    }
    
    public function create() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Create Maintenance Request',
            'message' => 'Maintenance request creation form is coming soon.'
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
