<?php

namespace App\Controllers;

class CommunicationController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Communications',
            'message' => 'Communication management module is coming soon.'
        ]);
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
