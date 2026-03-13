<?php

namespace App\Controllers;

class DocumentController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Get pagination and filter parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $category = $_GET['category'] ?? '';
        $propertyId = $_GET['property_id'] ?? '';
        $tenantId = $_GET['tenant_id'] ?? '';
        
        // Build query
        $where = ["d.admin_id = ?", "d.deleted_at IS NULL"];
        $params = [$admin['id']];
        
        if (!empty($search)) {
            $where[] = "(d.title LIKE ? OR d.description LIKE ? OR d.file_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($type)) {
            $where[] = "d.type = ?";
            $params[] = $type;
        }
        
        if (!empty($category)) {
            $where[] = "d.category = ?";
            $params[] = $category;
        }
        
        if (!empty($propertyId)) {
            $where[] = "d.property_id = ?";
            $params[] = $propertyId;
        }
        
        if (!empty($tenantId)) {
            $where[] = "d.tenant_id = ?";
            $params[] = $tenantId;
        }
        
        // Get documents with related info
        $sql = "SELECT d.*, 
                        t.name as tenant_name,
                        pr.name as property_name,
                        u.unit_number
                 FROM documents d
                 LEFT JOIN tenants t ON d.tenant_id = t.id
                 LEFT JOIN properties pr ON d.property_id = pr.id
                 LEFT JOIN units u ON d.unit_id = u.id
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY d.created_at DESC
                 LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;
        
        $documents = $this->db->query($sql, $params)->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM documents d WHERE " . implode(' AND ', $where);
        $total = $this->db->query($countSql, $params)->fetchColumn();
        
        // Get statistics
        $statsSql = "SELECT 
                        COUNT(*) as total_documents,
                        SUM(file_size) as total_size,
                        SUM(CASE WHEN type = 'pdf' THEN 1 ELSE 0 END) as pdf_count,
                        SUM(CASE WHEN type = 'image' THEN 1 ELSE 0 END) as image_count,
                        SUM(CASE WHEN type = 'document' THEN 1 ELSE 0 END) as document_count
                     FROM documents 
                     WHERE admin_id = ? AND deleted_at IS NULL";
        $stats = $this->db->query($statsSql, [$admin['id']])->fetch();
        
        // Get properties and tenants for filters
        $propertiesSql = "SELECT id, name FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $properties = $this->db->query($propertiesSql, [$admin['id']])->fetchAll();
        
        $tenantsSql = "SELECT id, name FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        $tenants = $this->db->query($tenantsSql, [$admin['id']])->fetchAll();
        
        // Set data for view (anti-scattering compliant)
        \ViewManager::set('documents', $documents);
        \ViewManager::set('pagination', [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'last_page' => ceil($total / $limit)
        ]);
        \ViewManager::set('stats', $stats);
        \ViewManager::set('properties', $properties);
        \ViewManager::set('tenants', $tenants);
        \ViewManager::set('filters', [
            'search' => $search,
            'type' => $type,
            'category' => $category,
            'property_id' => $propertyId,
            'tenant_id' => $tenantId
        ]);
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstone.com',
            'avatar' => null
        ]);
        \ViewManager::set('title', 'Documents');
        
        // Include the documents index view
        include __DIR__ . '/../../views/admin/documents/index.php';
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
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Handle file upload
        if (!isset($_FILES['file'])) {
            $_SESSION['error'] = 'No file uploaded';
            $this->redirect('/admin/documents/create');
            return;
        }
        
        $file = $_FILES['file'];
        
        // Validate required fields
        if (empty($_POST['title'])) {
            $_SESSION['error'] = 'Title is required';
            $this->redirect('/admin/documents/create');
            return;
        }
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'File upload error: ' . $file['error'];
            $this->redirect('/admin/documents/create');
            return;
        }
        
        // Check file size (10MB limit)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = 'File too large. Maximum size is 10MB';
            $this->redirect('/admin/documents/create');
            return;
        }
        
        // Check file type
        $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'txt'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedTypes)) {
            $_SESSION['error'] = 'File type not allowed';
            $this->redirect('/admin/documents/create');
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Create unique filename
            $fileName = uniqid() . '_' . $file['name'];
            $filePath = __DIR__ . '/../../storage/uploads/documents/' . $fileName;
            
            // Ensure upload directory exists
            $uploadDir = __DIR__ . '/../../storage/uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new Exception('Failed to move uploaded file');
            }
            
            // Insert document record
            $sql = "INSERT INTO documents (admin_id, tenant_id, property_id, unit_id, title, description, 
                      file_name, file_path, file_size, file_type, category, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $admin['id'],
                $_POST['tenant_id'] ?? null,
                $_POST['property_id'] ?? null,
                $_POST['unit_id'] ?? null,
                $_POST['title'],
                $_POST['description'] ?? null,
                $file['name'],
                $fileName,
                $file['size'],
                $this->getDocumentType($fileExtension),
                $_POST['category'] ?? 'general'
            ];
            
            $this->db->query($sql, $params);
            $documentId = $this->db->lastInsertId();
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Document uploaded successfully';
            $this->redirect('/admin/documents');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to upload document: ' . $e->getMessage();
            $this->redirect('/admin/documents/create');
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
                        u.unit_number
                 FROM documents d
                 LEFT JOIN tenants t ON d.tenant_id = t.id
                 LEFT JOIN properties pr ON d.property_id = pr.id
                 LEFT JOIN units u ON d.unit_id = u.id
                 WHERE d.id = ? AND d.admin_id = ? AND d.deleted_at IS NULL";
        
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
        $document = $this->db->query("SELECT * FROM documents WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
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
        $document = $this->db->query("SELECT id FROM documents WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
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
                
                $sql = "UPDATE documents SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
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
        
        // Check if document exists and belongs to admin
        $document = $this->db->query("SELECT id, file_path FROM documents WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$id, $admin['id']])->fetch();
        
        if (!$document) {
            $_SESSION['error'] = 'Document not found';
            $this->redirect('/admin/documents');
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Soft delete document
            $this->db->query("UPDATE documents SET deleted_at = NOW() WHERE id = ?", [$id]);
            
            // Optionally delete physical file
            if ($document['file_path'] && file_exists(__DIR__ . '/../../storage/uploads/documents/' . $document['file_path'])) {
                unlink(__DIR__ . '/../../storage/uploads/documents/' . $document['file_path']);
            }
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Document deleted successfully';
            $this->redirect('/admin/documents');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Failed to delete document: ' . $e->getMessage();
            $this->redirect('/admin/documents');
        }
    }
    
    public function download($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $document = $this->db->query("SELECT * FROM documents WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        
        if (!$document) {
            $_SESSION['error'] = 'Document not found';
            $this->redirect('/admin/documents');
            return;
        }
        
        $filePath = __DIR__ . '/../../storage/uploads/documents/' . $document['file_path'];
        
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
