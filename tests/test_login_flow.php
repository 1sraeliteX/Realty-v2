<?php

// Test login flow through web interface
echo "=== Testing Login Flow ===\n";

// Test 1: Check if login page loads
echo "1. Testing login page access...\n";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
    ]
]);

$response = file_get_contents('http://localhost:8000/login', false, $context);
if ($response !== false) {
    echo "✅ Login page accessible\n";
} else {
    echo "❌ Login page not accessible\n";
}

// Test 2: Test login submission
echo "\n2. Testing login submission...\n";
$postData = http_build_query([
    'email' => 'admin@cornerstone.com',
    'password' => 'admin123'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $postData
    ]
]);

$response = file_get_contents('http://localhost:8000/login', false, $context);
if ($response !== false) {
    echo "✅ Login submission processed\n";
    echo "Response: " . substr($response, 0, 200) . "...\n";
} else {
    echo "❌ Login submission failed\n";
}

// Test 3: Test dashboard access
echo "\n3. Testing dashboard access...\n";
$response = file_get_contents('http://localhost:8000/dashboard', false, $context);
if ($response !== false) {
    echo "✅ Dashboard accessible\n";
    if (strpos($response, 'dashboard') !== false) {
        echo "✅ Dashboard content loaded\n";
    } else {
        echo "⚠️  Dashboard may have redirected\n";
        if (strpos($response, 'login') !== false) {
            echo "❌ Redirected back to login\n";
        }
    }
} else {
    echo "❌ Dashboard not accessible\n";
}

echo "\n=== Test Complete ===\n";
?>
