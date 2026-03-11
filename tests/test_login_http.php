<?php
echo "=== Testing Login via HTTP ===\n\n";

// Test login page access
echo "1. Testing login page access...\n";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
    ]
]);

$response = file_get_contents('http://localhost:8000/login', false, $context);
if ($response && strpos($response, 'Sign in to your account') !== false) {
    echo "✅ Login page accessible\n";
} else {
    echo "❌ Login page not accessible\n";
}

echo "\n";

// Test admin login
echo "2. Testing admin login...\n";
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

// Follow redirects
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $postData,
        'follow_location' => true,
        'max_redirects' => 5
    ]
]);

$response = file_get_contents('http://localhost:8000/login', false, $context);

// Check if redirected to dashboard
if (strpos($response, 'dashboard') !== false || strpos($response, 'Dashboard') !== false) {
    echo "✅ Admin login successful - redirected to dashboard\n";
} else {
    echo "❌ Admin login failed\n";
    echo "Response length: " . strlen($response) . "\n";
}

echo "\n";

// Test super admin login
echo "3. Testing super admin login...\n";
$postData = http_build_query([
    'email' => 'superadmin@cornerstone.com',
    'password' => 'admin123'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $postData,
        'follow_location' => true,
        'max_redirects' => 5
    ]
]);

$response = file_get_contents('http://localhost:8000/login', false, $context);

// Check if redirected to superadmin
if (strpos($response, 'superadmin') !== false || strpos($response, 'Super Admin') !== false) {
    echo "✅ Super admin login successful - redirected to superadmin dashboard\n";
} else {
    echo "❌ Super admin login failed\n";
    echo "Response length: " . strlen($response) . "\n";
}

echo "\n=== HTTP Test Complete ===\n";
?>
