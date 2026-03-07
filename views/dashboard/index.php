<?php
$title = 'Dashboard';
$pageTitle = 'Dashboard Overview';
$content = ob_start();
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-primary-100 dark:bg-primary-900 rounded-lg p-3">
                <i class="fas fa-home text-primary-600 dark:text-primary-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Properties</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['total_properties']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                <i class="fas fa-door-open text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Units</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['total_units']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Tenants</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['active_tenants']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                <i class="fas fa-percentage text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupancy Rate</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $stats['occupancy_rate']; ?>%</p>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Monthly Revenue</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">$<?php echo number_format($stats['monthly_revenue'], 2); ?></p>
            </div>
            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                <i class="fas fa-dollar-sign text-green-600 dark:text-green-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupied Units</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?php echo number_format($stats['occupied_units']); ?></p>
            </div>
            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                <i class="fas fa-check-circle text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Payments</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400"><?php echo number_format($stats['pending_payments']); ?></p>
            </div>
            <div class="flex-shrink-0 bg-red-100 dark:bg-red-900 rounded-lg p-3">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Revenue Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Revenue Overview</h3>
            <select id="revenue-period" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="12">Last 12 months</option>
                <option value="6">Last 6 months</option>
                <option value="3">Last 3 months</option>
            </select>
        </div>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Occupancy Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Occupancy Status</h3>
        <div class="h-64">
            <canvas id="occupancyChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Properties -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Properties</h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentProperties)): ?>
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No properties found</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentProperties as $property): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($property['name']); ?></h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($property['address']); ?></p>
                                <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="mr-3"><?php echo $property['unit_count']; ?> units</span>
                                    <span><?php echo $property['occupied_units']; ?> occupied</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <?php echo ucfirst($property['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="/properties" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">View all properties →</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activities</h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentActivities)): ?>
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent activities</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-<?php echo $this->getActivityIcon($activity['action']); ?> text-primary-600 dark:text-primary-400 text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($activity['description']); ?>
                                </p>
                                <?php if ($activity['property_name']): ?>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Property: <?php echo htmlspecialchars($activity['property_name']); ?>
                                    </p>
                                <?php endif; ?>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?php echo json_encode($revenueData); ?>;
    const revenueLabels = Object.keys(revenueData);
    const revenueValues = Object.values(revenueData);

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueLabels.map(date => {
                const d = new Date(date + '-01');
                return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Revenue',
                data: revenueValues,
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
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Occupancy Chart
    const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
    const occupiedUnits = <?php echo $stats['occupied_units']; ?>;
    const totalUnits = <?php echo $stats['total_units']; ?>;
    const vacantUnits = totalUnits - occupiedUnits;

    new Chart(occupancyCtx, {
        type: 'doughnut',
        data: {
            labels: ['Occupied', 'Vacant'],
            datasets: [{
                data: [occupiedUnits, vacantUnits],
                backgroundColor: [
                    'rgb(34, 197, 94)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Revenue period change
    document.getElementById('revenue-period').addEventListener('change', function() {
        const months = this.value;
        // Reload chart data for selected period
        apiRequest(`/api/dashboard/revenue?months=${months}`)
            .then(data => {
                // Update chart with new data
                const chart = Chart.getChart('revenueChart');
                const labels = data.map(item => {
                    const d = new Date(item.month + '-01');
                    return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                });
                const values = data.map(item => item.revenue);
                
                chart.data.labels = labels;
                chart.data.datasets[0].data = values;
                chart.update();
            });
    });
});

<?php
// Helper function to get activity icon (moved from controller)
function getActivityIcon($action) {
    $icons = [
        'login' => 'sign-in-alt',
        'logout' => 'sign-out-alt',
        'create' => 'plus',
        'update' => 'edit',
        'delete' => 'trash',
        'register' => 'user-plus',
        'payment' => 'dollar-sign',
        'invoice' => 'file-invoice'
    ];
    
    return $icons[$action] ?? 'circle';
}
?>
