<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Invoice Details';
$pageTitle = 'Invoice Information';

$content = ob_start();
?>

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Invoice Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View and manage invoice information</p>
            </div>
            <div class="flex space-x-3">
                <a href="/admin/invoices" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Invoices
                </a>
                <a href="/admin/invoices/<?php echo $_GET['id'] ?? '1'; ?>/edit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Invoice
                </a>
            </div>
        </div>
    </div>

    <!-- Invoice Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Invoice Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Invoice #INV-001</h2>
                        <p class="text-gray-600 dark:text-gray-400">Created on March 1, 2024</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            Pending
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Bill To</h3>
                        <div class="text-gray-900 dark:text-white">
                            <p class="font-semibold">John Doe</p>
                            <p>Sunset Apartments - Unit 101</p>
                            <p>123 Sunset Boulevard</p>
                            <p>Los Angeles, CA 90001</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Invoice Date:</span>
                                <span class="ml-2 text-gray-900 dark:text-white">March 1, 2024</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Due Date:</span>
                                <span class="ml-2 text-gray-900 dark:text-white">March 15, 2024</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Amount Due:</span>
                                <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">$1,200.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Items -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Line Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Monthly Rent</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">1</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">$1,200.00</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">$1,200.00</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">Total</td>
                                <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-white">$1,200.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
                <p class="text-gray-600 dark:text-gray-400">Monthly rent payment for March 2024. Payment is due by the 15th of the month. Late fees will apply after the due date.</p>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="#" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>
                        Mark as Paid
                    </a>
                    <a href="#" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Invoice
                    </a>
                    <a href="#" class="block w-full text-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-download mr-2"></i>
                        Download PDF
                    </a>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment History</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">No payments recorded</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include the admin dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
