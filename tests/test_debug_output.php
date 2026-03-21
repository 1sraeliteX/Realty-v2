<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate the admin user
$admin = [
    'id' => 1,
    'name' => 'Test Admin',
    'email' => 'test@admin.com'
];

// Load bootstrap
require_once __DIR__ . '/config/bootstrap.php';

// Load just the part around the AutoFillComponent
ob_start();
?>
<form id="addPropertyForm" action="/admin/properties" method="POST" enctype="multipart/form-data" class="space-y-8">
    
    <?php
    // Add auto-fill button at the top
    try {
        echo "<!-- DEBUG: About to call AutoFillComponent -->\n";
        \Components\AutoFillComponent::generateAutoFillButton(
            'addPropertyForm', 
            \Components\AutoFillComponent::getPropertyFillData(),
            'Auto-Fill Property Form',
            'bg-purple-600 hover:bg-purple-700 text-white'
        );
        echo "<!-- DEBUG: AutoFillComponent call completed -->\n";
    } catch (Exception $e) {
        echo "<!-- DEBUG: AutoFillComponent error: " . $e->getMessage() . " -->\n";
    } catch (Error $e) {
        echo "<!-- DEBUG: AutoFillComponent fatal error: " . $e->getMessage() . " -->\n";
    }
    ?>
</form>
<?php
$output = ob_get_clean();

echo "=== DEBUG OUTPUT ===" . PHP_EOL;
echo $output . PHP_EOL;

echo "=== LOOKING FOR DEBUG COMMENTS ===" . PHP_EOL;
if (strpos($output, 'DEBUG') !== false) {
    echo "FOUND debug comments in output" . PHP_EOL;
    // Extract debug lines
    $lines = explode("\n", $output);
    foreach ($lines as $line) {
        if (strpos($line, 'DEBUG') !== false) {
            echo "DEBUG LINE: " . trim($line) . PHP_EOL;
        }
    }
} else {
    echo "NO debug comments found" . PHP_EOL;
}
?>
