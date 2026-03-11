<?php
echo "=== Testing Dashboard Access via HTTP ===\n\n";

// First, login as admin
echo "1. Logging in as admin...\n";
$postData = http_build_query([
    'email' => 'admin@cornerstone.com',
    'password' => 'admin123'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $postData,
        'follow_location' => false, // Don't follow redirects to capture cookies
        'ignore_errors' => true
    ]
]);

$response = file_get_contents('http://localhost:8000/login', false, $context);

// Extract cookies from response headers
$cookies = [];
foreach ($http_response_header as $header) {
    if (strpos($header, 'Set-Cookie:') === 0) {
        $cookie = substr($header, 12);
        $cookies[] = $cookie;
    }
}

if (!empty($cookies)) {
    echo "✅ Admin login successful - cookies received\n";
    
    // Test dashboard access with cookies
    echo "2. Testing admin dashboard access...\n";
    $cookieHeader = 'Cookie: ' . implode('; ', $cookies);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => $cookieHeader . "\r\n",
            'ignore_errors' => true
        ]
    ]);
    
    $dashboardResponse = file_get_contents('http://localhost:8000/dashboard', false, $context);
    
    if (strpos($dashboardResponse, 'dashboard') !== false || strpos($dashboardResponse, 'Dashboard') !== false) {
        echo "✅ Admin dashboard accessible\n";
    } else {
        echo "❌ Admin dashboard not accessible\n";
        echo "Response snippet: " . substr($dashboardResponse, 0, 200) . "...\n";
    }
} else {
    echo "❌ Admin login failed - no cookies received\n";
}

echo "\n";

// Now test super admin
echo "3. Logging in as super admin...\n";
$postData = http_build_query([
    'email' => 'superadmin@cornerstone.com',
    'password' => 'admin123'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $postData,
        'follow_location' => false,
        'ignore_errors' => true
    ]
]);

$response = file_get_contents('http://localhost:8000/login', false, $context);

// Extract cookies from response headers
$cookies = [];
foreach ($http_response_header as $header) {
    if (strpos($header, 'Set-Cookie:') === 0) {
        $cookie = substr($header, 12);
        $cookies[] = $cookie;
    }
}

if (!empty($cookies)) {
    echo "✅ Super admin login successful - cookies received\n";
    
    // Test super admin dashboard access with cookies
    echo "4. Testing super admin dashboard access...\n";
    $cookieHeader = 'Cookie: ' . implode('; ', $cookies);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => $cookieHeader . "\r\n",
            'ignore_errors' => true
        ]
    ]);
    
    $superDashboardResponse = file_get_contents('http://localhost:8000/superadmin', false, $context);
    
    if (strpos($superDashboardResponse, 'Super Admin') !== false || strpos($superDashboardResponse, 'superadmin') !== false) {
        echo "✅ Super admin dashboard accessible\n";
    } else {
        echo "❌ Super admin dashboard not accessible\n";
        echo "Response snippet: " . substr($superDashboardResponse, 0, 200) . "...\n";
    }
} else {
    echo "❌ Super admin login failed - no cookies received\n";
}

echo "\n=== Dashboard HTTP Test Complete ===\n";
?>
