<?php
$title = 'Super Admin Dashboard';
$pageTitle = 'Platform Overview';

// Initialize variables with default values to prevent undefined variable errors
$stats = $stats ?? [
    'total_admins' => 0,
    'total_properties' => 0,
    'active_subscriptions' => 0,
    'platform_revenue' => 0
];

$recentAdmins = $recentAdmins ?? [];
$recentActivities = $recentActivities ?? [];

$content = ob_start();
?>

<!-- Super Admin Tabs -->
<div class="flex space-x-1 mb-6">
    <button class="px-4 py-2 bg-primary-600 text-white rounded-lg font-medium">Super Admin</button>
    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600">Dev Super Admin</button>
</div>

<!-- Platform Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                <i class="fas fa-user-shield text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Admins</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['total_admins']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-primary-100 dark:bg-primary-900 rounded-lg p-3">
                <i class="fas fa-building text-primary-600 dark:text-primary-400 text-xl"></i>
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
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Subscriptions</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['active_subscriptions']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                <i class="fas fa-dollar-sign text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Platform Revenue</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" data-currency data-value="<?php echo $stats['platform_revenue']; ?>"><?php echo '$' . number_format($stats['platform_revenue'], 2); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Currency Settings -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Currency Settings</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Change the default currency for the platform</p>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Currency</label>
                <select id="currency-select" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="USD">USD - US Dollar ($)</option>
                    <option value="NGN">NGN - Nigerian Naira (₦)</option>
                    <option value="EUR">EUR - Euro (€)</option>
                    <option value="GBP">GBP - British Pound (£)</option>
                    <option value="JPY">JPY - Japanese Yen (¥)</option>
                    <option value="CNY">CNY - Chinese Yuan (¥)</option>
                    <option value="INR">INR - Indian Rupee (₹)</option>
                    <option value="CAD">CAD - Canadian Dollar (C$)</option>
                    <option value="AUD">AUD - Australian Dollar (A$)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency Symbol Position</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="symbol_position" value="before" class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Before (₦100)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="symbol_position" value="after" checked class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">After (100₦)</span>
                    </label>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Current: <span id="current-currency" class="font-medium text-gray-900 dark:text-white">USD</span>
                </div>
                <button onclick="saveCurrencySettings()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </div>
        </div>
    </div>

    <!-- Export Data Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Export Data</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Download platform data in various formats</p>
        <div class="flex space-x-3">
            <a href="/superadmin/export?format=json" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-download mr-2"></i>Export JSON
            </a>
            <a href="/superadmin/export?format=csv" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-file-csv mr-2"></i>Export CSV
            </a>
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
    <!-- Recent Admins -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Admins</h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentAdmins)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-user-shield text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No admins yet</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentAdmins as $adminItem): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-medium"><?php echo strtoupper(substr($adminItem['name'], 0, 1)); ?></span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($adminItem['name']); ?></h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($adminItem['email']); ?></p>
                                    <?php if ($adminItem['business_name']): ?>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($adminItem['business_name']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $adminItem['role'] === 'super_admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'; ?>">
                                    <?php echo ucfirst($adminItem['role']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="/superadmin/admins" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">View all admins →</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentActivities)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-clock text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-plus text-primary-600 dark:text-primary-400 text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    New admin registered: <?php echo htmlspecialchars($activity['description']); ?>
                                </p>
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
echo ViewManager::render('superadmin.superadmin_layout', ['content' => $content]);
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?php echo json_encode($revenueData ?? []); ?>;
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
    const occupiedUnits = <?php echo $stats['occupied_units'] ?? 0; ?>;
    const totalUnits = <?php echo $stats['total_units'] ?? 0; ?>;
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

    // Currency Management
    const currencySettings = {
        currencies: {
            'USD': { symbol: '$', name: 'US Dollar', position: 'before' },
            'NGN': { symbol: '₦', name: 'Nigerian Naira', position: 'before' },
            'EUR': { symbol: '€', name: 'Euro', position: 'before' },
            'GBP': { symbol: '£', name: 'British Pound', position: 'before' },
            'JPY': { symbol: '¥', name: 'Japanese Yen', position: 'before' },
            'CNY': { symbol: '¥', name: 'Chinese Yuan', position: 'before' },
            'INR': { symbol: '₹', name: 'Indian Rupee', position: 'before' },
            'CAD': { symbol: 'C$', name: 'Canadian Dollar', position: 'before' },
            'AUD': { symbol: 'A$', name: 'Australian Dollar', position: 'before' }
        },
        currentCurrency: localStorage.getItem('app_currency') || 'USD',
        symbolPosition: localStorage.getItem('symbol_position') || 'after'
    };

    function initializeCurrencySettings() {
        document.getElementById('currency-select').value = currencySettings.currentCurrency;
        document.getElementById('current-currency').textContent = currencySettings.currentCurrency;
        
        // Set radio button for symbol position
        const radios = document.querySelectorAll('input[name="symbol_position"]');
        radios.forEach(radio => {
            radio.checked = radio.value === currencySettings.symbolPosition;
        });
        
        updateCurrencyDisplay();
    }

    function saveCurrencySettings() {
        const selectedCurrency = document.getElementById('currency-select').value;
        const selectedPosition = document.querySelector('input[name="symbol_position"]:checked').value;
        
        localStorage.setItem('app_currency', selectedCurrency);
        localStorage.setItem('symbol_position', selectedPosition);
        
        currencySettings.currentCurrency = selectedCurrency;
        currencySettings.symbolPosition = selectedPosition;
        
        updateCurrencyDisplay();
        showToast(`Currency changed to ${currencySettings.currencies[selectedCurrency].name}`, 'success');
    }

    function updateCurrencyDisplay() {
        const currency = currencySettings.currencies[currencySettings.currentCurrency];
        document.getElementById('current-currency').textContent = `${currencySettings.currentCurrency} - ${currency.name}`;
        
        // Update all currency displays on the page
        updateAllCurrencySymbols();
    }

    function updateAllCurrencySymbols() {
        const currency = currencySettings.currencies[currencySettings.currentCurrency];
        const symbol = currency.symbol;
        const position = currencySettings.symbolPosition;
        
        // Update revenue displays
        const revenueElements = document.querySelectorAll('[data-currency]');
        revenueElements.forEach(element => {
            const value = element.getAttribute('data-value') || element.textContent;
            const formattedValue = position === 'before' ? `${symbol}${value}` : `${value}${symbol}`;
            element.textContent = formattedValue;
        });
    }

    function formatCurrency(amount, currencyCode = null) {
        const code = currencyCode || currencySettings.currentCurrency;
        const currency = currencySettings.currencies[code];
        const symbol = currency.symbol;
        const position = currencySettings.symbolPosition;
        
        const formattedAmount = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
        
        return position === 'before' ? `${symbol}${formattedAmount}` : `${formattedAmount}${symbol}`;
    }

    // Initialize currency settings on page load
    initializeCurrencySettings();
});
</script>
