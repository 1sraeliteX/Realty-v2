<?php
require_once __DIR__ . '/../../config/bootstrap.php';
$admin = ViewManager::get('user', []);
?>
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            My Profile
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Manage your personal information and account settings
        </p>
    </div>

    <!-- Profile card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center gap-6 mb-8">
            <div class="w-20 h-20 rounded-full bg-primary-600 flex items-center justify-center flex-shrink-0">
                <span class="text-3xl font-bold text-white">
                    <?php echo strtoupper(substr($admin['name'] ?? 'A', 0, 1)); ?>
                </span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    <?php echo htmlspecialchars($admin['name'] ?? 'Admin'); ?>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <?php echo htmlspecialchars($admin['email'] ?? ''); ?>
                </p>
                <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 rounded-full">
                    <?php echo ucfirst($admin['role'] ?? 'admin'); ?>
                </span>
            </div>
        </div>

        <form method="POST" action="/admin/profile" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Full Name
                    </label>
                    <input type="text" name="name"
                           value="<?php echo htmlspecialchars($admin['name'] ?? ''); ?>"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email Address
                    </label>
                    <input type="email" name="email"
                           value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Phone
                    </label>
                    <input type="tel" name="phone"
                           value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>"
                           placeholder="e.g. 08012345678"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Company / Business Name
                    </label>
                    <input type="text" name="company"
                           value="<?php echo htmlspecialchars($admin['company'] ?? ''); ?>"
                           placeholder="Optional"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <!-- Change password -->
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                    Change Password
                    <span class="text-xs font-normal text-gray-400 ml-2">
                        (leave blank to keep current)
                    </span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            New Password
                        </label>
                        <input type="password" name="new_password" minlength="8"
                               placeholder="Min. 8 characters"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" name="new_password_confirm"
                               placeholder="Repeat new password"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="/admin/dashboard"
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
