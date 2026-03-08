<?php

namespace App\Controllers;

// Manually require the database configuration
require_once __DIR__ . '/../../config/database.php';

use App\Middleware\JwtMiddleware;
use Config\Database;

class AdminAuthController extends BaseController {
    private $jwtMiddleware;
    
    public function __construct() {
        // Initialize database connection
        parent::__construct(); // Call parent constructor to initialize db
        $this->jwtMiddleware = new JwtMiddleware();
    }
    
    public function showLogin() {
        // Check if admin is already logged in
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin') {
            header('Location: /admin/dashboard');
            exit;
        }
        
        // Show login form for admin
        $this->view('auth.login', [
            'userType' => 'admin',
            'loginAction' => '/admin/login',
            'title' => 'Admin Login'
        ]);
    }
    
    public function showRegister() {
        // Check if admin is already logged in
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin') {
            header('Location: /admin/dashboard');
            exit;
        }
        
        // Show registration form for admin
        $this->view('auth.register', [
            'userType' => 'admin',
            'registerAction' => '/admin/register',
            'title' => 'Admin Registration'
        ]);
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();
            
            $name = $data['name'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $business_name = $data['business_name'] ?? '';
            $phone = $data['phone'] ?? '';
            
            if (empty($name) || empty($email) || empty($password)) {
                $_SESSION['error'] = 'Name, email, and password are required';
                header('Location: /admin/register');
                exit;
            }
            
            // Check if email already exists
            $stmt = $this->db->getConnection()->prepare("SELECT id FROM admins WHERE email = ? AND deleted_at IS NULL");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email already exists';
                header('Location: /admin/register');
                exit;
            }
            
            // Create new admin
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->getConnection()->prepare("
                INSERT INTO admins (name, email, password, business_name, phone, role, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, 'admin', NOW(), NOW())
            ");
            
            if ($stmt->execute([$name, $email, $hashedPassword, $business_name, $phone])) {
                $_SESSION['success'] = 'Registration successful! Please login.';
                header('Location: /admin/login');
                exit;
            } else {
                $_SESSION['error'] = 'Registration failed. Please try again.';
                header('Location: /admin/register');
                exit;
            }
        } else {
            $this->showRegister();
        }
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
                    header('Location: /admin/login');
                    exit;
                }
                return;
            }
            
            // Find admin user with role 'admin' only
            $stmt = $this->db->getConnection()->prepare("SELECT * FROM admins WHERE email = ? AND role = 'admin' AND deleted_at IS NULL");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password'])) {
                if ($this->isApiRequest()) {
                    $this->json(['error' => 'Invalid credentials'], 401);
                } else {
                    $_SESSION['error'] = 'Invalid credentials';
                    header('Location: /admin/login');
                    exit;
                }
                return;
            }
            
            // Set session for admin
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
                // Redirect to admin dashboard
                header('Location: /admin/dashboard');
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
            header('Location: /admin/login');
            exit;
        }
    }
    
    public function me() {
        $user = $this->jwtMiddleware->getCurrentUser();
        if ($user && $user['role'] === 'admin') {
            $this->json($user);
        } else {
            $this->json(['error' => 'Unauthorized'], 401);
        }
    }
}
