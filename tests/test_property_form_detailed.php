<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate the admin user that would be set by the controller
$admin = [
    'id' => 1,
    'name' => 'Test Admin',
    'email' => 'test@admin.com'
];

echo "=== Testing ComponentRegistry Loading ===" . PHP_EOL;

try {
    // Load bootstrap first
    require_once __DIR__ . '/config/bootstrap.php';
    echo "SUCCESS: Bootstrap loaded" . PHP_EOL;
    
    // Load components
    ComponentRegistry::load('ui-components');
    echo "SUCCESS: UIComponents loaded" . PHP_EOL;
    
    ComponentRegistry::load('autofill-component');
    echo "SUCCESS: AutoFillComponent loaded" . PHP_EOL;
    
    // Test the specific methods
    if (class_exists('Components\AutoFillComponent')) {
        echo "SUCCESS: AutoFillComponent class exists" . PHP_EOL;
        
        $data = \Components\AutoFillComponent::getPropertyFillData();
        echo "SUCCESS: getPropertyFillData() returned " . count($data) . " fields" . PHP_EOL;
        
        // Test the button generation
        ob_start();
        \Components\AutoFillComponent::generateAutoFillButton(
            'testForm', 
            $data,
            'Test Button',
            'bg-purple-600 hover:bg-purple-700 text-white'
        );
        $buttonHtml = ob_get_clean();
        
        echo "SUCCESS: Button generation worked, HTML length: " . strlen($buttonHtml) . PHP_EOL;
        
    } else {
        echo "ERROR: AutoFillComponent class not found" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}

echo PHP_EOL . "=== Testing Property Form View ===" . PHP_EOL;

try {
    ob_start();
    include __DIR__ . '/views/admin/properties/add.php';
    $output = ob_get_clean();
    
    echo "SUCCESS: Property form loaded, length: " . strlen($output) . PHP_EOL;
    
    if (strpos($output, 'Components\AutoFillComponent') !== false) {
        echo "SUCCESS: AutoFillComponent usage found in output" . PHP_EOL;
    } else {
        echo "WARNING: AutoFillComponent usage not found in output" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "ERROR loading property form: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
} catch (Error $e) {
    echo "FATAL ERROR loading property form: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}
?>
