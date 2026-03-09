<?php
// Test admin-specific login flow
echo "=== Testing Admin Login Flow ===\n";

// Test admin login endpoint directly
echo "1. Testing /admin/login endpoint...\n";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
    ]
]);

$response = file_get_contents('http://localhost:8000/admin/login', false, $context);
if ($response !== false) {
    echo "✅ Admin login page accessible\n";
} else {
    echo "❌ Admin login page not accessible\n";
}

// Test admin login submission
echo "\n2. Testing admin login submission...\n";
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

$response = file_get_contents('http://localhost:8000/admin/login', false, $context);
if ($response !== false) {
    echo "✅ Admin login submission processed\n";
    // Check if redirected to dashboard
    if (strpos($response, 'dashboard') !== false || strpos(http_response_headers(), 'dashboard') !== false) {
        echo "✅ Redirected to dashboard\n";
    } else {
        echo "⚠️  May not have redirected properly\n";
        echo "Response length: " . strlen($response) . "\n";
    }
} else {
    echo "❌ Admin login submission failed\n";
}

// Test admin dashboard access
echo "\n3. Testing admin dashboard access...\n";
$response = file_get_contents('http://localhost:8000/admin/dashboard', false, $context);
if ($response !== false) {
    echo "✅ Admin dashboard accessible\n";
    if (strpos($response, 'dashboard') !== false) {
        echo "✅ Dashboard content loaded\n";
    } else {
        echo "⚠️  Dashboard may have redirected\n";
    }
} else {
    echo "❌ Admin dashboard not accessible\n";
}

echo "\n=== Test Complete ===\n";
?>
