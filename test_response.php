<?php
echo "=== Checking HTTP Response ===\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
    ]
]);

$response = file_get_contents('http://localhost:8000/login', false, $context);
echo "Response length: " . strlen($response) . "\n";
echo "Response content:\n";
echo $response . "\n";
?>
