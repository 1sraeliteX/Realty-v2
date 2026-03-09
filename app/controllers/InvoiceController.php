<?php

namespace App\Controllers;

class InvoiceController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Invoices',
            'message' => 'Invoice management module is coming soon.'
        ]);
    }
    
    public function create() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Create Invoice',
            'message' => 'Invoice creation form is coming soon.'
        ]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Invoice creation is not yet implemented.';
        $this->redirect('/admin/invoices');
    }
    
    public function show($id) {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Invoice Details',
            'message' => "Invoice details for ID: $id are coming soon."
        ]);
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Edit Invoice',
            'message' => "Invoice edit form for ID: $id is coming soon."
        ]);
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Invoice update is not yet implemented.';
        $this->redirect('/admin/invoices');
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Invoice deletion is not yet implemented.';
        $this->redirect('/admin/invoices');
    }
}
