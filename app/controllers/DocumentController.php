<?php

namespace App\Controllers;

class DocumentController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize anti-scattering system
        require_once __DIR__ . '/../config/bootstrap.php';
        
        // Set page data
        ViewManager::set('title', 'Documents Management');
        ViewManager::set('user', $admin);
        
        // Get mock documents data
        $documents = DataProvider::get('documents', [
            [
                'id' => 1,
                'name' => 'Lease Agreement - Unit 101',
                'type' => 'PDF',
                'size' => '2.4 MB',
                'category' => 'Lease',
                'property' => 'Sunset Apartments',
                'upload_date' => '2024-01-15',
                'status' => 'active'
            ],
            [
                'id' => 2,
                'name' => 'Insurance Policy 2024',
                'type' => 'PDF',
                'size' => '1.8 MB',
                'category' => 'Insurance',
                'property' => 'Oak Villa Complex',
                'upload_date' => '2024-01-10',
                'status' => 'active'
            ],
            [
                'id' => 3,
                'name' => 'Maintenance Report - HVAC',
                'type' => 'DOCX',
                'size' => '456 KB',
                'category' => 'Maintenance',
                'property' => 'Downtown Office Building',
                'upload_date' => '2024-01-08',
                'status' => 'active'
            ]
        ]);
        
        ViewManager::set('documents', $documents);
        
        // Load the documents view
        $this->view('admin.documents.index', [
            'admin' => $admin,
            'documents' => $documents
        ]);
    }
    
    public function create() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Upload Document',
            'message' => 'Document upload form is coming soon.'
        ]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Document upload is not yet implemented.';
        $this->redirect('/admin/documents');
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Edit Document',
            'message' => "Document edit form for ID: $id is coming soon."
        ]);
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Document update is not yet implemented.';
        $this->redirect('/admin/documents');
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Document deletion is not yet implemented.';
        $this->redirect('/admin/documents');
    }
}
