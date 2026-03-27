<?php
session_start();
$_SESSION['admin_id'] = 1;
$_SESSION['admin_email'] = 'test@admin.com';
$_SESSION['admin_logged_in'] = true;

require_once 'config/bootstrap.php';
require_once 'app/controllers/BaseController.php';
require_once 'app/controllers/CommunicationController.php';

echo "=== Debugging Controller Step by Step ===" . PHP_EOL;

try {
    $controller = new \App\Controllers\CommunicationController();
    echo "1. Controller created" . PHP_EOL;
    
    // Test requireAuth method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('requireAuth');
    $method->setAccessible(true);
    
    echo "2. Testing requireAuth..." . PHP_EOL;
    $admin = $method->invoke($controller);
    
    if ($admin) {
        echo "   Admin auth: SUCCESS" . PHP_EOL;
        echo "   Admin data: " . print_r($admin, true) . PHP_EOL;
    } else {
        echo "   Admin auth: FAILED" . PHP_EOL;
        exit;
    }
    
    echo "3. Testing database connection..." . PHP_EOL;
    $dbReflection = new ReflectionProperty($controller, 'db');
    $db = $dbReflection->getValue($controller);
    
    if ($db) {
        echo "   Database: CONNECTED" . PHP_EOL;
    } else {
        echo "   Database: FAILED" . PHP_EOL;
        exit;
    }
    
    echo "4. Testing query execution..." . PHP_EOL;
    $sql = "SELECT COUNT(*) FROM communications WHERE deleted_at IS NULL";
    $result = $db->query($sql)->fetchColumn();
    echo "   Communications count: $result" . PHP_EOL;
    
    echo "5. Testing ViewManager..." . PHP_EOL;
    $communications = [
        ['id' => 1, 'subject' => 'Test', 'type' => 'email', 'status' => 'sent']
    ];
    
    \ViewManager::set('communications', $communications);
    $testData = \ViewManager::get('communications');
    echo "   ViewManager data: " . count($testData) . " items" . PHP_EOL;
    
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . PHP_EOL;
}
?>
