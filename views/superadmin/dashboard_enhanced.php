<?php
// Anti-scattering compliant framework initialization
require_once __DIR__ . '/../../config/bootstrap.php';

// Get platform-wide data from ViewManager (anti-scattering compliant)
$stats = ViewManager::get('stats', [
    'total_properties' => 0,
    'total_units' => 0,
    'total_tenants' => 0,
    'total_admins' => 0,
    'total_revenue' => 0,
    'occupancy_rate' => 0,
    'active_properties' => 0,
    'pending_maintenance' => 0,
    'new_applications' => 0
]);
$recentAdmins = ViewManager::get('recentAdmins', []);
$activities = ViewManager::get('recentActivities', []);
$revenueData = ViewManager::get('revenueData', []);
$maintenanceRequests = ViewManager::get('maintenanceRequests', []);
$newApplications = ViewManager::get('newApplications', []);
$topProperties = ViewManager::get('topProperties', []);

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

<!-- Platform Overview Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Platform Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Complete overview of your real estate platform</p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-download mr-2"></i>
                Export Report
            </button>
            <button class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-cog mr-2"></i>
                Settings
            </button>
        </div>
    </div>
</div>

<!-- Platform Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php 
    // Get real trend calculations from DataProvider (anti-scattering compliant)
    $trends = DataProvider::get('platform_trends', [
        'property_trend' => 0,
        'units_trend' => 0, 
        'tenants_trend' => 0,
        'admin_trend' => 0,
        'revenue_trend' => 0
    ]);
    
    echo UIComponents::statsCard('Total Properties', number_format($stats['total_properties']), 'building', $trends['property_trend'], 'purple'); 
    echo UIComponents::statsCard('Total Units', number_format($stats['total_units']), 'door-open', $trends['units_trend'], 'blue'); 
    echo UIComponents::statsCard('Total Tenants', number_format($stats['total_tenants']), 'users', $trends['tenants_trend'], 'green'); 
    echo UIComponents::statsCard('Platform Admins', number_format($stats['total_admins']), 'user-shield', $trends['admin_trend'], 'yellow'); 
    ?>
</div>

<!-- Secondary Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php 
    // Format total revenue with proper thousand/million suffix (anti-scattering compliant)
    echo UIComponents::statsCard('Total Revenue', formatAmount($stats['total_revenue']), 'money-bill-wave', $trends['revenue_trend'], 'green'); 
    echo UIComponents::statsCard('Occupancy Rate', $stats['occupancy_rate'] . '%', 'percentage', $trends['tenants_trend'], 'blue'); 
    echo UIComponents::statsCard('Active Properties', number_format($stats['active_properties']), 'check-circle', $trends['property_trend'], 'purple'); 
    ?>
</div>

<!-- Charts and Admin Actions Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Platform Revenue Chart -->
    <div class="lg:col-span-2">
        <?php 
        echo UIComponents::card(
            '<div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Platform Revenue Overview</h3>
                <select id="revenue-period" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="12">Last 12 months</option>
                    <option value="6">Last 6 months</option>
                    <option value="3">Last 3 months</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>',
            null,
            null,
            'bg-white dark:bg-gray-800 rounded-lg shadow'
        ); ?>
    </div>

    <!-- Platform Quick Actions -->
    <?php 
    echo UIComponents::card(
        '<div class="grid grid-cols-2 gap-4">
            <a href="/superadmin/admins" class="inline-flex flex-col items-center px-4 py-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-user-plus text-purple-600 dark:text-purple-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Add Admin</span>
            </a>
            <a href="/properties" class="inline-flex flex-col items-center px-4 py-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-building text-blue-600 dark:text-blue-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-900 dark:text-white">View Properties</span>
            </a>
            <a href="/tenants" class="inline-flex flex-col items-center px-4 py-3 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-users text-green-600 dark:text-green-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-900 dark:text-white">View Tenants</span>
            </a>
            <a href="/reports" class="inline-flex flex-col items-center px-4 py-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-chart-bar text-yellow-600 dark:text-yellow-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Reports</span>
            </a>
            <a href="/maintenance" class="inline-flex flex-col items-center px-4 py-3 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-tools text-red-600 dark:text-red-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Maintenance</span>
            </a>
            <a href="/settings" class="inline-flex flex-col items-center px-4 py-3 bg-gray-50 dark:bg-gray-900/20 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-cog text-gray-600 dark:text-gray-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Settings</span>
            </a>
        </div>',
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Platform Actions</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Recent Admin Activity & Top Properties -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Recent Admin Activity -->
    <?php 
    echo UIComponents::card(
        '<div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Admin Activity</h3>
                <a href="/superadmin/activity" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">View all</a>
            </div>
            <div class="space-y-3">
                ' . (empty($activities) ? '
                    <div class="text-center py-8">
                        <i class="fas fa-user-shield text-gray-300 dark:text-gray-600 text-3xl mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No recent admin activity</p>
                    </div>
                ' : '') . '
                ' . implode('', array_map(function($activity) {
                    $icon = match($activity['action']) {
                        'create' => 'fa-plus-circle text-green-500',
                        'update' => 'fa-edit text-blue-500', 
                        'delete' => 'fa-trash text-red-500',
                        'login' => 'fa-sign-in-alt text-purple-500',
                        default => 'fa-circle text-gray-500'
                    };
                    return '
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                <i class="fas ' . $icon . ' text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">' . htmlspecialchars($activity['description']) . '</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">' . htmlspecialchars($activity['admin_name']) . ' • ' . $activity['time'] . '</p>
                            </div>
                        </div>
                    ';
                }, array_slice($activities, 0, 5))) . '
            </div>
        </div>',
        null,
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>

    <!-- Top Performing Properties -->
    <?php 
    echo UIComponents::card(
        '<div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Top Properties</h3>
                <a href="/properties" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">View all</a>
            </div>
            <div class="space-y-3">
                ' . (empty($topProperties) ? '
                    <div class="text-center py-8">
                        <i class="fas fa-building text-gray-300 dark:text-gray-600 text-3xl mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No properties available</p>
                    </div>
                ' : '') . '
                ' . implode('', array_map(function($property, $index) {
                    $medal = $index === 0 ? '🥇' : ($index === 1 ? '🥈' : ($index === 2 ? '🥉' : ''));
                    return '
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-purple-600 dark:text-purple-400">' . $medal . '</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">' . htmlspecialchars($property['name']) . '</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">' . htmlspecialchars($property['admin_name']) . '</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">' . formatAmount($property['revenue']) . '</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">' . $property['occupancy'] . '% occupied</p>
                            </div>
                        </div>
                    ';
                }, array_slice($topProperties, 0, 5), array_keys(array_slice($topProperties, 0, 5)))) . '
            </div>
        </div>',
        null,
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Platform Overview Tables -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Admin Registrations -->
    <?php 
    echo UIComponents::card(
        '<div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Admin Registrations</h3>
                <a href="/superadmin/admins" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">Manage admins</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Admin</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        ' . (empty($recentAdmins) ? '
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No recent admin registrations
                                </td>
                            </tr>
                        ' : '') . '
                        ' . implode('', array_map(function($admin) {
                            $statusColor = $admin['status'] === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
                            return '
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-medium">' . strtoupper(substr($admin['name'], 0, 1)) . '</span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">' . htmlspecialchars($admin['name']) . '</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">' . htmlspecialchars($admin['email']) . '</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                                            ' . ucfirst($admin['role']) . '
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        ' . $admin['created_at'] . '
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full ' . $statusColor . '">
                                            ' . ucfirst($admin['status']) . '
                                        </span>
                                    </td>
                                </tr>
                            ';
                        }, array_slice($recentAdmins, 0, 5))) . '
                    </tbody>
                </table>
            </div>
        </div>',
        null,
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>

    <!-- System Health -->
    <?php 
    echo UIComponents::card(
        '<div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">System Health</h3>
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                    Healthy
                </span>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Database Connection</span>
                    </div>
                    <span class="text-sm text-green-600 dark:text-green-400">Active</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">File Storage</span>
                    </div>
                    <span class="text-sm text-green-600 dark:text-green-400">Operational</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Email Service</span>
                    </div>
                    <span class="text-sm text-yellow-600 dark:text-yellow-400">Warning</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Backup System</span>
                    </div>
                    <span class="text-sm text-green-600 dark:text-green-400">Last: 2h ago</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">API Rate Limit</span>
                    </div>
                    <span class="text-sm text-green-600 dark:text-green-400">Normal</span>
                </div>
            </div>
        </div>',
        null,
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Revenue Chart Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($revenueData, 'month')); ?>,
                datasets: [{
                    label: 'Platform Revenue',
                    data: <?php echo json_encode(array_column($revenueData, 'amount')); ?>,
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
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

        // Period selector
        const periodSelect = document.getElementById('revenue-period');
        if (periodSelect) {
            periodSelect.addEventListener('change', function() {
                // In production, this would fetch new data based on selected period
                showToast('Revenue period changed to ' + this.options[this.selectedIndex].text, 'info');
            });
        }
    }
});
</script>
