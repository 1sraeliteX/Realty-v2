<?php
$title = 'Super Admin Dashboard';
$pageTitle = 'Platform Overview';
$content = ob_start();
?>

<!-- Super Admin Tabs -->
<div class="flex space-x-1 mb-6">
    <button class="px-4 py-2 bg-primary-600 text-white rounded-lg font-medium">Super Admin</button>
    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600">Dev Super Admin</button>
</div>

<!-- Platform Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                <i class="fas fa-user-shield text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Admins</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['total_admins']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-primary-100 dark:bg-primary-900 rounded-lg p-3">
                <i class="fas fa-building text-primary-600 dark:text-primary-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Properties</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['total_properties']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Subscriptions</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['active_subscriptions']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                <i class="fas fa-dollar-sign text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Platform Revenue</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">$<?php echo number_format($stats['platform_revenue'], 2); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Export Data Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Export Data</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Download platform data in various formats</p>
        <div class="flex space-x-3">
            <a href="/superadmin/export?format=json" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-download mr-2"></i>Export JSON
            </a>
            <a href="/superadmin/export?format=csv" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-file-csv mr-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- DotBot Assistant Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">DotBot Assistant</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Toggle floating assistant button</p>
        <label class="flex items-center cursor-pointer">
            <input type="checkbox" id="dotbot-toggle" class="sr-only peer">
            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Assistant</span>
        </label>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Admins -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Admins</h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentAdmins)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-user-shield text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No admins yet</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentAdmins as $adminItem): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-medium"><?php echo strtoupper(substr($adminItem['name'], 0, 1)); ?></span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($adminItem['name']); ?></h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($adminItem['email']); ?></p>
                                    <?php if ($adminItem['business_name']): ?>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($adminItem['business_name']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $adminItem['role'] === 'super_admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'; ?>">
                                    <?php echo ucfirst($adminItem['role']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="/superadmin/admins" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">View all admins →</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentActivities)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-clock text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-plus text-primary-600 dark:text-primary-400 text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    New admin registered: <?php echo htmlspecialchars($activity['description']); ?>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'superadmin_layout.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DotBot Assistant Toggle
    const dotbotToggle = document.getElementById('dotbot-toggle');
    dotbotToggle.addEventListener('change', function() {
        if (this.checked) {
            showToast('DotBot Assistant enabled', 'success');
            // Here you would typically initialize the floating assistant
        } else {
            showToast('DotBot Assistant disabled', 'info');
            // Here you would remove the floating assistant
        }
    });
});
</script>
