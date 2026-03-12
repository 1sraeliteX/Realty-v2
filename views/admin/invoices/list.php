<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$invoices = ViewManager::get('invoices') ?? DataProvider::get('invoices');
$stats = ViewManager::get('stats') ?? DataProvider::get('invoice_stats');
$user = ViewManager::get('user') ?? DataProvider::get('user');
$notifications = ViewManager::get('notifications') ?? DataProvider::get('notifications');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Invoices Management');
ViewManager::set('pageTitle', 'Invoices');
ViewManager::set('pageDescription', 'Create and manage rental invoices');
ViewManager::set('user', $user);
ViewManager::set('notifications', $notifications);

// Start output buffering for the content
ob_start();
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <?php echo UIComponents::statsCard('Total Invoices', $stats['total_invoices'], 'file-invoice', 0, 'blue'); ?>
    <?php echo UIComponents::statsCard('Pending', $stats['pending'], 'clock', 0, 'yellow'); ?>
    <?php echo UIComponents::statsCard('Paid', $stats['paid'], 'check-circle', 0, 'green'); ?>
    <?php echo UIComponents::statsCard('Overdue', $stats['overdue'], 'exclamation-triangle', 0, 'red'); ?>
</div>

<!-- Invoices Table -->
<?php 
$tableContent = '';
if (empty($invoices)) {
    $tableContent = '
        <div class="text-center py-12">
            <i class="fas fa-file-invoice text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No invoices found</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by creating your first invoice</p>
            <a href="/admin/invoices/create" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <i class="fas fa-plus mr-2"></i>
                Create Invoice
            </a>
        </div>';
} else {
    $tableContent = '
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tenant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">';
    
    foreach ($invoices as $invoice) {
        $statusColor = $invoice['status'] === 'paid' ? 'success' : 
                      ($invoice['status'] === 'pending' ? 'warning' : 
                      ($invoice['status'] === 'overdue' ? 'danger' : 'info'));
        
        $tableContent .= '
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">' . htmlspecialchars($invoice['invoice_number']) . '</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">' . htmlspecialchars($invoice['tenant_name']) . '</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">' . htmlspecialchars($invoice['property_name']) . ' - ' . htmlspecialchars($invoice['unit_number'] ?? 'N/A') . '</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">$' . number_format($invoice['amount'], 2) . '</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">' . date('M j, Y', strtotime($invoice['due_date'])) . '</td>
                <td class="px-6 py-4 whitespace-nowrap">' . UIComponents::badge(ucfirst($invoice['status']), $statusColor, 'small') . '</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                        <a href="/admin/invoices/' . $invoice['id'] . '" class="text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/admin/invoices/' . $invoice['id'] . '/edit" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteInvoice(' . $invoice['id'] . ')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Delete">
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
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Invoice Records</h3>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Showing ' . count($invoices) . ' invoices
            </span>
            <a href="/admin/invoices/create" class="inline-flex items-center px-3 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700">
                <i class="fas fa-plus mr-2"></i>
                Create Invoice
            </a>
        </div>
    </div>',
    null,
    'bg-white dark:bg-gray-800 rounded-lg shadow'
); ?>

<!-- Invoice Management JavaScript -->
<script>
function deleteInvoice(invoiceId) {
    if (confirm('Are you sure you want to delete this invoice? This action cannot be undone.')) {
        showToast('Deleting invoice...', 'info');
    }
}
</script>

<?php
$content = ob_get_clean();

// Set content for layout (anti-scattering compliant)
ViewManager::set('content', $content);

// Include the dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
