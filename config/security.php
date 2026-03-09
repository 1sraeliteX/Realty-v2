<?php

namespace Config;

class Security {
    private static $instance = null;
    private $config;

    private function __construct() {
        $this->config = ConfigSimple::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // CSRF Protection
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Token expires after 1 hour
        if (time() - $_SESSION['csrf_token_time'] > 3600) {
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    // Input sanitization
    public function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    // XSS Protection
    public function sanitizeHTML($input) {
        return strip_tags($input);
    }

    // SQL Injection Protection (basic)
    public function sanitizeSQL($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeSQL'], $input);
        }
        
        // Remove dangerous SQL characters
        return preg_replace('/[\'";\\\\]/', '', $input);
    }

    // Rate limiting
    public function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 300) {
        $key = "rate_limit_" . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['attempts' => 0, 'first_attempt' => time()];
        }
        
        $rateData = $_SESSION[$key];
        
        // Reset if time window has passed
        if (time() - $rateData['first_attempt'] > $timeWindow) {
            $_SESSION[$key] = ['attempts' => 1, 'first_attempt' => time()];
            return true;
        }
        
        // Check if max attempts exceeded
        if ($rateData['attempts'] >= $maxAttempts) {
            return false;
        }
        
        // Increment attempts
        $_SESSION[$key]['attempts']++;
        return true;
    }

    // Password validation
    public function validatePassword($password) {
        if (strlen($password) < 8) {
            return 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            return 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            return 'Password must contain at least one number';
        }
        
        return true;
    }

    // Secure password hashing
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Secure headers
    public function setSecurityHeaders() {
        if (!headers_sent()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: DENY');
            header('X-XSS-Protection: 1; mode=block');
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
            header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.tailwindcss.com; style-src \'self\' \'unsafe-inline\' https://cdn.tailwindcss.com; img-src \'self\' data: https:; font-src \'self\' https://cdnjs.cloudflare.com;');
        }
    }

    // Session security
    public function secureSession() {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // Regenerate session ID
        session_regenerate_id(true);
    }

    // Input validation
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public function validateInteger($input, $min = null, $max = null) {
        $options = [];
        if ($min !== null) $options['min_range'] = $min;
        if ($max !== null) $options['max_range'] = $max;
        
        return filter_var($input, FILTER_VALIDATE_INT, ['options' => $options]) !== false;
    }

    // File upload security
    public function validateFileUpload($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'], $maxSize = 5242880) {
        $errors = [];
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'File type not allowed';
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error occurred';
        }
        
        return $errors;
    }

    // Logging
    public function logSecurityEvent($event, $details = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];
        
        $logFile = __DIR__ . '/../storage/logs/security.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
