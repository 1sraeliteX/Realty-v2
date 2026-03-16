<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
$report = ViewManager::get('report', []);
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900
                    dark:text-white">Edit Report</h1>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <p class="text-gray-500 dark:text-gray-400 text-center py-8">
            Report editor coming soon.
        </p>
        <div class="text-center">
            <a href="/admin/reports"
               class="text-primary-600 hover:underline text-sm">
                ← Back to Reports
            </a>
        </div>
    </div>
</div>
