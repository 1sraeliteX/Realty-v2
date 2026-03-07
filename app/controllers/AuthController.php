<?php

namespace App\Controllers;

use App\Middleware\JwtMiddleware;

class AuthController extends BaseController {
    private $jwtMiddleware;

    public function __construct() {
        parent::__construct();
        $this->jwtMiddleware = new JwtMiddleware();
    }

    public function showLogin() {
        // Redirect to dashboard if already logged in
        if ($this->getCurrentAdmin()) {
            $this->redirect('/dashboard');
        }
        
        $this->view('auth.login');
    }

    public function showRegister() {
        // Redirect to dashboard if already logged in
        if ($this->getCurrentAdmin()) {
            $this->redirect('/dashboard');
        }
        
        $this->view('auth.register');
    }

    public function login() {
        $data = $this->getPostData();
        
        // Validate input
        $errors = $this->validateRequired($data, ['email', 'password']);
        if (!empty($errors)) {
            if ($this->isApiRequest()) {
                $this->json(['errors' => $errors], 422);
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                $this->redirect('/login');
            }
        }

        // Validate email format
        if (!$this->validateEmail($data['email'])) {
            $errors['email'] = 'Invalid email format';
        }

        if (!empty($errors)) {
            if ($this->isApiRequest()) {
                $this->json(['errors' => $errors], 422);
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                $this->redirect('/login');
            }
        }

        // Find admin by email
        $sql = "SELECT * FROM admins WHERE email = ? AND deleted_at IS NULL";
        $admin = $this->db->fetch($sql, [$data['email']]);

        if (!$admin || !password_verify($data['password'], $admin['password'])) {
            if ($this->isApiRequest()) {
                $this->json(['error' => 'Invalid credentials'], 401);
            } else {
                $_SESSION['error'] = 'Invalid email or password';
                $this->redirect('/login');
            }
        }

        // Log activity
        $this->logActivity($admin['id'], 'login', 'Admin logged in');

        if ($this->isApiRequest()) {
            // Generate JWT token for API
            $token = $this->jwtMiddleware->generateToken(['admin_id' => $admin['id']]);
            
            $this->json([
                'token' => $token,
                'admin' => [
                    'id' => $admin['id'],
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'role' => $admin['role']
                ],
                'expires_in' => $this->config->get('jwt.expire')
            ]);
        } else {
            // Create session for web
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            
            $this->redirect('/dashboard');
        }
    }

    public function register() {
        $data = $this->getPostData();
        
        // Validate input
        $required = ['name', 'email', 'password', 'password_confirmation', 'role'];
        $errors = $this->validateRequired($data, $required);
        
        // Validate email format
        if (isset($data['email']) && !$this->validateEmail($data['email'])) {
            $errors['email'] = 'Invalid email format';
        }
        
        // Validate role
        if (isset($data['role']) && !in_array($data['role'], ['admin', 'super_admin'])) {
            $errors['role'] = 'Invalid role selected';
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
            $sql = "SELECT id FROM admins WHERE email = ? AND deleted_at IS NULL";
            $existing = $this->db->fetch($sql, [$data['email']]);
            if ($existing) {
                $errors['email'] = 'Email already registered';
            }
        }

        if (!empty($errors)) {
            if ($this->isApiRequest()) {
                $this->json(['errors' => $errors], 422);
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                $this->redirect('/register');
            }
        }

        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert new admin with selected role
        $adminId = $this->db->insert('admins', [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'business_name' => $data['business_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] // Use selected role
        ]);

        // Log activity
        $activityDescription = $data['role'] === 'super_admin' ? 'New super admin registered' : 'New admin registered';
        $this->logActivity($adminId, 'register', $activityDescription);

        if ($this->isApiRequest()) {
            $this->json([
                'message' => 'Registration successful',
                'admin' => [
                    'id' => $adminId,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'role' => $data['role']
                ]
            ], 201);
        } else {
            $roleText = $data['role'] === 'super_admin' ? 'Super Admin' : 'Admin';
            $_SESSION['success'] = "Registration successful! Your {$roleText} account has been created. Please login.";
            $this->redirect('/login');
        }
    }

    public function logout() {
        $admin = $this->getCurrentAdmin();
        
        if ($admin) {
            // Log activity
            $this->logActivity($admin['id'], 'logout', 'Admin logged out');
            
            // Destroy session
            session_destroy();
        }

        if ($this->isApiRequest()) {
            $this->json(['message' => 'Logged out successfully']);
        } else {
            $this->redirect('/login');
        }
    }
}
