<?php

namespace App\Middleware;

use Config\Config;

class JwtMiddleware {
    private $secret;
    
    public function __construct() {
        $config = Config::getInstance();
        $this->secret = $config->get('jwt.secret');
    }
    
    public function generateToken($user) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + 86400 // 24 hours
        ]);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    public function validateToken($token) {
        if (empty($token)) {
            return null;
        }
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        
        $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[0]));
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        $signature = $parts[2];
        
        // Verify signature
        $base64UrlHeader = $parts[0];
        $base64UrlPayload = $parts[1];
        
        $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlExpectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));
        
        if (!hash_equals($signature, $base64UrlExpectedSignature)) {
            return null;
        }
        
        $payloadData = json_decode($payload, true);
        
        // Check expiration
        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return null;
        }
        
        return $payloadData;
    }
    
    public function getCurrentUser() {
        $headers = [];
        
        // Try to get headers from web server
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            // Fallback for CLI testing
            $headers = $_SERVER['HTTP_AUTHORIZATION'] ? ['Authorization' => $_SERVER['HTTP_AUTHORIZATION']] : [];
        }
        
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
            $payload = $this->validateToken($token);
            
            if ($payload) {
                // Get user from database
                $db = \Config\Database::getInstance();
                $user = $db->fetch("SELECT id, name, email, role FROM admins WHERE id = ? AND deleted_at IS NULL", [$payload['user_id']]);
                
                if ($user) {
                    return $user;
                }
            }
        }
        
        return null;
    }
    
    public function authenticate() {
        $user = $this->getCurrentUser();
        if (!$user) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        return $user;
    }
}
