<?php

namespace App\Controllers;

class PaymentController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Payments',
            'message' => 'Payment management module is coming soon.'
        ]);
    }
    
    public function create() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Add Payment',
            'message' => 'Payment creation form is coming soon.'
        ]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Payment creation is not yet implemented.';
        $this->redirect('/admin/payments');
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Edit Payment',
            'message' => "Payment edit form for ID: $id is coming soon."
        ]);
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Payment update is not yet implemented.';
        $this->redirect('/admin/payments');
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Payment deletion is not yet implemented.';
        $this->redirect('/admin/payments');
    }
}
