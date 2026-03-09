<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Finance Record Details';
$pageTitle = 'Finance Details';
$pageDescription = 'View comprehensive finance record information and manage financial data';

// Mock finance data
$finance = [
    'id' => 1,
    'type' => 'income',
    'category' => 'rent',
    'description' => 'Monthly rent payment for January 2024',
    'amount' => 1200,
    'property_id' => 1,
    'property_name' => 'Sunset Apartments',
    'unit_id' => 1,
    'unit_number' => '101',
    'tenant_id' => 1,
    'tenant_name' => 'John Smith',
    'transaction_date' => '2024-01-01',
    'recorded_date' => '2024-01-01 10:30:00',
    'payment_method' => 'bank_transfer',
    'reference_number' => 'TXN123456789',
    'status' => 'completed',
    'recurring' => true,
    'frequency' => 'monthly',
    'next_due_date' => '2024-02-01',
    'tax_deductible' => false,
    'invoice_id' => null,
    'receipt_url' => '/uploads/receipts/TXN123456789.pdf',
    'notes' => 'Monthly rent payment received on time',
    'created_by' => 'Admin User',
    'created_at' => '2024-01-01 10:30:00',
    'updated_at' => '2024-01-01 10:30:00'
];

// Mock related transactions
$relatedTransactions = [
    ['id' => 2, 'date' => '2023-12-01', 'description' => 'December 2023 Rent', 'amount' => 1200, 'type' => 'income', 'status' => 'completed'],
    ['id' => 3, 'date' => '2023-11-01', 'description' => 'November 2023 Rent', 'amount' => 1200, 'type' => 'income', 'status' => 'completed'],
    ['id' => 4, 'date' => '2023-10-01', 'description' => 'October 2023 Rent', 'amount' => 1200, 'type' => 'income', 'status' => 'completed'],
];

ob_start();
?>

<!-- Finance Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($finance['description']); ?></h1>
                <p class="text-green-100"><?php echo htmlspecialchars($finance['tenant_name']); ?> • Unit <?php echo htmlspecialchars($finance['unit_number']); ?></p>
                <p class="text-green-100"><?php echo htmlspecialchars($finance['property_name']); ?></p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-green-100 text-sm">Amount</p>
                    <p class="text-2xl font-bold text-white">$<?php echo number_format($finance['amount']); ?></p>
                </div>
                <?php echo UIComponents::badge(ucfirst($finance['type']), $finance['type'] === 'income' ? 'success' : 'danger', 'large'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Finance Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Category', ucfirst($finance['category']), 'folder', 'blue', '', ucfirst($finance['type'])); ?>
    <?php echo UIComponents::statCard('Transaction Date', date('M j, Y', strtotime($finance['transaction_date'])), 'calendar', 'orange', '', ucfirst($finance['payment_method'])); ?>
    <?php echo UIComponents::statCard('Status', ucfirst($finance['status']), 'check-circle', 'green', '', 'Completed successfully'); ?>
    <?php echo UIComponents::statCard('Recurring', $finance['recurring'] ? 'Yes' : 'No', 'sync', 'purple', '', $finance['recurring'] ? $finance['frequency'] : 'One-time'); ?>
</div>

<!-- Finance Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Transaction Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Transaction Information</h3>
            
            <div class="space-y-4">
                <!-- Basic Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Transaction Details</h4>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Type</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo ucfirst($finance['type']); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Category</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo ucfirst($finance['category']); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Amount</dt>
                                <dd class="text-xs font-bold text-gray-900 dark:text-white">$<?php echo number_format($finance['amount']); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Status</dt>
                                <dd><?php echo UIComponents::badge(ucfirst($finance['status']), $finance['status'] === 'completed' ? 'success' : 'warning'); ?></dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Payment Information</h4>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Payment Method</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo ucfirst(str_replace('_', ' ', $finance['payment_method'])); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Reference #</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo htmlspecialchars($finance['reference_number']); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Transaction Date</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($finance['transaction_date'])); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Recorded Date</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo date('M j, Y H:i', strtotime($finance['recorded_date'])); ?></dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Description</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($finance['description']); ?></p>
                </div>

                <!-- Notes -->
                <?php if (!empty($finance['notes'])): ?>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Additional Notes</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($finance['notes']); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex space-x-4">
                <?php echo UIComponents::button('Edit Transaction', 'primary', 'medium', '/admin/finances/' . $finance['id'] . '/edit', 'edit'); ?>
                <?php echo UIComponents::button('Print Receipt', 'info', 'medium', '#', 'print'); ?>
                <?php echo UIComponents::button('Export PDF', 'secondary', 'medium', '#', 'file-pdf'); ?>
                <?php echo UIComponents::button('Duplicate', 'warning', 'medium', '#', 'copy'); ?>
            </div>
        </div>

        <!-- Recurring Information -->
        <?php if ($finance['recurring']): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recurring Transaction</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Frequency</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo ucfirst($finance['frequency']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Next Due Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($finance['next_due_date'])); ?></p>
                    </div>
                </div>
                
                <div class="mt-4 flex space-x-4">
                    <?php echo UIComponents::button('Skip Next', 'warning', 'small', '#', 'forward'); ?>
                    <?php echo UIComponents::button('Stop Recurring', 'danger', 'small', '#', 'stop'); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Related Transactions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Transactions</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($relatedTransactions as $transaction): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($transaction['date'])); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($transaction['description']); ?></td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">$<?php echo number_format($transaction['amount']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"><?php echo ucfirst($transaction['type']); ?></td>
                                <td class="px-4 py-3"><?php echo UIComponents::badge(ucfirst($transaction['status']), $transaction['status'] === 'completed' ? 'success' : 'warning'); ?></td>
                                <td class="px-4 py-3 text-sm font-medium">
                                    <a href="/admin/finances/<?php echo $transaction['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Associated Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Associated Information</h3>
            
            <div class="space-y-4">
                <!-- Property -->
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Property</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($finance['property_name']); ?></p>
                </div>
                
                <!-- Unit -->
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Unit</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($finance['unit_number']); ?></p>
                </div>
                
                <!-- Tenant -->
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tenant</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($finance['tenant_name']); ?></p>
                        <?php echo UIComponents::button('View', 'small', '', '/admin/tenants/' . $finance['tenant_id'], 'user'); ?>
                    </div>
                </div>
                
                <!-- Invoice -->
                <?php if ($finance['invoice_id']): ?>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Invoice</p>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">#<?php echo $finance['invoice_id']; ?></p>
                            <?php echo UIComponents::button('View', 'small', '', '/admin/invoices/' . $finance['invoice_id'], 'file-invoice'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Receipt -->
        <?php if ($finance['receipt_url']): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Receipt</h3>
                
                <div class="text-center">
                    <i class="fas fa-file-pdf text-4xl text-red-500 mb-3"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Transaction Receipt</p>
                    <div class="space-y-2">
                        <?php echo UIComponents::button('Download Receipt', 'primary', 'full', '#', 'download'); ?>
                        <?php echo UIComponents::button('Email Receipt', 'info', 'full', '#', 'envelope'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tax Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tax Information</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Tax Deductible</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo $finance['tax_deductible'] ? 'Yes' : 'No'; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Tax Category</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo ucfirst($finance['category']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Tax Year</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo date('Y', strtotime($finance['transaction_date'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <?php echo UIComponents::button('Create Invoice', 'success', 'full', '/admin/invoices/create?finance_id=' . $finance['id'], 'file-invoice'); ?>
                <?php echo UIComponents::button('Add Note', 'info', 'full', '#', 'sticky-note'); ?>
                <?php echo UIComponents::button('Generate Report', 'warning', 'full', '#', 'chart-bar'); ?>
                <?php echo UIComponents::button('Archive Transaction', 'secondary', 'full', '#', 'archive'); ?>
            </div>
        </div>
    </div>
</div>

<script>
// Edit transaction
function editTransaction() {
    window.location.href = '/admin/finances/<?php echo $finance['id']; ?>/edit';
}

// Print receipt
function printReceipt() {
    window.print();
}

// Export PDF
function exportPDF() {
    showToast('Generating PDF export...', 'info');
    setTimeout(() => {
        showToast('PDF exported successfully!', 'success');
    }, 2000);
}

// Download receipt
function downloadReceipt() {
    showToast('Downloading receipt...', 'info');
    setTimeout(() => {
        showToast('Receipt downloaded successfully!', 'success');
    }, 2000);
}

// Email receipt
function emailReceipt() {
    showToast('Opening email composer...', 'info');
}

// Duplicate transaction
function duplicateTransaction() {
    if (confirm('Create a duplicate of this transaction?')) {
        showToast('Duplicating transaction...', 'info');
        setTimeout(() => {
            showToast('Transaction duplicated successfully!', 'success');
        }, 2000);
    }
}

// Skip next recurring
function skipNextRecurring() {
    if (confirm('Skip the next recurring transaction?')) {
        showToast('Skipping next transaction...', 'info');
        setTimeout(() => {
            showToast('Next transaction skipped!', 'success');
        }, 2000);
    }
}

// Stop recurring
function stopRecurring() {
    if (confirm('Stop this recurring transaction? This cannot be undone.')) {
        showToast('Stopping recurring transaction...', 'info');
        setTimeout(() => {
            showToast('Recurring transaction stopped!', 'success');
        }, 2000);
    }
}
</script>

<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
