<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from ViewManager (anti-scattering compliant)
$invoices   = ViewManager::get('invoices', []);
$pagination = ViewManager::get('pagination', []);
$stats      = ViewManager::get('stats', []);
$filters    = ViewManager::get('filters', []);
$admin      = ViewManager::get('user', []);

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Invoices');
ViewManager::set('pageTitle', 'Invoices');
ViewManager::set('pageDescription', 'Manage all invoices and billing');
ViewManager::set('activeMenu', 'invoices');

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

// Start output buffering for content (anti-scattering compliant)
ob_start();
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center
                sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900
                        dark:text-white">Invoices</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Manage all invoices
            </p>
        </div>
        <a href="/admin/invoices/create"
           class="inline-flex items-center px-4 py-2 bg-primary-600
                  text-white rounded-lg hover:bg-primary-700
                  text-sm font-medium">
            <i class="fas fa-plus mr-2"></i>New Invoice
        </a>
    </div>

    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow
                overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200
                          dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs
                                   font-medium text-gray-500
                                   dark:text-gray-300 uppercase
                                   tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs
                                   font-medium text-gray-500
                                   dark:text-gray-300 uppercase
                                   tracking-wider">Tenant</th>
                        <th class="px-6 py-3 text-left text-xs
                                   font-medium text-gray-500
                                   dark:text-gray-300 uppercase
                                   tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs
                                   font-medium text-gray-500
                                   dark:text-gray-300 uppercase
                                   tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs
                                   font-medium text-gray-500
                                   dark:text-gray-300 uppercase
                                   tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs
                                   font-medium text-gray-500
                                   dark:text-gray-300 uppercase
                                   tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-cream-50 dark:bg-gray-800
                              divide-y divide-gray-200
                              dark:divide-gray-700">
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="6"
                                class="px-6 py-12 text-center
                                       text-gray-500 dark:text-gray-400">
                                <i class="fas fa-file-invoice
                                          text-3xl mb-3 block
                                          opacity-30"></i>
                                No invoices yet.
                                <a href="/admin/invoices/create"
                                   class="text-primary-600
                                          hover:underline ml-1">
                                  Create one
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr class="hover:bg-gray-50
                                       dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm
                                           font-medium text-gray-900
                                           dark:text-white">
                                    #<?php echo htmlspecialchars(
                                        $inv['invoice_number']
                                        ?? $inv['id']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm
                                           text-gray-500
                                           dark:text-gray-400">
                                    <?php echo htmlspecialchars(
                                        $inv['tenant_name'] ?? '—'); ?>
                                </td>
                                <td class="px-6 py-4 text-sm
                                           text-gray-900 dark:text-white">
                                    ₦<?php echo number_format(
                                        $inv['amount'] ?? 0, 0); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $sc = [
                                      'paid'   => 'bg-green-100 text-green-800',
                                      'unpaid' => 'bg-red-100 text-red-800',
                                      'overdue'=> 'bg-yellow-100 text-yellow-800',
                                    ];
                                    $cls = $sc[$inv['status'] ?? '']
                                         ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 py-1 text-xs
                                                 font-semibold rounded-full
                                                 <?php echo $cls; ?>">
                                        <?php echo ucfirst(
                                            $inv['status'] ?? 'unknown'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm
                                           text-gray-500 dark:text-gray-400">
                                    <?php echo htmlspecialchars(
                                        $inv['due_date'] ?? '—'); ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="/admin/invoices/
                                       <?php echo $inv['id']; ?>"
                                       class="text-primary-600
                                              hover:underline mr-3">
                                      View
                                    </a>
                                    <a href="/admin/invoices/
                                       <?php echo $inv['id']; ?>/edit"
                                       class="text-blue-600
                                              hover:underline">
                                      Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Set content for layout (anti-scattering compliant)
ViewManager::set('content', $content);

// Include the layout directly (anti-scattering compliant)
include __DIR__ . '/../dashboard_layout.php';
?>
