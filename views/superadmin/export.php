<?php
// Include UI Components
require_once __DIR__ . '/../components/UIComponents.php';

$title = 'Export Data';
$pageTitle = 'Platform Data Export';

$content = ob_start();
?>

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Export Platform Data</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Download comprehensive platform data and reports</p>
            </div>
            <a href="/superadmin/dashboard" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Export Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Properties Export -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                    <i class="fas fa-building text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Properties</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">All property data</p>
                </div>
            </div>
            <form method="POST" action="/superadmin/export" class="space-y-3">
                <input type="hidden" name="export_type" value="properties">
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="include[]" value="basic" checked class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Basic Info</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="include[]" value="units" checked class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Units</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="include[]" value="financials" class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Financial Data</span>
                    </label>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-download mr-2"></i>
                    Export Properties
                </button>
            </form>
        </div>

        <!-- Tenants Export -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                    <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tenants</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">All tenant information</p>
                </div>
            </div>
            <form method="POST" action="/superadmin/export" class="space-y-3">
                <input type="hidden" name="export_type" value="tenants">
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="include[]" value="basic" checked class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Basic Info</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="include[]" value="leases" checked class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Lease Data</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="include[]" value="payments" class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Payment History</span>
                    </label>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>
                    Export Tenants
                </button>
            </form>
        </div>

        <!-- Financial Export -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                    <i class="fas fa-dollar-sign text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Financials</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Financial reports</p>
                </div>
            </div>
            <form method="POST" action="/superadmin/export" class="space-y-3">
                <input type="hidden" name="export_type" value="financials">
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="include[]" value="revenue" checked class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Revenue</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="include[]" value="expenses" checked class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Expenses</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="include[]" value="profit_loss" class="mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">P&L Statement</span>
                    </label>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-download mr-2"></i>
                    Export Financials
                </button>
            </form>
        </div>
    </div>

    <!-- Recent Exports -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Exports</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Export Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Properties Report</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">2024-03-09 14:30</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">2.4 MB</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Completed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300">Download</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Tenants Report</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">2024-03-09 12:15</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">1.8 MB</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Completed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300">Download</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include the superadmin layout
include __DIR__ . '/../superadmin/superadmin_layout.php';
?>
