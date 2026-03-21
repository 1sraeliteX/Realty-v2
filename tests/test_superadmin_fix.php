<?php
require_once 'config/bootstrap.php';
require_once 'app/controllers/SuperAdminController.php';

$controller = new App\Controllers\SuperAdminController();

// Test the methods that were causing issues
echo 'Testing getPlatformStats...' . PHP_EOL;
try {
    $stats = $controller->getPlatformStats();
    echo 'SUCCESS: Platform stats retrieved' . PHP_EOL;
    print_r($stats);
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . 'Testing getRecentAdmins...' . PHP_EOL;
try {
    $admins = $controller->getRecentAdmins(5);
    echo 'SUCCESS: Recent admins retrieved' . PHP_EOL;
    print_r($admins);
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
?>
