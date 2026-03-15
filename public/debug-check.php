<?php
require_once 'C:/xampp/htdocs/Realty-v2/config/bootstrap.php';
session_start();
echo "<pre>";
echo "admin id: ";
echo $_SESSION['admin']['id'] ?? 'NOT SET';
echo "\n";
echo "admin name: ";
echo $_SESSION['admin']['name'] ?? 'NOT SET';
echo "\n";
$f = 'C:/xampp/htdocs/Realty-v2/config/database.php';
echo "DB file exists: ";
echo file_exists($f) ? 'YES' : 'NO';
echo "\n";
echo "</pre>";