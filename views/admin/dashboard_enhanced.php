<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Admin Dashboard';
$pageTitle = 'Dashboard Overview';
$pageDescription = 'Get a comprehensive overview of your property management business';

// Mock data for dashboard
$stats = [
    'total_properties' => 24,
    'total_units' => 156,
    'active_tenants' => 142,
    'occupancy_rate' => 91,
    'monthly_revenue' => 124500.00,
    'occupied_units' => 142,
    'pending_payments' => 8,
    'maintenance_requests' => 12,
    'new_applications' => 5
];

$recentProperties = [
    [
        'id' => 1,
        'name' => 'Sunset Apartments',
        'address' => '123 Main St, Los Angeles, CA',
        'type' => 'Residential',
        'status' => 'occupied',
        'unit_count' => 24,
        'occupied_units' => 22,
        'image' => 'https://picsum.photos/seed/prop1/400/300.jpg'
    ],
    [
        'id' => 2,
        'name' => 'Downtown Plaza',
        'address' => '456 Oak Ave, Los Angeles, CA',
        'type' => 'Commercial',
        'status' => 'available',
        'unit_count' => 12,
        'occupied_units' => 8,
        'image' => 'https://picsum.photos/seed/prop2/400/300.jpg'
    ],
    [
        'id' => 3,
        'name' => 'Riverside Complex',
        'address' => '789 River Rd, Los Angeles, CA',
        'type' => 'Residential',
        'status' => 'maintenance',
        'unit_count' => 36,
        'occupied_units' => 34,
        'image' => 'https://picsum.photos/seed/prop3/400/300.jpg'
    ]
];

$recentActivities = [
    [
        'id' => 1,
        'action' => 'payment',
        'description' => 'John Smith paid rent for Unit 3A',
        'property_name' => 'Sunset Apartments',
        'created_at' => '2024-01-15 10:30:00'
    ],
    [
        'id' => 2,
        'action' => 'maintenance',
        'description' => 'Maintenance request submitted for Unit 5B',
        'property_name' => 'Downtown Plaza',
        'created_at' => '2024-01-15 09:15:00'
    ],
    [
        'id' => 3,
        'action' => 'tenant',
        'description' => 'New tenant application received',
        'property_name' => 'Riverside Complex',
        'created_at' => '2024-01-15 08:45:00'
    ],
    [
        'id' => 4,
        'action' => 'create',
        'description' => 'New property added: Garden View Homes',
        'property_name' => null,
        'created_at' => '2024-01-14 16:20:00'
    ]
];

$revenueData = [
    '2024-01' => 124500,
    '2023-12' => 118200,
    '2023-11' => 115800,
    '2023-10' => 119400,
    '2023-09' => 122100,
    '2023-08' => 120600,
    '2023-07' => 117900,
    '2023-06' => 116400,
    '2023-05' => 114300,
    '2023-04' => 112800,
    '2023-03' => 109500,
    '2023-02' => 107400
];

ob_start();
?>

<!-- Stats Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php echo UIComponents::statsCard('Total Properties', $stats['total_properties'], 'home', 12.5, 'primary'); ?>
    <?php echo UIComponents::statsCard('Total Units', $stats['total_units'], 'door-open', 8.3, 'blue'); ?>
    <?php echo UIComponents::statsCard('Active Tenants', $stats['active_tenants'], 'users', 15.2, 'green'); ?>
    <?php echo UIComponents::statsCard('Occupancy Rate', $stats['occupancy_rate'] . '%', 'percentage', 2.1, 'yellow'); ?>
</div>

<!-- Secondary Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php echo UIComponents::statsCard('Monthly Revenue', '$' . number_format($stats['monthly_revenue'], 0), 'dollar-sign', 8.7, 'green'); ?>
    <?php echo UIComponents::statsCard('Occupied Units', $stats['occupied_units'], 'check-circle', 3.4, 'blue'); ?>
    <?php echo UIComponents::statsCard('Pending Payments', $stats['pending_payments'], 'exclamation-triangle', -25.0, 'red'); ?>
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
            <a href="/admin/properties/create" class="inline-flex items-center px-4 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-plus-circle text-blue-600 dark:text-blue-400 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Add Property</span>
            </a>
            <a href="/admin/tenants/create" class="inline-flex items-center px-4 py-2 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-user-plus text-green-600 dark:text-green-400 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Add Tenant</span>
            </a>
            <a href="/admin/payments/create" class="inline-flex items-center px-4 py-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-file-invoice-dollar text-yellow-600 dark:text-yellow-400 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Record Payment</span>
            </a>
            <a href="/admin/maintenance" class="inline-flex items-center px-4 py-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-tools text-purple-600 dark:text-purple-400 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Maintenance</span>
            </a>
            <a href="/admin/invoices/create" class="inline-flex items-center px-4 py-2 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-file-invoice text-red-600 dark:text-red-400 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Create Invoice</span>
            </a>
            <a href="/admin/reports" class="inline-flex items-center px-4 py-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-chart-bar text-indigo-600 dark:text-indigo-400 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reports</span>
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
            $statusColor = $property['status'] === 'occupied' ? 'success' : ($property['status'] === 'available' ? 'info' : 'warning');
            $propertiesContent .= "
                <div class=\"flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer\" onclick=\"window.location.href='/admin/dashboard/properties/{$property['id']}'\">
                    <div class=\"flex items-center space-x-4\">
                        <img src=\"{$property['image']}\" alt=\"{$property['name']}\" class=\"w-16 h-16 rounded-lg object-cover\">
                        <div>
                            <h4 class=\"text-sm font-semibold text-gray-900 dark:text-white\">{$property['name']}</h4>
                            <p class=\"text-xs text-gray-500 dark:text-gray-400\">{$property['address']}</p>
                            <div class=\"flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400 space-x-3\">
                                <span><i class=\"fas fa-door-open mr-1\"></i>{$property['unit_count']} units</span>
                                <span><i class=\"fas fa-users mr-1\"></i>{$property['occupied_units']} occupied</span>
                            </div>
                        </div>
                    </div>
                    <div class=\"text-right\">
                        " . UIComponents::badge(ucfirst($property['status']), $statusColor, 'small') . "
                        <div class=\"mt-2 text-xs text-gray-500 dark:text-gray-400\">{$property['type']}</div>
                    </div>
                </div>
            ";
        }
        $propertiesContent .= '</div>';
        $propertiesContent .= '<div class="mt-4 text-center"><a href="/admin/dashboard/properties" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all properties →</a></div>';
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
    if (empty($recentActivities)) {
        $activitiesContent = '<p class="text-gray-500 dark:text-gray-400 text-center py-8">No recent activities</p>';
    } else {
        $activitiesContent = '<div class="space-y-4">';
        foreach ($recentActivities as $activity) {
            $icon = $activity['action'] === 'payment' ? 'credit-card' : 
                   ($activity['action'] === 'maintenance' ? 'tools' : 
                   ($activity['action'] === 'tenant' ? 'user' : 
                   ($activity['action'] === 'create' ? 'plus' : 'circle')));
            $time = $activity['created_at'];
            $activitiesContent .= "
                <div class=\"flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors\">
                    <div class=\"flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center\">
                        <i class=\"fas fa-$icon text-primary-600 dark:text-primary-400 text-xs\"></i>
                    </div>
                    <div class=\"flex-1 min-w-0\">
                        <p class=\"text-sm text-gray-900 dark:text-white\">{$activity['description']}</p>";
            if ($activity['property_name']) {
                $activitiesContent .= "<p class=\"text-xs text-gray-500 dark:text-gray-400 mt-1\">Property: {$activity['property_name']}</p>";
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
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
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
            <a href="/admin/dashboard/maintenance" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all requests →</a>
        </div>',
        '<div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Maintenance Requests</h3>
            <span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs font-medium px-2.5 py-0.5 rounded-full">' . $stats['maintenance_requests'] . ' pending</span>
        </div>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
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
            <a href="/admin/dashboard/applications" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all applications →</a>
        </div>',
        '<div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">New Applications</h3>
            <span class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs font-medium px-2.5 py-0.5 rounded-full">' . $stats['new_applications'] . ' pending</span>
        </div>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>

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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + (value / 1000) + 'k';
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
</script>

<?php
$content = ob_get_clean();
include 'dashboard_layout.php';
?>
