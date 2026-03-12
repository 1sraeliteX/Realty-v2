<?php
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

<!-- Page Header with Breadcrumb -->
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/admin/dashboard" class="text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400 inline-flex items-center text-sm font-medium">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500 dark:text-gray-400 md:ml-2 text-sm font-medium">Reports</span>
                </div>
            </li>
        </ol>
    </nav>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-4">Dashboard Reports</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-1">Comprehensive analytics and insights for your property management business</p>
</div>

<!-- Reports Overview Stats -->
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

<!-- Financial Reports Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
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

    <!-- Occupancy Analytics -->
    <?php 
    echo UIComponents::card(
        '<div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Occupancy Rate</span>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">' . $stats['occupancy_rate'] . '%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-primary-600 h-2 rounded-full" style="width: ' . $stats['occupancy_rate'] . '%"></div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">' . $stats['occupied_units'] . '</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Occupied Units</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">' . ($stats['total_units'] - $stats['occupied_units']) . '</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Vacant Units</div>
                </div>
            </div>
        </div>',
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Occupancy Analytics</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Property Performance Reports -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Recent Properties Performance -->
    <?php 
    $propertiesContent = '<div class="space-y-3">';
    if (empty($recentProperties)) {
        $propertiesContent .= '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No properties found</p>';
    } else {
        foreach ($recentProperties as $property) {
            $propertiesContent .= '
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-building text-primary-600 dark:text-primary-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">' . htmlspecialchars($property['name']) . '</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">' . htmlspecialchars(isset($property['unit_count']) ? $property['unit_count'] : 0) . ' units</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">' . (isset($property['occupied_units']) ? $property['occupied_units'] : 0) . '/' . (isset($property['unit_count']) ? $property['unit_count'] : 0) . '</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">occupied</div>
                    </div>
                </div>';
        }
    }
    $propertiesContent .= '</div>
        <div class="mt-4 text-center">
            <a href="/admin/properties" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all properties →</a>
        </div>';
    
    echo UIComponents::card(
        $propertiesContent,
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Property Performance</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>

    <!-- Maintenance Summary -->
    <?php 
    $maintenanceContent = '<div class="space-y-3">';
    if (empty($maintenanceRequests)) {
        $maintenanceContent .= '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No maintenance requests</p>';
    } else {
        $priorityCounts = array('high' => 0, 'medium' => 0, 'low' => 0);
        foreach ($maintenanceRequests as $request) {
            if (isset($priorityCounts[$request['priority']])) {
                $priorityCounts[$request['priority']]++;
            }
        }
        $maintenanceContent .= '
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">High Priority</span>
                        <span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs font-medium px-2 py-1 rounded">' . $priorityCounts['high'] . '</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Medium Priority</span>
                        <span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 text-xs font-medium px-2 py-1 rounded">' . $priorityCounts['medium'] . '</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Low Priority</span>
                        <span class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs font-medium px-2 py-1 rounded">' . $priorityCounts['low'] . '</span>
                    </div>
                </div>';
    }
    $maintenanceContent .= '</div>
        <div class="mt-4 text-center">
            <a href="/admin/maintenance" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all maintenance →</a>
        </div>';
    
    echo UIComponents::card(
        $maintenanceContent,
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Maintenance Summary</h3>',
        '<span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs font-medium px-2.5 py-0.5 rounded-full">' . $stats['pending_maintenance'] . ' pending</span>',
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>

    <!-- Application Pipeline -->
    <?php 
    $applicationContent = '<div class="space-y-3">';
    if (empty($newApplications)) {
        $applicationContent .= '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No applications</p>';
    } else {
        $statusCounts = array('pending' => 0, 'under_review' => 0, 'approved' => 0, 'rejected' => 0);
        foreach ($newApplications as $application) {
            if (isset($statusCounts[$application['status']])) {
                $statusCounts[$application['status']]++;
            }
        }
        $applicationContent .= '
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                        <span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 text-xs font-medium px-2 py-1 rounded">' . $statusCounts['pending'] . '</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Under Review</span>
                        <span class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs font-medium px-2 py-1 rounded">' . $statusCounts['under_review'] . '</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Approved</span>
                        <span class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs font-medium px-2 py-1 rounded">' . $statusCounts['approved'] . '</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Rejected</span>
                        <span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs font-medium px-2 py-1 rounded">' . $statusCounts['rejected'] . '</span>
                    </div>
                </div>';
    }
    $applicationContent .= '</div>
        <div class="mt-4 text-center">
            <a href="/admin/tenants-occupants" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all applications →</a>
        </div>';
    
    echo UIComponents::card(
        $applicationContent,
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Application Pipeline</h3>',
        '<span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 text-xs font-medium px-2.5 py-0.5 rounded-full">' . $stats['new_applications'] . ' pending</span>',
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Recent Activities -->
<div class="mb-8">
    <?php 
    $activitiesContent = '<div class="space-y-3">';
    if (empty($activities)) {
        $activitiesContent .= '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent activities</p>';
    } else {
        foreach ($activities as $activity) {
            $activitiesContent .= '
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-' . getActivityIcon($activity['action']) . ' text-primary-600 dark:text-primary-400 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">' . htmlspecialchars($activity['description']) . '</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">' . formatActivityTime($activity['created_at']) . '</p>
                        </div>
                    </div>
                </div>';
        }
    }
    $activitiesContent .= '</div>';
    
    echo UIComponents::card(
        $activitiesContent,
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activities</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Export Options -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Export Reports</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <button onclick="exportReport('pdf')" class="flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
            <i class="fas fa-file-pdf mr-2"></i>
            Export as PDF
        </button>
        <button onclick="exportReport('excel')" class="flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
            <i class="fas fa-file-excel mr-2"></i>
            Export as Excel
        </button>
        <button onclick="exportReport('csv')" class="flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
            <i class="fas fa-file-csv mr-2"></i>
            Export as CSV
        </button>
    </div>
</div>

<!-- Helper Functions -->
<?php
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

function getActivityIcon($action) {
    $icons = array(
        'create' => 'plus',
        'update' => 'edit',
        'delete' => 'trash',
        'login' => 'right-to-bracket',
        'logout' => 'right-from-bracket',
        'view' => 'eye',
        'export' => 'download',
        'upload' => 'upload',
        'payment' => 'credit-card',
        'invoice' => 'file-invoice',
        'tenant' => 'user',
        'property' => 'home',
        'unit' => 'building',
        'maintenance' => 'tools'
    );
    
    return isset($icons[$action]) ? $icons[$action] : 'circle';
}
?>

<!-- Chart.js Script -->
<script src="/assets/js/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = <?php echo json_encode($revenueData); ?>;

const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: Object.keys(revenueData),
        datasets: [{
            label: 'Revenue',
            data: Object.values(revenueData),
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
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'N' + (value / 1000000).toFixed(1) + 'M';
                    }
                }
            }
        }
    }
});

// Export functions
function exportReport(format) {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';
    button.disabled = true;
    
    // Simulate export process
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Show success message
        showToast('Report exported successfully as ' + format.toUpperCase(), 'success');
    }, 2000);
}

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-white'
    };
    
    toast.className += ' ' + colors[type];
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
</script>
