# Real Estate Management System - Complete Build Guide

## Project Overview

This is a comprehensive real estate management platform built with PHP, MySQL, and modern web technologies. The system allows administrators to manage properties, units, tenants, payments, and invoices with full authentication and dashboard analytics.

## Technology Stack

### Backend
- **PHP 8+**: Server-side programming language
- **MySQL 8+**: Database management system  
- **Composer**: Dependency management
- **JWT**: JSON Web Tokens for authentication
- **Supabase**: Optional cloud database integration

### Frontend
- **HTML5**: Markup language
- **Tailwind CSS**: Utility-first CSS framework
- **Vanilla JavaScript (ES6+)**: Client-side scripting
- **Chart.js**: Data visualization library

### Key Libraries
- `supabase/supabase-php`: Supabase client integration
- `firebase/php-jwt`: JWT implementation
- `vlucas/phpdotenv`: Environment variable management
- `phpmailer/phpmailer`: Email functionality
- `respect/validation`: Input validation

## Complete Build Instructions

### Phase 1: Environment Setup

#### 1.1 Prerequisites Installation
```bash
# Install PHP 8.0+ with required extensions
sudo apt-get install php8.0 php8.0-mysql php8.0-json php8.0-mbstring php8.0-xml php8.0-curl

# Install MySQL 8.0+
sudo apt-get install mysql-server

# Install Apache web server with mod_rewrite
sudo apt-get install apache2 libapache2-mod-php8.0
sudo a2enmod rewrite

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 1.2 Project Setup
```bash
# Clone or create project directory
mkdir real-estate-management
cd real-estate-management

# Initialize Git repository
git init
git add .
git commit -m "Initial commit"

# Set up project structure
mkdir -p {app/{controllers,middleware,models,services},config,database,public/{assets/{css,js,images}},routes,storage/{logs,uploads},views/{auth,dashboard,properties,invoices,payments,layouts}}
```

### Phase 2: Backend Development

#### 2.1 Composer Dependencies
Create `composer.json`:
```json
{
    "name": "real-estate/management-system",
    "description": "A comprehensive real estate management platform",
    "type": "project",
    "license": "MIT",
    "require": {
        "php": ">=8.0",
        "supabase/supabase-php": "^0.10.0",
        "firebase/php-jwt": "^6.0",
        "vlucas/phpdotenv": "^5.0",
        "phpmailer/phpmailer": "^6.0",
        "respect/validation": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Config\\": "config/",
            "Database\\": "database/"
        }
    },
    "require-dev": {
        "phpunit/phpunit": "^9.0"
    },
    "scripts": {
        "post-install-cmd": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ]
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

Install dependencies:
```bash
composer install
```

#### 2.2 Environment Configuration
Create `.env.example`:
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=real_estate_db
DB_USER=root
DB_PASSWORD=

# JWT Configuration
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
JWT_EXPIRE=86400

# Application Configuration
APP_URL=http://localhost/real-estate-management
APP_ENV=development
APP_DEBUG=true

# Email Configuration (PHPMailer)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# Upload Configuration
UPLOAD_MAX_SIZE=5242880
UPLOAD_ALLOWED_TYPES=jpg,jpeg,png,pdf
```

#### 2.3 Database Schema
Create `database/schema.sql` with complete database structure including:
- `admins` table for user management
- `sessions` table for authentication
- `properties` table for property listings
- `units` table for rental units
- `tenants` table for tenant information
- `payments` table for payment records
- `invoices` table for billing

#### 2.4 Core Configuration Files

**Config/Database.php**:
```php
<?php
namespace Config;

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        
        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}
```

**Config/Config.php**:
```php
<?php
namespace Config;

class Config {
    public static function get($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
    
    public static function isDevelopment() {
        return self::get('APP_ENV') === 'development';
    }
    
    public static function isDebug() {
        return filter_var(self::get('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN);
    }
}
```

#### 2.5 Authentication System

**Middleware/JwtMiddleware.php**:
```php
<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware {
    public static function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (!$authHeader) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization header required']);
            exit;
        }
        
        $token = str_replace('Bearer ', '', $authHeader);
        
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }
    }
}
```

#### 2.6 Controllers Structure

**Controllers/AdminAuthController.php**:
```php
<?php
namespace App\Controllers;

use App\Models\Admin;
use Firebase\JWT\JWT;

class AdminAuthController {
    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $admin = Admin::findByEmail($data['email']);
        
        if (!$admin || !password_verify($data['password'], $admin['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }
        
        $token = JWT::encode([
            'admin_id' => $admin['id'],
            'email' => $admin['email'],
            'exp' => time() + (int)$_ENV['JWT_EXPIRE']
        ], $_ENV['JWT_SECRET'], 'HS256');
        
        echo json_encode([
            'token' => $token,
            'admin' => [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'email' => $admin['email'],
                'role' => $admin['role']
            ]
        ]);
    }
    
    public function register() {
        // Registration logic
    }
    
    public function profile() {
        $admin = App\Middleware\JwtMiddleware::authenticate();
        echo json_encode(['admin' => $admin]);
    }
}
```

#### 2.7 Models Layer

**Models/Admin.php**:
```php
<?php
namespace App\Models;

use Config\Database;

class Admin {
    public static function findByEmail($email) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM admins WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public static function create($data) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO admins (name, email, password, business_name, phone) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['business_name'] ?? null,
            $data['phone'] ?? null
        ]);
    }
}
```

### Phase 3: Frontend Development

#### 3.1 Layout Templates

**Views/layout.php**:
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Real Estate Management' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-gray-50">
    <?php if (isset($_SESSION['admin_id'])): ?>
        <?php include 'views/partials/navigation.php'; ?>
    <?php endif; ?>
    
    <main class="<?= isset($_SESSION['admin_id']) ? 'ml-64' : '' ?>">
        <?= $content ?? '' ?>
    </main>
    
    <script src="/assets/js/app.js"></script>
</body>
</html>
```

#### 3.2 Authentication Views

**Views/auth/login.php**:
```php
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
        </div>
        <form class="mt-8 space-y-6" action="/api/auth/login" method="POST" id="loginForm">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Password">
                </div>
            </div>
            
            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>
```

#### 3.3 Dashboard Components

**Views/dashboard/index.php**:
```php
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="py-4">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                    <i class="fas fa-home text-white"></i>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Properties</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="totalProperties">-</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- More stat cards... -->
            </div>
            
            <!-- Charts Section -->
            <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Overview</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
                
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activities</h3>
                    <div id="recentActivities"></div>
                </div>
            </div>
        </div>
    </div>
</div>
```

#### 3.4 JavaScript Functionality

**Assets/js/app.js**:
```javascript
// Authentication
document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            localStorage.setItem('token', result.token);
            localStorage.setItem('admin', JSON.stringify(result.admin));
            window.location.href = '/dashboard';
        } else {
            alert(result.error || 'Login failed');
        }
    } catch (error) {
        alert('Network error');
    }
});

// Dashboard Charts
function initRevenueChart() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
}

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    initRevenueChart();
    loadDashboardStats();
});
```

### Phase 4: API Development

#### 4.1 Route Configuration

**Routes/api.php**:
```php
<?php

// Authentication Routes
$route->post('/api/auth/login', 'AdminAuthController@login');
$route->post('/api/auth/register', 'AdminAuthController@register');
$route->get('/api/auth/me', 'AdminAuthController@profile')->middleware('jwt.auth');
$route->post('/api/auth/logout', 'AdminAuthController@logout')->middleware('jwt.auth');

// Property Routes
$route->get('/api/properties', 'PropertyController@index')->middleware('jwt.auth');
$route->post('/api/properties', 'PropertyController@store')->middleware('jwt.auth');
$route->get('/api/properties/{id}', 'PropertyController@show')->middleware('jwt.auth');
$route->put('/api/properties/{id}', 'PropertyController@update')->middleware('jwt.auth');
$route->delete('/api/properties/{id}', 'PropertyController@destroy')->middleware('jwt.auth');

// Dashboard Routes
$route->get('/api/dashboard/stats', 'DashboardController@stats')->middleware('jwt.auth');
$route->get('/api/dashboard/revenue', 'DashboardController@revenue')->middleware('jwt.auth');
$route->get('/api/dashboard/recent-activities', 'DashboardController@recentActivities')->middleware('jwt.auth');
```

#### 4.2 API Controllers

**Controllers/PropertyController.php**:
```php
<?php
namespace App\Controllers;

use App\Models\Property;
use App\Middleware\JwtMiddleware;

class PropertyController {
    public function index() {
        $admin = JwtMiddleware::authenticate();
        
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $properties = Property::paginate($page, 10, $search, $type, $status, $admin['admin_id']);
        
        echo json_encode([
            'properties' => $properties['data'],
            'pagination' => $properties['pagination']
        ]);
    }
    
    public function store() {
        $admin = JwtMiddleware::authenticate();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validation
        $required = ['name', 'address', 'type'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "$field is required"]);
                return;
            }
        }
        
        $propertyId = Property::create($data, $admin['admin_id']);
        
        echo json_encode([
            'message' => 'Property created successfully',
            'property_id' => $propertyId
        ]);
    }
    
    // Additional methods...
}
```

### Phase 5: Database Setup

#### 5.1 Database Creation
```sql
-- Create database
CREATE DATABASE real_estate_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Import schema
mysql -u root -p real_estate_db < database/schema.sql
```

#### 5.2 Initial Data Setup
Create test admin user:
```php
<?php
// create_admin.php
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Config\Database;

$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("INSERT INTO admins (name, email, password, role) VALUES (?, ?, ?, 'admin')");
$stmt->execute([
    'Test Admin',
    'admin@example.com',
    password_hash('password123', PASSWORD_DEFAULT)
]);

echo "Admin user created: admin@example.com / password123\n";
```

### Phase 6: Web Server Configuration

#### 6.1 Apache Configuration
Create `.htaccess` in public directory:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

Apache virtual host configuration:
```apache
<VirtualHost *:80>
    DocumentRoot /path/to/real-estate-management/public
    ServerName real-estate.local
    
    <Directory /path/to/real-estate-management/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### 6.2 Nginx Configuration
```nginx
server {
    listen 80;
    server_name real-estate.local;
    root /path/to/real-estate-management/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Phase 7: Testing & Deployment

#### 7.1 Development Testing
```bash
# Start development server
php -S localhost:8000 -t public

# Run tests
composer test

# Check code style
composer run lint
```

#### 7.2 Production Deployment
1. Set environment variables:
```env
APP_ENV=production
APP_DEBUG=false
```

2. Configure HTTPS with SSL certificate
3. Set up file permissions:
```bash
chmod -R 755 storage/
chmod -R 755 public/
```

4. Configure database backups
5. Set up monitoring and logging

### Phase 8: Security Implementation

#### 8.1 Security Measures
- Password hashing with `password_hash()`
- JWT token authentication
- SQL injection prevention with prepared statements
- CSRF protection
- Input validation and sanitization
- File upload security
- Rate limiting for API endpoints

#### 8.2 Security Headers
```php
// Add to index.php or middleware
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
```

### Phase 9: Additional Features

#### 9.1 File Upload System
- Property images upload
- Document management
- Image resizing and optimization
- Cloud storage integration

#### 9.2 Email Notifications
- Payment reminders
- Invoice notifications
- System alerts
- Email templates

#### 9.3 Reporting System
- Financial reports
- Occupancy reports
- Maintenance reports
- Export to PDF/Excel

### Phase 10: Performance Optimization

#### 10.1 Database Optimization
- Index optimization
- Query optimization
- Database caching
- Connection pooling

#### 10.2 Frontend Optimization
- Asset minification
- Image optimization
- Lazy loading
- Browser caching

## Final Checklist

### Pre-Launch Checklist
- [ ] All tests passing
- [ ] Security audit completed
- [ ] Performance benchmarks met
- [ ] Documentation complete
- [ ] Backup systems in place
- [ ] Monitoring configured
- [ ] SSL certificate installed
- [ ] Environment variables configured
- [ ] Database optimized
- [ ] Error logging configured

### Post-Launch Monitoring
- Application performance monitoring
- Error tracking
- User analytics
- System health checks
- Regular security updates

## Support & Maintenance

### Regular Tasks
- Database backups
- Security updates
- Performance monitoring
- Log analysis
- User support

### Troubleshooting
- Check error logs in `storage/logs/`
- Verify database connections
- Test API endpoints
- Monitor server resources
- Validate user permissions

This comprehensive guide covers building the entire real estate management system from scratch to production deployment. Follow each phase systematically and test thoroughly before proceeding to the next phase.
