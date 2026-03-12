<?php

namespace App\Controllers;

// Manually require required classes
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config_simple.php';
require_once __DIR__ . '/../../config/database_factory.php';
require_once __DIR__ . '/../../config/supabase_database.php';
require_once __DIR__ . '/../../config/security.php';

use Config\ConfigSimple;
use Config\DatabaseFactory;
use Config\Security;

class BaseController {
    protected $config;
    protected $db;
    protected $security;

    public function __construct() {
        $this->config = ConfigSimple::getInstance();
        $this->db = DatabaseFactory::create();
        $this->security = Security::getInstance();
        
        // Apply security measures
        $this->security->setSecurityHeaders();
    }

    protected function view($view, $data = []) {
        // Extract data to make variables available in view
        extract($data);
        
        // Build view path
        $viewPath = __DIR__ . '/../../views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View {$view} not found");
        }
        
        // Start output buffering
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        
        // Include layout if available
        if (strpos($view, 'admin/') === 0) {
            // Check if the view already handles its own layout (includes simple_layout.php)
            if (strpos($content, "include '../simple_layout.php'") !== false || 
                strpos($content, 'include "../simple_layout.php"') !== false ||
                strpos($content, "include '../simple_layout.php';") !== false ||
                strpos($content, 'include "../simple_layout.php";') !== false) {
                // View already handles its own layout, just echo the content
                echo $content;
            } else {
                // Use simple layout for admin views
                $layoutPath = __DIR__ . '/../../views/simple_layout.php';
                if (file_exists($layoutPath)) {
                    // Pass the content to the simple layout
                    include $layoutPath;
                } else {
                    echo $content;
                }
            }
        } else {
            // Use main layout for non-admin views
            $layoutPath = __DIR__ . '/../../views/layout.php';
            if (file_exists($layoutPath)) {
                include $layoutPath;
            } else {
                echo $content;
            }
        }
    }

    protected function renderView($view, $data = []) {
        // Extract data to make variables available in view
        extract($data);
        
        // Build view path
        $viewPath = __DIR__ . '/../../views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View {$view} not found");
        }
        
        // Start output buffering and return content
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    protected function validateRequired($data, $required) {
        $errors = [];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        return $errors;
    }

    protected function validateEmail($email) {
        return $this->security->validateEmail($email);
    }

    protected function sanitize($input) {
        return $this->security->sanitizeInput($input);
    }

    protected function getCSRFToken() {
        return $this->security->generateCSRFToken();
    }

    protected function validateCSRFToken($token) {
        if (!$this->security->validateCSRFToken($token)) {
            if ($this->isApiRequest()) {
                $this->json(['error' => 'Invalid CSRF token'], 403);
            } else {
                $this->security->logSecurityEvent('CSRF_TOKEN_INVALID', [
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
                $_SESSION['error'] = 'Security token expired. Please try again.';
                $this->redirect('/admin/login');
            }
        }
    }

    protected function checkRateLimit($identifier = null) {
        $identifier = $identifier ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        if (!$this->security->checkRateLimit($identifier)) {
            if ($this->isApiRequest()) {
                $this->json(['error' => 'Too many requests. Please try again later.'], 429);
            } else {
                $this->security->logSecurityEvent('RATE_LIMIT_EXCEEDED', [
                    'ip' => $identifier,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
                $_SESSION['error'] = 'Too many attempts. Please try again later.';
                $this->redirect('/admin/login');
            }
        }
    }

    protected function getCurrentAdmin() {
        if (isset($_SESSION['admin_id'])) {
            $stmt = $this->db->getConnection()->prepare("SELECT * FROM admins WHERE id = ? AND deleted_at IS NULL");
            $stmt->execute([$_SESSION['admin_id']]);
            return $stmt->fetch();
        }
        return null;
    }

    protected function requireAuth() {
        $admin = $this->getCurrentAdmin();
        if (!$admin) {
            if ($this->isApiRequest()) {
                $this->json(['error' => 'Unauthorized'], 401);
            } else {
                $this->redirect('/admin/login');
            }
        }
        return $admin;
    }

    protected function requireSuperAdmin() {
        $admin = $this->requireAuth();
        if ($admin['role'] !== 'super_admin') {
            if ($this->isApiRequest()) {
                $this->json(['error' => 'Forbidden - Super Admin access required'], 403);
            } else {
                $this->redirect('/superadmin/login');
            }
        }
        return $admin;
    }

    protected function isApiRequest() {
        // Check for API route prefix
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($requestUri, '/api/') === 0) {
            return true;
        }
        
        // Check for AJAX requests with specific headers
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        
        // Check for Accept header expecting JSON
        if (isset($_SERVER['HTTP_ACCEPT']) && 
            strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json') !== false) {
            return true;
        }
        
        return false;
    }

    protected function getPostData() {
        // Check if this is JSON input
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $decoded = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        
        // Parse form data (application/x-www-form-urlencoded)
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
            parse_str($json, $formData);
            return $formData;
        }
        
        // Default to $_POST
        return $_POST;
    }

    protected function uploadFile($file, $destination, $allowedTypes = null) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('File upload failed');
        }

        $allowedTypes = $allowedTypes ?? $this->config->get('upload.allowed_types');
        $maxSize = $this->config->get('upload.max_size');

        // Check file size
        if ($file['size'] > $maxSize) {
            throw new \Exception('File size exceeds maximum allowed size');
        }

        // Check file type
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedTypes)) {
            throw new \Exception('File type not allowed');
        }

        // Generate unique filename
        $filename = uniqid() . '.' . $fileExt;
        $uploadPath = $destination . '/' . $filename;

        // Create directory if it doesn't exist
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        // Move file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new \Exception('Failed to move uploaded file');
        }

        return $filename;
    }

    protected function logActivity($adminId, $action, $description, $entityType = null, $entityId = null, $metadata = null) {
        $activityData = [
            'admin_id' => $adminId,
            'action' => $action,
            'description' => $description,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert('activities', $activityData);
    }

    protected function paginate($query, $page = 1, $limit = 10, $params = []) {
        $offset = ($page - 1) * $limit;
        
        // Get total count - extract main query before ORDER BY
        $mainQuery = preg_replace('/\s+ORDER\s+BY\s+.*$/i', '', $query);
        $countQuery = "SELECT COUNT(*) FROM ({$mainQuery}) as count_table";
        $total = $this->db->fetch($countQuery, $params)['COUNT(*)'];
        
        // Get paginated results
        $paginatedQuery = $query . " LIMIT {$limit} OFFSET {$offset}";
        $results = $this->db->fetchAll($paginatedQuery, $params);
        
        return [
            'data' => $results,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
                'has_next' => $page < ceil($total / $limit),
                'has_prev' => $page > 1
            ]
        ];
    }
}
