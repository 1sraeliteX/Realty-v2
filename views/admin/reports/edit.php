<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'View Report';
$pageTitle = 'Report Details';

$content = ob_start();
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Report Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View generated report information</p>
            </div>
            <a href="/admin/reports" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Reports
            </a>
        </div>
    </div>

    <!-- Report Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Monthly Financial Report</h2>
                <div class="mt-2 space-y-1">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Type:</span> Financial Report
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Period:</span> March 1, 2024 - March 31, 2024
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Generated:</span> March 9, 2024 at 10:30 AM
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Format:</span> PDF
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Size:</span> 1.2 MB
                    </p>
                </div>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                    Completed
                </span>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Report Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">$54,320</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">$18,450</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Expenses</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">$35,870</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Net Profit</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>
        <div class="flex flex-wrap gap-4">
            <a href="#" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <i class="fas fa-download mr-2"></i>
                Download Report
            </a>
            <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-envelope mr-2"></i>
                Email Report
            </a>
            <a href="/admin/reports/create" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-redo mr-2"></i>
                Regenerate
            </a>
            <a href="#" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-trash mr-2"></i>
                Delete Report
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include the admin dashboard layout
include __DIR__ . '/../simple_layout.php';
?>
