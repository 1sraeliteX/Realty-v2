<?php
require_once __DIR__ . '/config/database.php';

$db = Config\Database::getInstance();

echo "=== ADMINS TABLE STRUCTURE ===\n";
$result = $db->query('DESCRIBE admins');
foreach($result as $row) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . PHP_EOL;
}

echo "\n=== PROPERTIES TABLE STRUCTURE ===\n";
$result = $db->query('DESCRIBE properties');
foreach($result as $row) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . PHP_EOL;
}

echo "\n=== UNITS TABLE STRUCTURE ===\n";
$result = $db->query('DESCRIBE units');
foreach($result as $row) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . PHP_EOL;
}

echo "\n=== TENANTS TABLE STRUCTURE ===\n";
$result = $db->query('DESCRIBE tenants');
foreach($result as $row) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . PHP_EOL;
}

echo "\n=== PAYMENTS TABLE STRUCTURE ===\n";
$result = $db->query('DESCRIBE payments');
foreach($result as $row) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . PHP_EOL;
}
?>
