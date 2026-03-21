<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from ViewManager (anti-scattering compliant)
$payments = ViewManager::get('payments') ?? [];
$pagination = ViewManager::get('pagination') ?? [];
$stats = ViewManager::get('stats') ?? [];
$filters = ViewManager::get('filters') ?? [];
$admin = ViewManager::get('admin') ?? [];

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Payments');
ViewManager::set('pageTitle', 'Payments');
ViewManager::set('pageDescription', 'Track rent collections and payment history');
ViewManager::set('activeMenu', 'payments');

// Helper function for amount formatting
function formatAmount($amount) {
    if ($amount >= 1000000000) {
        return '₦' . number_format($amount / 1000000000, 1) . 'B';
    } elseif ($amount >= 1000000) {
        return '₦' . number_format($amount / 1000000, 1) . 'M';
    } elseif ($amount >= 1000) {
        return '₦' . number_format($amount / 1000, 1) . 'K';
    } else {
        return '₦' . number_format($amount, 0);
    }
}

// Helper function for status badge
function getStatusBadge($status) {
    $colors = [
        'paid' => 'success',
        'pending' => 'warning', 
        'overdue' => 'danger',
        'failed' => 'secondary'
    ];
    $color = $colors[$status] ?? 'secondary';
    return UIComponents::badge(ucfirst($status), $color, 'small');
}

// Start output buffering for the content
ob_start();
?>

<!-- Header with Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payments</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Track rent collections and payment history</p>
    </div>
    <div class="mt-4 sm:mt-0 flex space-x-3">
        <button onclick="exportPayments()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-cream-50 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-download mr-2"></i>
            Export
        </button>
        <button onclick="showRecordPaymentModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            <i class="fas fa-plus mr-2"></i>
            Record Payment
        </button>
    </div>
</div>

<!-- Summary Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php echo UIComponents::statsCard(
        'Total Revenue',
        formatAmount($stats['total_revenue'] ?? 0),
        'money-bill-wave',
        null,
        'success'
    ); ?>
    
    <?php echo UIComponents::statsCard(
        'Pending',
        formatAmount($stats['pending_amount'] ?? 0),
        'clock',
        null,
        'warning'
    ); ?>
    
    <?php echo UIComponents::statsCard(
        'Overdue',
        $stats['overdue_count'] ?? 0,
        'exclamation-triangle',
        null,
        'danger'
    ); ?>
    
    <?php echo UIComponents::statsCard(
        'This Month',
        formatAmount($stats['this_month_collections'] ?? 0),
        'calendar-alt',
        null,
        'primary'
    ); ?>
</div>

    <!-- Filters and Search -->
<div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
    <form method="GET" action="/admin/payments" class="space-y-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <?php echo UIComponents::searchBar('Search tenant name or reference...', $filters['search'] ?? '', null); ?>
            </div>
            
            <!-- Status Filter -->
            <?php 
            echo UIComponents::select(
                'status',
                'Status',
                [
                    '' => 'All Status',
                    'paid' => 'Paid',
                    'pending' => 'Pending',
                    'overdue' => 'Overdue',
                    'failed' => 'Failed'
                ],
                $filters['status'] ?? '',
                false,
                'col-span-1'
            ); ?>
            
            <!-- Date Range -->
            <div class="col-span-1 flex-shrink-0">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Range</label>
                <div class="flex items-center gap-2">
                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>" 
                           class="min-w-[145px] flex-shrink-0 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <span class="text-gray-400 text-sm flex-shrink-0">to</span>
                    <input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>" 
                           class="min-w-[145px] flex-shrink-0 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
        </div>
        
        <!-- Filter Actions -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-filter mr-2"></i>
                    Apply Filters
                </button>
                <a href="/admin/payments" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-times mr-2"></i>
                    Clear
                </a>
            </div>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Showing <?php echo count($payments); ?> payments
            </span>
        </div>
    </form>
</div>

    <!-- Payments Table -->
<div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        #
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Tenant
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Property/Unit
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Amount
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Due Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Paid Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-cream-50 dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-money-bill-wave text-4xl text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No payments found</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">
                                    <?php if (!empty($filters['status']) || !empty($filters['search']) || !empty($filters['date_from'])): ?>
                                        No payments match your current filters. Try adjusting your search criteria.
                                    <?php else: ?>
                                        No payments have been recorded yet.
                                    <?php endif; ?>
                                </p>
                                <div class="flex space-x-3">
                                    <?php if (!empty($filters['status']) || !empty($filters['search']) || !empty($filters['date_from'])): ?>
                                        <a href="/admin/payments" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <i class="fas fa-times mr-2"></i>
                                            Clear Filters
                                        </a>
                                    <?php endif; ?>
                                    <button onclick="showRecordPaymentModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                        <i class="fas fa-plus mr-2"></i>
                                        Record Payment
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                #<?php echo str_pad($payment['id'], 6, '0', STR_PAD_LEFT); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                                            <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
                                                <?php echo strtoupper(substr($payment['first_name'] ?? '', 0, 1) . substr($payment['last_name'] ?? '', 0, 1)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo htmlspecialchars($payment['tenant_email'] ?? ''); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($payment['property_name'] ?? ''); ?>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Unit <?php echo htmlspecialchars($payment['unit_number'] ?? ''); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                <?php echo formatAmount($payment['amount']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <?php echo date('M j, Y', strtotime($payment['due_date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <?php echo $payment['payment_date'] ? date('M j, Y', strtotime($payment['payment_date'])) : '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo getStatusBadge($payment['status']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="viewPayment(<?php echo $payment['id']; ?>)" 
                                            class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400" 
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($payment['status'] === 'pending'): ?>
                                        <button onclick="markAsPaid(<?php echo $payment['id']; ?>)" 
                                                class="text-gray-400 hover:text-green-600 dark:hover:text-green-400" 
                                                title="Mark as Paid">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="deletePayment(<?php echo $payment['id']; ?>)" 
                                            class="text-gray-400 hover:text-red-600 dark:hover:text-red-400" 
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- Pagination -->
<?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
    <div class="flex items-center justify-between mt-6">
        <div class="text-sm text-gray-700 dark:text-gray-300">
            Showing 
            <span class="font-medium"><?php echo (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?></span> 
            to 
            <span class="font-medium"><?php echo min($pagination['current_page'] * $pagination['per_page'], $pagination['total']); ?></span> 
            of 
            <span class="font-medium"><?php echo $pagination['total']; ?></span> 
            results
        </div>
        <?php echo UIComponents::pagination($pagination['current_page'], $pagination['total_pages'], 'goToPage'); ?>
    </div>
<?php endif; ?>

<!-- Record Payment Modal -->
<?php 
echo UIComponents::modal(
    'recordPaymentModal',
    'Record Payment',
    '
    <form id="recordPaymentForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tenant *</label>
                <select name="tenant_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Select Tenant</option>
                    <!-- Tenants will be populated via JavaScript -->
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (₦) *</label>
                <input type="number" name="amount" step="0.01" min="0" required 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date *</label>
                <input type="date" name="due_date" required 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date</label>
                <input type="date" name="payment_date" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Select Method</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                    <option value="paystack">Paystack</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Paystack Reference</label>
            <input type="text" name="paystack_reference" placeholder="Optional - for Paystack payments"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
    </form>
    ',
    '<button type="button" onclick="savePayment()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Save Payment</button>
    <button type="button" onclick="closeModal(\'recordPaymentModal\')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>',
    'large'
); ?>

<!-- Payment Details Modal -->
<?php 
echo UIComponents::modal(
    'paymentDetailsModal',
    'Payment Details',
    '<div id="paymentDetailsContent">Loading...</div>',
    '<button type="button" onclick="closeModal(\'paymentDetailsModal\')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Close</button>',
    'large'
); ?>

<!-- Payment Management JavaScript -->
<script>
// Navigation functions
function goToPage(page) {
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    window.location.href = url.toString();
}

// Modal functions
function showRecordPaymentModal() {
    document.getElementById('recordPaymentModal').classList.remove('hidden');
    loadTenants();
}

function viewPayment(id) {
    document.getElementById('paymentDetailsModal').classList.remove('hidden');
    loadPaymentDetails(id);
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Payment operations
function loadTenants() {
    // This would typically load tenants from API
    // For now, using placeholder data
    const select = document.querySelector('select[name="tenant_id"]');
    select.innerHTML = `
        <option value="">Select Tenant</option>
        <option value="1">John Smith</option>
        <option value="2">Sarah Johnson</option>
        <option value="3">Mike Chen</option>
    `;
}

function loadPaymentDetails(id) {
    // This would typically load payment details from API
    const content = document.getElementById('paymentDetailsContent');
    content.innerHTML = `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Payment ID</label>
                    <p class="text-gray-900">#${String(id).padStart(6, '0')}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Status</label>
                    <p><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Paid</span></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Amount</label>
                    <p class="text-lg font-semibold">₦120,000</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Payment Date</label>
                    <p>Dec 1, 2023</p>
                </div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Paystack Reference</label>
                <p class="font-mono text-sm bg-gray-100 p-2 rounded">ref_123456789</p>
            </div>
        </div>
    `;
}

function savePayment() {
    const form = document.getElementById('recordPaymentForm');
    const formData = new FormData(form);
    
    // This would typically send data to API
    alert('Payment recording functionality would be implemented here');
    closeModal('recordPaymentModal');
}

function markAsPaid(id) {
    if (confirm('Mark this payment as paid?')) {
        // This would typically update payment status via API
        alert('Payment status update would be implemented here');
        location.reload();
    }
}

function deletePayment(id) {
    if (confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
        // This would typically delete payment via API
        alert('Payment deletion would be implemented here');
        location.reload();
    }
}

function exportPayments() {
    // This would typically export payments data
    alert('Export functionality would be implemented here');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
    }
});
</script>

<?php
$content = ob_get_clean();

// Set content for layout (anti-scattering compliant)
ViewManager::set('content', $content);

// Include the minimal layout without sidebar and navbar (anti-scattering compliant)
include __DIR__ . '/minimal_layout.php';
?>
