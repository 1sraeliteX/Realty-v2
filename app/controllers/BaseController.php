<?php

namespace App\Controllers;

use Config\Database;
use Config\Config;

class BaseController {
    protected $db;
    protected $config;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->config = Config::getInstance();
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
        $layoutPath = __DIR__ . '/../../views/layout.php';
        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
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
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function sanitize($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    protected function getCurrentAdmin() {
        if (isset($_SESSION['admin_id'])) {
            $sql = "SELECT * FROM admins WHERE id = ? AND deleted_at IS NULL";
            return $this->db->fetch($sql, [$_SESSION['admin_id']]);
        }
        return null;
    }

    protected function requireAuth() {
        $admin = $this->getCurrentAdmin();
        if (!$admin) {
            if ($this->isApiRequest()) {
                $this->json(['error' => 'Unauthorized'], 401);
            } else {
                $this->redirect('/login');
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
                $this->redirect('/dashboard');
            }
        }
        return $admin;
    }

    protected function isApiRequest() {
        return strpos($_SERVER['REQUEST_URI'], '/api/') === 0;
    }

    protected function getPostData() {
        $json = file_get_contents('php://input');
        return !empty($json) ? json_decode($json, true) : $_POST;
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
        $sql = "INSERT INTO activities (admin_id, action, description, entity_type, entity_id, metadata, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $adminId,
            $action,
            $description,
            $entityType,
            $entityId,
            $metadata ? json_encode($metadata) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ];
        
        $this->db->query($sql, $params);
    }

    protected function paginate($query, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $countQuery = preg_replace('/SELECT.*?FROM/s', 'SELECT COUNT(*) FROM', $query);
        $total = $this->db->fetch($countQuery)['COUNT(*)'];
        
        // Get paginated results
        $paginatedQuery = $query . " LIMIT {$limit} OFFSET {$offset}";
        $results = $this->db->fetchAll($paginatedQuery);
        
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
