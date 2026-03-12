<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Admin Page');
ViewManager::set('user', [
    'name' => 'Admin User',
    'email' => 'admin@cornerstone.com',
    'avatar' => null
]);
ViewManager::set('notifications', []);

ob_start();
?>


<!-- Payment Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Payment #<?php echo $payment['id']; ?></h1>
                <p class="text-green-100"><?php echo htmlspecialchars($payment['tenant_name']); ?> • Unit <?php echo htmlspecialchars($payment['unit_number']); ?></p>
                <p class="text-green-100"><?php echo htmlspecialchars($payment['property_name']); ?></p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-green-100 text-sm">Amount</p>
                    <p class="text-2xl font-bold text-white">$<?php echo number_format($payment['total_amount']); ?></p>
                </div>
                <?php echo UIComponents::badge(ucfirst($payment['status']), $payment['status'] === 'paid' ? 'success' : 'warning', 'large'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Payment Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Payment Date', date('M j, Y', strtotime($payment['payment_date'])), 'calendar', 'blue', '', ucfirst($payment['type'])); ?>
    <?php echo UIComponents::statCard('Payment Method', ucfirst(str_replace('_', ' ', $payment['method'])), 'credit-card', 'purple', '', 'Transaction ID: ' . $payment['transaction_id']); ?>
    <?php echo UIComponents::statCard('Due Date', date('M j, Y', strtotime($payment['due_date'])), 'clock', 'orange', '', $payment['late_fee'] > 0 ? 'Late fee applied' : 'On time'); ?>
    <?php echo UIComponents::statCard('Tenant', htmlspecialchars($payment['tenant_name']), 'user', 'green', '', 'View profile'); ?>
</div>

<!-- Payment Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Payment Information -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h3>
            
            <div class="space-y-4">
                <!-- Payment Breakdown -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Payment Breakdown</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Base Rent</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">$<?php echo number_format($payment['amount']); ?></span>
                        </div>
                        <?php if ($payment['late_fee'] > 0): ?>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Late Fee</span>
                                <span class="text-sm font-medium text-red-600 dark:text-red-400">$<?php echo number_format($payment['late_fee']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($payment['discount'] > 0): ?>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Discount</span>
                                <span class="text-sm font-medium text-green-600 dark:text-green-400">-$<?php echo number_format($payment['discount']); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Total Amount</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">$<?php echo number_format($payment['total_amount']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Transaction Details</h4>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Payment Date</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y H:i', strtotime($payment['processed_at'])); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Due Date</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($payment['due_date'])); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Payment Method</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo ucfirst(str_replace('_', ' ', $payment['method'])); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Transaction ID</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($payment['transaction_id']); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Reference</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($payment['reference']); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Status</dt>
                            <dd><?php echo UIComponents::badge(ucfirst($payment['status']), $payment['status'] === 'paid' ? 'success' : 'warning'); ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Notes -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Notes</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($payment['notes']); ?></p>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex space-x-4">
                <?php if ($payment['status'] !== 'paid'): ?>
                    <?php echo UIComponents::button('Mark as Paid', 'success', 'medium', '#', 'check'); ?>
                <?php endif; ?>
                <?php echo UIComponents::button('Send Receipt', 'info', 'medium', '#', 'envelope'); ?>
                <?php echo UIComponents::button('Print Receipt', 'secondary', 'medium', '#', 'print'); ?>
                <?php echo UIComponents::button('Edit Payment', 'primary', 'medium', '/admin/payments/' . $payment['id'] . '/edit', 'edit'); ?>
            </div>
        </div>
    </div>

    <!-- Tenant & Property Info -->
    <div class="space-y-6">
        <!-- Tenant Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tenant Information</h3>
            <div class="text-center mb-4">
                <?php echo UIComponents::avatar($payment['tenant_name'], null, 'large'); ?>
                <h4 class="mt-3 text-lg font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($payment['tenant_name']); ?></h4>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Property</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($payment['property_name']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Unit</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($payment['unit_number']); ?></span>
                </div>
            </div>
            <div class="mt-4">
                <?php echo UIComponents::button('View Tenant', 'primary', 'small', '/admin/tenants/' . $payment['tenant_id'], 'user'); ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <?php echo UIComponents::button('Create Invoice', 'success', 'full', '/admin/invoices/create?tenant_id=' . $payment['tenant_id'], 'file-invoice'); ?>
                <?php echo UIComponents::button('Record New Payment', 'primary', 'full', '/admin/payments/create?tenant_id=' . $payment['tenant_id'], 'plus'); ?>
                <?php echo UIComponents::button('View Payment History', 'info', 'full', '/admin/tenants/' . $payment['tenant_id'], 'history'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Related Payments -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment History</h3>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($relatedPayments as $relatedPayment): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($relatedPayment['date'])); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo ucfirst($relatedPayment['type']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">$<?php echo number_format($relatedPayment['amount']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo UIComponents::badge(ucfirst($relatedPayment['status']), $relatedPayment['status'] === 'paid' ? 'success' : 'warning'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="/admin/payments/<?php echo $relatedPayment['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Print receipt functionality
function printReceipt() {
    window.print();
}

// Send receipt functionality
function sendReceipt() {
    showToast('Sending receipt to tenant...', 'info');
    setTimeout(() => {
        showToast('Receipt sent successfully!', 'success');
    }, 2000);
}

// Mark as paid functionality
function markAsPaid() {
    if (confirm('Are you sure you want to mark this payment as paid?')) {
        showToast('Processing payment...', 'info');
        setTimeout(() => {
            showToast('Payment marked as paid!', 'success');
            location.reload();
        }, 2000);
    }
}
</script>


<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
