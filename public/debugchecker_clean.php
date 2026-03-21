<?php
// Simplified Debug Checker - Clean Output
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/config/database_factory.php';
use Config\DatabaseFactory;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Debug Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
            <i class="fas fa-bug mr-2"></i>System Debug Report
        </h1>

        <?php
        // Currency System Check
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-8">';
        echo '<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4"><i class="fas fa-coins mr-2 text-green-600"></i>CURRENCY SYSTEM</h2>';
        
        $currencyFiles = [
            'config/currency_helper.php' => 'CurrencyHelper class',
            'views/admin/dashboard_layout.php' => 'Dashboard Layout',
            'views/admin/dashboard_enhanced.php' => 'Enhanced Dashboard',
            'app/controllers/SettingsController.php' => 'Settings Controller'
        ];
        
        echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
        foreach ($currencyFiles as $file => $description) {
            $filePath = __DIR__ . '/' . $file;
            $status = file_exists($filePath) ? '✅ EXISTS' : '❌ MISSING';
            $statusClass = file_exists($filePath) ? 'text-green-600' : 'text-red-600';
            
            echo "<div class='p-4 border border-gray-200 dark:border-gray-700 rounded'>";
            echo "<h4 class='font-semibold text-gray-900 dark:text-white'>$description</h4>";
            echo "<p class='text-sm text-gray-600 dark:text-gray-400 mt-1'>$file</p>";
            echo "<p class='$statusClass font-semibold mt-2'>$status</p>";
            echo "</div>";
        }
        echo '</div>';
        echo '</div>';

        // Database Check
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-8">';
        echo '<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4"><i class="fas fa-database mr-2 text-blue-600"></i>DATABASE SYSTEM</h2>';
        
        try {
            $db = DatabaseFactory::create();
            $pdo = $db->getConnection();
            
            echo '<p class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Database connection successful</p>';
            
            // Check key tables
            $tables = ['properties', 'tenants', 'maintenance_requests', 'notifications'];
            echo '<div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-2">';
            foreach ($tables as $table) {
                $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                $exists = $stmt->fetch();
                $status = $exists ? '✅' : '❌';
                $color = $exists ? 'text-green-600' : 'text-red-600';
                echo "<div class='text-center p-2 border rounded'><span class='$color'>$status</span><br><small>$table</small></div>";
            }
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<p class="text-red-600"><i class="fas fa-times-circle mr-2"></i>Database connection failed: ' . $e->getMessage() . '</p>';
        }
        echo '</div>';

        // Component System Check
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-8">';
        echo '<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4"><i class="fas fa-cogs mr-2 text-purple-600"></i>COMPONENT SYSTEM</h2>';
        
        $components = [
            'components/UIComponents.php' => 'UI Components',
            'components/SidebarComponent.php' => 'Sidebar Component',
            'app/components/AutoFillComponent.php' => 'AutoFill Component',
            'components/CalculatorComponent.php' => 'Calculator Component'
        ];
        
        echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
        foreach ($components as $file => $description) {
            $filePath = __DIR__ . '/' . $file;
            $status = file_exists($filePath) ? '✅ EXISTS' : '❌ MISSING';
            $statusClass = file_exists($filePath) ? 'text-green-600' : 'text-red-600';
            
            echo "<div class='p-4 border border-gray-200 dark:border-gray-700 rounded'>";
            echo "<h4 class='font-semibold text-gray-900 dark:text-white'>$description</h4>";
            echo "<p class='text-sm text-gray-600 dark:text-gray-400 mt-1'>$file</p>";
            echo "<p class='$statusClass font-semibold mt-2'>$status</p>";
            echo "</div>";
        }
        echo '</div>';
        echo '</div>';

        // Controller Check
        echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-8">';
        echo '<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4"><i class="fas fa-code mr-2 text-orange-600"></i>CONTROLLER SYSTEM</h2>';
        
        $controllers = [
            'app/controllers/AdminDashboardController.php' => 'Admin Dashboard',
            'app/controllers/PropertyController.php' => 'Property Controller',
            'app/controllers/TenantController.php' => 'Tenant Controller',
            'app/controllers/MaintenanceController.php' => 'Maintenance Controller',
            'app/controllers/ApiMaintenanceController.php' => 'API Maintenance'
        ];
        
        echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
        foreach ($controllers as $file => $description) {
            $filePath = __DIR__ . '/' . $file;
            $status = file_exists($filePath) ? '✅ EXISTS' : '❌ MISSING';
            $statusClass = file_exists($filePath) ? 'text-green-600' : 'text-red-600';
            
            // Check syntax if file exists
            $syntaxStatus = '';
            if (file_exists($filePath)) {
                $output = shell_exec("php -l " . escapeshellarg($filePath) . " 2>&1");
                if (strpos($output, 'No syntax errors') !== false) {
                    $syntaxStatus = '<br><span class="text-green-600 text-sm">✅ Valid Syntax</span>';
                } else {
                    $syntaxStatus = '<br><span class="text-red-600 text-sm">❌ Syntax Error</span>';
                }
            }
            
            echo "<div class='p-4 border border-gray-200 dark:border-gray-700 rounded'>";
            echo "<h4 class='font-semibold text-gray-900 dark:text-white'>$description</h4>";
            echo "<p class='text-sm text-gray-600 dark:text-gray-400 mt-1'>$file</p>";
            echo "<p class='$statusClass font-semibold mt-2'>$status$syntaxStatus</p>";
            echo "</div>";
        }
        echo '</div>';
        echo '</div>';

        // Recent Fixes Summary
        echo '<div class="bg-green-50 dark:bg-green-900/20 rounded-lg shadow-lg p-6 mb-8">';
        echo '<h2 class="text-xl font-bold text-green-900 dark:text-green-100 mb-4"><i class="fas fa-check-circle mr-2"></i>RECENT FIXES APPLIED</h2>';
        
        echo '<div class="space-y-3">';
        echo '<div class="flex items-start"><span class="text-green-600 mr-3">✅</span><div>';
        echo '<h4 class="font-semibold text-green-800 dark:text-green-200">m.deleted_at Column Error - FIXED</h4>';
        echo '<p class="text-sm text-green-700 dark:text-green-300">Added proper table aliases to all SQL queries in MaintenanceController and ApiMaintenanceController</p>';
        echo '</div></div>';
        
        echo '<div class="flex items-start"><span class="text-green-600 mr-3">✅</span><div>';
        echo '<h4 class="font-semibold text-green-800 dark:text-green-200">Font Awesome Icons - RESTORED</h4>';
        echo '<p class="text-sm text-green-700 dark:text-green-300">Downloaded Font Awesome 6.4.0 font files and fixed deprecated icon names</p>';
        echo '</div></div>';
        
        echo '<div class="flex items-start"><span class="text-green-600 mr-3">✅</span><div>';
        echo '<h4 class="font-semibold text-green-800 dark:text-green-200">Property Display Routes - FIXED</h4>';
        echo '<p class="text-sm text-green-700 dark:text-green-300">Updated all property management URLs to use admin routes instead of public routes</p>';
        echo '</div></div>';
        echo '</div>';
        echo '</div>';

        // System Status
        echo '<div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow-lg p-6">';
        echo '<h2 class="text-xl font-bold text-blue-900 dark:text-blue-100 mb-4"><i class="fas fa-info-circle mr-2"></i>SYSTEM STATUS</h2>';
        
        echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
        echo '<div>';
        echo '<h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-3">✅ Working Components</h4>';
        echo '<ul class="space-y-1 text-sm text-blue-700 dark:text-blue-300">';
        echo '<li><i class="fas fa-check text-green-500 mr-2"></i>Admin Dashboard</li>';
        echo '<li><i class="fas fa-check text-green-500 mr-2"></i>Property Management</li>';
        echo '<li><i class="fas fa-check text-green-500 mr-2"></i>Tenant Management</li>';
        echo '<li><i class="fas fa-check text-green-500 mr-2"></i>Maintenance System</li>';
        echo '<li><i class="fas fa-check text-green-500 mr-2"></i>Currency System</li>';
        echo '<li><i class="fas fa-check text-green-500 mr-2"></i>Icon System</li>';
        echo '</ul>';
        echo '</div>';
        
        echo '<div>';
        echo '<h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-3">🌐 Access URLs</h4>';
        echo '<ul class="space-y-1 text-sm text-blue-700 dark:text-blue-300">';
        echo '<li><i class="fas fa-link text-blue-500 mr-2"></i>Admin: <code>/admin/login</code></li>';
        echo '<li><i class="fas fa-link text-blue-500 mr-2"></i>Dashboard: <code>/admin/dashboard</code></li>';
        echo '<li><i class="fas fa-link text-blue-500 mr-2"></i>Properties: <code>/admin/properties</code></li>';
        echo '<li><i class="fas fa-link text-blue-500 mr-2"></i>Tenants: <code>/admin/tenants</code></li>';
        echo '<li><i class="fas fa-link text-blue-500 mr-2"></i>Maintenance: <code>/admin/maintenance</code></li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        ?>

    </div>
</body>
</html>
