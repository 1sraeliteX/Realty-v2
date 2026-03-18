<?php
// Anti-scattering compliant framework initialization
require_once __DIR__ . '/../../../config/bootstrap.php';

// Get data from ViewManager (anti-scattering compliant)
$stats = ViewManager::get('stats', [
    'total_properties' => 0,
    'total_units' => 0,
    'active_tenants' => 0,
    'occupancy_rate' => 0,
    'monthly_revenue' => 0,
    'occupied_units' => 0,
    'pending_payments' => 0,
    'pending_maintenance' => 0,
    'new_applications' => 0
]);
$recentProperties = ViewManager::get('recentProperties', []);
$activities = ViewManager::get('recentActivities', []);
$revenueData = ViewManager::get('revenueData', []);
$maintenanceRequests = ViewManager::get('maintenanceRequests', []);
$newApplications = ViewManager::get('newApplications', []);
$upcomingTasks = ViewManager::get('upcomingTasks', []);

// Helper functions for dashboard (anti-scattering compliant - isolated in view)
function formatAmount($amount) {
    if ($amount >= 1000000000) {
        return 'N' . number_format($amount / 1000000000, 2) . 'B';
    } elseif ($amount >= 1000000) {
        return 'N' . number_format($amount / 1000000, 2) . 'M';
    } elseif ($amount >= 1000) {
        return 'N' . number_format($amount / 1000, 1) . 'K';
    } else {
        return 'N' . number_format($amount);
    }
}

function calculateTrend($current, $previous) {
    if ($previous == 0) return 0;
    return round((($current - $previous) / $previous) * 100, 1);
}
?>

<!-- Breadcrumb Navigation -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="/admin/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Reports</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Reports</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Comprehensive analytics and insights for your property management business</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button onclick="exportReports()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-download mr-2"></i>
                Export
            </button>
            <button onclick="refreshReports()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh
            </button>
        </div>
    </div>
</div>

<!-- Reports Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php 
    // Get real trend calculations from DataProvider (anti-scattering compliant)
    $trends = DataProvider::get('dashboard_trends', [
        'property_trend' => 0,
        'units_trend' => 0, 
        'tenants_trend' => 0,
        'occupancy_trend' => 0,
        'revenue_trend' => 0
    ]);
    ?>
    
    <!-- Properties Report Card -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-building text-primary-600 dark:text-primary-400 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Properties</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo number_format($stats['total_properties']); ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
            <div class="text-sm">
                <a href="/admin/properties" class="font-medium text-primary-700 dark:text-primary-300 hover:text-primary-900">View all properties</a>
            </div>
        </div>
    </div>

    <!-- Units Report Card -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-door-closed text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Units</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo number_format($stats['total_units']); ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
            <div class="text-sm">
                <a href="/admin/units" class="font-medium text-primary-700 dark:text-primary-300 hover:text-primary-900">View all units</a>
            </div>
        </div>
    </div>

    <!-- Tenants Report Card -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Tenants</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo number_format($stats['active_tenants']); ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
            <div class="text-sm">
                <a href="/admin/tenants" class="font-medium text-primary-700 dark:text-primary-300 hover:text-primary-900">View all tenants</a>
            </div>
        </div>
    </div>

    <!-- Revenue Report Card -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-dollar-sign text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Monthly Revenue</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo formatAmount($stats['monthly_revenue']); ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
            <div class="text-sm">
                <a href="/admin/payments" class="font-medium text-primary-700 dark:text-primary-300 hover:text-primary-900">View payments</a>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart Section -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg mb-8">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Revenue Trend</h3>
        <div class="mt-5">
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Detailed Reports Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Properties Performance -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Properties Performance</h3>
            <div class="space-y-4">
                <?php if (!empty($recentProperties)): ?>
                    <?php foreach ($recentProperties as $property): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                                        <i class="fas fa-building text-primary-600 dark:text-primary-400"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($property['name'] ?? 'Unknown Property'); ?></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($property['address'] ?? 'No Address'); ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo $property['occupancy_rate'] ?? 0; ?> Occupancy</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo formatAmount($property['monthly_revenue'] ?? 0); ?>/mo</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-building text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No properties data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Recent Activities</h3>
            <div class="space-y-4">
                <?php if (!empty($activities)): ?>
                    <?php foreach ($activities as $activity): ?>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-<?php echo $activity['color'] ?? 'gray'; ?>-100 dark:bg-<?php echo $activity['color'] ?? 'gray'; ?>-900 flex items-center justify-center">
                                    <i class="fas fa-<?php echo $activity['icon'] ?? 'circle'; ?> text-<?php echo $activity['color'] ?? 'gray'; ?>-600 dark:text-<?php echo $activity['color'] ?? 'gray'; ?>-400 text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($activity['description'] ?? 'No description'); ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo $activity['time'] ?? 'Unknown time'; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No recent activities</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance & Applications Overview -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Maintenance Requests -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Maintenance Requests</h3>
            <div class="space-y-4">
                <?php if (!empty($maintenanceRequests)): ?>
                    <?php foreach ($maintenanceRequests as $request): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['title'] ?? 'Unknown Request'); ?></div>
                                <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($request['property_name'] ?? 'Unknown Property'); ?></div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?php echo $request['priority'] === 'high' ? 'red' : ($request['priority'] === 'medium' ? 'yellow' : 'green'); ?>-100 text-<?php echo $request['priority'] === 'high' ? 'red' : ($request['priority'] === 'medium' ? 'yellow' : 'green'); ?>-800 dark:bg-<?php echo $request['priority'] === 'high' ? 'red' : ($request['priority'] === 'medium' ? 'yellow' : 'green'); ?>-900 dark:text-<?php echo $request['priority'] === 'high' ? 'red' : ($request['priority'] === 'medium' ? 'yellow' : 'green'); ?>-200">
                                <?php echo ucfirst($request['priority'] ?? 'medium'); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-tools text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No maintenance requests</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- New Applications -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">New Applications</h3>
            <div class="space-y-4">
                <?php if (!empty($newApplications)): ?>
                    <?php foreach ($newApplications as $application): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($application['name'] ?? 'Unknown Applicant'); ?></div>
                                <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($application['property_name'] ?? 'Unknown Property'); ?></div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <?php echo ucfirst($application['status'] ?? 'pending'); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-user-plus text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No new applications</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Charts and Interactions -->
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = <?php echo json_encode($revenueData ?? []); ?>;

new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueData.labels || [],
        datasets: [{
            label: 'Monthly Revenue',
            data: revenueData.values || [],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'N' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Export Reports Function
function exportReports() {
    const toast = {
        type: 'success',
        message: 'Reports exported successfully!',
        duration: 3000
    };
    
    if (typeof showToast === 'function') {
        showToast(toast);
    }
    
    // Create a simple CSV export
    const csvContent = "data:text/csv;charset=utf-8," 
        + "Report,Value\n"
        + "Properties," + <?php echo $stats['total_properties']; ?> + "\n"
        + "Units," + <?php echo $stats['total_units']; ?> + "\n"
        + "Tenants," + <?php echo $stats['active_tenants']; ?> + "\n"
        + "Revenue," + "<?php echo $stats['monthly_revenue']; ?>\n";
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "dashboard_reports.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Refresh Reports Function
function refreshReports() {
    const toast = {
        type: 'info',
        message: 'Refreshing reports...',
        duration: 2000
    };
    
    if (typeof showToast === 'function') {
        showToast(toast);
    }
    
    // Reload the page to refresh data
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}
</script>
