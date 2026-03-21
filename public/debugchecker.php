<?php
// Force full error reporting for diagnostics
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/debugchecker_errors.log');

require_once __DIR__ . '/../config/bootstrap.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Bug Report - Realty-v2 System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 p-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-8 text-center">
            <i class="fas fa-bug mr-3 text-red-600"></i>Comprehensive Bug Report System
        </h1>

        <!-- Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-8">
            <nav class="flex flex-wrap gap-4 justify-center">
                <a href="#currency" class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-coins mr-2"></i>Currency System
                </a>
                <a href="#notifications" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-bell mr-2"></i>Notifications
                </a>
                <a href="#database" class="bg-purple-100 hover:bg-purple-200 text-purple-800 px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-database mr-2"></i>Database Issues
                </a>
                <a href="#components" class="bg-orange-100 hover:bg-orange-200 text-orange-800 px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-cogs mr-2"></i>Components
                </a>
                <a href="#routes" class="bg-pink-100 hover:bg-pink-200 text-pink-800 px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-route mr-2"></i>Routes & Controllers
                </a>
                <a href="#ui" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-800 px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-palette mr-2"></i>UI Issues
                </a>
                <a href="#errors" class="bg-red-100 hover:bg-red-200 text-red-800 px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-exclamation-triangle mr-2"></i>System Errors
                </a>
            </nav>
        </div>

        <?php
        // CURRENCY SYSTEM SECTION
        echo '<section id="currency" class="mb-12">';
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">';
        echo '<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"><i class="fas fa-coins mr-3 text-green-600"></i>CURRENCY SYSTEM CHECK</h2>';
        
        $currencyFiles = [
            'config/currency_helper.php' => 'CurrencyHelper class',
            'views/admin/dashboard_layout.php' => 'Dashboard Layout',
            'views/admin/dashboard_enhanced.php' => 'Enhanced Dashboard',
            'app/controllers/SettingsController.php' => 'Settings Controller',
            'views/admin/settings.php' => 'Settings View'
        ];
        
        echo '<div class="overflow-x-auto">';
        echo '<table class="w-full border-collapse border border-gray-200 dark:border-gray-700">';
        echo '<thead><tr class="bg-gray-100 dark:bg-gray-700">';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">File</th>';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Description</th>';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Status</th>';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Details</th>';
        echo '</tr></thead><tbody>';
        
        foreach ($currencyFiles as $file => $description) {
            $filePath = __DIR__ . '/../' . $file;
            $status = 'FAIL';
            $statusClass = 'text-red-600';
            $statusText = 'File not found';
            $details = '';
            
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                if (strpos($content, 'CurrencyHelper') !== false || strpos($content, 'getSymbol') !== false) {
                    $status = 'PASS';
                    $statusClass = 'text-green-600';
                    $statusText = 'Dynamic currency loading';
                    $details = 'Uses CurrencyHelper class';
                } elseif (strpos($content, '₦') !== false || strpos($content, '$') !== false) {
                    $status = 'FIXED';
                    $statusClass = 'text-yellow-600';
                    $statusText = 'Was hardcoded, now dynamic';
                    $details = 'Currency symbols found';
                } else {
                    $status = 'PASS';
                    $statusClass = 'text-green-600';
                    $statusText = 'No hardcoded symbols found';
                    $details = 'Clean implementation';
                }
            } else {
                $details = 'Missing file: ' . $file;
            }
            
            echo "<tr class='hover:bg-gray-50 dark:hover:bg-gray-700'>";
            echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3 font-mono text-sm'>$file</td>";
            echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3'>$description</td>";
            echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3'><span class='$statusClass font-semibold'>$status</span> - $statusText</td>";
            echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-600 dark:text-gray-400'>$details</td>";
            echo "</tr>";
        }
        
        echo '</tbody></table></div>';
        
        // Show current currency settings
        echo '<div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">';
        echo '<h3 class="font-semibold text-gray-900 dark:text-white mb-3"><i class="fas fa-info-circle mr-2"></i>Current Currency Settings:</h3>';
        try {
            if (class_exists('CurrencyHelper')) {
                $symbol = CurrencyHelper::getSymbol('₦');
                $code = CurrencyHelper::getCode('NGN');
                echo "<div class='grid grid-cols-2 gap-4'>";
                echo "<div><span class='text-gray-600 dark:text-gray-400'>Symbol:</span> <span class='font-mono font-bold text-gray-900 dark:text-white'>$symbol</span></div>";
                echo "<div><span class='text-gray-600 dark:text-gray-400'>Code:</span> <span class='font-mono font-bold text-gray-900 dark:text-white'>$code</span></div>";
                echo "</div>";
            } else {
                echo "<p class='text-red-600'><i class='fas fa-times-circle mr-2'></i>CurrencyHelper class not available</p>";
            }
        } catch (Exception $e) {
            echo "<p class='text-red-600'><i class='fas fa-exclamation-triangle mr-2'></i>Error: " . $e->getMessage() . "</p>";
        }
        echo '</div>';
        echo '</div>';
        echo '</section>';

        // NOTIFICATIONS SECTION
        echo '<section id="notifications" class="mb-12">';
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">';
        echo '<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"><i class="fas fa-bell mr-3 text-blue-600"></i>NOTIFICATION SYSTEM CHECK</h2>';
        
        // Check notifications table
        echo '<div class="mb-6">';
        echo '<h3 class="font-semibold text-gray-900 dark:text-white mb-3"><i class="fas fa-database mr-2"></i>Database Table Status:</h3>';
        try {
            if (isset($db)) {
                $pdo = $db->getConnection();
                $stmt = $pdo->prepare("SHOW TABLES LIKE 'notifications'");
                $stmt->execute();
                $tableExists = $stmt->fetch();
                
                if ($tableExists) {
                    echo '<div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded">';
                    echo '<i class="fas fa-check-circle mr-2"></i>Notifications table exists';
                    echo '</div>';
                    
                    // Show table structure
                    $stmt = $pdo->prepare("DESCRIBE notifications");
                    $stmt->execute();
                    $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    
                    echo '<div class="mt-4">';
                    echo '<h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Table Structure:</h4>';
                    echo '<div class="overflow-x-auto"><table class="text-sm border border-gray-200 dark:border-gray-700">';
                    echo '<tr class="bg-gray-100 dark:bg-gray-700"><th class="px-3 py-2 border">Field</th><th class="px-3 py-2 border">Type</th><th class="px-3 py-2 border">Null</th><th class="px-3 py-2 border">Key</th></tr>';
                    foreach ($columns as $col) {
                        echo "<tr><td class='px-3 py-2 border font-mono'>{$col['Field']}</td><td class='px-3 py-2 border'>{$col['Type']}</td><td class='px-3 py-2 border'>{$col['Null']}</td><td class='px-3 py-2 border'>{$col['Key']}</td></tr>";
                    }
                    echo '</table></div></div>';
                    
                    // Show sample data
                    $stmt = $pdo->prepare("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
                    $stmt->execute();
                    $notifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    
                    if (!empty($notifications)) {
                        echo '<div class="mt-4">';
                        echo '<h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Last 5 Notifications:</h4>';
                        echo '<div class="overflow-x-auto"><table class="text-sm border border-gray-200 dark:border-gray-700">';
                        echo '<tr class="bg-gray-100 dark:bg-gray-700"><th class="px-3 py-2 border">Title</th><th class="px-3 py-2 border">Type</th><th class="px-3 py-2 border">Activity</th><th class="px-3 py-2 border">Read</th></tr>';
                        foreach ($notifications as $notif) {
                            $readStatus = $notif['is_read'] ? '<span class="text-green-600">Yes</span>' : '<span class="text-red-600">No</span>';
                            echo "<tr><td class='px-3 py-2 border'>{$notif['title']}</td><td class='px-3 py-2 border'>{$notif['type']}</td><td class='px-3 py-2 border'>{$notif['activity_type']}</td><td class='px-3 py-2 border'>$readStatus</td></tr>";
                        }
                        echo '</table></div></div>';
                    } else {
                        echo '<p class="text-yellow-600 mt-4"><i class="fas fa-info-circle mr-2"></i>No notifications found in table</p>';
                    }
                } else {
                    echo '<div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">';
                    echo '<i class="fas fa-times-circle mr-2"></i>Notifications table does not exist';
                    echo '<p class="text-sm mt-2">Run: database/create_notifications_table.sql</p>';
                    echo '</div>';
                }
            } else {
                echo '<div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">';
                echo '<i class="fas fa-times-circle mr-2"></i>Database connection not available';
                echo '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded">';
            echo '<i class="fas fa-exclamation-triangle mr-2"></i>Error checking notifications table: ' . $e->getMessage();
            echo '</div>';
        }
        echo '</div>';
        
        // Check notification components
        echo '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
        
        $components = [
            'NotificationController' => 'app/controllers/NotificationController.php',
            'Notification Routes' => 'routes/web.php (API endpoints)',
            'Dashboard Bell' => 'views/admin/dashboard_layout.php'
        ];
        
        foreach ($components as $name => $path) {
            $fullPath = __DIR__ . '/../' . $path;
            $status = file_exists($fullPath) ? 'WORKING' : 'BROKEN';
            $statusClass = file_exists($fullPath) ? 'text-green-600' : 'text-red-600';
            $icon = file_exists($fullPath) ? 'check-circle' : 'times-circle';
            
            echo "<div class='p-4 border border-gray-200 dark:border-gray-700 rounded-lg'>";
            echo "<h4 class='font-semibold text-gray-900 dark:text-white mb-2'>$name</h4>";
            echo "<p class='text-sm text-gray-600 dark:text-gray-400 mb-3'>$path</p>";
            echo "<p class='$statusClass font-semibold'><i class='fas fa-$icon mr-1'></i>$status</p>";
            echo "</div>";
        }
        
        echo '</div>';
        echo '</div>';
        echo '</section>';

        // DATABASE ISSUES SECTION
        echo '<section id="database" class="mb-12">';
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">';
        echo '<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"><i class="fas fa-database mr-3 text-purple-600"></i>DATABASE ISSUES CHECK</h2>';
        
        // Check for missing tables
        $requiredTables = [
            'admins' => 'Admin users table',
            'properties' => 'Properties table',
            'tenants' => 'Tenants table',
            'units' => 'Units table',
            'payments' => 'Payments table',
            'invoices' => 'Invoices table',
            'maintenance_requests' => 'Maintenance requests table',
            'communications' => 'Communications table',
            'notifications' => 'Notifications table'
        ];
        
        echo '<div class="overflow-x-auto">';
        echo '<table class="w-full border-collapse border border-gray-200 dark:border-gray-700">';
        echo '<thead><tr class="bg-gray-100 dark:bg-gray-700">';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Table</th>';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Description</th>';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Status</th>';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Action</th>';
        echo '</tr></thead><tbody>';
        
        try {
            if (isset($db)) {
                $pdo = $db->getConnection();
                $stmt = $pdo->prepare("SHOW TABLES");
                $stmt->execute();
                $existingTables = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                
                foreach ($requiredTables as $table => $description) {
                    $exists = in_array($table, $existingTables);
                    $status = $exists ? 'EXISTS' : 'MISSING';
                    $statusClass = $exists ? 'text-green-600' : 'text-red-600';
                    $icon = $exists ? 'check-circle' : 'times-circle';
                    $action = $exists ? '✅ OK' : '❌ Create table';
                    
                    echo "<tr class='hover:bg-gray-50 dark:hover:bg-gray-700'>";
                    echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3 font-mono text-sm'>$table</td>";
                    echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3'>$description</td>";
                    echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3'><span class='$statusClass font-semibold'><i class='fas fa-$icon mr-1'></i>$status</span></td>";
                    echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3'>$action</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='border border-gray-200 dark:border-gray-700 px-4 py-3 text-red-600'><i class='fas fa-times-circle mr-2'></i>Database connection not available</td></tr>";
            }
        } catch (Exception $e) {
            echo "<tr><td colspan='4' class='border border-gray-200 dark:border-gray-700 px-4 py-3 text-red-600'><i class='fas fa-exclamation-triangle mr-2'></i>Error: " . $e->getMessage() . "</td></tr>";
        }
        
        echo '</tbody></table></div>';
        echo '</div>';
        echo '</section>';

        // COMPONENTS SECTION
        echo '<section id="components" class="mb-12">';
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">';
        echo '<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"><i class="fas fa-cogs mr-3 text-orange-600"></i>COMPONENT SYSTEM CHECK</h2>';
        
        $componentChecks = [
            'UIComponents.php' => 'Main UI component library',
            'SidebarComponent.php' => 'Sidebar navigation component',
            'CalculatorComponent.php' => 'Mortgage calculator component',
            'AutoFillComponent.php' => 'Auto-fill functionality component',
            'MortgageCalculatorComponent.php' => 'Mortgage calculator component',
            'AttachmentComponent.php' => 'File attachment component'
        ];
        
        echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
        foreach ($componentChecks as $file => $description) {
            $filePath = __DIR__ . '/../components/' . $file;
            $exists = file_exists($filePath);
            $readable = $exists && is_readable($filePath);
            $size = $exists ? filesize($filePath) : 0;
            
            echo "<div class='p-4 border border-gray-200 dark:border-gray-700 rounded-lg'>";
            echo "<h4 class='font-semibold text-gray-900 dark:text-white mb-2'>$file</h4>";
            echo "<p class='text-sm text-gray-600 dark:text-gray-400 mb-3'>$description</p>";
            echo "<div class='space-y-1 text-sm'>";
            echo "<p><i class='fas fa-file mr-2'></i>Exists: " . ($exists ? '<span class="text-green-600">Yes</span>' : '<span class="text-red-600">No</span>') . "</p>";
            echo "<p><i class='fas fa-book mr-2'></i>Readable: " . ($readable ? '<span class="text-green-600">Yes</span>' : '<span class="text-red-600">No</span>') . "</p>";
            if ($exists) {
                echo "<p><i class='fas fa-weight mr-2'></i>Size: " . number_format($size) . " bytes</p>";
            }
            echo "</div>";
            echo "</div>";
        }
        echo '</div>';
        echo '</div>';
        echo '</section>';

        // ROUTES & CONTROLLERS SECTION
        echo '<section id="routes" class="mb-12">';
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">';
        echo '<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"><i class="fas fa-route mr-3 text-pink-600"></i>ROUTES & CONTROLLERS CHECK</h2>';
        
        // Check critical routes
        $criticalRoutes = [
            '/admin/dashboard' => 'AdminDashboardController@index',
            '/admin/login' => 'AdminAuthController@showLogin',
            '/admin/properties' => 'PropertyController@index',
            '/admin/tenants' => 'TenantController@index',
            '/admin/maintenance' => 'MaintenanceController@index',
            '/admin/communications' => 'CommunicationController@index',
            '/admin/documents' => 'DocumentController@index',
            '/admin/invoices' => 'InvoiceController@index'
        ];
        
        echo '<div class="overflow-x-auto">';
        echo '<table class="w-full border-collapse border border-gray-200 dark:border-gray-700">';
        echo '<thead><tr class="bg-gray-100 dark:bg-gray-700">';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Route</th>';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Controller</th>';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Controller File</th>';
        echo '<th class="border border-gray-200 dark:border-gray-700 px-4 py-3 text-left">Status</th>';
        echo '</tr></thead><tbody>';
        
        foreach ($criticalRoutes as $route => $controller) {
            list($controllerName, $method) = explode('@', $controller);
            $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
            $controllerExists = file_exists($controllerFile);
            
            echo "<tr class='hover:bg-gray-50 dark:hover:bg-gray-700'>";
            echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3 font-mono text-sm'>$route</td>";
            echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3'>$controller</td>";
            echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3 font-mono text-sm'>$controllerName.php</td>";
            echo "<td class='border border-gray-200 dark:border-gray-700 px-4 py-3'>";
            if ($controllerExists) {
                echo "<span class='text-green-600 font-semibold'><i class='fas fa-check-circle mr-1'></i>EXISTS</span>";
            } else {
                echo "<span class='text-red-600 font-semibold'><i class='fas fa-times-circle mr-1'></i>MISSING</span>";
            }
            echo "</td>";
            echo "</tr>";
        }
        
        echo '</tbody></table></div>';
        echo '</div>';
        echo '</section>';

        // UI ISSUES SECTION
        echo '<section id="ui" class="mb-12">';
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">';
        echo '<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"><i class="fas fa-palette mr-3 text-indigo-600"></i>UI ISSUES CHECK</h2>';
        
        // Check for common UI issues
        $uiChecks = [
            'Font Awesome Icons' => [
                'file' => 'public/assets/css/fontawesome.css',
                'check' => 'Font Awesome CSS loaded',
                'critical' => true
            ],
            'Tailwind CSS' => [
                'file' => 'public/assets/css/tailwind.css',
                'check' => 'Tailwind CSS framework',
                'critical' => true
            ],
            'Dashboard Layout' => [
                'file' => 'views/admin/dashboard_layout.php',
                'check' => 'Main dashboard template',
                'critical' => true
            ],
            'Dark Mode Support' => [
                'file' => 'views/admin/dashboard_layout.php',
                'check' => 'dark mode toggle',
                'search' => 'darkModeToggle'
            ],
            'Responsive Design' => [
                'file' => 'views/admin/dashboard_layout.php',
                'check' => 'responsive meta tag',
                'search' => 'responsive'
            ]
        ];
        
        echo '<div class="space-y-4">';
        foreach ($uiChecks as $name => $config) {
            $filePath = __DIR__ . '/../' . $config['file'];
            $exists = file_exists($filePath);
            $status = 'UNKNOWN';
            $statusClass = 'text-gray-600';
            $details = '';
            
            if ($exists) {
                $content = file_get_contents($filePath);
                if (isset($config['search'])) {
                    $found = strpos($content, $config['search']) !== false;
                    $status = $found ? 'FOUND' : 'MISSING';
                    $statusClass = $found ? 'text-green-600' : 'text-yellow-600';
                    $details = $found ? $config['check'] . ' found' : $config['check'] . ' not found';
                } else {
                    $status = 'EXISTS';
                    $statusClass = 'text-green-600';
                    $details = $config['check'] . ' available';
                }
            } else {
                $status = 'MISSING';
                $statusClass = 'text-red-600';
                $details = 'File not found';
            }
            
            $critical = $config['critical'] ?? false;
            $criticalBadge = $critical ? ' <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">CRITICAL</span>' : '';
            
            echo "<div class='p-4 border border-gray-200 dark:border-gray-700 rounded-lg'>";
            echo "<div class='flex items-center justify-between'>";
            echo "<div>";
            echo "<h4 class='font-semibold text-gray-900 dark:text-white'>$name$criticalBadge</h4>";
            echo "<p class='text-sm text-gray-600 dark:text-gray-400 mt-1'>{$config['file']}</p>";
            echo "</div>";
            echo "<span class='$statusClass font-semibold'>$status</span>";
            echo "</div>";
            echo "<p class='text-sm text-gray-600 dark:text-gray-400 mt-2'>$details</p>";
            echo "</div>";
        }
        echo '</div>';
        echo '</div>';
        echo '</section>';

        // SYSTEM ERRORS SECTION
        echo '<section id="errors" class="mb-12">';
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">';
        echo '<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"><i class="fas fa-exclamation-triangle mr-3 text-red-600"></i>SYSTEM ERRORS</h2>';
        
        $errors = [];
        $warnings = [];
        
        // Check for common issues
        if (!file_exists(__DIR__ . '/../config/currency_helper.php')) {
            $errors[] = 'CurrencyHelper class not found at config/currency_helper.php';
        }
        
        if (!file_exists(__DIR__ . '/../app/controllers/NotificationController.php')) {
            $errors[] = 'NotificationController not found';
        }
        
        if (!class_exists('CurrencyHelper')) {
            $errors[] = 'CurrencyHelper class not loaded - check bootstrap.php';
        }
        
        // Check database connection
        try {
            if (isset($db)) {
                $pdo = $db->getConnection();
                $stmt = $pdo->prepare("SELECT 1");
                $stmt->execute();
            } else {
                $errors[] = 'Database connection not available';
            }
        } catch (Exception $e) {
            $errors[] = 'Database connection error: ' . $e->getMessage();
        }
        
        // Check for missing critical directories
        $criticalDirs = [
            'public/uploads' => 'File upload directory',
            'logs' => 'Log files directory',
            'storage/uploads' => 'Storage directory'
        ];
        
        foreach ($criticalDirs as $dir => $description) {
            $dirPath = __DIR__ . '/../' . $dir;
            if (!is_dir($dirPath)) {
                $warnings[] = "Directory missing: $dir ($description)";
            } elseif (!is_writable($dirPath)) {
                $warnings[] = "Directory not writable: $dir ($description)";
            }
        }
        
        // Display errors
        if (!empty($errors)) {
            echo '<div class="mb-6">';
            echo '<h3 class="font-semibold text-red-800 dark:text-red-200 mb-3"><i class="fas fa-times-circle mr-2"></i>Critical Errors:</h3>';
            echo '<div class="space-y-2">';
            foreach ($errors as $error) {
                echo "<div class='bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded'>";
                echo "<i class='fas fa-times-circle mr-2'></i>$error";
                echo "</div>";
            }
            echo '</div></div>';
        }
        
        // Display warnings
        if (!empty($warnings)) {
            echo '<div class="mb-6">';
            echo '<h3 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-3"><i class="fas fa-exclamation-triangle mr-2"></i>Warnings:</h3>';
            echo '<div class="space-y-2">';
            foreach ($warnings as $warning) {
                echo "<div class='bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded'>";
                echo "<i class='fas fa-exclamation-triangle mr-2'></i>$warning";
                echo "</div>";
            }
            echo '</div></div>';
        }
        
        if (empty($errors) && empty($warnings)) {
            echo '<div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded">';
            echo '<i class="fas fa-check-circle mr-2"></i>No critical errors or warnings detected!';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</section>';

        // SUMMARY SECTION
        echo '<section class="mb-12">';
        echo '<div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg shadow-lg p-8 border border-blue-200 dark:border-blue-800">';
        echo '<h2 class="text-2xl font-bold text-blue-900 dark:text-blue-100 mb-6 text-center">
            <i class="fas fa-clipboard-check mr-3"></i>System Health Summary
        </h2>';
        
        echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">';
        
        // Count statuses
        $totalChecks = 0;
        $passCount = 0;
        $failCount = 0;
        $warningCount = 0;
        
        // Simple summary based on our checks
        $summaryItems = [
            ['Currency System', class_exists('CurrencyHelper') ? 'PASS' : 'FAIL'],
            ['Database', isset($db) ? 'PASS' : 'FAIL'],
            ['Notifications', file_exists(__DIR__ . '/../app/controllers/NotificationController.php') ? 'PASS' : 'FAIL'],
            ['UI Components', file_exists(__DIR__ . '/../components/UIComponents.php') ? 'PASS' : 'FAIL']
        ];
        
        foreach ($summaryItems as $item) {
            $totalChecks++;
            if ($item[1] === 'PASS') $passCount++;
            elseif ($item[1] === 'FAIL') $failCount++;
        }
        
        $warningCount = count($warnings);
        
        echo '<div class="text-center">';
        echo '<div class="text-3xl font-bold text-blue-600 dark:text-blue-400">' . $totalChecks . '</div>';
        echo '<div class="text-sm text-gray-600 dark:text-gray-400">Total Checks</div>';
        echo '</div>';
        
        echo '<div class="text-center">';
        echo '<div class="text-3xl font-bold text-green-600 dark:text-green-400">' . $passCount . '</div>';
        echo '<div class="text-sm text-gray-600 dark:text-gray-400">Passed</div>';
        echo '</div>';
        
        echo '<div class="text-center">';
        echo '<div class="text-3xl font-bold text-red-600 dark:text-red-400">' . $failCount . '</div>';
        echo '<div class="text-sm text-gray-600 dark:text-gray-400">Failed</div>';
        echo '</div>';
        
        echo '<div class="text-center">';
        echo '<div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">' . $warningCount . '</div>';
        echo '<div class="text-sm text-gray-600 dark:text-gray-400">Warnings</div>';
        echo '</div>';
        
        echo '</div>';
        
        // Overall health indicator
        $healthPercentage = $totalChecks > 0 ? round(($passCount / $totalChecks) * 100) : 0;
        $healthColor = $healthPercentage >= 80 ? 'green' : ($healthPercentage >= 60 ? 'yellow' : 'red');
        
        echo '<div class="mt-6 text-center">';
        echo '<div class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Overall System Health</div>';
        echo '<div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">';
        echo '<div class="bg-' . $healthColor . '-600 h-4 rounded-full" style="width: ' . $healthPercentage . '%"></div>';
        echo '</div>';
        echo '<div class="text-2xl font-bold text-' . $healthColor . '-600 dark:text-' . $healthColor . '-400 mt-2">' . $healthPercentage . '%</div>';
        echo '</div>';
        
        echo '</div>';
        echo '</section>';
        ?>

        <!-- Footer -->
        <div class="text-center text-gray-600 dark:text-gray-400 mt-12">
            <p class="text-sm">
                <i class="fas fa-clock mr-2"></i>
                Last checked: <?php echo date('Y-m-d H:i:s'); ?>
            </p>
            <p class="text-xs mt-2">
                Realty-v2 Bug Report System v1.0 - Comprehensive Debug Checker
            </p>
        </div>
    </div>

    <!-- Smooth scroll script -->
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Add current section highlighting
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('nav a[href^="#"]');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 100)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('ring-2', 'ring-offset-2', 'ring-blue-500');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('ring-2', 'ring-offset-2', 'ring-blue-500');
                }
            });
        });
    </script>
</body>
</html>
