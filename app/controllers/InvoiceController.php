<?php

namespace App\Controllers;

use DataProvider;
use ViewManager;

class InvoiceController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Load invoice data using anti-scattering compliant approach
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get data from centralized provider
        $invoices = DataProvider::get('invoices');
        $stats = DataProvider::get('invoice_stats');
        $user = DataProvider::get('user');
        $notifications = DataProvider::get('notifications');
        
        // Set data through ViewManager
        ViewManager::set('title', 'Invoices Management');
        ViewManager::set('invoices', $invoices);
        ViewManager::set('stats', $stats);
        ViewManager::set('user', $user);
        ViewManager::set('notifications', $notifications);
        
        // Include the invoice list view
        include __DIR__ . '/../../views/admin/invoices/list.php';
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
