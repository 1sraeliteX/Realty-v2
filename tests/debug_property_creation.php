<?php
require_once __DIR__ . '/config/bootstrap.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Creation Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
            <i class="fas fa-bug mr-2"></i>Property Creation Debug
        </h1>

        <?php
        // Debug information
        $debugInfo = [];
        
        // Check admin session
        $debugInfo['admin_session'] = [
            'logged_in' => isset($_SESSION['admin_id']),
            'admin_id' => $_SESSION['admin_id'] ?? null,
            'admin_email' => $_SESSION['admin_email'] ?? null,
            'session_data' => $_SESSION
        ];
        
        // Check database connection
        try {
            $db = \Config\DatabaseFactory::create();
            $debugInfo['database'] = [
                'status' => 'connected',
                'type' => get_class($db)
            ];
            
            // Test properties table
            $stmt = $db->query("SELECT COUNT(*) as count FROM properties WHERE admin_id = ?", [$_SESSION['admin_id'] ?? 0]);
            $result = $stmt ? $stmt->fetch() : ['count' => 0];
            $debugInfo['properties_count'] = $result['count'];
            
            // Test recent properties
            $stmt = $db->query("SELECT * FROM properties WHERE admin_id = ? ORDER BY created_at DESC LIMIT 5", [$_SESSION['admin_id'] ?? 0]);
            $debugInfo['recent_properties'] = $stmt ? $stmt->fetchAll() : [];
            
        } catch (Exception $e) {
            $debugInfo['database'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
        
        // Check routes
        $routesFile = __DIR__ . '/routes/web.php';
        $debugInfo['routes'] = [
            'file_exists' => file_exists($routesFile),
            'routes_loaded' => false
        ];
        
        if (file_exists($routesFile)) {
            $routes = include $routesFile;
            $debugInfo['routes']['routes_loaded'] = true;
            $debugInfo['routes']['admin_properties_route'] = isset($routes['POST /admin/properties']);
            $debugInfo['routes']['total_routes'] = count($routes);
        }
        
        // Check controller
        $controllerFile = __DIR__ . '/app/controllers/PropertyController.php';
        $debugInfo['controller'] = [
            'file_exists' => file_exists($controllerFile),
            'readable' => is_readable($controllerFile),
            'store_method_exists' => false
        ];
        
        if (file_exists($controllerFile)) {
            $content = file_get_contents($controllerFile);
            $debugInfo['controller']['store_method_exists'] = strpos($content, 'public function store()') !== false;
            $debugInfo['controller']['json_method_exists'] = strpos($content, 'protected function json(') !== false;
        }
        
        // Display debug information
        echo '<div class="space-y-6">';
        
        foreach ($debugInfo as $section => $info) {
            echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">';
            echo '<h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">';
            echo '<i class="fas fa-info-circle mr-2"></i>' . ucfirst(str_replace('_', ' ', $section));
            echo '</h2>';
            
            echo '<dl class="space-y-2">';
            foreach ($info as $key => $value) {
                if (is_bool($value)) {
                    $status = $value ? '✅ Yes' : '❌ No';
                    $color = $value ? 'text-green-600' : 'text-red-600';
                    echo '<dt class="text-sm font-medium text-gray-600 dark:text-gray-400">' . ucfirst(str_replace('_', ' ', $key)) . '</dt>';
                    echo '<dd class="text-sm ' . $color . '">' . $status . '</dd>';
                } elseif (is_array($value)) {
                    echo '<dt class="text-sm font-medium text-gray-600 dark:text-gray-400">' . ucfirst(str_replace('_', ' ', $key)) . '</dt>';
                    echo '<dd class="text-sm text-gray-900 dark:text-white">';
                    echo '<pre class="bg-gray-100 dark:bg-gray-700 p-2 rounded text-xs overflow-x-auto">' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</pre>';
                    echo '</dd>';
                } else {
                    echo '<dt class="text-sm font-medium text-gray-600 dark:text-gray-400">' . ucfirst(str_replace('_', ' ', $key)) . '</dt>';
                    echo '<dd class="text-sm text-gray-900 dark:text-white">' . htmlspecialchars(print_r($value, true)) . '</dd>';
                }
            }
            echo '</dl>';
            echo '</div>';
        }
        
        echo '</div>';
        ?>

        <!-- Test Property Creation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-test mr-2"></i>Test Property Creation
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Test Property Name</label>
                    <input type="text" id="testPropertyName" value="Debug Test Property" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Test Property Address</label>
                    <input type="text" id="testPropertyAddress" value="123 Debug Street, Test City" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Test Property Type</label>
                    <select id="testPropertyType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="residential">Residential</option>
                        <option value="commercial">Commercial</option>
                        <option value="mixed">Mixed</option>
                    </select>
                </div>
                
                <button onclick="testPropertyCreation()" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium">
                    <i class="fas fa-play mr-2"></i>Test Property Creation
                </button>
            </div>
            
            <div id="testResults" class="mt-6 hidden">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Test Results:</h3>
                <div id="testOutput" class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-sm font-mono"></div>
            </div>
        </div>
        
        <!-- Error Log Display -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-file-alt mr-2"></i>Recent Error Logs
            </h2>
            
            <?php
            // Try to read recent error logs
            $errorLog = [];
            $logFiles = [
                __DIR__ . '/logs/error.log',
                __DIR__ . '/storage/logs/error.log',
                'C:/xampp/php/logs/php_error_log'
            ];
            
            foreach ($logFiles as $logFile) {
                if (file_exists($logFile) && is_readable($logFile)) {
                    $lines = file($logFile);
                    $errorLog[$logFile] = array_slice($lines, -20); // Last 20 lines
                }
            }
            
            if (!empty($errorLog)) {
                foreach ($errorLog as $logFile => $lines) {
                    echo '<div class="mb-4">';
                    echo '<h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">' . htmlspecialchars($logFile) . '</h4>';
                    echo '<pre class="bg-red-50 dark:bg-red-900/20 p-3 rounded text-xs overflow-x-auto max-h-40">';
                    foreach ($lines as $line) {
                        echo htmlspecialchars($line);
                    }
                    echo '</pre>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-sm text-gray-600 dark:text-gray-400">No error logs found or accessible</p>';
            }
            ?>
        </div>
    </div>

    <script>
        function testPropertyCreation() {
            const resultsDiv = document.getElementById('testResults');
            const outputDiv = document.getElementById('testOutput');
            
            resultsDiv.classList.remove('hidden');
            outputDiv.innerHTML = 'Testing property creation...';
            
            const formData = new FormData();
            formData.append('name', document.getElementById('testPropertyName').value);
            formData.append('address', document.getElementById('testPropertyAddress').value);
            formData.append('type', document.getElementById('testPropertyType').value);
            formData.append('status', 'active');
            formData.append('water_availability', 'yes');
            
            fetch('/admin/properties', {
                method: 'POST',
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
                        throw new Error('Invalid JSON response: ' + text.substring(0, 200));
                    }
                });
            })
            .then(data => {
                console.log('Parsed response data:', data);
                outputDiv.innerHTML = '<div class="text-green-600">✅ Success!</div><pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                console.error('Error:', error);
                outputDiv.innerHTML = '<div class="text-red-600">❌ Error: ' + error.message + '</div>';
            });
        }
        
        // Console debug info
        console.log('Property Creation Debug Info:');
        console.log('Admin Session:', <?php echo json_encode($debugInfo['admin_session']); ?>);
        console.log('Database:', <?php echo json_encode($debugInfo['database']); ?>);
        console.log('Routes:', <?php echo json_encode($debugInfo['routes']); ?>);
        console.log('Controller:', <?php echo json_encode($debugInfo['controller']); ?>);
    </script>
</body>
</html>
