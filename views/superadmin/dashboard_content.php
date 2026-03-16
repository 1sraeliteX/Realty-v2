<?php
// Anti-scattering compliant framework initialization
require_once __DIR__ . '/../../../config/bootstrap.php';

// Get data from ViewManager (anti-scattering compliant)
$stats = ViewManager::get('stats', [
    'total_admins' => 0,
    'total_properties' => 0,
    'total_units' => 0,
    'total_tenants' => 0,
    'total_revenue' => 0,
    'active_maintenance' => 0,
    'pending_applications' => 0,
    'system_health' => 100
]);
$recentAdmins = ViewManager::get('recentAdmins', []);
$systemStats = ViewManager::get('systemStats', []);
$revenueData = ViewManager::get('revenueData', []);

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

<!-- Super Admin Dashboard Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php 
    // Get real trend calculations from DataProvider (anti-scattering compliant)
    $trends = DataProvider::get('dashboard_trends', [
        'admin_trend' => 0,
        'property_trend' => 0,
        'unit_trend' => 0,
        'tenant_trend' => 0,
        'revenue_trend' => 0
    ]);
    ?>
    
    <!-- Total Admins Card -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-user-shield text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Admins</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo number_format($stats['total_admins']); ?></div>
                            <div class="ml-2 flex items-baseline text-sm font-semibold <?php echo $trends['admin_trend'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'; ?>">
                                <i class="fas fa-<?php echo $trends['admin_trend'] >= 0 ? 'arrow-up' : 'arrow-down'; ?> text-xs mr-1"></i>
                                <?php echo abs($trends['admin_trend']); ?>%
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
            <div class="text-sm">
                <a href="/superadmin/admins" class="font-medium text-purple-700 dark:text-purple-300 hover:text-purple-900">Manage admins</a>
            </div>
        </div>
    </div>

    <!-- Total Properties Card -->
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
                            <div class="ml-2 flex items-baseline text-sm font-semibold <?php echo $trends['property_trend'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'; ?>">
                                <i class="fas fa-<?php echo $trends['property_trend'] >= 0 ? 'arrow-up' : 'arrow-down'; ?> text-xs mr-1"></i>
                                <?php echo abs($trends['property_trend']); ?>%
                            </div>
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

    <!-- Total Units Card -->
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
                            <div class="ml-2 flex items-baseline text-sm font-semibold <?php echo $trends['unit_trend'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'; ?>">
                                <i class="fas fa-<?php echo $trends['unit_trend'] >= 0 ? 'arrow-up' : 'arrow-down'; ?> text-xs mr-1"></i>
                                <?php echo abs($trends['unit_trend']); ?>%
                            </div>
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

    <!-- Total Revenue Card -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-naira-sign text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Revenue</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo formatAmount($stats['total_revenue']); ?></div>
                            <div class="ml-2 flex items-baseline text-sm font-semibold <?php echo $trends['revenue_trend'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'; ?>">
                                <i class="fas fa-<?php echo $trends['revenue_trend'] >= 0 ? 'arrow-up' : 'arrow-down'; ?> text-xs mr-1"></i>
                                <?php echo abs($trends['revenue_trend']); ?>%
                            </div>
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

<!-- System Health & Performance -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- System Health -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">System Health</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Health</span>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400"><?php echo $stats['system_health']; ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo $stats['system_health']; ?>%"></div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Database</span>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Online</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">API Server</span>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Running</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Storage</span>
                        <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">85% Full</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Admin Activity -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Recent Admin Activity</h3>
            <div class="space-y-4">
                <?php if (!empty($recentAdmins)): ?>
                    <?php foreach ($recentAdmins as $admin): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                        <i class="fas fa-user text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($admin['name'] ?? 'Unknown Admin'); ?></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo $admin['action'] ?? 'No action'; ?></div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo $admin['time'] ?? 'Unknown time'; ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-user-shield text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No recent admin activity</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <button onclick="window.location.href='/superadmin/admins'" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-user-plus mr-2"></i>
                    Add New Admin
                </button>
                <button onclick="window.location.href='/superadmin/export'" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-download mr-2"></i>
                    Export Data
                </button>
                <button onclick="runSystemMaintenance()" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-tools mr-2"></i>
                    System Maintenance
                </button>
                <button onclick="viewSystemLogs()" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-file-alt mr-2"></i>
                    View System Logs
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Platform Statistics -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg mb-8">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Platform Statistics</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400"><?php echo number_format($stats['total_tenants']); ?></div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total Tenants</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400"><?php echo $stats['active_maintenance']; ?></div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Active Maintenance</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400"><?php echo $stats['pending_applications']; ?></div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pending Applications</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">24/7</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">System Uptime</div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart Section -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Platform Revenue Trend</h3>
        <div class="mt-5">
            <canvas id="revenueChart" width="400" height="200"></canvas>
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
            label: 'Platform Revenue',
            data: revenueData.values || [],
            borderColor: 'rgb(147, 51, 234)',
            backgroundColor: 'rgba(147, 51, 234, 0.1)',
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

function runSystemMaintenance() {
    const toast = {
        type: 'info',
        message: 'System maintenance initiated...',
        duration: 3000
    };
    
    if (typeof showToast === 'function') {
        showToast(toast);
    }
}

function viewSystemLogs() {
    window.open('/admin/logs', '_blank');
}
</script>
