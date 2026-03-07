<?php
// Simple PHP test
echo "<h1>PHP is Working!</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";

// Check if project files exist
$files_to_check = [
    '../app/controllers/AuthController.php',
    '../views/auth/login.php',
    '../config/config.php'
];

echo "<h3>Project Files Check:</h3>";
foreach ($files_to_check as $file) {
    $exists = file_exists($file) ? "✅ Found" : "❌ Missing";
    echo "<p>$file: $exists</p>";
}
?>
