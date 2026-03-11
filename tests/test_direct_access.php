<?php
echo "=== Testing Direct Dashboard Access (No Login) ===\n\n";

// Test admin dashboard access
echo "1. Testing admin dashboard direct access...\n";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'ignore_errors' => true
    ]
]);

$response = file_get_contents('http://localhost:8000/dashboard', false, $context);

if (strpos($response, 'dashboard') !== false || strpos($response, 'Dashboard') !== false) {
    echo "✅ Admin dashboard accessible without login\n";
} else {
    echo "❌ Admin dashboard not accessible\n";
    echo "Response snippet: " . substr($response, 0, 200) . "...\n";
}

echo "\n";

// Test super admin dashboard access
echo "2. Testing super admin dashboard direct access...\n";
$response = file_get_contents('http://localhost:8000/superadmin', false, $context);

if (strpos($response, 'Super Admin') !== false || strpos($response, 'superadmin') !== false) {
    echo "✅ Super admin dashboard accessible without login\n";
} else {
    echo "❌ Super admin dashboard not accessible\n";
    echo "Response snippet: " . substr($response, 0, 200) . "...\n";
}

echo "\n";

// Test login page access
echo "3. Testing login page access...\n";
$response = file_get_contents('http://localhost:8000/login', false, $context);

if (strpos($response, 'Sign in to your account') !== false) {
    echo "✅ Login page still accessible\n";
} else {
    echo "❌ Login page not accessible\n";
}

echo "\n=== Direct Access Test Complete ===\n";
?>
