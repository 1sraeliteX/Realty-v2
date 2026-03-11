<?php
require_once 'config/config_simple.php';
require_once 'config/database.php';

use Config\Database;

try {
    $db = Database::getInstance();
    $props = $db->fetchAll('SELECT * FROM properties WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5');
    
    echo "Properties found: " . count($props) . PHP_EOL;
    foreach($props as $p) {
        echo "- " . $p['name'] . " (ID: " . $p['id'] . ")" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>
