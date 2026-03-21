<?php
require_once 'config/database.php';
$pdo = Config\Database::getInstance()->getConnection();
$result = $pdo->query('DESCRIBE units');
foreach($result as $row) {
    echo $row['Field'] . PHP_EOL;
}
