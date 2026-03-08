<?php

namespace App\Controllers;

// Manually require the database configuration
require_once __DIR__ . '/../../config/database.php';

use App\Middleware\JwtMiddleware;
use Config\Database;

class SuperAdminAuthController extends BaseController {
    private $jwtMiddleware;
    
    public function __construct() {
        // Initialize database connection
        parent::__construct(); // Call parent constructor to initialize db
        $this->jwtMiddleware = new JwtMiddleware();
    }
    
    public function showLogin() {
        // Check if super admin is already logged in
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'super_admin') {
            header('Location: /superadmin/dashboard');
            exit;
        }
        
        // Show login form for super admin
        $this->view('auth.login', [
            'userType' => 'superadmin',
            'loginAction' => '/superadmin/login',
            'title' => 'Super Admin Login'
        ]);
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();
            
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                if ($this->isApiRequest()) {
                    $this->json(['error' => 'Email and password are required'], 400);
                } else {
                    $_SESSION['error'] = 'Email and password are required';
                    header('Location: /superadmin/login');
                    exit;
                }
                return;
            }
            
            // Find super admin user with role 'super_admin' only
            $stmt = $this->db->getConnection()->prepare("SELECT * FROM admins WHERE email = ? AND role = 'super_admin' AND deleted_at IS NULL");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password'])) {
                if ($this->isApiRequest()) {
                    $this->json(['error' => 'Invalid credentials'], 401);
                } else {
                    $_SESSION['error'] = 'Invalid credentials';
                    header('Location: /superadmin/login');
                    exit;
                }
                return;
            }
            
            // Set session for super admin
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_role'] = $user['role'];
            
            // Handle API vs Web response
            if ($this->isApiRequest()) {
                // Generate token for API
                $token = $this->jwtMiddleware->generateToken([
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]);
                
                $this->json([
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ]);
            } else {
                // Redirect to super admin dashboard
                header('Location: /superadmin/dashboard');
                exit;
            }
        } else {
            $this->showLogin();
        }
    }
    
    public function logout() {
        session_destroy();
        if ($this->isApiRequest()) {
            $this->json(['message' => 'Logged out successfully']);
        } else {
            header('Location: /superadmin/login');
            exit;
        }
    }
    
    public function me() {
        $user = $this->jwtMiddleware->getCurrentUser();
        if ($user && $user['role'] === 'super_admin') {
            $this->json($user);
        } else {
            $this->json(['error' => 'Unauthorized'], 401);
        }
    }
}
