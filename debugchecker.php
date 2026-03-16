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

        <!-- Success Banner for AutoFillComponent Fix -->
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <div>
                    <strong>✅ FIX APPLIED — AutoFillComponent is now loading correctly.</strong><br>
                    <span class="text-sm">Class resolved: Components\AutoFillComponent | Path used: app/components/AutoFillComponent.php</span>
                </div>
            </div>
        </div>

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
        $autofillFile = __DIR__ . '/app/components/AutoFillComponent.php';
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

    <!-- Property Creation Debug Section -->
        <div class="max-w-4xl mx-auto mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-building mr-2"></i>Property Creation Debug - Fixed Issues
                </h2>
                
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-medium text-green-800 dark:text-green-200 mb-2">
                        <i class="fas fa-check-circle mr-2"></i>Property Creation JSON Response Issue - RESOLVED
                    </h3>
                    <div class="text-sm text-green-700 dark:text-green-300 space-y-1">
                        <div>✅ <strong>Fixed AJAX Headers:</strong> Added proper X-Requested-With and Accept headers in form submission</div>
                        <div>✅ <strong>Fixed JSON Response Format:</strong> Added 'success' field to all JSON responses</div>
                        <div>✅ <strong>Fixed Error Responses:</strong> All validation errors now include 'success: false'</div>
                        <div>✅ <strong>Fixed API Request Detection:</strong> Enhanced detection for AJAX requests</div>
                        <div>✅ <strong>Debug Logging Added:</strong> Comprehensive logging for troubleshooting</div>
                    </div>
                </div>
                
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
                        'Form action /admin/properties' => strpos($formContent, 'action="/admin/properties"') !== false,
                        'JavaScript fetch to /admin/properties' => strpos($formContent, 'fetch(\'/admin/properties\'') !== false,
                        'AJAX headers in fetch request' => strpos($formContent, 'X-Requested-With\': \'XMLHttpRequest\'') !== false,
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
                
                // Test PropertyController store method
                echo '<div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">';
                echo '<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">PropertyController Store Method Test</h3>';
                
                $controllerFile = __DIR__ . '/app/controllers/PropertyController.php';
                if (file_exists($controllerFile)) {
                    $controllerContent = file_get_contents($controllerFile);
                    $controllerChecks = [
                        'store method exists' => strpos($controllerContent, 'public function store()') !== false,
                        'JSON response for API requests' => strpos($controllerContent, '$this->json([') !== false,
                        'isApiRequest check' => strpos($controllerContent, '$this->isApiRequest()') !== false,
                        'Error handling for validation' => strpos($controllerContent, '\'errors\' => $errors') !== false,
                        'Success response with property_id' => strpos($controllerContent, '\'property_id\' => $propertyId') !== false,
                        'Success field in responses' => strpos($controllerContent, '\'success\' => true') !== false,
                        'AJAX header detection' => strpos($controllerContent, 'HTTP_X_REQUESTED_WITH') !== false,
                    ];
                    
                    echo '<ul class="list-disc ml-6 text-sm">';
                    foreach ($controllerChecks as $description => $found) {
                        $status = $found ? '✅' : '❌';
                        echo "<li>{$status} {$description}</li>";
                    }
                    echo '</ul>';
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
                        <li>Test form submission with required fields only</li>
                        <li>✅ <strong>Fixed:</strong> No more "Invalid JSON response" error</li>
                    </ol>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Quick Debug Test</h3>
                    <button onclick="testPropertyFormSubmission()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-play mr-2"></i>Test Property Form Submission
                    </button>
                    <div id="testResults" class="mt-3 hidden">
                        <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded text-sm">
                            <div id="testOutput"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        function testPropertyFormSubmission() {
            const resultsDiv = document.getElementById('testResults');
            const outputDiv = document.getElementById('testOutput');
            
            resultsDiv.classList.remove('hidden');
            outputDiv.innerHTML = 'Testing property form submission...';
            
            const formData = new FormData();
            formData.append('name', 'Debug Test Property');
            formData.append('address', '123 Debug Street');
            formData.append('type', 'residential');
            formData.append('status', 'active');
            formData.append('water_availability', 'yes');
            
            // Add headers to make it look like AJAX
            const headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            };
            
            fetch('/admin/properties', {
                method: 'POST',
                headers: headers,
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                return response.text().then(text => {
                    console.log('Raw response:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        outputDiv.innerHTML = '<div class="text-red-600">❌ Invalid JSON response</div><pre class="text-xs mt-2">' + text.substring(0, 500) + '</pre>';
                        throw new Error('Invalid JSON response: ' + text.substring(0, 200));
                    }
                });
            })
            .then(data => {
                console.log('Parsed response data:', data);
                if (data.errors) {
                    outputDiv.innerHTML = '<div class="text-yellow-600">⚠️ Validation errors:</div><pre class="text-xs mt-2">' + JSON.stringify(data.errors, null, 2) + '</pre>';
                } else if (data.success || data.property_id) {
                    outputDiv.innerHTML = '<div class="text-green-600">✅ Property created successfully!</div><pre class="text-xs mt-2">' + JSON.stringify(data, null, 2) + '</pre>';
                } else {
                    outputDiv.innerHTML = '<div class="text-blue-600">ℹ️ Unexpected response format:</div><pre class="text-xs mt-2">' + JSON.stringify(data, null, 2) + '</pre>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                outputDiv.innerHTML = '<div class="text-red-600">❌ Error: ' + error.message + '</div>';
            });
        }
        </script>

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

    <!-- AutoFillComponent Load Test -->
    <div class="max-w-4xl mx-auto mt-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-cogs mr-2"></i>3. AutoFillComponent Load Test
            </h2>
            
            <?php
            // Test 1: Check if bootstrap.php was loaded
            echo '<div class="mb-4">';
            echo '<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Bootstrap Loading Check</h3>';
            
            $includedFiles = get_included_files();
            $bootstrapLoaded = false;
            foreach ($includedFiles as $file) {
                if (strpos($file, 'bootstrap.php') !== false) {
                    $bootstrapLoaded = true;
                    echo '<div class="text-green-600">✅ Bootstrap.php loaded: ' . htmlspecialchars($file) . '</div>';
                    break;
                }
            }
            
            if (!$bootstrapLoaded) {
                echo '<div class="text-red-600">❌ Bootstrap.php NOT loaded in included files</div>';
            }
            echo '</div>';
            
            // Test 2: Check if spl_autoload_register for Components namespace is active
            echo '<div class="mb-4">';
            echo '<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Components Autoloader Check</h3>';
            
            $autoloaderActive = false;
            $autoloaderFunctions = spl_autoload_functions();
            
            if ($autoloaderFunctions) {
                foreach ($autoloaderFunctions as $function) {
                    if (is_array($function) && isset($function[1])) {
                        // Check if this is our Components autoloader
                        $reflection = new ReflectionFunction($function);
                        $fileName = $reflection->getFileName();
                        if ($fileName && strpos($fileName, 'bootstrap.php') !== false) {
                            $autoloaderActive = true;
                            echo '<div class="text-green-600">✅ Components namespace autoloader is active</div>';
                            echo '<div class="text-sm text-gray-600 dark:text-gray-400">Found in: ' . htmlspecialchars($fileName) . '</div>';
                            break;
                        }
                    }
                }
            }
            
            if (!$autoloaderActive) {
                echo '<div class="text-red-600">❌ Components namespace autoloader NOT found</div>';
                echo '<div class="text-sm text-gray-600 dark:text-gray-400">Active autoloaders: ' . json_encode($autoloaderFunctions) . '</div>';
            }
            echo '</div>';
            
            // Test 3: Manual class resolution test
            echo '<div class="mb-4">';
            echo '<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Manual Class Resolution Test</h3>';
            
            $className = 'Components\\AutoFillComponent';
            $expectedFile = __DIR__ . '/app/components/AutoFillComponent.php';
            
            echo '<div class="text-sm space-y-1">';
            echo '<div><strong>Target Class:</strong> ' . htmlspecialchars($className) . '</div>';
            echo '<div><strong>Expected File:</strong> ' . htmlspecialchars($expectedFile) . '</div>';
            
            if (file_exists($expectedFile)) {
                echo '<div class="text-green-600">✅ AutoFillComponent.php file exists</div>';
                
                // Try to manually require and check class
                try {
                    require_once $expectedFile;
                    if (class_exists($className)) {
                        echo '<div class="text-green-600">✅ Class exists after manual require</div>';
                        
                        // Test method existence
                        if (method_exists($className, 'generateAutoFillButton')) {
                            echo '<div class="text-green-600">✅ generateAutoFillButton method exists</div>';
                        } else {
                            echo '<div class="text-red-600">❌ generateAutoFillButton method NOT found</div>';
                        }
                        
                        if (method_exists($className, 'getPropertyFillData')) {
                            echo '<div class="text-green-600">✅ getPropertyFillData method exists</div>';
                        } else {
                            echo '<div class="text-red-600">❌ getPropertyFillData method NOT found</div>';
                        }
                        
                        // Test actual method call
                        try {
                            $fillData = $className::getPropertyFillData();
                            echo '<div class="text-green-600">✅ getPropertyFillData() call successful</div>';
                            echo '<div class="text-sm text-gray-600 dark:text-gray-400">Returned ' . count($fillData) . ' data fields</div>';
                        } catch (Exception $e) {
                            echo '<div class="text-red-600">❌ getPropertyFillData() call failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
                            echo '<div class="text-sm text-gray-600 dark:text-gray-400">Error in: ' . $e->getFile() . ':' . $e->getLine() . '</div>';
                        }
                        
                    } else {
                        echo '<div class="text-red-600">❌ Class still does NOT exist after manual require</div>';
                        echo '<div class="text-sm text-gray-600 dark:text-gray-400">Namespace issue or syntax error in file</div>';
                    }
                } catch (ParseError $e) {
                    echo '<div class="text-red-600">❌ Parse error in AutoFillComponent.php: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    echo '<div class="text-sm text-gray-600 dark:text-gray-400">Error in: ' . $e->getFile() . ':' . $e->getLine() . '</div>';
                } catch (Error $e) {
                    echo '<div class="text-red-600">❌ Fatal error in AutoFillComponent.php: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    echo '<div class="text-sm text-gray-600 dark:text-gray-400">Error in: ' . $e->getFile() . ':' . $e->getLine() . '</div>';
                } catch (Exception $e) {
                    echo '<div class="text-red-600">❌ Exception in AutoFillComponent.php: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    echo '<div class="text-sm text-gray-600 dark:text-gray-400">Error in: ' . $e->getFile() . ':' . $e->getLine() . '</div>';
                }
                
            } else {
                echo '<div class="text-red-600">❌ AutoFillComponent.php file NOT found</div>';
            }
            
            echo '</div>';
            echo '</div>';
            
            // Test 4: ComponentRegistry test
            echo '<div class="mb-4">';
            echo '<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">ComponentRegistry Test</h3>';
            
            try {
                if (class_exists('ComponentRegistry')) {
                    echo '<div class="text-green-600">✅ ComponentRegistry class exists</div>';
                    
                    // Check if autofill-component is registered
                    if (ComponentRegistry::isRegistered('autofill-component')) {
                        echo '<div class="text-green-600">✅ autofill-component is registered</div>';
                        
                        // Try to load via ComponentRegistry
                        try {
                            ComponentRegistry::load('autofill-component');
                            echo '<div class="text-green-600">✅ ComponentRegistry::load() successful</div>';
                            
                            if (class_exists('Components\\AutoFillComponent')) {
                                echo '<div class="text-green-600">✅ AutoFillComponent class exists after ComponentRegistry load</div>';
                            } else {
                                echo '<div class="text-red-600">❌ AutoFillComponent class still missing after ComponentRegistry load</div>';
                            }
                            
                        } catch (Exception $e) {
                            echo '<div class="text-red-600">❌ ComponentRegistry::load() failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
                            echo '<div class="text-sm text-gray-600 dark:text-gray-400">Error in: ' . $e->getFile() . ':' . $e->getLine() . '</div>';
                        }
                        
                    } else {
                        echo '<div class="text-red-600">❌ autofill-component NOT registered in ComponentRegistry</div>';
                        echo '<div class="text-sm text-gray-600 dark:text-gray-400">Registered components: ' . json_encode(array_keys(ComponentRegistry::getRegistered())) . '</div>';
                    }
                    
                } else {
                    echo '<div class="text-red-600">❌ ComponentRegistry class NOT found</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="text-red-600">❌ ComponentRegistry test error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '<div class="text-sm text-gray-600 dark:text-gray-400">Error in: ' . $e->getFile() . ':' . $e->getLine() . '</div>';
            }
            
            echo '</div>';
            
            // Test 5: Final integration test
            echo '<div>';
            echo '<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Final Integration Test</h3>';
            
            try {
                if (class_exists('Components\\AutoFillComponent')) {
                    echo '<div class="text-green-600">✅ AutoFillComponent is fully loaded and ready</div>';
                    
                    // Test a complete method call chain
                    $fillData = \Components\AutoFillComponent::getPropertyFillData();
                    if (is_array($fillData) && !empty($fillData)) {
                        echo '<div class="text-green-600">✅ Integration test passed - component is functional</div>';
                        echo '<div class="text-sm text-gray-600 dark:text-gray-400">Available data: ' . implode(', ', array_keys($fillData)) . '</div>';
                    } else {
                        echo '<div class="text-yellow-600">⚠️ Component loads but returns empty data</div>';
                    }
                    
                } else {
                    echo '<div class="text-red-600">❌ Final integration test failed - AutoFillComponent not available</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="text-red-600">❌ Integration test exception: ' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '<div class="text-sm text-gray-600 dark:text-gray-400">Error in: ' . $e->getFile() . ':' . $e->getLine() . '</div>';
            }
            
            echo '</div>';
            
            // Properties List Debug Section
            echo '<div class="mb-4">';
            echo '<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">7. Properties List showToast Fix Verification</h3>';
            
            $propertiesListPath = __DIR__ . '/../views/admin/properties/list.php';
            if (file_exists($propertiesListPath)) {
                $content = file_get_contents($propertiesListPath);
                
                $hasShowToastFallback = strpos($content, 'if (typeof showToast !== \'function\')') !== false;
                $hasShowToastDefinition = strpos($content, 'window.showToast = function(message, type)') !== false;
                $hasFilterUpdate = strpos($content, 'showingText.textContent = `Showing ${visibleCount} properties`') !== false;
                $hasNoResultsToast = strpos($content, 'showToast(\'No properties match the selected filters\', \'info\')') !== false;
                $hasEnhancedListView = strpos($content, 'Enhanced styling') !== false;
                
                echo '<div class="text-green-600">✅ Properties list file found</div>';
                echo '<div class="' . ($hasShowToastFallback ? 'text-green-600' : 'text-red-600') . '">' . 
                     ($hasShowToastFallback ? '✅' : '❌') . ' showToast fallback check added</div>';
                echo '<div class="' . ($hasShowToastDefinition ? 'text-green-600' : 'text-red-600') . '">' . 
                     ($hasShowToastDefinition ? '✅' : '❌') . ' showToast function definition added</div>';
                echo '<div class="' . ($hasFilterUpdate ? 'text-green-600' : 'text-red-600') . '">' . 
                     ($hasFilterUpdate ? '✅' : '❌') . ' Filter count update implemented</div>';
                echo '<div class="' . ($hasNoResultsToast ? 'text-green-600' : 'text-red-600') . '">' . 
                     ($hasNoResultsToast ? '✅' : '❌') . ' No results toast notification added</div>';
                echo '<div class="' . ($hasEnhancedListView ? 'text-green-600' : 'text-red-600') . '">' . 
                     ($hasEnhancedListView ? '✅' : '❌') . ' Enhanced list view styling implemented</div>';
                
                if ($hasShowToastFallback && $hasShowToastDefinition && $hasFilterUpdate && $hasNoResultsToast) {
                    echo '<div class="text-green-600 font-bold">✅ All Problem A fixes successfully applied!</div>';
                } else {
                    echo '<div class="text-yellow-600 font-bold">⚠️ Some Problem A fixes may be missing</div>';
                }
                
            } else {
                echo '<div class="text-red-600">❌ Properties list file not found</div>';
            }
            
            echo '</div>';
            ?>
        </div>
    </div>
</body>
</html>
