<?php
require_once 'config/config_simple.php';
require_once 'config/database.php';

try {
    $db = Config\Database::getInstance();
    $admins = $db->fetchAll('SELECT id, name, email, role FROM admins WHERE deleted_at IS NULL LIMIT 5');
    
    if (empty($admins)) {
        echo "No admin users found in database.\n";
    } else {
        foreach ($admins as $admin) {
            echo "ID: {$admin['id']}, Name: {$admin['name']}, Email: {$admin['email']}, Role: {$admin['role']}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
