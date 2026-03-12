<?php
// Anti-scattering compliant framework initialization
require_once __DIR__ . '/../../config/bootstrap.php';

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

<!-- Dashboard Overview -->
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
    
    echo UIComponents::statsCard('Total Properties', number_format($stats['total_properties']), 'home', $trends['property_trend'], 'primary'); 
    echo UIComponents::statsCard('Total Units', number_format($stats['total_units']), 'door-open', $trends['units_trend'], 'blue'); 
    echo UIComponents::statsCard('Active Tenants', number_format($stats['active_tenants']), 'users', $trends['tenants_trend'], 'green'); 
    echo UIComponents::statsCard('Occupancy Rate', $stats['occupancy_rate'] . '%', 'percentage', $trends['occupancy_trend'], 'yellow'); 
    ?>
</div>

<!-- Secondary Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php 
    // Format monthly revenue with proper thousand/million suffix (anti-scattering compliant)
    echo UIComponents::statsCard('Monthly Revenue', formatAmount($stats['monthly_revenue']), 'money-bill-wave', $trends['revenue_trend'], 'green'); 
    echo UIComponents::statsCard('Occupied Units', number_format($stats['occupied_units']), 'check-circle', $trends['units_trend'], 'blue'); 
    echo UIComponents::statsCard('Pending Payments', number_format($stats['pending_payments']), 'exclamation-triangle', -25.0, 'red'); 
    ?>
</div>

<!-- Charts and Actions Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Revenue Chart -->
    <div class="lg:col-span-2">
        <?php 
        echo UIComponents::card(
            '<div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Revenue Overview</h3>
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

    <!-- Quick Actions -->
    <?php 
    echo UIComponents::card(
        '<div class="grid grid-cols-2 gap-4">
            <a href="/admin/properties/create" class="inline-flex flex-col items-center px-4 py-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-plus-circle text-blue-600 dark:text-blue-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">Add Property</span>
            </a>
            <a href="/admin/tenants/create" class="inline-flex flex-col items-center px-4 py-3 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-user-plus text-green-600 dark:text-green-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">Add Tenant</span>
            </a>
            <a href="/admin/payments/create" class="inline-flex flex-col items-center px-4 py-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-file-invoice-dollar text-yellow-600 dark:text-yellow-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">Record Payment</span>
            </a>
            <a href="/admin/maintenance" class="inline-flex flex-col items-center px-4 py-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-tools text-purple-600 dark:text-purple-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">Maintenance</span>
            </a>
            <a href="/admin/invoices/create" class="inline-flex flex-col items-center px-4 py-3 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-file-invoice text-red-600 dark:text-red-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">Create Invoice</span>
            </a>
            <a href="/admin/reports" class="inline-flex flex-col items-center px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-chart-bar text-indigo-600 dark:text-indigo-400 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-1">Reports</span>
            </a>
        </div>',
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Recent Properties and Activities -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Recent Properties -->
    <?php 
    $propertiesContent = '';
    if (empty($recentProperties)) {
        $propertiesContent = '<p class="text-gray-500 dark:text-gray-400 text-center py-8">No properties found</p>';
    } else {
        $propertiesContent = '<div class="space-y-4">';
        foreach ($recentProperties as $property) {
            $statusColor = arr_get($property, 'status') === 'occupied' ? 'success' : (arr_get($property, 'status') === 'available' ? 'info' : 'warning');
            $propertiesContent .= "
                <div class=\"flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer\" onclick=\"window.location.href='/admin/properties/" . arr_get($property, 'id') . "'\">
                    <div class=\"flex items-center space-x-4 min-w-0 flex-1\">
                        <img src=\"" . arr_escape($property, 'image') . "\" alt=\"" . arr_escape($property, 'name') . "\" class=\"w-16 h-16 rounded-lg object-cover flex-shrink-0\">
                        <div class=\"min-w-0 flex-1\">
                            <h4 class=\"text-sm font-semibold text-gray-900 dark:text-white truncate\">" . arr_escape($property, 'name') . "</h4>
                            <p class=\"text-xs text-gray-500 dark:text-gray-400 truncate\">" . arr_escape($property, 'address') . "</p>
                            <div class=\"flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400 space-x-3\">
                                <span class=\"flex-shrink-0\"><i class=\"fas fa-door-open mr-1\"></i>" . arr_get($property, 'unit_count', 0) . " units</span>
                                <span class=\"flex-shrink-0\"><i class=\"fas fa-users mr-1\"></i>" . arr_get($property, 'occupied_units', 0) . " occupied</span>
                            </div>
                        </div>
                    </div>
                    <div class=\"text-right flex-shrink-0 ml-4\">
                        " . UIComponents::badge(ucfirst(arr_get($property, 'status')), $statusColor, 'small') . "
                        <div class=\"mt-2 text-xs text-gray-500 dark:text-gray-400\">" . arr_escape($property, 'type') . "</div>
                    </div>
                </div>
            ";
        }
        $propertiesContent .= '</div>';
        $propertiesContent .= '<div class="mt-4 text-center"><a href="/admin/properties" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all properties →</a></div>';
    }
    
    echo UIComponents::card(
        $propertiesContent,
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Properties</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>

    <!-- Recent Activities -->
    <?php 
    $activitiesContent = '';
    if (empty($activities)) {
        $activitiesContent = '<p class="text-gray-500 dark:text-gray-400 text-center py-8">No recent activities</p>';
    } else {
        $activitiesContent = '<div class="space-y-4">';
        foreach ($activities as $activity) {
            $icon = arr_get($activity, 'action') === 'payment' ? 'credit-card' : 
                   (arr_get($activity, 'action') === 'maintenance' ? 'tools' : 
                   (arr_get($activity, 'action') === 'tenant' ? 'user' : 
                   (arr_get($activity, 'action') === 'create' ? 'plus' : 'circle')));
            $time = arr_get($activity, 'created_at');
            $description = arr_escape($activity, 'description');
            $activitiesContent .= "
                <div class=\"flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors\">
                    <div class=\"flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center\">
                        <i class=\"fas fa-$icon text-primary-600 dark:text-primary-400 text-xs\"></i>
                    </div>
                    <div class=\"flex-1 min-w-0\">
                        <p class=\"text-sm text-gray-900 dark:text-white truncate\">{$description}</p>";
            if (arr_get($activity, 'property_name')) {
                $activitiesContent .= "<p class=\"text-xs text-gray-500 dark:text-gray-400 mt-1 truncate\">Property: " . arr_escape($activity, 'property_name') . "</p>";
            }
            $activitiesContent .= "<p class=\"text-xs text-gray-500 dark:text-gray-400 mt-1\">" . formatActivityTime($time) . "</p>
                    </div>
                </div>
            ";
        }
        $activitiesContent .= '</div>';
    }
    
    echo UIComponents::card(
        $activitiesContent,
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activities</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Additional Dashboard Widgets -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Maintenance Requests -->
    <?php 
    echo UIComponents::card(
        '<div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Urgent: HVAC Repair</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Unit 5A - Sunset Apartments</p>
                    </div>
                </div>
                <span class="text-xs text-red-600 dark:text-red-400">2h ago</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-wrench text-yellow-600 dark:text-yellow-400 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Plumbing Issue</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Unit 2B - Downtown Plaza</p>
                    </div>
                </div>
                <span class="text-xs text-yellow-600 dark:text-yellow-400">5h ago</span>
            </div>
        </div>
        <div class="mt-4 text-center">
            <a href="/admin/maintenance" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all requests →</a>
        </div>',
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Maintenance Requests</h3>',
        '<span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs font-medium px-2.5 py-0.5 rounded-full">' . arr_get($stats, 'maintenanceRequests', 0) . ' pending</span>'
    ); ?>

    <!-- New Applications -->
    <?php 
    echo UIComponents::card(
        '<div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <div class="flex items-center space-x-3">
                    ' . UIComponents::avatar('Sarah Johnson', null, 'small') . '
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Sarah Johnson</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Unit 3C - Sunset Apartments</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button class="text-green-600 hover:text-green-700 dark:text-green-400">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="text-red-600 hover:text-red-700 dark:text-red-400">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <div class="flex items-center space-x-3">
                    ' . UIComponents::avatar('Mike Chen', null, 'small') . '
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Mike Chen</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Unit 1A - Riverside Complex</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button class="text-green-600 hover:text-green-700 dark:text-green-400">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="text-red-600 hover:text-red-700 dark:text-red-400">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-4 text-center">
            <a href="/admin/applications" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all applications →</a>
        </div>',
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">New Applications</h3>',
        '<span class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs font-medium px-2.5 py-0.5 rounded-full">' . arr_get($stats, 'newApplications', 0) . ' pending</span>'
    ); ?>

    <!-- Notes Section -->
    <?php 
    // Load notes component (anti-scattering compliant)
    ComponentRegistry::load('notes-component');
    $notes = NotesComponent::getNotes();
    echo NotesComponent::render($notes);
    ?>

    <!-- Upcoming Tasks -->
    <?php 
    echo UIComponents::card(
        '<div class="space-y-3">
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                <div class="flex items-center space-x-3">
                    <input type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Inspect Unit 4B</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Riverside Complex - Today 2:00 PM</p>
                    </div>
                </div>
                <span class="text-xs text-orange-600 dark:text-orange-400">Today</span>
            </div>
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                <div class="flex items-center space-x-3">
                    <input type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Send rent reminders</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Monthly task - Tomorrow</p>
                    </div>
                </div>
                <span class="text-xs text-blue-600 dark:text-blue-400">Tomorrow</span>
            </div>
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                <div class="flex items-center space-x-3">
                    <input type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Property tax filing</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Due next week</p>
                    </div>
                </div>
                <span class="text-xs text-gray-600 dark:text-gray-400">Next week</span>
            </div>
        </div>',
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Upcoming Tasks</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Chart.js Script -->
<script src="/assets/js/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?php echo json_encode($revenueData); ?>;
    const revenueLabels = Object.keys(revenueData);
    const revenueValues = Object.values(revenueData);

    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueLabels.map(date => {
                const d = new Date(date + '-01');
                return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Revenue',
                data: revenueValues,
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                borderRadius: 4,
                hoverBackgroundColor: 'rgba(59, 130, 246, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.y;
                            if (value >= 1000000000) {
                                return 'Revenue: N' + (value / 1000000000).toFixed(2) + 'B';
                            } else if (value >= 1000000) {
                                return 'Revenue: N' + (value / 1000000).toFixed(2) + 'M';
                            } else if (value >= 1000) {
                                return 'Revenue: N' + (value / 1000).toFixed(2) + 'K';
                            } else {
                                return 'Revenue: N' + value.toFixed(2);
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value === 0) return 'N0';
                            if (value >= 1000000000) {
                                return 'N' + (value / 1000000000).toFixed(1) + 'B';
                            } else if (value >= 1000000) {
                                return 'N' + (value / 1000000).toFixed(1) + 'M';
                            } else if (value >= 1000) {
                                return 'N' + (value / 1000).toFixed(1) + 'K';
                            } else {
                                return 'N' + value;
                            }
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Revenue period change handler
    document.getElementById('revenue-period').addEventListener('change', function() {
        const months = this.value;
        showToast(`Loading data for last ${months} months...`, 'info');
        // In real app, this would reload chart data
    });
});
</script>

<!-- Load Notes Component JavaScript (anti-scattering compliant) -->
<?php
ComponentRegistry::load('notes-component');
echo NotesComponent::renderScript();
?>

<?php
// Helper function for formatting activity time
function formatActivityTime($timestamp) {
    $time = strtotime($timestamp);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' minutes ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hours ago';
    } else {
        return date('M j, Y', $time);
    }
}
?>
