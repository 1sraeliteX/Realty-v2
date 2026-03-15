<?php
$file = file_get_contents('C:/xampp/htdocs/Realty-v2/views/admin/properties/add.php');
$lines = explode("\n", $file);
echo "<pre>";
// Show first 40 lines to see the full layout structure
for ($i = 0; $i <= 40; $i++) {
    echo "Line " . ($i+1) . ": " . htmlspecialchars($lines[$i] ?? '') . "\n";
}
echo "</pre>";
?>
