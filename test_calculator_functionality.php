<?php
require_once __DIR__ . '/config/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator Functionality Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
            <i class="fas fa-calculator mr-2"></i>Calculator Functionality Test
        </h1>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Direct Calculator Access</h2>
            <a href="/admin/calculator" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium inline-block">
                <i class="fas fa-calculator mr-2"></i>Go to Calculator Page
            </a>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                Click this link to navigate to the calculator page.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Component Loading Test</h2>
            <?php
            try {
                ComponentRegistry::load('calculator-component');
                ComponentRegistry::load('mortgage-calculator-component');
                ComponentRegistry::load('roi-calculator-component');
                echo '<div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">';
                echo '<i class="fas fa-check-circle mr-2"></i>All calculator components loaded successfully!';
                echo '</div>';
                
                // Render the basic calculator component
                echo '<div class="mt-4">';
                CalculatorComponent::render();
                echo '</div>';
            } catch (Exception $e) {
                echo '<div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">';
                echo '<i class="fas fa-exclamation-triangle mr-2"></i>Error loading calculator components: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
            ?>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Test Results</h2>
            <div id="test-results" class="text-sm space-y-2">
                <div>Testing calculator functionality...</div>
            </div>
        </div>
    </div>

    <!-- Include calculator JavaScript -->
    <script>
        // Test calculator functions
        function runTests() {
            const results = document.getElementById('test-results');
            const tests = [];

            // Test 1: Check if calculator functions exist
            tests.push({
                name: 'openCalculator function exists',
                status: typeof openCalculator === 'function',
                details: typeof openCalculator
            });

            tests.push({
                name: 'closeCalculator function exists',
                status: typeof closeCalculator === 'function',
                details: typeof closeCalculator
            });

            // Test 2: Check if calculator modal exists
            const modal = document.getElementById('calculator-modal');
            tests.push({
                name: 'Calculator modal exists',
                status: modal !== null,
                details: modal ? 'Found' : 'Not found'
            });

            // Test 3: Check calculator display elements
            const display = document.getElementById('calc-display');
            const expression = document.getElementById('calc-expression');
            tests.push({
                name: 'Calculator display elements exist',
                status: display !== null && expression !== null,
                details: `Display: ${display ? 'Found' : 'Not found'}, Expression: ${expression ? 'Found' : 'Not found'}`
            });

            // Test 4: Test calculator functions
            const calcFunctions = ['clearCalc', 'appendNumber', 'appendOperator', 'calculate', 'updateDisplay'];
            calcFunctions.forEach(func => {
                tests.push({
                    name: `${func} function exists`,
                    status: typeof window[func] === 'function',
                    details: typeof window[func]
                });
            });

            // Display results
            let html = '<div class="space-y-2">';
            tests.forEach(test => {
                const icon = test.status ? '✅' : '❌';
                const color = test.status ? 'text-green-600' : 'text-red-600';
                html += `<div class="${color}">${icon} ${test.name}: ${test.details}</div>`;
            });
            html += '</div>';

            results.innerHTML = html;
        }

        // Run tests when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(runTests, 1000);
        });
    </script>

    <!-- Include calculator component JavaScript -->
    <?php
    try {
        // Include the calculator JavaScript
        include_once __DIR__ . '/components/CalculatorComponent.php';
        // The JavaScript should be included in the component render method
    } catch (Exception $e) {
        echo '<script>console.error("Error including calculator JS:", ' . json_encode($e->getMessage()) . ');</script>';
    }
    ?>
</body>
</html>
