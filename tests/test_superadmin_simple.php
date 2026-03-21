<?php
require_once 'config/bootstrap.php';
require_once 'app/controllers/SuperAdminController.php';

try {
    $controller = new App\Controllers\SuperAdminController();
    echo 'SuperAdminController instantiated successfully' . PHP_EOL;
    
    // Test a simple query
    $result = $controller->getRecentAdmins(1);
    echo 'getRecentAdmins method works' . PHP_EOL;
    
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo 'File: ' . $e->getFile() . PHP_EOL;
    echo 'Line: ' . $e->getLine() . PHP_EOL;
}
?>
