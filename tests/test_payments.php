<?php
// Simple test script to verify payments functionality
require_once __DIR__ . '/config/bootstrap.php';

echo "<h1>Payments System Test</h1>";

// Test 1: Check if PaymentModel exists and can be instantiated
try {
    require_once __DIR__ . '/models/PaymentModel.php';
    $paymentModel = new PaymentModel();
    echo "<p>✅ PaymentModel loaded successfully</p>";
} catch (Exception $e) {
    echo "<p>❌ PaymentModel error: " . $e->getMessage() . "</p>";
}

// Test 2: Check if PaymentsController exists
try {
    require_once __DIR__ . '/app/controllers/PaymentsController.php';
    echo "<p>✅ PaymentsController loaded successfully</p>";
} catch (Exception $e) {
    echo "<p>❌ PaymentsController error: " . $e->getMessage() . "</p>";
}

// Test 3: Check if payments view exists
if (file_exists(__DIR__ . '/views/admin/payments/index.php')) {
    echo "<p>✅ Payments view exists</p>";
} else {
    echo "<p>❌ Payments view not found</p>";
}

// Test 4: Check routes
$routes = require __DIR__ . '/routes/web.php';
if (isset($routes['GET /admin/payments'])) {
    echo "<p>✅ Payments route found: " . $routes['GET /admin/payments'] . "</p>";
} else {
    echo "<p>❌ Payments route not found</p>";
}

echo "<h2>Test Complete</h2>";
echo "<p><a href='/admin/payments'>Test Payments Page</a></p>";
?>
