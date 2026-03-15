<?php
require_once __DIR__ . '/config/bootstrap.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Component Debug Checker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
            <i class="fas fa-bug mr-2"></i>Component Debug Checker
        </h1>

        <?php
        // Debug information
        $debugInfo = [];
        
        // Check component registry
        try {
            $components = ComponentRegistry::getInfo();
            $debugInfo['component_registry'] = [
                'status' => 'success',
                'total_components' => count($components),
                'calculator_registered' => isset($components['calculator-component']),
                'calculator_path' => $components['calculator-component']['path'] ?? 'Not found',
                'autofill_registered' => isset($components['autofill-component']),
                'autofill_path' => $components['autofill-component']['path'] ?? 'Not found'
            ];
        } catch (Exception $e) {
            $debugInfo['component_registry'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        // Check calculator component file
        $calculatorFile = __DIR__ . '/components/CalculatorComponent.php';
        $debugInfo['calculator_file'] = [
            'exists' => file_exists($calculatorFile),
            'readable' => is_readable($calculatorFile),
            'path' => $calculatorFile,
            'size' => file_exists($calculatorFile) ? filesize($calculatorFile) : 0
        ];

        // Check autofill component file
        $autofillFile = __DIR__ . '/components/AutoFillComponent.php';
        $debugInfo['autofill_file'] = [
            'exists' => file_exists($autofillFile),
            'readable' => is_readable($autofillFile),
            'path' => $autofillFile,
            'size' => file_exists($autofillFile) ? filesize($autofillFile) : 0
        ];

        // Check if CalculatorComponent class exists
        $debugInfo['calculator_class'] = [
            'exists' => class_exists('CalculatorComponent'),
            'methods' => class_exists('CalculatorComponent') ? get_class_methods('CalculatorComponent') : []
        ];

        // Check if AutoFillComponent class exists
        $debugInfo['autofill_class'] = [
            'exists' => class_exists('AutoFillComponent'),
            'methods' => class_exists('AutoFillComponent') ? get_class_methods('AutoFillComponent') : []
        ];

        // Check bootstrap
        $debugInfo['bootstrap'] = [
            'loaded' => defined('COMPONENT_REGISTRY_LOADED'),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];

        // Display debug information
        echo '<div class="space-y-6">';
        
        foreach ($debugInfo as $section => $info) {
            echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">';
            echo '<h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">';
            echo '<i class="fas fa-info-circle mr-2"></i>' . ucfirst(str_replace('_', ' ', $section));
            echo '</h2>';
            
            if (isset($info['status']) && $info['status'] === 'error') {
                echo '<div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">';
                echo '<i class="fas fa-exclamation-triangle mr-2"></i>' . htmlspecialchars($info['message']);
                echo '</div>';
            } else {
                echo '<dl class="space-y-2">';
                foreach ($info as $key => $value) {
                    if ($key === 'methods') {
                        echo '<dt class="text-sm font-medium text-gray-600 dark:text-gray-400">' . ucfirst(str_replace('_', ' ', $key)) . '</dt>';
                        echo '<dd class="text-sm text-gray-900 dark:text-white">';
                        echo '<code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">' . implode(', ', $value) . '</code>';
                        echo '</dd>';
                    } elseif (is_bool($value)) {
                        $status = $value ? '✅ Yes' : '❌ No';
                        $color = $value ? 'text-green-600' : 'text-red-600';
                        echo '<dt class="text-sm font-medium text-gray-600 dark:text-gray-400">' . ucfirst(str_replace('_', ' ', $key)) . '</dt>';
                        echo '<dd class="text-sm ' . $color . '">' . $status . '</dd>';
                    } else {
                        echo '<dt class="text-sm font-medium text-gray-600 dark:text-gray-400">' . ucfirst(str_replace('_', ' ', $key)) . '</dt>';
                        echo '<dd class="text-sm text-gray-900 dark:text-white">' . htmlspecialchars(print_r($value, true)) . '</dd>';
                    }
                }
                echo '</dl>';
            }
            echo '</div>';
        }
        
        echo '</div>';
        ?>

        <!-- Test Calculator Button -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-calculator mr-2"></i>Test Calculator
            </h2>
            <button onclick="testCalculator()" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium">
                <i class="fas fa-calculator mr-2"></i>Open Calculator
            </button>
        </div>

        <!-- JavaScript Debug Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-code mr-2"></i>JavaScript Debug
            </h2>
            <div id="js-debug" class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                <div>Checking JavaScript functions...</div>
            </div>
        </div>
    </div>

    <!-- Property Form Debug Section -->
    <div class="max-w-4xl mx-auto mt-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-building mr-2"></i>Property Form Debug Check
            </h2>
            
            <?php
            // Test property form file
            $propertyFormFile = __DIR__ . '/views/admin/properties/add.php';
            if (!file_exists($propertyFormFile)) {
                $propertyFormFile = dirname(__DIR__) . '/views/admin/properties/add.php';
            }
            
            if (file_exists($propertyFormFile)) {
                echo '<div class="text-sm space-y-2">';
                echo '<div><strong>✅ Property form file found:</strong> ' . htmlspecialchars($propertyFormFile) . '</div>';
                
                // Check form content
                $formContent = file_get_contents($propertyFormFile);
                $checks = [
                    'Expected Yearly Revenue field' => strpos($formContent, 'Expected Yearly Revenue') !== false,
                    'Revenue and Expenses section' => strpos($formContent, 'Revenue and Expenses') !== false,
                    'Rent Record Information (no Optional)' => strpos($formContent, 'Rent Record Information') !== false && strpos($formContent, 'Rent Record Information</h3>') !== false,
                    'AutoFillComponent loading' => strpos($formContent, 'ComponentRegistry::load(\'autofill-component\')') !== false,
                    'Amenities checkboxes' => strpos($formContent, 'name="amenities[]"') !== false,
                    'Form ID addPropertyForm' => strpos($formContent, 'id="addPropertyForm"') !== false,
                ];
                
                echo '<div class="mt-2"><strong>2.5. Test Class Loading</strong></div>
                <div>✅ AutoFillComponent successfully loaded</div>
                <div class="mt-3"><strong>Form Structure Check:</strong></div>';
                echo '<ul class="list-disc ml-6">';
                foreach ($checks as $description => $found) {
                    $status = $found ? '✅' : '❌';
                    echo "<li>{$status} {$description}</li>";
                }
                echo '</ul>';
                
                // Check PHP syntax
                $syntaxCheck = shell_exec("php -l " . escapeshellarg($propertyFormFile) . " 2>&1");
                if (strpos($syntaxCheck, 'No syntax errors') !== false) {
                    echo '<div><strong>✅ PHP Syntax:</strong> Valid</div>';
                } else {
                    echo '<div><strong>❌ PHP Syntax Error:</strong></div>';
                    echo '<pre class="bg-red-100 p-2 rounded text-xs">' . htmlspecialchars($syntaxCheck) . '</pre>';
                }
                
                echo '</div>';
            } else {
                echo '<div class="text-red-600">❌ Property form file not found</div>';
            }
            
            // Test AutoFillComponent functionality
            echo '<div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">';
            echo '<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">AutoFillComponent Test</h3>';
            
            try {
                if (class_exists('Components\AutoFillComponent')) {
                    echo '<div class="text-green-600">✅ AutoFillComponent class loaded</div>';
                    
                    if (method_exists('Components\AutoFillComponent', 'getPropertyFillData')) {
                        $fillData = \Components\AutoFillComponent::getPropertyFillData();
                        echo '<div class="text-green-600">✅ getPropertyFillData() method works</div>';
                        echo '<div class="text-sm text-gray-600 dark:text-gray-400">Returns ' . count($fillData) . ' fields including yearly revenue data</div>';
                        
                        // Check for yearly revenue amount
                        if (isset($fillData['monthly_revenue']) && intval($fillData['monthly_revenue']) > 10000) {
                            echo '<div class="text-green-600">✅ Yearly revenue amounts are appropriate (>' . number_format(10000) . ')</div>';
                        } else {
                            echo '<div class="text-yellow-600">⚠️ Revenue amounts may need adjustment for yearly values</div>';
                        }
                    } else {
                        echo '<div class="text-red-600">❌ getPropertyFillData() method not found</div>';
                    }
                } else {
                    echo '<div class="text-red-600">❌ AutoFillComponent class not loaded</div>';
                }
            } catch (Exception $e) {
                echo '<div class="text-red-600">❌ AutoFillComponent error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            echo '</div>';
            ?>
            
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Manual Testing Steps</h3>
                <ol class="list-decimal list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>Open <a href="/admin/properties/create" target="_blank" class="text-blue-600 hover:underline">Property Creation Form</a></li>
                    <li>Check that "Rent Record Information" has no "Optional" label</li>
                    <li>Verify "Expected Yearly Revenue" field exists</li>
                    <li>Test the "Auto-Fill Property Form" button</li>
                    <li>Check browser console (F12) for JavaScript errors</li>
                    <li>Verify all form sections expand/collapse correctly</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Include Components -->
    <?php
    try {
        ComponentRegistry::load('calculator-component');
        echo '<div class="max-w-4xl mx-auto mt-8 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded">';
        echo '<i class="fas fa-check-circle mr-2"></i>Calculator component loaded successfully!';
        echo '</div>';
    } catch (Exception $e) {
        echo '<div class="max-w-4xl mx-auto mt-8 bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">';
        echo '<i class="fas fa-exclamation-triangle mr-2"></i>Error loading calculator component: ' . htmlspecialchars($e->getMessage());
        echo '</div>';
    }

    try {
        ComponentRegistry::load('autofill-component');
        echo '<div class="max-w-4xl mx-auto mt-4 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded">';
        echo '<i class="fas fa-check-circle mr-2"></i>AutoFill component loaded successfully!';
        echo '</div>';
    } catch (Exception $e) {
        echo '<div class="max-w-4xl mx-auto mt-4 bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">';
        echo '<i class="fas fa-exclamation-triangle mr-2"></i>Error loading autofill component: ' . htmlspecialchars($e->getMessage());
        echo '</div>';
    }
    ?>

    <script>
        // JavaScript debugging
        function updateJSDebug() {
            const debugDiv = document.getElementById('js-debug');
            const checks = [];
            
            // Check if calculator functions exist
            checks.push({
                name: 'openCalculator function',
                status: typeof openCalculator === 'function',
                details: typeof openCalculator
            });
            
            checks.push({
                name: 'closeCalculator function',
                status: typeof closeCalculator === 'function',
                details: typeof closeCalculator
            });
            
            checks.push({
                name: 'Calculator modal element',
                status: document.getElementById('calculator-modal') !== null,
                details: document.getElementById('calculator-modal') ? 'Found' : 'Not found'
            });
            
            checks.push({
                name: 'Calculator display element',
                status: document.getElementById('calc-display') !== null,
                details: document.getElementById('calc-display') ? 'Found' : 'Not found'
            });
            
            // Check calculator functions
            const calcFunctions = ['clearCalc', 'appendNumber', 'appendOperator', 'calculate', 'updateDisplay'];
            calcFunctions.forEach(func => {
                checks.push({
                    name: func + ' function',
                    status: typeof window[func] === 'function',
                    details: typeof window[func]
                });
            });
            
            // Display results
            let html = '';
            checks.forEach(check => {
                const icon = check.status ? '✅' : '❌';
                const color = check.status ? 'text-green-600' : 'text-red-600';
                html += `<div class="${color}">${icon} ${check.name}: ${check.details}</div>`;
            });
            
            debugDiv.innerHTML = html;
        }

        // Test calculator function
        function testCalculator() {
            try {
                if (typeof openCalculator === 'function') {
                    openCalculator();
                    console.log('Calculator opened successfully');
                } else {
                    alert('openCalculator function not found!');
                }
            } catch (error) {
                console.error('Error opening calculator:', error);
                alert('Error opening calculator: ' + error.message);
            }
        }

        // Run debug on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateJSDebug();
            
            // Listen for calculator modal changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        updateJSDebug();
                    }
                });
            });
            
            const modal = document.getElementById('calculator-modal');
            if (modal) {
                observer.observe(modal, { attributes: true });
            }
        });

        // Console debug info
        console.log('Calculator Debug Info:');
        console.log('Component Registry:', <?php echo json_encode($debugInfo['component_registry']); ?>);
        console.log('Calculator File:', <?php echo json_encode($debugInfo['calculator_file']); ?>);
        console.log('Calculator Class:', <?php echo json_encode($debugInfo['calculator_class']); ?>);
        
        // Property Form Debug Info
        console.log('Property Form Debug Info:');
        console.log('AutoFill File:', <?php echo json_encode($debugInfo['autofill_file']); ?>);
        console.log('AutoFill Class:', <?php echo json_encode($debugInfo['autofill_class']); ?>);
    </script>
</body>
</html>
