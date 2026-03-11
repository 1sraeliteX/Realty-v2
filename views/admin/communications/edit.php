<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'View Communication';
$pageTitle = 'Message Details';

$content = ob_start();
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Message Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View communication details</p>
            </div>
            <a href="/admin/communications" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Communications
            </a>
        </div>
    </div>

    <!-- Message Content -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Rent Reminder</h2>
                    <div class="mt-2 space-y-1">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">From:</span> Admin
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">To:</span> John Doe
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">Date:</span> March 9, 2024 at 10:30 AM
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">Type:</span> Email
                        </p>
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Sent
                    </span>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Message Content</h3>
            <div class="prose dark:prose-invert max-w-none">
                <p>Dear John Doe,</p>
                <p>This is a friendly reminder that your rent payment for March 2024 is due on March 15, 2024.</p>
                <p>Amount due: $1,200.00</p>
                <p>Please ensure payment is made on time to avoid any late fees.</p>
                <p>Thank you for your cooperation.</p>
                <p>Best regards,<br>Cornerstone Realty Management</p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include the admin dashboard layout
include __DIR__ . '/../simple_layout.php';
?>
