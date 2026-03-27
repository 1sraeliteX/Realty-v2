<?php

namespace App\Models;

use Config\Database;

class DocumentModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all documents with optional filtering
     */
    public function getAllDocuments($filters = []) {
        $where = ["d.deleted_at IS NULL"];
        $params = [];
        
        // Role-based scoping will be handled in controller
        
        // Search by title or file_name
        if (!empty($filters['search'])) {
            $where[] = "(d.title LIKE ? OR d.file_name LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Filter by property
        if (!empty($filters['property_id'])) {
            $where[] = "(d.property_id = ? OR (d.related_to_type = 'property' AND d.related_to_id = ?))";
            $params[] = $filters['property_id'];
            $params[] = $filters['property_id'];
        }
        
        // Filter by tenant
        if (!empty($filters['tenant_id'])) {
            $where[] = "(d.tenant_id = ? OR (d.related_to_type = 'tenant' AND d.related_to_id = ?))";
            $params[] = $filters['tenant_id'];
            $params[] = $filters['tenant_id'];
        }
        
        // Filter by related type
        if (!empty($filters['related_to_type'])) {
            $where[] = "d.related_to_type = ?";
            $params[] = $filters['related_to_type'];
        }
        
        // Filter by file type
        if (!empty($filters['file_type'])) {
            $where[] = "d.file_type = ?";
            $params[] = $filters['file_type'];
        }
        
        // Add admin scope if provided
        if (!empty($filters['admin_id'])) {
            $where[] = "d.uploaded_by = ?";
            $params[] = $filters['admin_id'];
        }
        
        $sql = "SELECT d.*, 
                       t.name as tenant_name,
                       pr.name as property_name,
                       u.unit_number,
                       a.name as uploaded_by_name,
                       a.email as uploaded_by_email
                FROM documents d
                LEFT JOIN tenants t ON (d.tenant_id = t.id OR (d.related_to_type = 'tenant' AND d.related_to_id = t.id)) AND t.deleted_at IS NULL
                LEFT JOIN properties pr ON (d.property_id = pr.id OR (d.related_to_type = 'property' AND d.related_to_id = pr.id)) AND pr.deleted_at IS NULL
                LEFT JOIN units u ON d.unit_id = u.id AND u.deleted_at IS NULL
                LEFT JOIN admins a ON d.uploaded_by = a.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY d.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = $filters['limit'];
            
            if (!empty($filters['offset'])) {
                $sql .= " OFFSET ?";
                $params[] = $filters['offset'];
            }
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get total count of documents with filters
     */
    public function getDocumentsCount($filters = []) {
        $where = ["deleted_at IS NULL"];
        $params = [];
        
        // Same filtering logic as getAllDocuments but without joins for performance
        if (!empty($filters['search'])) {
            $where[] = "(title LIKE ? OR file_name LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['property_id'])) {
            $where[] = "(property_id = ? OR (related_to_type = 'property' AND related_to_id = ?))";
            $params[] = $filters['property_id'];
            $params[] = $filters['property_id'];
        }
        
        if (!empty($filters['tenant_id'])) {
            $where[] = "(tenant_id = ? OR (related_to_type = 'tenant' AND related_to_id = ?))";
            $params[] = $filters['tenant_id'];
            $params[] = $filters['tenant_id'];
        }
        
        if (!empty($filters['related_to_type'])) {
            $where[] = "related_to_type = ?";
            $params[] = $filters['related_to_type'];
        }
        
        if (!empty($filters['file_type'])) {
            $where[] = "file_type = ?";
            $params[] = $filters['file_type'];
        }
        
        if (!empty($filters['admin_id'])) {
            $where[] = "uploaded_by = ?";
            $params[] = $filters['admin_id'];
        }
        
        $sql = "SELECT COUNT(*) FROM documents WHERE " . implode(' AND ', $where);
        return $this->db->fetch($sql, $params)['COUNT(*)'];
    }
    
    /**
     * Upload a new document
     */
    public function uploadDocument($data, $file) {
        try {
            $this->db->beginTransaction();
            
            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File upload error: ' . $file['error']);
            }
            
            // Check file size (10MB limit)
            $maxSize = 10 * 1024 * 1024; // 10MB
            if ($file['size'] > $maxSize) {
                throw new Exception('File too large. Maximum size is 10MB');
            }
            
            // Check file type
            $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'txt'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedTypes)) {
                throw new Exception('File type not allowed');
            }
            
            // Create directory structure: public/uploads/documents/{year}/{month}/
            $uploadDir = __DIR__ . '/../../public/uploads/documents/' . date('Y') . '/' . date('m') . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Create unique filename
            $fileName = uniqid() . '_' . $file['name'];
            $filePath = $uploadDir . $fileName;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new Exception('Failed to move uploaded file');
            }
            
            // Store relative path in database
            $relativePath = 'uploads/documents/' . date('Y') . '/' . date('m') . '/' . $fileName;
            
            // Insert document record
            $sql = "INSERT INTO documents (title, description, file_name, file_path, file_size, file_type, 
                      uploaded_by, related_to_type, related_to_id, property_id, tenant_id, unit_id, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $params = [
                $data['title'] ?? '',
                $data['description'] ?? '',
                $file['name'],
                $relativePath,
                $file['size'],
                $fileExtension,
                $data['uploaded_by'],
                $data['related_to_type'] ?? null,
                $data['related_to_id'] ?? null,
                $data['property_id'] ?? null,
                $data['tenant_id'] ?? null,
                $data['unit_id'] ?? null
            ];
            
            $this->db->query($sql, $params);
            $documentId = $this->db->getConnection()->lastInsertId();
            
            $this->db->commit();
            
            return $documentId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Soft delete a document
     */
    public function deleteDocument($id, $userId) {
        try {
            $this->db->beginTransaction();
            
            // Check if document exists and belongs to user
            $document = $this->getDocumentById($id, $userId);
            if (!$document) {
                throw new Exception('Document not found');
            }
            
            // Soft delete
            $this->db->query("UPDATE documents SET deleted_at = NOW() WHERE id = ? AND uploaded_by = ?", [$id, $userId]);
            
            // Optionally delete physical file
            if ($document['file_path']) {
                $fullPath = __DIR__ . '/../../public/' . $document['file_path'];
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            
            $this->db->commit();
            
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Get document by ID
     */
    public function getDocumentById($id, $userId = null) {
        $where = ["d.id = ?", "d.deleted_at IS NULL"];
        $params = [$id];
        
        if ($userId) {
            $where[] = "d.uploaded_by = ?";
            $params[] = $userId;
        }
        
        $sql = "SELECT d.*, 
                       t.name as tenant_name,
                       t.email as tenant_email,
                       pr.name as property_name,
                       pr.address as property_address,
                       u.unit_number,
                       a.name as uploaded_by_name,
                       a.email as uploaded_by_email
                FROM documents d
                LEFT JOIN tenants t ON (d.tenant_id = t.id OR (d.related_to_type = 'tenant' AND d.related_to_id = t.id)) AND t.deleted_at IS NULL
                LEFT JOIN properties pr ON (d.property_id = pr.id OR (d.related_to_type = 'property' AND d.related_to_id = pr.id)) AND pr.deleted_at IS NULL
                LEFT JOIN units u ON d.unit_id = u.id AND u.deleted_at IS NULL
                LEFT JOIN admins a ON d.uploaded_by = a.id
                WHERE " . implode(' AND ', $where);
        
        return $this->db->fetch($sql, $params);
    }
    
    /**
     * Get documents statistics
     */
    public function getDocumentsStats($adminId) {
        $sql = "SELECT 
                    COUNT(*) as total_documents,
                    SUM(file_size) as total_size,
                    SUM(CASE WHEN file_type = 'pdf' THEN 1 ELSE 0 END) as pdf_count,
                    SUM(CASE WHEN file_type IN ('jpg', 'jpeg', 'png', 'gif') THEN 1 ELSE 0 END) as image_count,
                    SUM(CASE WHEN file_type IN ('doc', 'docx', 'xls', 'xlsx', 'txt') THEN 1 ELSE 0 END) as document_count,
                    SUM(CASE WHEN related_to_type = 'property' THEN 1 ELSE 0 END) as property_docs,
                    SUM(CASE WHEN related_to_type = 'tenant' THEN 1 ELSE 0 END) as tenant_docs,
                    SUM(CASE WHEN related_to_type = 'lease' THEN 1 ELSE 0 END) as lease_docs
                FROM documents 
                WHERE uploaded_by = ? AND deleted_at IS NULL";
        
        return $this->db->fetch($sql, [$adminId]);
    }
    
    /**
     * Get properties for filtering
     */
    public function getPropertiesForFilters($adminId) {
        $sql = "SELECT id, name FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        return $this->db->fetchAll($sql, [$adminId]);
    }
    
    /**
     * Get tenants for filtering
     */
    public function getTenantsForFilters($adminId) {
        $sql = "SELECT id, name, property_id FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
        return $this->db->fetchAll($sql, [$adminId]);
    }
    
    /**
     * Get related entities based on type
     */
    public function getRelatedEntities($type, $adminId) {
        switch ($type) {
            case 'property':
                $sql = "SELECT id, name as display_name FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
                return $this->db->fetchAll($sql, [$adminId]);
                
            case 'tenant':
                $sql = "SELECT id, name as display_name FROM tenants WHERE admin_id = ? AND deleted_at IS NULL ORDER BY name";
                return $this->db->fetchAll($sql, [$adminId]);
                
            case 'lease':
                // For leases, we might need to join tenants and properties
                $sql = "SELECT DISTINCT 
                           CONCAT(t.name, ' - ', pr.name) as display_name,
                           t.id
                        FROM tenants t
                        JOIN properties pr ON t.property_id = pr.id
                        WHERE t.admin_id = ? AND t.deleted_at IS NULL AND pr.deleted_at IS NULL
                        ORDER BY t.name";
                return $this->db->fetchAll($sql, [$adminId]);
                
            default:
                return [];
        }
    }
}
?>
