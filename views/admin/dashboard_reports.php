<?php
// Anti-scattering compliant framework initialization
require_once __DIR__ . '/../../config/bootstrap.php';

// Get data from ViewManager (anti-scattering compliant)
$reports = ViewManager::get('reports', []);
$user = ViewManager::get('user', []);

// Helper functions for reports (anti-scattering compliant - isolated in view)
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

function formatPercentage($value) {
    return number_format($value, 1) . '%';
}
?>

<!-- Include the dashboard layout -->
<?php include __DIR__ . '/dashboard_layout.php'; ?>

<!-- Reports Content -->
<div class="flex-1 p-6 overflow-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Comprehensive insights and analytics</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="exportReport('pdf')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center space-x-2">
                    <i class="fas fa-file-pdf"></i>
                    <span>Export PDF</span>
                </button>
                <button onclick="exportReport('excel')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>
                </button>
                <button onclick="showGenerateReportModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Generate Report</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo formatAmount($reports['financial']['total_revenue']); ?></p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-arrow-up text-green-500 text-xs mr-1"></i>
                        <span class="text-sm text-green-500">+<?php echo $reports['financial']['monthly_growth']; ?>%</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">vs last month</span>
                    </div>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <i class="fas fa-dollar-sign text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Net Profit</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo formatAmount($reports['financial']['net_profit']); ?></p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Profit margin: <?php echo formatPercentage(($reports['financial']['net_profit'] / $reports['financial']['total_revenue']) * 100); ?></span>
                    </div>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i class="fas fa-chart-line text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupancy Rate</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo formatPercentage($reports['occupancy']['occupancy_rate']); ?></p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400"><?php echo $reports['occupancy']['occupied_units']; ?> of <?php echo $reports['occupancy']['total_units']; ?> units</span>
                    </div>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <i class="fas fa-home text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Tenants</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $reports['tenants']['total_tenants']; ?></p>
                    <div class="flex items-center mt-2">
                        <span class="text-sm text-green-500">+<?php echo $reports['tenants']['new_this_month']; ?> new</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">this month</span>
                    </div>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <i class="fas fa-users text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue by Property</h2>
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </div>

        <!-- Occupancy Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Occupancy Overview</h2>
            <canvas id="occupancyChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Detailed Reports Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Financial Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Financial Details</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Total Revenue</span>
                        <span class="font-semibold text-gray-900 dark:text-white"><?php echo formatAmount($reports['financial']['total_revenue']); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Total Expenses</span>
                        <span class="font-semibold text-red-600"><?php echo formatAmount($reports['financial']['total_expenses']); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Net Profit</span>
                        <span class="font-semibold text-green-600"><?php echo formatAmount($reports['financial']['net_profit']); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Monthly Growth</span>
                        <span class="font-semibold text-green-600">+<?php echo formatPercentage($reports['financial']['monthly_growth']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Maintenance Summary</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Total Requests</span>
                        <span class="font-semibold text-gray-900 dark:text-white"><?php echo $reports['maintenance']['total_requests']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Completed</span>
                        <span class="font-semibold text-green-600"><?php echo $reports['maintenance']['completed']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Pending</span>
                        <span class="font-semibold text-yellow-600"><?php echo $reports['maintenance']['pending']; ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Avg. Completion Time</span>
                        <span class="font-semibold text-gray-900 dark:text-white"><?php echo $reports['maintenance']['average_completion_time']; ?> days</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Performance Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Property Performance</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Occupancy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Units</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($reports['financial']['revenue_by_property'] as $property): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"><?php echo $property['name']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo formatAmount($property['revenue']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <?php 
                            $property_name = $property['name'];
                            $property_data = array_filter($reports['occupancy']['properties'], function($p) use ($property_name) {
                                return $p['name'] === $property_name;
                            });
                            $property_data = reset($property_data);
                            $occupancy_rate = $property_data ? ($property_data['occupied'] / $property_data['total']) * 100 : 0;
                            echo formatPercentage($occupancy_rate);
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <?php 
                            echo $property_data ? $property_data['occupied'] . '/' . $property_data['total'] : '0/0';
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
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

<!-- JavaScript for Charts and Interactions -->
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($reports['financial']['revenue_by_property'], 'name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($reports['financial']['revenue_by_property'], 'revenue')); ?>,
            backgroundColor: [
                '#3B82F6',
                '#10B981',
                '#F59E0B',
                '#EF4444',
                '#8B5CF6'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Occupancy Chart
const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
const occupancyData = <?php echo json_encode($reports['occupancy']['properties']); ?>;
const occupancyChart = new Chart(occupancyCtx, {
    type: 'bar',
    data: {
        labels: occupancyData.map(p => p.name),
        datasets: [
            {
                label: 'Occupied',
                data: occupancyData.map(p => p.occupied),
                backgroundColor: '#10B981'
            },
            {
                label: 'Vacant',
                data: occupancyData.map(p => p.total - p.occupied),
                backgroundColor: '#EF4444'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                stacked: true,
                grid: {
                    display: false
                }
            },
            y: {
                stacked: true,
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Export functions
function exportReport(format) {
    const url = format === 'pdf' ? '/api/reports/export/pdf' : '/api/reports/export/excel';
    window.open(url, '_blank');
}

function showGenerateReportModal() {
    window.location.href = '/admin/reports/create';
}
</script>
