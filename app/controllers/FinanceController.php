<?php

namespace App\Controllers;

class FinanceController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Finances',
            'message' => 'Financial management module is coming soon.'
        ]);
    }
    
    public function create() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Add Financial Record',
            'message' => 'Financial record creation form is coming soon.'
        ]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Financial record creation is not yet implemented.';
        $this->redirect('/admin/finances');
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Edit Financial Record',
            'message' => "Financial record edit form for ID: $id is coming soon."
        ]);
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Financial record update is not yet implemented.';
        $this->redirect('/admin/finances');
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Financial record deletion is not yet implemented.';
        $this->redirect('/admin/finances');
    }
}
