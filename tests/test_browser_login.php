<?php
// Test login through browser simulation
echo "<h2>Browser Login Test</h2>";

// Initialize curl session
$ch = curl_init();

// Set up cookies to maintain session
$cookieFile = tempnam(sys_get_temp_dir(), 'cookie_');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);

// Step 1: Get login page
echo "<h3>Step 1: Getting login page...</h3>";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/login');
curl_setopt($ch, CURLOPT_POST, false);
$response1 = curl_exec($ch);
$httpCode1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "<p>HTTP Code: $httpCode1</p>";
if ($httpCode1 === 200) {
    echo "<p style='color: green;'>✓ Login page accessible</p>";
} else {
    echo "<p style='color: red;'>✗ Login page not accessible</p>";
}

// Step 2: Submit login form
echo "<h3>Step 2: Submitting login form...</h3>";
$postData = [
    'email' => 'admin@cornerstone.com',
    'password' => 'admin123'
];

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

echo "<p>HTTP Code: $httpCode2</p>";
echo "<p>Final URL: $finalUrl</p>";

if ($httpCode2 === 200 || $httpCode2 === 302) {
    if (strpos($finalUrl, 'dashboard') !== false) {
        echo "<p style='color: green;'>✓ Login successful - redirected to dashboard</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Login processed but may not have redirected properly</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Login failed</p>";
}

// Step 3: Try to access dashboard
echo "<h3>Step 3: Accessing dashboard...</h3>";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/dashboard');
curl_setopt($ch, CURLOPT_POST, false);
$response3 = curl_exec($ch);
$httpCode3 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "<p>HTTP Code: $httpCode3</p>";
if ($httpCode3 === 200) {
    if (strpos($response3, 'dashboard') !== false) {
        echo "<p style='color: green;'>✓ Dashboard accessible</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Dashboard returned content but may have errors</p>";
    }
} elseif ($httpCode3 === 302) {
    echo "<p style='color: orange;'>⚠ Dashboard redirected (likely back to login)</p>";
} else {
    echo "<p style='color: red;'>✗ Dashboard not accessible</p>";
}

// Show detailed responses for debugging
echo "<h3>Detailed Response Analysis:</h3>";
echo "<h4>Login Page Response:</h4>";
echo "<pre>" . htmlspecialchars(substr($response1, 0, 500)) . "...</pre>";

echo "<h4>Login Submission Response:</h4>";
echo "<pre>" . htmlspecialchars(substr($response2, 0, 500)) . "...</pre>";

echo "<h4>Dashboard Response:</h4>";
echo "<pre>" . htmlspecialchars(substr($response3, 0, 500)) . "...</pre>";

// Clean up
curl_close($ch);
unlink($cookieFile);

echo "<h3>Test Complete</h3>";
?>
