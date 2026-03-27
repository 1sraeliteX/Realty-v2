<?php

namespace App\Controllers;

use App\Models\DocumentModel;

class DocumentController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Initialize DocumentModel
        $documentModel = new \App\Models\DocumentModel();
        
        // Get pagination and filter parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';
        $propertyId = $_GET['property_id'] ?? '';
        $tenantId = $_GET['tenant_id'] ?? '';
        $relatedToType = $_GET['related_to_type'] ?? '';
        
        // Build filters for model
        $filters = [
            'admin_id' => $admin['id'],
            'search' => $search,
            'property_id' => $propertyId,
            'tenant_id' => $tenantId,
            'related_to_type' => $relatedToType,
            'limit' => $limit,
            'offset' => ($page - 1) * $limit
        ];
        
        // Get documents using model
        $documents = $documentModel->getAllDocuments($filters);
        $total = $documentModel->getDocumentsCount([
            'admin_id' => $admin['id'],
            'search' => $search,
            'property_id' => $propertyId,
            'tenant_id' => $tenantId,
            'related_to_type' => $relatedToType
        ]);
        
        // Get statistics
        $stats = $documentModel->getDocumentsStats($admin['id']);
        
        // Get properties and tenants for filters
        $properties = $documentModel->getPropertiesForFilters($admin['id']);
        $tenants = $documentModel->getTenantsForFilters($admin['id']);
        
        // Render the documents view using BaseController's view method
        $this->view('admin.documents.index', [
            'documents' => $documents,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit)
            ],
            'stats' => $stats,
            'properties' => $properties,
            'tenants' => $tenants,
            'filters' => [
                'search' => $search,
                'property_id' => $propertyId,
                'tenant_id' => $tenantId,
                'related_to_type' => $relatedToType
            ],
            'user' => [
                'name' => $admin['name'] ?? 'Admin User',
                'email' => $admin['email'] ?? 'admin@cornerstone.com',
                'avatar' => null
            ],
            'title' => 'Documents'
        ]);
    }
    
    public function create() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get properties and tenants for assignment
        $propertiesSql = "SELECT id, name FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $properties = $this->db->query($propertiesSql, [$admin['id']])->fetchAll();
        
        $tenantsSql = "SELECT id, name, property_id FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Get document categories
        $categories = ['lease', 'insurance', 'maintenance', 'financial', 'legal', 'general'];
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('properties', $properties);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('categories', $categories);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Upload Document');
        
        // Include the create view
        include __DIR__ . '/../../views/admin/documents/create.php';
    }
    
    public function upload() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Initialize DocumentModel
        $documentModel = new DocumentModel();
        
        // Handle POST request (same as store method)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_FILES['file'])) {
                $_SESSION['error'] = 'No file uploaded';
                $this->redirect('/admin/documents');
                return;
            }
            
            $file = $_FILES['file'];
            
            if (empty($_POST['title'])) {
                $_SESSION['error'] = 'Title is required';
                $this->redirect('/admin/documents');
                return;
            }
            
            try {
                $data = [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'] ?? '',
                    'uploaded_by' => $admin['id'],
                    'related_to_type' => $_POST['related_to_type'] ?? null,
                    'related_to_id' => $_POST['related_to_id'] ?? null,
                    'property_id' => $_POST['property_id'] ?? null,
                    'tenant_id' => $_POST['tenant_id'] ?? null,
                    'unit_id' => $_POST['unit_id'] ?? null
                ];
                
                $documentId = $documentModel->uploadDocument($data, $file);
                
                $_SESSION['success'] = 'Document uploaded successfully';
                $this->redirect('/admin/documents');
                
            } catch (Exception $e) {
                $_SESSION['error'] = 'Failed to upload document: ' . $e->getMessage();
                $this->redirect('/admin/documents');
            }
        } else {
            // For GET requests, redirect to index
            $this->redirect('/admin/documents');
        }
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Initialize DocumentModel
        $documentModel = new DocumentModel();
        
        // Handle file upload
        if (!isset($_FILES['file'])) {
            $_SESSION['error'] = 'No file uploaded';
            $this->redirect('/admin/documents');
            return;
        }
        
        $file = $_FILES['file'];
        
        // Validate required fields
        if (empty($_POST['title'])) {
            $_SESSION['error'] = 'Title is required';
            $this->redirect('/admin/documents');
            return;
        }
        
        try {
            // Prepare data for model
            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'] ?? '',
                'uploaded_by' => $admin['id'],
                'related_to_type' => $_POST['related_to_type'] ?? null,
                'related_to_id' => $_POST['related_to_id'] ?? null,
                'property_id' => $_POST['property_id'] ?? null,
                'tenant_id' => $_POST['tenant_id'] ?? null,
                'unit_id' => $_POST['unit_id'] ?? null
            ];
            
            // Upload document using model
            $documentId = $documentModel->uploadDocument($data, $file);
            
            $_SESSION['success'] = 'Document uploaded successfully';
            $this->redirect('/admin/documents');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to upload document: ' . $e->getMessage();
            $this->redirect('/admin/documents');
        }
    }
    
    public function show($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $sql = "SELECT d.*, 
                        t.name as tenant_name,
                        t.email as tenant_email,
                        pr.name as property_name,
                        pr.address as property_address,
                        u.unit_number,
                        a.name as uploaded_by_name,
                        a.email as uploaded_by_email
                 FROM documents d
                 LEFT JOIN tenants t ON d.tenant_id = t.id
                 LEFT JOIN properties pr ON d.property_id = pr.id
                 LEFT JOIN units u ON d.unit_id = u.id
                 LEFT JOIN admins a ON d.uploaded_by = a.id
                 WHERE d.id = ? AND d.uploaded_by = ? AND d.deleted_at IS NULL";
        
        $document = $this->db->query($sql, [$id, $admin['id']])->fetch();
        
        if (!$document) {
            $_SESSION['error'] = 'Document not found';
            $this->redirect('/admin/documents');
            return;
        }
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('document', $document);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Document Details');
        
        // Include the show view
        include __DIR__ . '/../../views/admin/documents/show.php';
    }
    
    public function edit($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if document exists and belongs to admin
        $document = $this->db->query("SELECT * FROM documents WHERE id = ? AND uploaded_by = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$document) {
            $_SESSION['error'] = 'Document not found';
            $this->redirect('/admin/documents');
            return;
        }
        
        // Get properties and tenants for assignment
        $propertiesSql = "SELECT id, name FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $properties = $this->db->query($propertiesSql, [$admin['id']])->fetchAll();
        
        $tenantsSql = "SELECT id, name FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Get document categories
        $categories = ['lease', 'insurance', 'maintenance', 'financial', 'legal', 'general'];
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('document', $document);
        \ViewManager::set('properties', $properties);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('categories', $categories);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Edit Document');
        
        // Include the edit view
        include __DIR__ . '/../../views/admin/documents/edit.php';
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if document exists and belongs to admin
        $document = $this->db->query("SELECT id FROM documents WHERE id = ? AND uploaded_by = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$document) {
            $_SESSION['error'] = 'Document not found';
            $this->redirect('/admin/documents');
            return;
        }
        
        try {
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['title', 'description', 'category', 'property_id', 'tenant_id', 'unit_id'];
            
            foreach ($allowedFields as $field) {
                if (isset($_POST[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $_POST[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE documents SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND uploaded_by = ?";
                $this->db->query($sql, $params);
            }
            
            $_SESSION['success'] = 'Document updated successfully';
            $this->redirect('/admin/documents');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to update document: ' . $e->getMessage();
            $this->redirect("/admin/documents/$id/edit");
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Initialize DocumentModel
        $documentModel = new DocumentModel();
        
        try {
            // Delete document using model
            $documentModel->deleteDocument($id, $admin['id']);
            
            $_SESSION['success'] = 'Document deleted successfully';
            $this->redirect('/admin/documents');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to delete document: ' . $e->getMessage();
            $this->redirect('/admin/documents');
        }
    }
    
    public function download($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Initialize DocumentModel
        $documentModel = new DocumentModel();
        
        $document = $documentModel->getDocumentById($id, $admin['id']);
        
        if (!$document) {
            $_SESSION['error'] = 'Document not found';
            $this->redirect('/admin/documents');
            return;
        }
        
        $filePath = __DIR__ . '/../../public/' . $document['file_path'];
        
        if (!file_exists($filePath)) {
            $_SESSION['error'] = 'File not found';
            $this->redirect('/admin/documents');
            return;
        }
        
        // Set headers for file download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $document['file_name'] . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        readfile($filePath);
        exit;
    }
    
    private function getDocumentType($extension) {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $documentTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
        
        if (in_array($extension, $imageTypes)) {
            return 'image';
        } elseif (in_array($extension, $documentTypes)) {
            return 'document';
        } else {
            return 'other';
        }
    }
}
