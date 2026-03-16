<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
$reports = ViewManager::get('reports', []);
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Generate and view portfolio reports
            </p>
        </div>
        <a href="/admin/reports/create"
           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i>New Report
        </a>
    </div>

    <!-- Quick report cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php
        $quickReports = [
            ['title'=>'Occupancy Report','desc'=>'View occupancy rates across all properties','icon'=>'fa-chart-pie','color'=>'blue','url'=>'/admin/reports/create?type=occupancy'],
            ['title'=>'Revenue Report','desc'=>'Monthly and annual revenue breakdown','icon'=>'fa-chart-line','color'=>'green','url'=>'/admin/reports/create?type=revenue'],
            ['title'=>'Maintenance Report','desc'=>'Outstanding and completed maintenance','icon'=>'fa-tools','color'=>'yellow','url'=>'/admin/reports/create?type=maintenance'],
        ];
        foreach ($quickReports as $r): ?>
        <a href="<?php echo $r['url']; ?>" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700 block">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-<?php echo $r['color']; ?>-100 dark:bg-<?php echo $r['color']; ?>-900 rounded-lg flex-shrink-0">
                    <i class="fas <?php echo $r['icon']; ?> text-<?php echo $r['color']; ?>-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                        <?php echo $r['title']; ?>
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <?php echo $r['desc']; ?>
                    </p>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Past reports -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Reports</h2>
        <?php if (empty($reports)): ?>
        <div class="text-center py-8 text-gray-400 dark:text-gray-500">
            <i class="fas fa-file-chart-column text-3xl mb-3 block opacity-30"></i>
            <p class="text-sm">No reports generated yet.</p>
        </div>
        <?php else: ?>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php foreach ($reports as $rep): ?>
            <div class="py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        <?php echo htmlspecialchars($rep['title'] ?? 'Report'); ?>
                    </p>
                    <p class="text-xs text-gray-400">
                        <?php echo htmlspecialchars($rep['created_at'] ?? ''); ?>
                    </p>
                </div>
                <a href="/admin/reports/<?php echo $rep['id']; ?>" class="text-xs text-primary-600 hover:underline">
                    View
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
