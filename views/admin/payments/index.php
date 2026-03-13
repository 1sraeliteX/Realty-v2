<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$payments = ViewManager::get('payments') ?? DataProvider::get('payments');
$stats = ViewManager::get('stats') ?? DataProvider::get('payment_stats');
$properties = ViewManager::get('properties') ?? DataProvider::get('properties');
$user = DataProvider::get('user');
$notifications = DataProvider::get('notifications');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Payments Management');
ViewManager::set('pageTitle', 'Payments');
ViewManager::set('pageDescription', 'Track and manage all rental payments and transactions');
ViewManager::set('activeMenu', 'payments');
ViewManager::set('user', $user);
ViewManager::set('notifications', $notifications);

// Start output buffering for the content
ob_start();
?>

<!-- Payments Overview -->
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php echo UIComponents::statsCard('Total Revenue', '$' . number_format($stats['total_revenue'], 2), 'dollar-sign', 12.5, 'green'); ?>
        <?php echo UIComponents::statsCard('This Month', '$' . number_format($stats['this_month'], 2), 'calendar', 8.3, 'blue'); ?>
        <?php echo UIComponents::statsCard('Overdue', '$' . number_format($stats['overdue'], 2), 'exclamation-triangle', -5.2, 'red'); ?>
        <?php echo UIComponents::statsCard('Pending', '$' . number_format($stats['pending'], 2), 'clock', 2.1, 'yellow'); ?>
    </div>

    <!-- Filters and Search -->
    <?php 
    echo UIComponents::card(
        '<form id="paymentsFilter" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="searchPayments"
                        name="search"
                        placeholder="Search payments..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property</label>
                <select id="propertyFilter" name="property_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">All Properties</option>
                    <?php foreach ($properties as $property): ?>
                        <option value="<?php echo $property[\'id\']; ?>"><?php echo htmlspecialchars($property[\'name\']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select id="statusFilter" name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Month</label>
                <input 
                    type="month" 
                    id="monthFilter"
                    name="month"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                >
            </div>
        </form>',
        null,
        '<div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filter Payments</h3>
            <button type="button" onclick="resetFilters()" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-times mr-1"></i>Clear Filters
            </button>
        </div>',
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>

    <!-- Payments Table -->
    <?php 
    $tableContent = '';
    if (empty($payments)) {
        $tableContent = '
            <div class="text-center py-12">
                <i class="fas fa-credit-card text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No payments found</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by recording your first payment</p>
                <a href="/admin/payments/create" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i>
                    Record Payment
                </a>
            </div>';
    } else {
        $tableContent = '
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable(\'date\')">
                                Date <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable(\'tenant\')">
                                Tenant <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable(\'amount\')">
                                Amount <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="paymentsTableBody">';
        
        foreach ($payments as $payment) {
            $statusColor = $payment['status'] === 'paid' ? 'success' : 
                          ($payment['status'] === 'pending' ? 'warning' : 
                          ($payment['status'] === 'overdue' ? 'danger' : 'info'));
            
            $tableContent .= '
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        ' . date('M j, Y', strtotime($payment['payment_date'])) . '
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            ' . UIComponents::avatar($payment['tenant_name'], null, 'small') . '
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">' . htmlspecialchars($payment['tenant_name']) . '</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">' . htmlspecialchars($payment['tenant_email'] ?? '') . '</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        ' . htmlspecialchars($payment['property_name']) . ' - ' . htmlspecialchars($payment['unit_number'] ?? 'N/A') . '
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        $' . number_format($payment['amount'], 2) . '
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        <span class="capitalize">' . htmlspecialchars($payment['payment_type']) . '</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ' . UIComponents::badge(ucfirst($payment['status']), $statusColor, 'small') . '
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="/admin/payments/' . $payment['id'] . '" class="text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/payments/' . $payment['id'] . '/edit" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>';
            
            if ($payment['status'] !== 'paid') {
                $tableContent .= '
                            <button onclick="markAsPaid(' . $payment['id'] . ')" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300" title="Mark as Paid">
                                <i class="fas fa-check"></i>
                            </button>';
            }
            
            if ($payment['receipt_count'] > 0) {
                $tableContent .= '
                            <a href="/admin/payments/' . $payment['id'] . '#receipts" class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300" title="View Receipts">
                                <i class="fas fa-file-invoice"></i>
                            </a>';
            }
            
            $tableContent .= '
                            <button onclick="deletePayment(' . $payment['id'] . ')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>';
        }
        
        $tableContent .= '
                    </tbody>
                </table>
            </div>';
    }
    
    echo UIComponents::card(
        $tableContent,
        '<div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment Records</h3>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Showing ' . count($payments) . ' payments
                </span>
                <a href="/admin/payments/create" class="inline-flex items-center px-3 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i>
                    Record Payment
                </a>
            </div>
        </div>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    ); ?>
</div>

<!-- Payment Management JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchPayments');
    if (searchInput) {
        searchInput.addEventListener('input', filterPayments);
    }
    
    // Filter functionality
    const propertyFilter = document.getElementById('propertyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const monthFilter = document.getElementById('monthFilter');
    
    if (propertyFilter) propertyFilter.addEventListener('change', filterPayments);
    if (statusFilter) statusFilter.addEventListener('change', filterPayments);
    if (monthFilter) monthFilter.addEventListener('change', filterPayments);
});

function filterPayments() {
    const searchTerm = document.getElementById('searchPayments')?.value.toLowerCase() || '';
    const propertyId = document.getElementById('propertyFilter')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';
    const month = document.getElementById('monthFilter')?.value || '';
    
    const rows = document.querySelectorAll('#paymentsTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const rowPropertyId = row.dataset.propertyId || '';
        const rowStatus = row.dataset.status || '';
        const rowDate = row.dataset.date || '';
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && !text.includes(searchTerm)) {
            showRow = false;
        }
        
        // Property filter
        if (propertyId && rowPropertyId !== propertyId) {
            showRow = false;
        }
        
        // Status filter
        if (status && rowStatus !== status) {
            showRow = false;
        }
        
        // Month filter
        if (month && !rowDate.startsWith(month)) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('searchPayments').value = '';
    document.getElementById('propertyFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('monthFilter').value = '';
    filterPayments();
}

function sortTable(column) {
    // Implementation for sorting table columns
    showToast('Sorting by ' + column + ' is not yet implemented', 'info');
}

function markAsPaid(paymentId) {
    if (confirm('Are you sure you want to mark this payment as paid?')) {
        // Implementation for marking payment as paid
        showToast('Marking payment as paid...', 'info');
    }
}

function deletePayment(paymentId) {
    if (confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
        // Implementation for deleting payment
        showToast('Deleting payment...', 'info');
    }
}
</script>

<?php
$content = ob_get_clean();

// Set content for layout (anti-scattering compliant)
ViewManager::set('content', $content);

// Use ViewManager for rendering (anti-scattering compliant)
echo ViewManager::render('admin.dashboard_layout');
?>
