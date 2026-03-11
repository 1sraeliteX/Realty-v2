<?php

namespace App\Controllers;

// Manually require the database configuration
require_once __DIR__ . '/../../config/database.php';

use App\Middleware\JwtMiddleware;
use Config\Database;

class AuthController extends BaseController {
    private $jwtMiddleware;
    
    public function __construct() {
        // Initialize database connection
        parent::__construct(); // Call parent constructor to initialize db
        $this->jwtMiddleware = new JwtMiddleware();
    }
    
    public function showLogin() {
        // TEMPORARILY DISABLED - Allow direct access without login check
        /*
        // Check if user is already logged in
        if (isset($_SESSION['admin_id'])) {
            header('Location: /dashboard');
            exit;
        }
        */
        
        // Show login form for web requests
        $this->view('auth/login');
    }
    
    public function showRegister() {
        // TEMPORARILY DISABLED - Allow direct access without login check
        /*
        // Check if user is already logged in
        if (isset($_SESSION['admin_id'])) {
            header('Location: /dashboard');
            exit;
        }
        */
        
        // Show registration form for web requests
        $this->view('auth/register');
    }
    
    public function register() {
        // Handle registration logic here
        $this->json(['message' => 'Registration not implemented yet'], 501);
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
                    header('Location: /login');
                    exit;
                }
                return;
            }
            
            // Find user using MySQL database
            $stmt = $this->db->getConnection()->prepare("SELECT * FROM admins WHERE email = ? AND deleted_at IS NULL");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password'])) {
                if ($this->isApiRequest()) {
                    $this->json(['error' => 'Invalid credentials'], 401);
                } else {
                    $_SESSION['error'] = 'Invalid credentials';
                    header('Location: /login');
                    exit;
                }
                return;
            }
            
            // Set session for web routes
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
                // Redirect to appropriate dashboard for web
                if ($user['role'] === 'super_admin') {
                    header('Location: /superadmin/dashboard');
                } else {
                    header('Location: /admin/dashboard');
                }
                exit;
            }
        } else {
            // Show login form
            $this->view('auth/login');
        }
    }
    
    public function logout() {
        session_destroy();
        if ($this->isApiRequest()) {
            $this->json(['message' => 'Logged out successfully']);
        } else {
            header('Location: /login');
            exit;
        }
    }
    
    public function me() {
        $user = $this->jwtMiddleware->getCurrentUser();
        if ($user) {
            $this->json($user);
        } else {
            $this->json(['error' => 'Unauthorized'], 401);
        }
    }
}
