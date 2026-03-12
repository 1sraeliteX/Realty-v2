<?php
// Initialize framework (anti-scattering compliant)
require_once $_SERVER['DOCUMENT_ROOT'] . '/../config/bootstrap.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$stats = ViewManager::get('stats') ?? DataProvider::get('finance_stats');
$recentTransactions = ViewManager::get('recentTransactions') ?? DataProvider::get('recent_transactions');
$revenueData = ViewManager::get('revenueData') ?? DataProvider::get('revenue_data');
$expenseData = ViewManager::get('expenseData') ?? DataProvider::get('expense_data');
$upcomingPayments = ViewManager::get('upcomingPayments') ?? DataProvider::get('upcoming_payments');
$overduePayments = ViewManager::get('overduePayments') ?? DataProvider::get('overdue_payments');
$user = DataProvider::get('user');
$notifications = DataProvider::get('notifications');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Finance Management');
ViewManager::set('pageTitle', 'Finance Management');
ViewManager::set('pageDescription', 'Monitor revenue, expenses, and financial performance');
ViewManager::set('user', $user);
ViewManager::set('notifications', $notifications);

// Start output buffering for the content
ob_start();
?>

<!-- Financial Overview Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php echo UIComponents::statsCard('Monthly Revenue', '$' . number_format($stats['monthly_revenue'], 0), 'dollar-sign', 12.5, 'green'); ?>
    <?php echo UIComponents::statsCard('Monthly Expenses', '$' . number_format($stats['monthly_expenses'], 0), 'chart-line', -8.2, 'red'); ?>
    <?php echo UIComponents::statsCard('Net Profit', '$' . number_format($stats['net_profit'], 0), 'chart-pie', 18.7, 'blue'); ?>
    <?php echo UIComponents::statsCard('Pending Payments', $stats['pending_payments_count'], 'clock', 0, 'yellow'); ?>
</div>

<!-- Charts and Financial Data -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Revenue vs Expense Chart -->
    <div class="lg:col-span-2">
        <?php 
        echo UIComponents::card(
            '<div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Revenue vs Expenses</h3>
                <div class="flex items-center space-x-2">
                    <button class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">6M</button>
                    <button class="text-sm bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 px-3 py-1 rounded">1Y</button>
                    <button class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">All</button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="financeChart"></canvas>
            </div>',
            null,
            null,
            'bg-white dark:bg-gray-800 rounded-lg shadow'
        ); ?>
    </div>

    <!-- Quick Actions -->
    <?php 
    echo UIComponents::card(
        '<div class="space-y-3">
            <a href="/admin/payments/create" class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-plus text-green-600 dark:text-green-400 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Record Payment</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Add new payment transaction</p>
                </div>
            </a>
            <a href="/admin/invoices/create" class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-file-invoice text-blue-600 dark:text-blue-400 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Create Invoice</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Generate new invoice</p>
                </div>
            </a>
            <a href="/admin/expenses/create" class="flex items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-receipt text-red-600 dark:text-red-400 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Add Expense</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Record new expense</p>
                </div>
            </a>
            <a href="/admin/reports" class="flex items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors cursor-pointer group">
                <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Generate Report</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">View financial reports</p>
                </div>
            </a>
        </div>',
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Recent Transactions and Upcoming Payments -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Recent Transactions -->
    <?php 
    $transactionsContent = '';
    if (empty($recentTransactions)) {
        $transactionsContent = '<div class="text-center py-8">
            <i class="fas fa-exchange-alt text-gray-300 dark:text-gray-600 text-4xl mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400">No recent transactions</p>
        </div>';
    } else {
        $transactionsContent = '<div class="space-y-4">';
        foreach ($recentTransactions as $transaction) {
            $iconClass = $transaction['type'] === 'payment' ? 'fa-arrow-down text-green-600 dark:text-green-400' : 'fa-arrow-up text-red-600 dark:text-red-400';
            $bgClass = $transaction['type'] === 'payment' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900';
            $amountClass = $transaction['amount'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
            
            $transactionsContent .= "
                <div class='flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors'>
                    <div class='flex items-center space-x-3'>
                        <div class='{$bgClass} rounded-full p-2'>
                            <i class='fas {$iconClass} text-sm'></i>
                        </div>
                        <div>
                            <p class='text-sm font-medium text-gray-900 dark:text-white'>" . htmlspecialchars($transaction['description']) . "</p>
                            <p class='text-xs text-gray-500 dark:text-gray-400'>" . date('M j, Y', strtotime($transaction['date'])) . "</p>
                        </div>
                    </div>
                    <div class='text-right'>
                        <p class='text-sm font-medium {$amountClass}'>
                            " . ($transaction['amount'] > 0 ? '+' : '') . "$" . number_format(abs($transaction['amount']), 0) . "
                        </p>
                        <p class='text-xs text-gray-500 dark:text-gray-400'>" . htmlspecialchars($transaction['method']) . "</p>
                    </div>
                </div>
            ";
        }
        $transactionsContent .= '</div>';
        $transactionsContent .= '<div class="mt-4 text-center"><a href="/admin/payments" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all transactions →</a></div>';
    }
    
    echo UIComponents::card(
        $transactionsContent,
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Transactions</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>

    <!-- Upcoming Payments -->
    <?php 
    $paymentsContent = '';
    if (empty($upcomingPayments)) {
        $paymentsContent = '<div class="text-center py-8">
            <i class="fas fa-calendar-check text-gray-300 dark:text-gray-600 text-4xl mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400">No upcoming payments</p>
        </div>';
    } else {
        $paymentsContent = '<div class="space-y-4">';
        foreach ($upcomingPayments as $payment) {
            $paymentsContent .= "
                <div class='flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors'>
                    <div class='flex items-center space-x-3'>
                        " . UIComponents::avatar($payment['tenant_name'], null, 'small') . "
                        <div>
                            <p class='text-sm font-medium text-gray-900 dark:text-white'>" . htmlspecialchars($payment['tenant_name']) . "</p>
                            <p class='text-xs text-gray-500 dark:text-gray-400'>" . htmlspecialchars($payment['property']) . " - " . htmlspecialchars($payment['unit']) . "</p>
                        </div>
                    </div>
                    <div class='text-right'>
                        <p class='text-sm font-medium text-gray-900 dark:text-white'>$" . number_format($payment['amount'], 0) . "</p>
                        <p class='text-xs text-gray-500 dark:text-gray-400'>" . date('M j', strtotime($payment['due_date'])) . "</p>
                    </div>
                </div>
            ";
        }
        $paymentsContent .= '</div>';
        $paymentsContent .= '<div class="mt-4 text-center"><a href="/admin/payments" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">View all payments →</a></div>';
    }
    
    echo UIComponents::card(
        $paymentsContent,
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Upcoming Payments</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Overdue Payments Alert -->
<?php if (!empty($overduePayments)): ?>
<?php 
echo UIComponents::card(
    '<div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="bg-red-100 dark:bg-red-900 rounded-full p-2">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Overdue Payments</h3>
                <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                    ' . count($overduePayments) . ' overdue payments totaling $' . number_format($stats['overdue_payments_total'], 0) . '
                </p>
            </div>
        </div>
        <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
            Send Reminders
        </button>
    </div>',
    null,
    null,
    'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg'
); ?>
<?php endif; ?>

<!-- Chart.js Script -->
<script src="/assets/js/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('financeChart').getContext('2d');
    
    const revenueData = <?php echo json_encode(array_values($revenueData)); ?>;
    const expenseData = <?php echo json_encode(array_values($expenseData)); ?>;
    const labels = <?php echo json_encode(array_values(array_map(function($key) {
        return date('M Y', strtotime($key . '-01'));
    }, array_slice($revenueData, -6)))); ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: revenueData.slice(-6),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4
            }, {
                label: 'Expenses',
                data: expenseData.slice(-6),
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
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php
$content = ob_get_clean();

// Set content for layout (anti-scattering compliant)
ViewManager::set('content', $content);

// Include the dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
