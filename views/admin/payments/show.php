<?php
// Initialize framework (anti-scattering compliant)
require_once $_SERVER['DOCUMENT_ROOT'] . '/../config/bootstrap.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$payment = ViewManager::get('payment') ?? [];
$receipts = ViewManager::get('receipts') ?? [];
$user = DataProvider::get('user');
$notifications = DataProvider::get('notifications');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Payment Details');
ViewManager::set('pageTitle', 'Payment Details');
ViewManager::set('pageDescription', 'View payment information and uploaded receipts');
ViewManager::set('user', $user);
ViewManager::set('notifications', $notifications);

// Start output buffering for the content
ob_start();
?>

<!-- Breadcrumb Navigation -->
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/admin/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="/admin/finances" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 md:ml-2">
                        Finances
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">
                        Payment Details
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Details</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">View payment information and uploaded receipts</p>
        </div>
        <a href="/admin/finances" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Finances
        </a>
    </div>
</div>

<!-- Payment Information -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tenant</label>
            <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($payment['tenant_name'] ?? 'N/A'); ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Property / Unit</label>
            <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars(($payment['property_name'] ?? 'N/A') . ' - ' . ($payment['unit_number'] ?? 'N/A')); ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Amount</label>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">₦<?php echo number_format($payment['amount'], 2); ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Payment Type</label>
            <p class="text-gray-900 dark:text-white"><?php echo ucfirst($payment['payment_type'] ?? 'N/A'); ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Payment Date</label>
            <p class="text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                <?php 
                $statusClass = $payment['status'] === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                             ($payment['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                             'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200');
                echo $statusClass;
                ?>">
                <?php echo ucfirst($payment['status'] ?? 'N/A'); ?>
            </span>
        </div>
        <?php if (!empty($payment['notes'])): ?>
        <div class="md:col-span-2 lg:col-span-3">
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notes</label>
            <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($payment['notes']); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Receipts Section -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Receipts</h2>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            <?php echo count($receipts); ?> file(s)
        </span>
    </div>
    
    <?php if (empty($receipts)): ?>
        <div class="text-center py-8">
            <i class="fas fa-file-invoice text-gray-300 dark:text-gray-600 text-4xl mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400">No receipts uploaded for this payment</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($receipts as $receipt): ?>
                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <i class="fas <?php 
                                echo $receipt['mime_type'] === 'application/pdf' ? 'fa-file-pdf text-red-500' : 
                                     (strpos($receipt['mime_type'], 'image/') !== false ? 'fa-file-image text-green-500' : 
                                     (strpos($receipt['mime_type'], 'word') !== false ? 'fa-file-word text-blue-500' : 'fa-file text-gray-500'));
                            ?> text-2xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                <?php echo htmlspecialchars($receipt['original_name']); ?>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <?php echo date('M j, Y g:i A', strtotime($receipt['created_at'])); ?> • 
                                <?php echo number_format($receipt['file_size'] / 1024, 2); ?> KB
                            </p>
                            <?php if (!empty($receipt['description'])): ?>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    <?php echo htmlspecialchars($receipt['description']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="/admin/payments/receipt/<?php echo $receipt['id']; ?>/download" 
                           class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                            <i class="fas fa-download mr-1"></i>
                            Download
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();

// Set content for layout (anti-scattering compliant)
ViewManager::set('content', $content);

// Include the dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
