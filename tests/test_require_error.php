<?php
// Test file to reproduce the require error
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing require error</h1>";

// This should cause the error mentioned in the prompt
try {
    require_once __DIR__ . '/public/app.php';
} catch (Error $e) {
    echo "<h2>Error reproduced:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Now testing the fix:</h2>";

// This should work
try {
    require_once __DIR__ . '/public/app.php';
    echo "<p>✅ Fixed version works correctly</p>";
} catch (Error $e) {
    echo "<p>❌ Fixed version failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
