<?php
// Simulate the admin user that would be set by the controller
$admin = [
    'id' => 1,
    'name' => 'Test Admin',
    'email' => 'test@admin.com'
];

// Load the property form view
ob_start();
include __DIR__ . '/views/admin/properties/add.php';
$output = ob_get_clean();

echo "=== Property Form Test ===" . PHP_EOL;
echo "Form loaded successfully: " . (strlen($output) > 0 ? 'YES' : 'NO') . PHP_EOL;
echo "Output length: " . strlen($output) . " characters" . PHP_EOL;

// Check for AutoFillComponent usage
if (strpos($output, 'Components\AutoFillComponent') !== false) {
    echo "SUCCESS: AutoFillComponent is being used in the form" . PHP_EOL;
} else {
    echo "ERROR: AutoFillComponent not found in form output" . PHP_EOL;
}

// Check for auto-fill button
if (strpos($output, 'autoFillForm') !== false) {
    echo "SUCCESS: Auto-fill JavaScript function found" . PHP_EOL;
} else {
    echo "ERROR: Auto-fill JavaScript function not found" . PHP_EOL;
}

echo PHP_EOL . "=== First 500 characters of form output ===" . PHP_EOL;
echo substr($output, 0, 500) . PHP_EOL;
?>
