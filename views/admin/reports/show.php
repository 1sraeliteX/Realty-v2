<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Report Details';
$pageTitle = 'Report Details';
$pageDescription = 'View comprehensive report information and analytics';

// Mock report data
$report = [
    'id' => 1,
    'name' => 'Monthly Financial Report - January 2024',
    'description' => 'Comprehensive financial report for January 2024 including income, expenses, profit analysis, and key performance indicators.',
    'type' => 'financial',
    'category' => 'monthly',
    'period_start' => '2024-01-01',
    'period_end' => '2024-01-31',
    'generated_date' => '2024-02-05 10:30:00',
    'generated_by' => 'Admin User',
    'status' => 'completed',
    'format' => 'pdf',
    'file_size' => 2456789,
    'file_path' => '/uploads/reports/monthly_financial_jan_2024.pdf',
    'auto_generate' => true,
    'recipients' => [
        ['name' => 'Property Manager', 'email' => 'manager@cornerstone.com'],
        ['name' => 'Accounting Department', 'email' => 'accounting@cornerstone.com']
    ],
    'metrics' => [
        'total_income' => 45000,
        'total_expenses' => 28000,
        'net_profit' => 17000,
        'occupancy_rate' => 92.5,
        'total_properties' => 5,
        'total_units' => 120,
        'occupied_units' => 111
    ]
];

// Mock report sections
$sections = [
    [
        'title' => 'Executive Summary',
        'description' => 'Overview of key financial metrics and performance indicators for January 2024.',
        'charts' => true
    ],
    [
        'title' => 'Income Analysis',
        'description' => 'Detailed breakdown of all income sources including rent, fees, and other revenue.',
        'charts' => true
    ],
    [
        'title' => 'Expense Analysis',
        'description' => 'Comprehensive analysis of operating expenses, maintenance costs, and capital expenditures.',
        'charts' => true
    ],
    [
        'title' => 'Property Performance',
        'description' => 'Individual property performance metrics and comparative analysis.',
        'charts' => true
    ],
    [
        'title' => 'Tenant Analysis',
        'description' => 'Tenant demographics, lease expirations, and payment patterns.',
        'charts' => false
    ]
];

// Mock related reports
$relatedReports = [
    ['id' => 2, 'name' => 'December 2023 Financial Report', 'type' => 'financial', 'date' => '2024-01-05', 'status' => 'completed'],
    ['id' => 3, 'name' => 'Q4 2023 Quarterly Report', 'type' => 'quarterly', 'date' => '2024-01-10', 'status' => 'completed'],
    ['id' => 4, 'name' => 'Annual Report 2023', 'type' => 'annual', 'date' => '2024-01-15', 'status' => 'completed'],
];

ob_start();
?>

<!-- Report Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($report['name']); ?></h1>
                <p class="text-purple-100"><?php echo ucfirst($report['type']); ?> Report • <?php echo ucfirst($report['category']); ?></p>
                <p class="text-purple-100"><?php echo date('M j, Y', strtotime($report['period_start'])); ?> - <?php echo date('M j, Y', strtotime($report['period_end'])); ?></p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-purple-100 text-sm">Generated</p>
                    <p class="text-lg font-bold text-white"><?php echo date('M j, Y', strtotime($report['generated_date'])); ?></p>
                </div>
                <?php echo UIComponents::badge(ucfirst($report['status']), $report['status'] === 'completed' ? 'success' : 'warning', 'large'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Report Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Total Income', '$' . number_format($report['metrics']['total_income']), 'dollar-sign', 'green', '', 'Revenue period'); ?>
    <?php echo UIComponents::statCard('Total Expenses', '$' . number_format($report['metrics']['total_expenses']), 'receipt', 'red', '', 'Operating costs'); ?>
    <?php echo UIComponents::statCard('Net Profit', '$' . number_format($report['metrics']['net_profit']), 'chart-line', 'blue', '', 'Profit margin: ' . round(($report['metrics']['net_profit'] / $report['metrics']['total_income']) * 100, 1) . '%'); ?>
    <?php echo UIComponents::statCard('Occupancy Rate', $report['metrics']['occupancy_rate'] . '%', 'home', 'purple', '', $report['metrics']['occupied_units'] . '/' . $report['metrics']['total_units'] . ' units'); ?>
</div>

<!-- Report Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Report Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Report Information</h3>
            
            <div class="space-y-4">
                <!-- Description -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Description</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($report['description']); ?></p>
                </div>

                <!-- Report Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Report Details</h4>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Type</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo ucfirst($report['type']); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Category</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo ucfirst($report['category']); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Format</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo strtoupper($report['format']); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">File Size</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo number_format($report['file_size'] / 1024 / 1024, 2); ?> MB</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Period Information</h4>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Start Date</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($report['period_start'])); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">End Date</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($report['period_end'])); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Generated Date</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo date('M j, Y H:i', strtotime($report['generated_date'])); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Generated By</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo htmlspecialchars($report['generated_by']); ?></dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Auto Generate -->
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Auto Generate</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">This report is automatically generated monthly</p>
                    </div>
                    <div><?php echo UIComponents::badge($report['auto_generate'] ? 'Enabled' : 'Disabled', $report['auto_generate'] ? 'success' : 'secondary'); ?></div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex space-x-4">
                <?php echo UIComponents::button('Download Report', 'primary', 'medium', '#', 'download'); ?>
                <?php echo UIComponents::button('Print Report', 'info', 'medium', '#', 'print'); ?>
                <?php echo UIComponents::button('Share Report', 'secondary', 'medium', '#', 'share'); ?>
                <?php echo UIComponents::button('Regenerate', 'warning', 'medium', '#', 'sync'); ?>
            </div>
        </div>

        <!-- Report Sections -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Report Sections</h3>
            
            <div class="space-y-4">
                <?php foreach ($sections as $index => $section): ?>
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white"><?php echo ($index + 1) . '. ' . htmlspecialchars($section['title']); ?></h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?php echo htmlspecialchars($section['description']); ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if ($section['charts']): ?>
                                    <i class="fas fa-chart-bar text-blue-500" title="Contains charts"></i>
                                <?php endif; ?>
                                <button class="text-primary-600 hover:text-primary-900 dark:text-primary-400">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Key Performance Metrics</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Income</p>
                            <p class="text-lg font-bold text-green-600 dark:text-green-400">$<?php echo number_format($report['metrics']['total_income']); ?></p>
                        </div>
                        <i class="fas fa-arrow-up text-green-500"></i>
                    </div>
                </div>
                
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Expenses</p>
                            <p class="text-lg font-bold text-red-600 dark:text-red-400">$<?php echo number_format($report['metrics']['total_expenses']); ?></p>
                        </div>
                        <i class="fas fa-arrow-down text-red-500"></i>
                    </div>
                </div>
                
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Net Profit</p>
                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">$<?php echo number_format($report['metrics']['net_profit']); ?></p>
                        </div>
                        <i class="fas fa-chart-line text-blue-500"></i>
                    </div>
                </div>
                
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Occupancy Rate</p>
                            <p class="text-lg font-bold text-purple-600 dark:text-purple-400"><?php echo $report['metrics']['occupancy_rate']; ?>%</p>
                        </div>
                        <i class="fas fa-home text-purple-500"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Distribution List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribution List</h3>
            
            <div class="space-y-3">
                <?php foreach ($report['recipients'] as $recipient): ?>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($recipient['name']); ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($recipient['email']); ?></p>
                        </div>
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-4">
                <?php echo UIComponents::button('Add Recipient', 'primary', 'small', '#', 'user-plus'); ?>
            </div>
        </div>

        <!-- Related Reports -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Reports</h3>
            
            <div class="space-y-3">
                <?php foreach ($relatedReports as $related): ?>
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($related['name']); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y', strtotime($related['date'])); ?></p>
                            </div>
                            <a href="/admin/reports/<?php echo $related['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <?php echo UIComponents::button('Schedule Report', 'success', 'full', '#', 'calendar'); ?>
                <?php echo UIComponents::button('Create Template', 'info', 'full', '#', 'file-alt'); ?>
                <?php echo UIComponents::button('Export Data', 'warning', 'full', '#', 'database'); ?>
                <?php echo UIComponents::button('Archive Report', 'secondary', 'full', '#', 'archive'); ?>
            </div>
        </div>
    </div>
</div>

<script>
// Download report
function downloadReport() {
    showToast('Downloading report...', 'info');
    setTimeout(() => {
        showToast('Report downloaded successfully!', 'success');
    }, 2000);
}

// Print report
function printReport() {
    window.print();
}

// Share report
function shareReport() {
    showToast('Opening share dialog...', 'info');
}

// Regenerate report
function regenerateReport() {
    if (confirm('Regenerate this report? This may take a few minutes.')) {
        showToast('Regenerating report...', 'info');
        setTimeout(() => {
            showToast('Report regenerated successfully!', 'success');
            location.reload();
        }, 3000);
    }
}

// Schedule report
function scheduleReport() {
    showToast('Opening schedule dialog...', 'info');
}

// Create template
function createTemplate() {
    if (confirm('Create a template from this report?')) {
        showToast('Creating template...', 'info');
        setTimeout(() => {
            showToast('Template created successfully!', 'success');
        }, 2000);
    }
}

// Export data
function exportData() {
    showToast('Exporting report data...', 'info');
    setTimeout(() => {
        showToast('Data exported successfully!', 'success');
    }, 2000);
}

// Archive report
function archiveReport() {
    if (confirm('Archive this report? It will be moved to the archive folder.')) {
        showToast('Archiving report...', 'info');
        setTimeout(() => {
            showToast('Report archived successfully!', 'success');
        }, 2000);
    }
}
</script>

<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
