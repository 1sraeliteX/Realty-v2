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
            'exists' => class_exists('Components\AutoFillComponent'),
            'methods' => class_exists('Components\AutoFillComponent') ? get_class_methods('Components\AutoFillComponent') : []
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

    <!-- AutoFillComponent Debug Error -->
    <div class="max-w-4xl mx-auto mt-8">
        <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded">
            <h3 class="font-bold text-lg mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>AutoFillComponent Debug Check
            </h3>
            <div class="text-sm space-y-1">
                <div><strong>1. File Search</strong></div>
                <div>❌ NOT FOUND → C:\xampp\htdocs\Realty-v2\public/app/components/AutoFillComponent.php</div>
                <div>❌ NOT FOUND → C:\xampp\htdocs\Realty-v2\public/../app/components/AutoFillComponent.php</div>
                <div>❌ NOT FOUND → C:\xampp\htdocs\Realty-v2/app/components/AutoFillComponent.php</div>
                <div>✅ FOUND → C:\xampp\htdocs\Realty-v2/components/AutoFillComponent.php</div>
                
                <div class="mt-2"><strong>2. Class Loaded?</strong></div>
                <div>❌ AutoFillComponent is NOT loaded in current scope</div>
                <div>❌ Components\AutoFillComponent is NOT loaded in current scope</div>
                
                <div class="mt-2"><strong>2.5. Test Class Loading</strong></div>
                <div>✅ Components\AutoFillComponent successfully loaded</div>
                
                <div class="mt-2"><strong>3. add.php Line 61 Context</strong></div>
                <div>59:</div>
                <div>60: // Load AutoFillComponent using ComponentRegistry</div>
                <div>61: ComponentRegistry::load('autofill-component');</div>
                <div>&lt;-- LINE 61</div>
                <div>62: ?&gt;</div>
                <div>63:</div>
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
    </script>
</body>
</html>
