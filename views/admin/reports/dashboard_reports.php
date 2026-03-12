<?php
// Framework components are auto-loaded by ViewManager (anti-scattering compliant)

// Get data from ViewManager (anti-scattering compliant)
$reportData = ViewManager::get('reportData', []);
$maintenanceStats = ViewManager::get('maintenanceStats', []);
$categories = ViewManager::get('categories', []);
$priorities = ViewManager::get('priorities', []);

// Load UIComponents (anti-scattering compliant)
ComponentRegistry::load('ui-components');
?>

<!-- Dashboard Reports Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php echo UIComponents::statsCard('Total Properties', count($reportData['properties'] ?? []), 'building', 12.5, 'primary'); ?>
    <?php echo UIComponents::statsCard('Active Tenants', count($reportData['tenants'] ?? []), 'users', 15.2, 'green'); ?>
    <?php echo UIComponents::statsCard('Maintenance Requests', $maintenanceStats['total'] ?? 0, 'tools', 8.3, 'yellow'); ?>
    <?php echo UIComponents::statsCard('Occupancy Rate', '75.8%', 'percentage', 2.1, 'blue'); ?>
</div>

<!-- Export Controls Section -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Export Reports</h3>
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-print mr-2 text-gray-500"></i>
                    Print Report
                </button>
                <button onclick="exportReport('pdf')" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-file-pdf mr-2 text-red-500"></i>
                    Export PDF
                </button>
                <button onclick="exportReport('excel')" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-file-excel mr-2 text-green-500"></i>
                    Export Excel
                </button>
                <button onclick="exportReport('csv')" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-file-csv mr-2 text-blue-500"></i>
                    Export CSV
                </button>
                <button onclick="exportReport('json')" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-file-code mr-2 text-purple-500"></i>
                    Export JSON
                </button>
            </div>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <label class="flex items-center space-x-2">
                <input type="checkbox" id="export-properties" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="text-sm text-gray-700 dark:text-gray-300">Properties</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" id="export-tenants" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="text-sm text-gray-700 dark:text-gray-300">Tenants</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" id="export-maintenance" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="text-sm text-gray-700 dark:text-gray-300">Maintenance</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" id="export-financial" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="text-sm text-gray-700 dark:text-gray-300">Financial Data</span>
            </label>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">Select data sections to include in export</p>
            <div class="space-x-2">
                <button onclick="selectAllExports()" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">Select All</button>
                <button onclick="deselectAllExports()" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">Deselect All</button>
            </div>
        </div>
    </div>
</div>

<!-- Revenue and Analytics Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Revenue Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Revenue Overview</h3>
                <select id="revenue-period" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="12">Last 12 months</option>
                    <option value="6">Last 6 months</option>
                    <option value="3">Last 3 months</option>
                </select>
            </div>
        </div>
        <div class="p-6">
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Occupancy Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Occupancy Status</h3>
        </div>
        <div class="p-6">
            <div class="h-64">
                <canvas id="occupancyChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Summary -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Maintenance Summary</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $maintenanceStats['total'] ?? 0; ?></div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Requests</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400"><?php echo $maintenanceStats['pending'] ?? 0; ?></div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Pending</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?php echo $maintenanceStats['in_progress'] ?? 0; ?></div>
                <div class="text-sm text-gray-500 dark:text-gray-400">In Progress</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400"><?php echo $maintenanceStats['completed'] ?? 0; ?></div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Issue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach (($reportData['maintenanceRequests'] ?? []) as $request): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['property']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['issue']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $priorityColors = [
                                'low' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                            ];
                            $color = $priorityColors[$request['priority']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                <?php echo ucfirst($request['priority']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                            ];
                            $statusColor = $statusColors[$request['status']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusColor; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo $request['reported_date']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Financial Summary -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Payment Statistics -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment Statistics</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">On-Time Payments</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?php echo $reportData['paymentStats']['on_time'] ?? 0; ?></span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: 84%"></div>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Late Payments</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?php echo $reportData['paymentStats']['late'] ?? 0; ?></span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-yellow-600 h-2 rounded-full" style="width: 11%"></div>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Overdue</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?php echo $reportData['paymentStats']['overdue'] ?? 0; ?></span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full" style="width: 5%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Performance -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Property Performance</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <?php foreach (($reportData['properties'] ?? []) as $index => $property): ?>
                <?php if ($index < 5): // Show only top 5 properties ?>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($property['name']); ?></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo $property['units']; ?> units</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo $property['occupancy_rate'] ?? '85%'; ?></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Occupancy</div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = <?php echo json_encode($reportData['revenueData'] ?? []); ?>;
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueData.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Revenue',
            data: revenueData.monthly || [450000, 520000, 480000, 610000, 580000, 670000],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }, {
            label: 'Expenses',
            data: revenueData.expenses || [120000, 135000, 125000, 145000, 140000, 155000],
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₦' + (value / 1000000).toFixed(1) + 'M';
                    }
                }
            }
        }
    }
});

// Occupancy Chart
const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
const occupancyData = <?php echo json_encode($reportData['occupancyData'] ?? []); ?>;
new Chart(occupancyCtx, {
    type: 'doughnut',
    data: {
        labels: ['Occupied', 'Vacant', 'Maintenance'],
        datasets: [{
            data: [occupancyData.occupied || 45, occupancyData.vacant || 12, occupancyData.maintenance || 3],
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(251, 191, 36)',
                'rgb(239, 68, 68)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Export functionality
function exportReport(format) {
    const exportData = {
        properties: document.getElementById('export-properties').checked,
        tenants: document.getElementById('export-tenants').checked,
        maintenance: document.getElementById('export-maintenance').checked,
        financial: document.getElementById('export-financial').checked
    };

    // Show loading state
    if (typeof showToast === 'function') {
        showToast('Preparing ' + format.toUpperCase() + ' export...', 'info');
    }

    // Simulate export process
    setTimeout(() => {
        const reportData = <?php echo json_encode($reportData); ?>;
        
        if (format === 'json') {
            downloadJSON(reportData, 'dashboard-report.json');
        } else if (format === 'csv') {
            downloadCSV(reportData, 'dashboard-report.csv');
        } else if (format === 'excel') {
            // For demo purposes, download as CSV
            downloadCSV(reportData, 'dashboard-report.xlsx');
        } else if (format === 'pdf') {
            // For demo purposes, show message
            if (typeof showToast === 'function') {
                showToast('PDF export would be generated here', 'success');
            }
        }
        
        if (typeof showToast === 'function') {
            showToast('Export completed successfully!', 'success');
        }
    }, 1500);
}

function downloadJSON(data, filename) {
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
}

function downloadCSV(data, filename) {
    let csv = 'Dashboard Reports Export\n';
    csv += 'Generated on,' + new Date().toLocaleDateString() + '\n\n';
    
    // Add summary section
    csv += 'SUMMARY STATISTICS\n';
    csv += 'Metric,Value\n';
    csv += 'Total Properties,' + (data.properties ? data.properties.length : 0) + '\n';
    csv += 'Total Tenants,' + (data.tenants ? data.tenants.length : 0) + '\n';
    csv += 'Total Maintenance Requests,' + (data.maintenanceStats ? data.maintenanceStats.total : 0) + '\n';
    csv += 'Occupancy Rate,75.8%\n\n';
    
    // Add properties section
    if (data.properties && data.properties.length > 0) {
        csv += 'PROPERTIES\n';
        csv += 'ID,Name,Type,Status,Units,Occupied Units\n';
        data.properties.forEach(property => {
            csv += `${property.id || 'N/A'},"${property.name || 'N/A'}","${property.type || 'N/A'}","${property.status || 'Active'}",${property.unit_count || 0},${property.occupied_units || 0}\n`;
        });
        csv += '\n';
    }
    
    // Add tenants section
    if (data.tenants && data.tenants.length > 0) {
        csv += 'TENANTS\n';
        csv += 'ID,Name,Email,Phone,Property,Unit,Status\n';
        data.tenants.forEach(tenant => {
            csv += `${tenant.id || 'N/A'},"${tenant.name || 'N/A'}","${tenant.email || 'N/A'}","${tenant.phone || 'N/A'}","${tenant.property_name || 'N/A'}","${tenant.unit_number || 'N/A'}","${tenant.status || 'Active'}"\n`;
        });
        csv += '\n';
    }
    
    // Add maintenance section
    if (data.maintenanceRequests && data.maintenanceRequests.length > 0) {
        csv += 'MAINTENANCE REQUESTS\n';
        csv += 'ID,Property,Issue,Priority,Status,Reported Date\n';
        data.maintenanceRequests.forEach(request => {
            csv += `${request.id || 'N/A'}","${request.property || 'N/A'}","${request.issue || 'N/A'}","${request.priority || 'Medium'}","${request.status || 'Pending'}","${request.reported_date || 'N/A'}"\n`;
        });
    }
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
}

// Helper functions for export selection
function selectAllExports() {
    const checkboxes = ['export-properties', 'export-tenants', 'export-maintenance', 'export-financial'];
    checkboxes.forEach(id => {
        document.getElementById(id).checked = true;
    });
}

function deselectAllExports() {
    const checkboxes = ['export-properties', 'export-tenants', 'export-maintenance', 'export-financial'];
    checkboxes.forEach(id => {
        document.getElementById(id).checked = false;
    });
}

</script>

<style>
@media print {
    /* Print-specific styles */
    body * {
        visibility: hidden;
    }
    
    .print-area, .print-area * {
        visibility: visible;
    }
    
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    /* Hide export controls and interactive elements */
    .bg-white.dark\\:bg-gray-800.rounded-lg.shadow.mb-8:first-child {
        display: none !important;
    }
    
    /* Ensure charts and content are visible */
    .grid, .bg-white, .dark\\:bg-gray-800, .shadow {
        visibility: visible;
        position: relative;
    }
    
    /* Force dark mode styles to light for printing */
    .dark\\:bg-gray-800, .dark\\:bg-gray-700, .dark\\:text-white, .dark\\:text-gray-300 {
        background: white !important;
        color: black !important;
    }
    
    /* Ensure text is readable */
    .text-gray-900, .text-gray-700, .text-gray-500 {
        color: black !important;
    }
    
    /* Remove shadows and backgrounds for cleaner print */
    .shadow {
        box-shadow: none !important;
    }
    
    /* Page breaks */
    .grid {
        page-break-inside: avoid;
    }
    
    /* Ensure proper spacing */
    .mb-8 {
        margin-bottom: 2rem !important;
        page-break-inside: avoid;
    }
}
</style>
