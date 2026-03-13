<?php

namespace App\Controllers;

class ApiDocumentController extends BaseController {
    public function index() {
        $admin = $this->requireApiAuth();
        
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
        
        $this->json([
            'success' => true,
            'data' => $documents,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit)
            ]
        ]);
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
            $this->json(['success' => false, 'message' => 'Document not found'], 404);
            return;
        }
        
        $this->json(['success' => true, 'data' => $document]);
    }
    
    public function store() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Handle file upload
        if (!isset($_FILES['file'])) {
            $this->json(['success' => false, 'message' => 'No file uploaded'], 400);
            return;
        }
        
        $file = $_FILES['file'];
        $data = json_decode($_POST['data'] ?? '{}', true);
        
        // Validate required fields
        if (empty($data['title'])) {
            $this->json(['success' => false, 'message' => 'Title is required'], 400);
            return;
        }
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'File upload error: ' . $file['error']], 400);
            return;
        }
        
        // Check file size (10MB limit)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file['size'] > $maxSize) {
            $this->json(['success' => false, 'message' => 'File too large. Maximum size is 10MB'], 400);
            return;
        }
        
        // Check file type
        $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'txt'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedTypes)) {
            $this->json(['success' => false, 'message' => 'File type not allowed'], 400);
            return;
        }
        
        // Validate property/unit/tenant if provided
        if (!empty($data['property_id'])) {
            $propertyCheck = $this->db->query("SELECT id FROM properties WHERE id = ? AND admin_id = ?", 
                                             [$data['property_id'], $admin['id']])->fetch();
            if (!$propertyCheck) {
                $this->json(['success' => false, 'message' => 'Property not found'], 400);
                return;
            }
        }
        
        if (!empty($data['tenant_id'])) {
            $tenantCheck = $this->db->query("SELECT id FROM tenants WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", 
                                         [$data['tenant_id'], $admin['id']])->fetch();
            if (!$tenantCheck) {
                $this->json(['success' => false, 'message' => 'Tenant not found'], 400);
                return;
            }
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
                $data['tenant_id'] ?? null,
                $data['property_id'] ?? null,
                $data['unit_id'] ?? null,
                $data['title'],
                $data['description'] ?? null,
                $file['name'],
                $fileName,
                $file['size'],
                $this->getDocumentType($fileExtension),
                $data['category'] ?? 'general'
            ];
            
            $this->db->query($sql, $params);
            $documentId = $this->db->lastInsertId();
            
            $this->db->commit();
            
            $this->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'data' => ['id' => $documentId, 'file_name' => $fileName]
            ]);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to upload document: ' . $e->getMessage()], 500);
        }
    }
    
    public function update($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if document exists and belongs to admin
        $document = $this->db->query("SELECT id FROM documents WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$document) {
            $this->json(['success' => false, 'message' => 'Document not found'], 404);
            return;
        }
        
        try {
            // Build update query dynamically
            $updateFields = [];
            $params = [];
            
            $allowedFields = ['title', 'description', 'category', 'property_id', 'tenant_id', 'unit_id'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $id;
                $params[] = $admin['id'];
                
                $sql = "UPDATE documents SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ? AND admin_id = ?";
                $this->db->query($sql, $params);
            }
            
            $this->json(['success' => true, 'message' => 'Document updated successfully']);
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update document: ' . $e->getMessage()], 500);
        }
    }
    
    public function delete($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Check if document exists and belongs to admin
        $document = $this->db->query("SELECT id, file_path FROM documents WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        if (!$document) {
            $this->json(['success' => false, 'message' => 'Document not found'], 404);
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
            
            $this->json(['success' => true, 'message' => 'Document deleted successfully']);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to delete document: ' . $e->getMessage()], 500);
        }
    }
    
    public function download($id) {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $document = $this->db->query("SELECT * FROM documents WHERE id = ? AND admin_id = ? AND deleted_at IS NULL", [$id, $admin['id']])->fetch();
        
        if (!$document) {
            $this->json(['success' => false, 'message' => 'Document not found'], 404);
            return;
        }
        
        $filePath = __DIR__ . '/../../storage/uploads/documents/' . $document['file_path'];
        
        if (!file_exists($filePath)) {
            $this->json(['success' => false, 'message' => 'File not found'], 404);
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
    
    public function getDocumentStats() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        $period = $_GET['period'] ?? 'month';
        
        $dateFilter = '';
        if ($period === 'week') {
            $dateFilter = "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        } elseif ($period === 'month') {
            $dateFilter = "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        } elseif ($period === 'year') {
            $dateFilter = "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        }
        
        $sql = "SELECT 
                    COUNT(*) as total_documents,
                    SUM(file_size) as total_size,
                    SUM(CASE WHEN type = 'pdf' THEN 1 ELSE 0 END) as pdf_count,
                    SUM(CASE WHEN type = 'image' THEN 1 ELSE 0 END) as image_count,
                    SUM(CASE WHEN type = 'document' THEN 1 ELSE 0 END) as document_count,
                    AVG(file_size) as avg_file_size
                 FROM documents 
                 WHERE admin_id = ? AND deleted_at IS NULL $dateFilter";
        
        $stats = $this->db->query($sql, [$admin['id']])->fetch();
        
        $this->json(['success' => true, 'data' => $stats]);
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
