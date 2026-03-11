<?php
session_start();
require_once 'config/config_simple.php';
require_once 'config/database.php';
require_once 'app/middleware/JwtMiddleware.php';

use Config\Database;
use App\Middleware\JwtMiddleware;

echo "=== Authentication Debug ===\n\n";

// Check session
echo "Session data:\n";
print_r($_SESSION);
echo "\n";

// Check JWT token if present
$token = $_SESSION['token'] ?? $_COOKIE['token'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if ($token) {
    echo "Token found: " . substr($token, 0, 50) . "...\n";
    
    try {
        $jwt = new JwtMiddleware();
        $payload = $jwt->validateToken($token);
        if ($payload) {
            echo "Token valid. User ID: " . $payload['user_id'] . "\n";
            echo "User email: " . $payload['email'] . "\n";
            echo "User role: " . $payload['role'] . "\n";
            
            // Get user from database
            $db = Database::getInstance();
            $user = $db->fetch("SELECT * FROM admins WHERE id = ? AND deleted_at IS NULL", [$payload['user_id']]);
            if ($user) {
                echo "Database user found: " . $user['name'] . " (ID: " . $user['id'] . ")\n";
                
                // Check their properties
                $props = $db->fetchAll("SELECT * FROM properties WHERE admin_id = ? AND deleted_at IS NULL", [$user['id']]);
                echo "Properties for this user: " . count($props) . "\n";
            } else {
                echo "User not found in database!\n";
            }
        } else {
            echo "Token validation failed\n";
        }
    } catch (Exception $e) {
        echo "JWT Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "No authentication token found\n";
}

echo "\n=== Recommendations ===\n";
echo "1. Make sure you're logged in as Test Admin (ID: 1)\n";
echo "2. Try accessing: /admin/login\n";
echo "3. After login, visit: /admin/properties\n";
?>
