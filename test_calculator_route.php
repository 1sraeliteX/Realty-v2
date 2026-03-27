<?php
// Simple test to check if calculator route works
require_once __DIR__ . '/config/bootstrap.php';

// Simulate the request
$_SERVER['REQUEST_URI'] = '/admin/calculator';
$_SERVER['REQUEST_METHOD'] = 'GET';

try {
    // Load the calculator controller
    $controller = new \App\Controllers\CalculatorController();
    
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator Route Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
            <i class="fas fa-calculator mr-2"></i>Calculator Route Test
        </h1>';

    echo '<div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6">';
    echo '<i class="fas fa-check-circle mr-2"></i>CalculatorController loaded successfully!';
    echo '</div>';

    // Test if calculator components exist
    $components = ['calculator-component', 'mortgage-calculator-component', 'roi-calculator-component'];
    foreach ($components as $component) {
        try {
            ComponentRegistry::load($component);
            echo '<div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-2">';
            echo '<i class="fas fa-check-circle mr-2"></i>' . $component . ' loaded successfully!';
            echo '</div>';
        } catch (Exception $e) {
            echo '<div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-2">';
            echo '<i class="fas fa-exclamation-triangle mr-2"></i>' . $component . ' failed: ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
    }

    echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Test Calculator Access</h2>
        <a href="/admin/calculator" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium inline-block">
            <i class="fas fa-calculator mr-2"></i>Access Calculator Page
        </a>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            Click this link to navigate to the calculator page.
        </p>
    </div>';

    echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Direct Component Test</h2>';
    
    // Render the calculator component directly
    try {
        CalculatorComponent::render();
        echo '<script>
            // Test calculator functions
            document.addEventListener("DOMContentLoaded", function() {
                setTimeout(function() {
                    if (typeof openCalculator === "function") {
                        console.log("✅ openCalculator function available");
                        openCalculator();
                    } else {
                        console.log("❌ openCalculator function not available");
                    }
                }, 1000);
            });
        </script>';
    } catch (Exception $e) {
        echo '<div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">';
        echo '<i class="fas fa-exclamation-triangle mr-2"></i>Error rendering calculator: ' . htmlspecialchars($e->getMessage());
        echo '</div>';
    }

    echo '</div>
    </div>
</body>
</html>';

} catch (Exception $e) {
    echo '<div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">';
    echo '<i class="fas fa-exclamation-triangle mr-2"></i>Error: ' . htmlspecialchars($e->getMessage());
    echo '</div>';
}
?>
