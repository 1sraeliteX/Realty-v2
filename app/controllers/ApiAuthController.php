<?php

namespace App\Controllers;

// Manually require the SupabaseClient to ensure it's loaded
require_once __DIR__ . '/../../config/supabase.php';

use App\Middleware\JwtMiddleware;
use Config\SupabaseClient;

class ApiAuthController extends BaseController {
    private $jwtMiddleware;

    public function __construct() {
        parent::__construct();
        $this->jwtMiddleware = new JwtMiddleware();
    }

    public function register() {
        $data = $this->getPostData();
        
        // Validate input
        $required = ['name', 'email', 'password', 'password_confirmation'];
        $errors = $this->validateRequired($data, $required);
        
        // Validate email format
        if (isset($data['email']) && !$this->validateEmail($data['email'])) {
            $errors['email'] = 'Invalid email format';
        }
        
        // Validate password confirmation
        if (isset($data['password']) && isset($data['password_confirmation'])) {
            if ($data['password'] !== $data['password_confirmation']) {
                $errors['password_confirmation'] = 'Passwords do not match';
            }
            
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Password must be at least 8 characters long';
            }
        }

        // Check if email already exists
        if (isset($data['email'])) {
            $existing = $this->supabase->select('admins', 'id', ['email' => $data['email']]);
            if ($existing) {
                $errors['email'] = 'Email already registered';
            }
        }

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
        }

        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert new admin
        $adminId = $this->supabase->insert('admins', [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'business_name' => $data['business_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'role' => 'admin'
        ]);

        // Log activity
        $this->logActivity($adminId, 'register', 'New admin registered via API');

        // Generate JWT token
        $token = $this->jwtMiddleware->generateToken(['id' => $adminId, 'email' => $data['email'], 'role' => 'admin']);

        $this->json([
            'message' => 'Registration successful',
            'token' => $token,
            'admin' => [
                'id' => $adminId,
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => 'admin'
            ],
            'expires_in' => $this->config->get('jwt.expire')
        ], 201);
    }

    public function login() {
        $data = $this->getPostData();
        
        // Validate input
        $errors = $this->validateRequired($data, ['email', 'password']);
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
        }

        // Validate email format
        if (!$this->validateEmail($data['email'])) {
            $this->json(['errors' => ['email' => 'Invalid email format']], 422);
        }

        // Find admin by email
        $admin = $this->supabase->select('admins', '*', ['email' => $data['email']]);
        
        if (!$admin || !password_verify($data['password'], $admin[0]['password'])) {
            $this->json(['error' => 'Invalid credentials'], 401);
        }
        
        $admin = $admin[0]; // Get first result

        // Log activity
        $this->logActivity($admin['id'], 'login', 'Admin logged in via API');

        // Generate JWT token
        $token = $this->jwtMiddleware->generateToken(['id' => $admin['id'], 'email' => $admin['email'], 'role' => $admin['role']]);
        
        $this->json([
            'token' => $token,
            'admin' => [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'email' => $admin['email'],
                'role' => $admin['role'],
                'business_name' => $admin['business_name'],
                'phone' => $admin['phone']
            ],
            'expires_in' => $this->config->get('jwt.expire')
        ]);
    }

    public function logout() {
        $payload = $this->jwtMiddleware->requireAuth();
        
        // Log activity
        $this->logActivity($payload['admin_id'], 'logout', 'Admin logged out from API');

        $this->json(['message' => 'Logged out successfully']);
    }

    public function me() {
        $admin = $this->jwtMiddleware->getCurrentUser();
        
        $this->json([
            'admin' => [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'email' => $admin['email'],
                'role' => $admin['role'],
                'business_name' => $admin['business_name'],
                'phone' => $admin['phone'],
                'created_at' => $admin['created_at']
            ]
        ]);
    }
}
