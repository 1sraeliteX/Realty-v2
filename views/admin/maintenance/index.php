<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
$requests = ViewManager::get('maintenance_requests', []);
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center
                sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900
                        dark:text-white">Maintenance</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Track and manage maintenance requests
            </p>
        </div>
        <a href="/admin/maintenance/create"
           class="inline-flex items-center px-4 py-2 bg-primary-600
                  text-white rounded-lg hover:bg-primary-700
                  text-sm font-medium">
            <i class="fas fa-plus mr-2"></i>New Request
        </a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <?php if (empty($requests)): ?>
            <div class="text-center py-12">
                <i class="fas fa-tools text-4xl text-gray-300
                          dark:text-gray-600 mb-3 block"></i>
                <p class="text-gray-500 dark:text-gray-400">
                    No maintenance requests yet.
                </p>
                <a href="/admin/maintenance/create"
                   class="mt-3 inline-block text-primary-600
                          hover:underline text-sm">
                      Create first request
                </a>
            </div>
        <?php else: ?>
            <p class="text-gray-500 dark:text-gray-400">
                <?php echo count($requests); ?> request(s) found.
            </p>
        <?php endif; ?>
    </div>
</div>
