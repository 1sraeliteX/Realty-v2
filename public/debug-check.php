<?php
require_once 'C:/xampp/htdocs/Realty-v2/config/bootstrap.php';
session_start();

echo "<pre>";
echo "Classes available after bootstrap:\n";
$classes = get_declared_classes();
$relevant = array_filter($classes, function($c) {
    return stripos($c, 'data') !== false || 
           stripos($c, 'db') !== false ||
           stripos($c, 'pdo') !== false ||
           stripos($c, 'config') !== false;
});
foreach ($relevant as $c) echo $c . "\n";
echo "</pre>";