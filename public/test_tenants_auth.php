<?php
// Test tenants with admin session
session_start();

// Create admin session
$_SESSION['admin'] = [
    'id' => 1,
    'name' => 'Test Admin',
    'email' => 'admin@test.com',
    'role' => 'admin'
];

// Load and test controller
require_once __DIR__ . '/../app/controllers/BaseController.php';
require_once __DIR__ . '/../app/controllers/TenantController.php';

use App\Controllers\TenantController;

try {
    $controller = new TenantController();
    $controller->index();
    echo "✅ Tenants page executed successfully";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
