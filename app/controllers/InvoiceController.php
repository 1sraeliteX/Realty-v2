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
        
        // Load invoice creation page using anti-scattering compliant approach
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set data through ViewManager
        ViewManager::set('title', 'Create Invoice');
        ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        ViewManager::set('notifications', []);
        
        // Include the invoice creation view
        include __DIR__ . '/../../views/admin/invoices/create.php';
    }
    
    public function store() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Invoice creation is not yet implemented.';
        $this->redirect('/admin/invoices');
    }
    
    public function show($id) {
        $admin = $this->requireAuth();
        
        // Load invoice details page using anti-scattering compliant approach
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set data through ViewManager
        ViewManager::set('title', 'Invoice Details');
        ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        ViewManager::set('notifications', []);
        ViewManager::set('invoice_id', $id);
        
        // Include the invoice details view
        include __DIR__ . '/../../views/admin/invoices/details.php';
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        
        // Load invoice edit page using anti-scattering compliant approach
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set data through ViewManager
        ViewManager::set('title', 'Edit Invoice');
        ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        ViewManager::set('notifications', []);
        ViewManager::set('invoice_id', $id);
        
        // Include the invoice edit view
        include __DIR__ . '/../../views/admin/invoices/edit.php';
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
