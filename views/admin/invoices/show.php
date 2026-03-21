<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
$invoice = ViewManager::get('invoice', []);
?>
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900
                        dark:text-white">
                Invoice #<?php echo htmlspecialchars(
                    $invoice['invoice_number'] ?? $invoice['id'] ?? ''); ?>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <?php echo htmlspecialchars(
                    $invoice['created_at'] ?? ''); ?>
            </p>
        </div>
        <a href="/admin/invoices"
           class="inline-flex items-center px-4 py-2 border
                  border-gray-300 dark:border-gray-600 rounded-lg
                  text-sm text-gray-700 dark:text-gray-300
                  hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6">
        <p class="text-gray-500 dark:text-gray-400 text-center py-8">
            Invoice details coming soon.
        </p>
    </div>
</div>
