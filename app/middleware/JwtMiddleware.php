<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Config;

class JwtMiddleware {
    private $secret;

    public function __construct() {
        $this->secret = Config::getInstance()->get('jwt.secret');
    }

    public function generateToken($payload) {
        $payload['iat'] = time();
        $payload['exp'] = time() + Config::getInstance()->get('jwt.expire');
        
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function extractTokenFromHeader() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    public function requireAuth() {
        $token = $this->extractTokenFromHeader();
        
        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization token required']);
            exit;
        }

        $payload = $this->validateToken($token);
        
        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit;
        }

        return $payload;
    }

    public function getCurrentUser() {
        $payload = $this->requireAuth();
        
        // Get user from database
        $db = \Config\Database::getInstance();
        $sql = "SELECT * FROM admins WHERE id = ? AND deleted_at IS NULL";
        $user = $db->fetch($sql, [$payload['admin_id']]);
        
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'User not found']);
            exit;
        }

        return $user;
    }
}
