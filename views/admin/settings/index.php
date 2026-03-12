<?php
// Initialize anti-scattering system
require_once __DIR__ . '/../../../config/bootstrap.php';

// Anti-scattering compliance: Get centralized data from ViewManager
$user = \ViewManager::get('user');
$notifications = \ViewManager::get('notifications');
$settings = \ViewManager::get('settings');

// Set page title
\ViewManager::set('title', 'Settings');

// Build settings page content using UIComponents (anti-scattering compliant)
ob_start();
?>

<!-- Breadcrumbs -->
<nav class="flex mb-6" aria-label="Breadcrumb">
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
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Settings</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Settings Page Content -->
<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your application settings and preferences</p>
            </div>
            <div class="flex space-x-3">
                <button type="button" onclick="resetSettings()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-undo mr-2"></i>Reset to Defaults
                </button>
                <button type="submit" form="settings-form" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Settings Navigation -->
    <div class="bg-white dark:bg-gray-800 rounded-full shadow-sm border border-gray-200 dark:border-gray-700 mb-6 px-2 py-2">
        <nav class="flex space-x-1" aria-label="Settings tabs">
            <button onclick="showTab('general')" class="settings-tab flex items-center px-4 py-2 rounded-full text-sm font-medium text-black bg-green-100 dark:bg-green-900/30" data-tab="general">
                <i class="fas fa-cog mr-2"></i>General
                <span class="ml-2 w-5 h-5 bg-green-600 dark:bg-green-500 text-white text-xs rounded-full flex items-center justify-center">4</span>
            </button>
            <button onclick="showTab('email')" class="settings-tab flex items-center px-4 py-2 rounded-full text-sm font-medium text-black hover:bg-gray-100 dark:hover:bg-gray-700" data-tab="email">
                <i class="fas fa-envelope mr-2"></i>Email
            </button>
            <button onclick="showTab('security')" class="settings-tab flex items-center px-4 py-2 rounded-full text-sm font-medium text-black hover:bg-gray-100 dark:hover:bg-gray-700" data-tab="security">
                <i class="fas fa-shield-alt mr-2"></i>Security
            </button>
            <button onclick="showTab('notifications')" class="settings-tab flex items-center px-4 py-2 rounded-full text-sm font-medium text-black hover:bg-gray-100 dark:hover:bg-gray-700" data-tab="notifications">
                <i class="fas fa-bell mr-2"></i>Notifications
            </button>
            <button onclick="showTab('appearance')" class="settings-tab flex items-center px-4 py-2 rounded-full text-sm font-medium text-black hover:bg-gray-100 dark:hover:bg-gray-700" data-tab="appearance">
                <i class="fas fa-palette mr-2"></i>Appearance
            </button>
        </nav>
    </div>

        <!-- Settings Form -->
        <form id="settings-form" method="POST" action="/admin/settings" class="p-6">
            
            <!-- General Settings -->
            <div id="general-tab" class="settings-tab-content">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">General Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site Name</label>
                                <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['general']['site_name']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site Email</label>
                                <input type="email" name="site_email" value="<?php echo htmlspecialchars($settings['general']['site_email']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site Phone</label>
                                <input type="tel" name="site_phone" value="<?php echo htmlspecialchars($settings['general']['site_phone']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site Address</label>
                                <input type="text" name="site_address" value="<?php echo htmlspecialchars($settings['general']['site_address']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                                <select name="timezone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="America/New_York" <?php echo $settings['general']['timezone'] === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time</option>
                                    <option value="America/Chicago" <?php echo $settings['general']['timezone'] === 'America/Chicago' ? 'selected' : ''; ?>>Central Time</option>
                                    <option value="America/Denver" <?php echo $settings['general']['timezone'] === 'America/Denver' ? 'selected' : ''; ?>>Mountain Time</option>
                                    <option value="America/Los_Angeles" <?php echo $settings['general']['timezone'] === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency</label>
                                <select name="currency" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="USD" <?php echo $settings['general']['currency'] === 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                                    <option value="EUR" <?php echo $settings['general']['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                                    <option value="GBP" <?php echo $settings['general']['currency'] === 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Settings -->
            <div id="email-tab" class="settings-tab-content hidden">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Email Configuration</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Host</label>
                                <input type="text" name="smtp_host" value="<?php echo htmlspecialchars($settings['email']['smtp_host']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Port</label>
                                <input type="text" name="smtp_port" value="<?php echo htmlspecialchars($settings['email']['smtp_port']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Username</label>
                                <input type="text" name="smtp_username" value="<?php echo htmlspecialchars($settings['email']['smtp_username']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Password</label>
                                <input type="password" name="smtp_password" placeholder="••••••••" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Encryption</label>
                                <select name="smtp_encryption" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="tls" <?php echo $settings['email']['smtp_encryption'] === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                    <option value="ssl" <?php echo $settings['email']['smtp_encryption'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                    <option value="none" <?php echo $settings['email']['smtp_encryption'] === 'none' ? 'selected' : ''; ?>>None</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Name</label>
                                <input type="text" name="email_from_name" value="<?php echo htmlspecialchars($settings['email']['email_from_name']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="button" onclick="testEmailSettings()" class="px-4 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30">
                                <i class="fas fa-paper-plane mr-2"></i>Test Email Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div id="security-tab" class="settings-tab-content hidden">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Security Configuration</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Session Timeout (minutes)</label>
                                <input type="number" name="session_timeout" value="<?php echo htmlspecialchars($settings['security']['session_timeout']); ?>" min="5" max="480" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Password Length</label>
                                <input type="number" name="password_min_length" value="<?php echo htmlspecialchars($settings['security']['password_min_length']); ?>" min="6" max="50" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Login Attempts</label>
                                <input type="number" name="login_attempts" value="<?php echo htmlspecialchars($settings['security']['login_attempts']); ?>" min="3" max="10" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lockout Duration (minutes)</label>
                                <input type="number" name="lockout_duration" value="<?php echo htmlspecialchars($settings['security']['lockout_duration']); ?>" min="5" max="60" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                        <div class="mt-6 space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="require_2fa" <?php echo $settings['security']['require_2fa'] ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Require Two-Factor Authentication</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div id="notifications-tab" class="settings-tab-content hidden">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notification Preferences</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Notifications</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Receive notifications via email</p>
                                </div>
                                <input type="checkbox" name="email_notifications" <?php echo $settings['notifications']['email_notifications'] ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">SMS Notifications</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Receive notifications via SMS</p>
                                </div>
                                <input type="checkbox" name="sms_notifications" <?php echo $settings['notifications']['sms_notifications'] ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Payment Reminders</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Send reminders for upcoming payments</p>
                                </div>
                                <input type="checkbox" name="payment_reminders" <?php echo $settings['notifications']['payment_reminders'] ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Maintenance Alerts</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Alert for maintenance requests</p>
                                </div>
                                <input type="checkbox" name="maintenance_alerts" <?php echo $settings['notifications']['maintenance_alerts'] ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">New Application Alerts</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Notify when new applications are received</p>
                                </div>
                                <input type="checkbox" name="new_application_alerts" <?php echo $settings['notifications']['new_application_alerts'] ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div id="appearance-tab" class="settings-tab-content hidden">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Appearance Configuration</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Theme</label>
                                <select name="default_theme" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="light" <?php echo $settings['appearance']['default_theme'] === 'light' ? 'selected' : ''; ?>>Light</option>
                                    <option value="dark" <?php echo $settings['appearance']['default_theme'] === 'dark' ? 'selected' : ''; ?>>Dark</option>
                                    <option value="system" <?php echo $settings['appearance']['default_theme'] === 'system' ? 'selected' : ''; ?>>System</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Primary Color</label>
                                <input type="color" name="primary_color" value="<?php echo htmlspecialchars($settings['appearance']['primary_color']); ?>" class="w-full h-10 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Logo URL</label>
                                <input type="text" name="company_logo" value="<?php echo htmlspecialchars($settings['appearance']['company_logo']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Favicon URL</label>
                                <input type="text" name="favicon" value="<?php echo htmlspecialchars($settings['appearance']['favicon']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
// Settings tab functionality
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.settings-tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active state from all tab buttons
    document.querySelectorAll('.settings-tab').forEach(btn => {
        btn.classList.remove('bg-green-100', 'dark:bg-green-900/30');
        btn.classList.add('hover:bg-gray-100', 'dark:hover:bg-gray-700');
        
        // Remove badge from all tabs
        const badge = btn.querySelector('span');
        if (badge) {
            badge.remove();
        }
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active state to selected tab button
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.remove('hover:bg-gray-100', 'dark:hover:bg-gray-700');
    activeBtn.classList.add('bg-green-100', 'dark:bg-green-900/30');
    
    // Add badge to active tab (only for General tab in this example)
    if (tabName === 'general') {
        const badge = document.createElement('span');
        badge.className = 'ml-2 w-5 h-5 bg-green-600 dark:bg-green-500 text-white text-xs rounded-full flex items-center justify-center';
        badge.textContent = '4';
        activeBtn.appendChild(badge);
    }
}

// Reset settings to defaults
function resetSettings() {
    if (confirm('Are you sure you want to reset all settings to their default values? This action cannot be undone.')) {
        showToast('Settings have been reset to defaults', 'info');
        // In a real implementation, this would call an API endpoint
        setTimeout(() => {
            location.reload();
        }, 1500);
    }
}

// Test email settings
function testEmailSettings() {
    showToast('Sending test email...', 'info');
    // In a real implementation, this would call an API endpoint
    setTimeout(() => {
        showToast('Test email sent successfully!', 'success');
    }, 2000);
}

// Initialize first tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('general');
});
</script>

<?php
// Capture content and set it for layout rendering
$content = ob_get_clean();

// Set content for layout (anti-scattering compliant)
\ViewManager::set('content', $content);

// Include the dashboard layout directly
include __DIR__ . '/../dashboard_layout.php';
?>
