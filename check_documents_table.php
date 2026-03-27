<?php
require_once 'config/config_simple.php';
require_once 'config/database.php';

$config = Config\ConfigSimple::getInstance();
$db = Config\Database::getInstance();

echo "=== DOCUMENTS TABLE STRUCTURE ===\n";
$stmt = $db->query('SHOW COLUMNS FROM documents');
while ($row = $stmt->fetch()) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . ' | ' . $row['Key'] . ' | ' . $row['Default'] . "\n";
}

echo "\n=== SAMPLE DATA ===\n";
$stmt = $db->query('SELECT * FROM documents LIMIT 3');
while ($row = $stmt->fetch()) {
    echo "ID: " . $row['id'] . " | Title: " . $row['title'] . " | File: " . $row['file_name'] . "\n";
}
?>
