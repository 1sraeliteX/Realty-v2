<?php
// Dashboard view shim — check controller for actual view path
$dashFile = __DIR__ . '/dashboard/index.php';
if (file_exists($dashFile)) {
    require_once $dashFile;
} else {
    // Fallback minimal dashboard
    require_once __DIR__ . '/../../config/bootstrap.php';
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Dashboard
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Welcome back! Here's your portfolio overview.
        </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        $cards = [
            ['label'=>'Properties','icon'=>'fa-building','color'=>'blue','key'=>'total_properties'],
            ['label'=>'Units','icon'=>'fa-door-open','color'=>'green','key'=>'total_units'],
            ['label'=>'Tenants','icon'=>'fa-users','color'=>'yellow','key'=>'total_tenants'],
            ['label'=>'Revenue','icon'=>'fa-money-bill-wave','color'=>'purple','key'=>'total_revenue'],
        ];
        $stats = ViewManager::get('stats', []);
        foreach ($cards as $c):
        ?>
        <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-<?php echo $c['color']; ?>-100 dark:bg-<?php echo $c['color']; ?>-900 rounded-lg p-3">
                    <i class="fas <?php echo $c['icon']; ?> text-<?php echo $c['color']; ?>-600 dark:text-<?php echo $c['color']; ?>-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        <?php echo $c['label']; ?>
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        <?php echo $stats[$c['key']] ?? 0; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php } ?>
