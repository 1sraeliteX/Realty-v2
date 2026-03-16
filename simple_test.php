<?php
echo "<h1>Simple Test</h1>";

// Test basic file existence
if (file_exists(__DIR__ . '/models/PaymentModel.php')) {
    echo "<p>✅ PaymentModel.php exists</p>";
} else {
    echo "<p>❌ PaymentModel.php not found</p>";
}

if (file_exists(__DIR__ . '/app/controllers/PaymentsController.php')) {
    echo "<p>✅ PaymentsController.php exists</p>";
} else {
    echo "<p>❌ PaymentsController.php not found</p>";
}

if (file_exists(__DIR__ . '/views/admin/payments/index.php')) {
    echo "<p>✅ Payments view exists</p>";
} else {
    echo "<p>❌ Payments view not found</p>";
}

// Check routes
$routes = include __DIR__ . '/routes/web.php';
if (isset($routes['GET /admin/payments'])) {
    echo "<p>✅ Payments route found: " . $routes['GET /admin/payments'] . "</p>";
} else {
    echo "<p>❌ Payments route not found</p>";
}

echo "<h2>Files are created. Test accessing: <a href='/admin/payments'>/admin/payments</a></h2>";
?>
