<?php
echo "Test: PHP is working";
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/../app/controllers/BaseController.php';
    require_once __DIR__ . '/../app/controllers/TenantController.php';
    
    $controller = new \App\Controllers\TenantController();
    
    echo "Controller created successfully<br>";
    
    // Test the index method
    $controller->index();
    
    echo "Index method executed<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "Fatal Error: " . $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
