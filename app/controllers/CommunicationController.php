<?php

namespace App\Controllers;

class CommunicationController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/init_framework.php';
        
        // Set user data through ViewManager (anti-scattering compliant)
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        
        // Include the communications index view
        include __DIR__ . '/../../views/admin/communications/index.php';
    }
    
    public function create() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Create Communication',
            'message' => 'Communication creation form is coming soon.'
        ]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Communication creation is not yet implemented.';
        $this->redirect('/admin/communications');
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Edit Communication',
            'message' => "Communication edit form for ID: $id is coming soon."
        ]);
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Communication update is not yet implemented.';
        $this->redirect('/admin/communications');
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Communication deletion is not yet implemented.';
        $this->redirect('/admin/communications');
    }
}
