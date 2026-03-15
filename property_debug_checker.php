<?php
/**
 * Property Debug Checker - Comprehensive Debugging Tool
 * 
 * This script debugs the property creation and display flow
 * to identify why properties aren't showing up properly.
 */

// Initialize framework
require_once __DIR__ . '/config/bootstrap.php';

// Initialize database connection
$database = new \App\Config\Database();
$db = $database->getConnection();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Debug Checker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/fontawesome.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
            <i class="fas fa-bug mr-2"></i>Property Debug Checker
        </h1>

        <!-- Debug Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Debug Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded">
                    <div class="text-sm text-blue-600 dark:text-blue-300">Database Connection</div>
                    <div class="text-lg font-semibold text-blue-900 dark:text-blue-100" id="db-status">Testing...</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900 p-4 rounded">
                    <div class="text-sm text-green-600 dark:text-green-300">Admin Session</div>
                    <div class="text-lg font-semibold text-green-900 dark:text-green-100" id="session-status">Testing...</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded">
                    <div class="text-sm text-purple-600 dark:text-purple-300">Properties Count</div>
                    <div class="text-lg font-semibold text-purple-900 dark:text-purple-100" id="properties-count">Testing...</div>
                </div>
            </div>
        </div>

        <!-- 1. Database Connection Test -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-database mr-2"></i>1. Database Connection Test
            </h2>
            <?php
            $dbStatus = 'Connected';
            $dbError = '';
            try {
                if (!$db) {
                    throw new Exception('Database connection failed');
                }
                // Test query
                $stmt = $db->query("SELECT 1 as test");
                $result = $stmt->fetch();
                if (!$result || $result['test'] != 1) {
                    throw new Exception('Database query failed');
                }
            } catch (Exception $e) {
                $dbStatus = 'Failed: ' . $e->getMessage();
                $dbError = $e->getMessage();
            }
            ?>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Connection Status:</span>
                    <span class="<?php echo $dbStatus === 'Connected' ? 'text-green-600' : 'text-red-600'; ?> font-semibold">
                        <?php echo $dbStatus; ?>
                    </span>
                </div>
                <?php if ($dbError): ?>
                    <div class="bg-red-100 dark:bg-red-900 p-3 rounded text-red-800 dark:text-red-200">
                        <strong>Error:</strong> <?php echo $dbError; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 2. Admin Session Test -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-user-shield mr-2"></i>2. Admin Session Test
            </h2>
            <?php
            $sessionStatus = 'Not Logged In';
            $adminId = null;
            $adminEmail = '';
            
            session_start();
            if (isset($_SESSION['admin_id'])) {
                $adminId = $_SESSION['admin_id'];
                $sessionStatus = 'Logged In';
                
                // Get admin details
                try {
                    $stmt = $db->prepare("SELECT id, name, email, role FROM admins WHERE id = ? AND deleted_at IS NULL");
                    $stmt->execute([$adminId]);
                    $admin = $stmt->fetch();
                    if ($admin) {
                        $adminEmail = $admin['email'];
                    } else {
                        $sessionStatus = 'Invalid Admin ID';
                    }
                } catch (Exception $e) {
                    $sessionStatus = 'Database Error: ' . $e->getMessage();
                }
            } else {
                // Check all session data
                $sessionStatus = 'No admin session found. Session data: ' . json_encode($_SESSION);
            }
            ?>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Session Status:</span>
                    <span class="<?php echo strpos($sessionStatus, 'Logged') !== false ? 'text-green-600' : 'text-red-600'; ?> font-semibold">
                        <?php echo $sessionStatus; ?>
                    </span>
                </div>
                <?php if ($adminId): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Admin ID:</span>
                        <span class="text-gray-900 dark:text-white"><?php echo $adminId; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Admin Email:</span>
                        <span class="text-gray-900 dark:text-white"><?php echo $adminEmail; ?></span>
                    </div>
                <?php endif; ?>
                <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded">
                    <strong>Full Session Data:</strong>
                    <pre class="text-xs mt-2"><?php echo json_encode($_SESSION, JSON_PRETTY_PRINT); ?></pre>
                </div>
            </div>
        </div>

        <!-- 3. Properties Table Test -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-building mr-2"></i>3. Properties Table Test
            </h2>
            <?php
            $totalProperties = 0;
            $adminProperties = 0;
            $recentProperties = [];
            $tableError = '';
            
            try {
                // Count all properties
                $stmt = $db->query("SELECT COUNT(*) as count FROM properties WHERE deleted_at IS NULL");
                $totalProperties = $stmt->fetchColumn();
                
                // Count properties for current admin (if logged in)
                if ($adminId) {
                    $stmt = $db->prepare("SELECT COUNT(*) as count FROM properties WHERE admin_id = ? AND deleted_at IS NULL");
                    $stmt->execute([$adminId]);
                    $adminProperties = $stmt->fetchColumn();
                    
                    // Get recent properties for this admin
                    $stmt = $db->prepare("SELECT * FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 5");
                    $stmt->execute([$adminId]);
                    $recentProperties = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    // Get recent properties from all admins
                    $stmt = $db->query("SELECT * FROM properties WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5");
                    $recentProperties = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (Exception $e) {
                $tableError = $e->getMessage();
            }
            ?>
            <div class="space-y-4">
                <?php if ($tableError): ?>
                    <div class="bg-red-100 dark:bg-red-900 p-3 rounded text-red-800 dark:text-red-200">
                        <strong>Error:</strong> <?php echo $tableError; ?>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Properties (All Admins)</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $totalProperties; ?></div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Properties for Current Admin</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $adminProperties; ?></div>
                        </div>
                    </div>
                    
                    <?php if (!empty($recentProperties)): ?>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Recent Properties:</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Admin ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Created</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <?php foreach ($recentProperties as $property): ?>
                                        <tr>
                                            <td class="px-4 py-2 text-sm"><?php echo $property['id']; ?></td>
                                            <td class="px-4 py-2 text-sm"><?php echo htmlspecialchars($property['name']); ?></td>
                                            <td class="px-4 py-2 text-sm"><?php echo $property['admin_id']; ?></td>
                                            <td class="px-4 py-2 text-sm"><?php echo $property['created_at']; ?></td>
                                            <td class="px-4 py-2 text-sm"><?php echo $property['status']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded text-yellow-800 dark:text-yellow-200">
                            No properties found in the database.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- 4. Controller Query Test -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-code mr-2"></i>4. Controller Query Test
            </h2>
            <?php
            $controllerQueryError = '';
            $controllerResults = [];
            
            if ($adminId) {
                try {
                    // Simulate the exact query from PropertyController::index()
                    $sql = "
                        SELECT 
                            p.*,
                            COUNT(u.id) AS unit_count,
                            SUM(CASE WHEN u.status = 'occupied' THEN 1 ELSE 0 END) AS occupied_units
                        FROM properties p
                        LEFT JOIN units u ON u.property_id = p.id
                        WHERE p.admin_id = :admin_id AND p.deleted_at IS NULL
                        GROUP BY p.id ORDER BY p.created_at DESC
                    ";
                    
                    $stmt = $db->prepare($sql);
                    $stmt->execute(['admin_id' => $adminId]);
                    $controllerResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                } catch (Exception $e) {
                    $controllerQueryError = $e->getMessage();
                }
            } else {
                $controllerQueryError = 'No admin session - cannot test controller query';
            }
            ?>
            <div class="space-y-4">
                <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded">
                    <strong>Query Used:</strong>
                    <pre class="text-xs mt-2 whitespace-pre-wrap"><?php echo htmlspecialchars($sql ?? 'No query executed'); ?></pre>
                </div>
                
                <?php if ($controllerQueryError): ?>
                    <div class="bg-red-100 dark:bg-red-900 p-3 rounded text-red-800 dark:text-red-200">
                        <strong>Query Error:</strong> <?php echo $controllerQueryError; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded text-green-800 dark:text-green-200">
                        <strong>Query Success:</strong> Found <?php echo count($controllerResults); ?> properties
                    </div>
                    
                    <?php if (!empty($controllerResults)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Units</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Occupied</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <?php foreach ($controllerResults as $property): ?>
                                        <tr>
                                            <td class="px-4 py-2 text-sm"><?php echo $property['id']; ?></td>
                                            <td class="px-4 py-2 text-sm"><?php echo htmlspecialchars($property['name']); ?></td>
                                            <td class="px-4 py-2 text-sm"><?php echo $property['unit_count']; ?></td>
                                            <td class="px-4 py-2 text-sm"><?php echo $property['occupied_units']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- 5. Route Test -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-route mr-2"></i>5. Route Configuration Test
            </h2>
            <?php
            $routesFile = __DIR__ . '/routes/web.php';
            $routesExist = file_exists($routesFile);
            $routesContent = '';
            $propertyRoutes = [];
            
            if ($routesExist) {
                $routesContent = file_get_contents($routesFile);
                // Extract property routes
                preg_match_all("/'(GET|POST) \/admin\/properties[^']*'\s*=>\s*'[^']*'/", $routesContent, $matches);
                $propertyRoutes = $matches[0];
            }
            ?>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Routes File Exists:</span>
                    <span class="<?php echo $routesExist ? 'text-green-600' : 'text-red-600'; ?> font-semibold">
                        <?php echo $routesExist ? 'Yes' : 'No'; ?>
                    </span>
                </div>
                
                <?php if ($routesExist): ?>
                    <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded">
                        <strong>Property Routes Found:</strong>
                        <ul class="text-sm mt-2 space-y-1">
                            <?php foreach ($propertyRoutes as $route): ?>
                                <li><code><?php echo htmlspecialchars($route); ?></code></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 6. Test Property Creation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-plus-circle mr-2"></i>6. Test Property Creation
            </h2>
            <?php
            $testPropertyCreated = false;
            $testPropertyError = '';
            
            if ($adminId && isset($_POST['create_test_property'])) {
                try {
                    $testData = [
                        'admin_id' => $adminId,
                        'name' => 'Test Property ' . date('Y-m-d H:i:s'),
                        'address' => '123 Test Street, Test City',
                        'type' => 'residential',
                        'description' => 'This is a test property created by the debug tool',
                        'status' => 'active',
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $stmt = $db->prepare("INSERT INTO properties (admin_id, name, address, type, description, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $result = $stmt->execute([
                        $testData['admin_id'],
                        $testData['name'],
                        $testData['address'],
                        $testData['type'],
                        $testData['description'],
                        $testData['status'],
                        $testData['created_at']
                    ]);
                    
                    if ($result) {
                        $testPropertyCreated = true;
                    } else {
                        $testPropertyError = 'Failed to insert test property';
                    }
                } catch (Exception $e) {
                    $testPropertyError = $e->getMessage();
                }
            }
            ?>
            
            <div class="space-y-4">
                <?php if ($testPropertyCreated): ?>
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded text-green-800 dark:text-green-200">
                        <strong>Success!</strong> Test property created successfully. Refresh the page to see it in the properties list.
                    </div>
                <?php elseif ($testPropertyError): ?>
                    <div class="bg-red-100 dark:bg-red-900 p-3 rounded text-red-800 dark:text-red-200">
                        <strong>Error:</strong> <?php echo $testPropertyError; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($adminId): ?>
                    <form method="POST" class="inline">
                        <button type="submit" name="create_test_property" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create Test Property
                        </button>
                    </form>
                <?php else: ?>
                    <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded text-yellow-800 dark:text-yellow-200">
                        You must be logged in as an admin to create a test property.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 7. Error Log Check -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-file-alt mr-2"></i>7. Recent Error Log Entries
            </h2>
            <?php
            $errorLogPath = ini_get('error_log');
            $recentErrors = [];
            
            if ($errorLogPath && file_exists($errorLogPath)) {
                $errorLogContent = file_get_contents($errorLogPath);
                $errorLines = explode("\n", $errorLogContent);
                $recentErrors = array_slice($errorLines, -10); // Last 10 lines
            }
            ?>
            <div class="space-y-2">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Error Log Path: <?php echo $errorLogPath ?: 'Not configured'; ?>
                </div>
                
                <?php if (!empty($recentErrors)): ?>
                    <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded max-h-40 overflow-y-auto">
                        <?php foreach (array_reverse($recentErrors) as $error): ?>
                            <?php if (trim($error)): ?>
                                <div class="text-xs font-mono mb-1"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded text-gray-600 dark:text-gray-400">
                        No recent error log entries found.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-tools mr-2"></i>Quick Actions
            </h2>
            <div class="flex flex-wrap gap-4">
                <a href="/admin/login" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>Go to Admin Login
                </a>
                <a href="/admin/properties" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                    <i class="fas fa-building mr-2"></i>Go to Properties List
                </a>
                <a href="/admin/properties/create" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Go to Create Property
                </a>
                <button onclick="window.location.reload()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                    <i class="fas fa-sync mr-2"></i>Refresh Debug Info
                </button>
            </div>
        </div>
    </div>

    <script>
        // Update status indicators
        document.getElementById('db-status').textContent = '<?php echo $dbStatus === "Connected" ? "✅ Connected" : "❌ Failed"; ?>';
        document.getElementById('session-status').textContent = '<?php echo strpos($sessionStatus, "Logged") !== false ? "✅ " . $sessionStatus : "❌ " . $sessionStatus; ?>';
        document.getElementById('properties-count').textContent = '<?php echo $adminProperties . " (Admin) / " . $totalProperties . " (Total)"; ?>';
    </script>
</body>
</html>
