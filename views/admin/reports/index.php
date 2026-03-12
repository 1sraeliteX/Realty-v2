<?php
// Framework components are auto-loaded by ViewManager (anti-scattering compliant)

// Get data from ViewManager (anti-scattering compliant)
$stats = ViewManager::get('stats', [
    'total_properties' => 0,
    'total_units' => 0,
    'active_tenants' => 0,
    'occupancy_rate' => 0,
    'monthly_revenue' => 0,
    'occupied_units' => 0,
    'pending_payments' => 0,
    'maintenanceRequests' => 0,
    'newApplications' => 0
]);
$recentProperties = ViewManager::get('recentProperties', []);
$activities = ViewManager::get('activities', []);
$revenueData = ViewManager::get('revenueData', []);
$maintenanceRequests = ViewManager::get('maintenanceRequests', []);
$newApplications = ViewManager::get('newApplications', []);

// Load UIComponents (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Export functionality (anti-scattering compliant - functions at top)
function exportToCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // Add CSV header
    fputcsv($output, ['Report Generated: ' . date('Y-m-d H:i:s')]);
    fputcsv($output, []);
    
    if (!empty($data)) {
        // Add column headers
        $headers = array_keys($data[0]);
        fputcsv($output, $headers);
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }
    
    fclose($output);
    exit;
}

function exportToJSON($data, $filename) {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

function exportToPDF($data, $filename) {
    // For demo purposes, show a message
    $_SESSION['info'] = 'PDF export would be generated here: ' . $filename;
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/admin/reports');
    exit;
}

// Handle export requests
if (isset($_GET['export']) && isset($_GET['type'])) {
    $exportType = $_GET['type'];
    $reportType = $_GET['export'];
    
    switch ($reportType) {
        case 'dashboard':
            $exportData = [
                'summary' => $stats,
                'properties' => $recentProperties,
                'activities' => $activities,
                'revenue' => $revenueData
            ];
            $filename = 'dashboard_report_' . date('Y-m-d_H-i-s');
            break;
        case 'properties':
            $exportData = $recentProperties;
            $filename = 'properties_report_' . date('Y-m-d_H-i-s');
            break;
        case 'maintenance':
            $exportData = $maintenanceRequests;
            $filename = 'maintenance_report_' . date('Y-m-d_H-i-s');
            break;
        case 'financial':
            $exportData = [
                'revenue_data' => $revenueData,
                'monthly_revenue' => $stats['monthly_revenue']
            ];
            $filename = 'financial_report_' . date('Y-m-d_H-i-s');
            break;
        default:
            $exportData = [];
            $filename = 'report_' . date('Y-m-d_H-i-s');
    }
    
    switch ($exportType) {
        case 'csv':
            exportToCSV($exportData, $filename . '.csv');
            break;
        case 'json':
            exportToJSON($exportData, $filename . '.json');
            break;
        case 'pdf':
            exportToPDF($exportData, $filename . '.pdf');
            break;
    }
}
?>

<!-- Reports Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php echo UIComponents::statsCard('Total Properties', $stats['total_properties'], 'building', 0, 'primary'); ?>
    <?php echo UIComponents::statsCard('Active Tenants', $stats['active_tenants'], 'users', 0, 'green'); ?>
    <?php echo UIComponents::statsCard('Occupancy Rate', $stats['occupancy_rate'] . '%', 'percentage', 0, 'blue'); ?>
    <?php echo UIComponents::statsCard('Monthly Revenue', '₦' . number_format($stats['monthly_revenue']), 'dollar-sign', 0, 'yellow'); ?>
</div>

<!-- Export Controls Section -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Export Reports</h3>
            <div class="flex flex-wrap items-center gap-2">
                <a href="?export=dashboard&type=csv" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-file-csv mr-2 text-blue-500"></i>
                    Export CSV
                </a>
                <a href="?export=dashboard&type=json" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-file-code mr-2 text-purple-500"></i>
                    Export JSON
                </a>
                <a href="?export=properties&type=csv" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-building mr-2 text-green-500"></i>
                    Properties
                </a>
                <a href="?export=maintenance&type=csv" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-tools mr-2 text-yellow-500"></i>
                    Maintenance
                </a>
                <a href="?export=financial&type=csv" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-chart-line mr-2 text-red-500"></i>
                    Financial
                </a>
            </div>
        </div>
    </div>
    <div class="p-6">
        <p class="text-sm text-gray-500 dark:text-gray-400">Select export type above to download comprehensive reports in various formats.</p>
    </div>
</div>

<!-- Recent Properties -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Properties</h3>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Units</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Occupancy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach (array_slice($recentProperties, 0, 5) as $property): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($property['name'] ?? 'N/A'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($property['type'] ?? 'N/A'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo $property['unit_count'] ?? 0; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo $property['occupied_units'] ?? 0; ?>/<?php echo $property['unit_count'] ?? 0; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Active
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
