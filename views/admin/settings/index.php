<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Settings';
$pageTitle = 'System Settings';

$content = ob_start();
?>

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage system configuration and preferences</p>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px">
                <a href="#general" class="tab-link py-4 px-6 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-400">
                    General
                </a>
                <a href="#notifications" class="tab-link py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    Notifications
                </a>
                <a href="#security" class="tab-link py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    Security
                </a>
                <a href="#billing" class="tab-link py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    Billing
                </a>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- General Settings -->
            <div id="general" class="tab-content">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">General Settings</h2>
                <form method="POST" action="/admin/settings" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name</label>
                            <input type="text" name="company_name" value="Cornerstone Realty" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Email</label>
                            <input type="email" name="company_email" value="info@cornerstonerealty.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Phone</label>
                            <input type="tel" name="company_phone" value="(555) 123-4567" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Zone</label>
                            <select name="timezone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="America/New_York" selected>Eastern Time (ET)</option>
                                <option value="America/Chicago">Central Time (CT)</option>
                                <option value="America/Denver">Mountain Time (MT)</option>
                                <option value="America/Los_Angeles">Pacific Time (PT)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Address</label>
                        <textarea name="company_address" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">123 Business Ave, Suite 100
Los Angeles, CA 90001</textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notifications Tab (Placeholder) -->
            <div id="notifications" class="tab-content hidden">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notification Settings</h2>
                <p class="text-gray-600 dark:text-gray-400">Configure email and SMS notifications for various system events.</p>
            </div>

            <!-- Security Tab (Placeholder) -->
            <div id="security" class="tab-content hidden">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Security Settings</h2>
                <p class="text-gray-600 dark:text-gray-400">Manage password policies, two-factor authentication, and security configurations.</p>
            </div>

            <!-- Billing Tab (Placeholder) -->
            <div id="billing" class="tab-content hidden">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Billing Settings</h2>
                <p class="text-gray-600 dark:text-gray-400">Configure billing information, payment methods, and subscription management.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Simple tab switching
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active state from all tabs
            tabLinks.forEach(l => {
                l.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
                l.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Activate clicked tab
            this.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            this.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
            
            // Show corresponding content
            const targetId = this.getAttribute('href').substring(1);
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();

// Include the admin dashboard layout
include __DIR__ . '/../simple_layout.php';
?>
