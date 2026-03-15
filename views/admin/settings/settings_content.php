<?php
// Anti-scattering compliant framework initialization
require_once __DIR__ . '/../../../config/bootstrap.php';

// Get centralized data from ViewManager (anti-scattering compliant)
$user = ViewManager::get('user');
$settings = ViewManager::get('settings', [
    'general' => [
        'site_name' => 'Cornerstone Realty',
        'site_email' => 'admin@cornerstone.com',
        'site_phone' => '+1 (555) 123-4567',
        'site_address' => '123 Business Ave, Suite 100, City, State 12345',
        'timezone' => 'America/New_York',
        'currency' => 'USD',
        'date_format' => 'Y-m-d',
        'time_format' => '12-hour'
    ],
    'email' => [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => '587',
        'smtp_username' => 'noreply@cornerstone.com',
        'smtp_password' => '********',
        'smtp_encryption' => 'tls',
        'email_notifications' => true,
        'maintenance_alerts' => true,
        'payment_reminders' => true,
        'new_application_alerts' => true
    ],
    'appearance' => [
        'default_theme' => 'dark',
        'primary_color' => '#3b82f6',
        'company_logo' => '/assets/images/logo.png',
        'favicon' => '/assets/images/favicon.ico'
    ]
]);
?>

<!-- Breadcrumb Navigation -->
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

<!-- Page Header -->
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage your application settings and preferences</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button onclick="saveSettings()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <i class="fas fa-save mr-2"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Settings Navigation Tabs -->
<div class="border-b border-gray-200 dark:border-gray-700 mb-8">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <button onclick="showTab('general')" id="general-tab" class="tab-button border-primary-500 text-primary-600 dark:text-primary-400 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-cog mr-2"></i>
            General
        </button>
        <button onclick="showTab('email')" id="email-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-envelope mr-2"></i>
            Email
        </button>
        <button onclick="showTab('appearance')" id="appearance-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-palette mr-2"></i>
            Appearance
        </button>
        <button onclick="showTab('security')" id="security-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            <i class="fas fa-shield-alt mr-2"></i>
            Security
        </button>
    </nav>
</div>

<!-- Tab Content -->
<div class="space-y-6">
    <!-- General Settings -->
    <div id="general-panel" class="tab-panel">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-6">General Settings</h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Name</label>
                        <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['general']['site_name']); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="site_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Email</label>
                        <input type="email" id="site_email" name="site_email" value="<?php echo htmlspecialchars($settings['general']['site_email']); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="site_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Phone</label>
                        <input type="tel" id="site_phone" name="site_phone" value="<?php echo htmlspecialchars($settings['general']['site_phone']); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Timezone</label>
                        <select id="timezone" name="timezone" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            <option value="America/New_York" <?php echo $settings['general']['timezone'] === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time</option>
                            <option value="America/Chicago" <?php echo $settings['general']['timezone'] === 'America/Chicago' ? 'selected' : ''; ?>>Central Time</option>
                            <option value="America/Denver" <?php echo $settings['general']['timezone'] === 'America/Denver' ? 'selected' : ''; ?>>Mountain Time</option>
                            <option value="America/Los_Angeles" <?php echo $settings['general']['timezone'] === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
                        <select id="currency" name="currency" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            <option value="USD" <?php echo $settings['general']['currency'] === 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                            <option value="EUR" <?php echo $settings['general']['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                            <option value="GBP" <?php echo $settings['general']['currency'] === 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date Format</label>
                        <select id="date_format" name="date_format" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            <option value="Y-m-d" <?php echo $settings['general']['date_format'] === 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                            <option value="m/d/Y" <?php echo $settings['general']['date_format'] === 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                            <option value="d/m/Y" <?php echo $settings['general']['date_format'] === 'd/m/Y' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="site_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Address</label>
                    <textarea id="site_address" name="site_address" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white"><?php echo htmlspecialchars($settings['general']['site_address']); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Settings -->
    <div id="email-panel" class="tab-panel hidden">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-6">Email Configuration</h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="smtp_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SMTP Host</label>
                        <input type="text" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($settings['email']['smtp_host']); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="smtp_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SMTP Port</label>
                        <input type="number" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($settings['email']['smtp_port']); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="smtp_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SMTP Username</label>
                        <input type="text" id="smtp_username" name="smtp_username" value="<?php echo htmlspecialchars($settings['email']['smtp_username']); ?>" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SMTP Encryption</label>
                        <select id="smtp_encryption" name="smtp_encryption" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            <option value="tls" <?php echo $settings['email']['smtp_encryption'] === 'tls' ? 'selected' : ''; ?>>TLS</option>
                            <option value="ssl" <?php echo $settings['email']['smtp_encryption'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                            <option value="none" <?php echo $settings['email']['smtp_encryption'] === 'none' ? 'selected' : ''; ?>>None</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input id="email_notifications" name="email_notifications" type="checkbox" <?php echo $settings['email']['email_notifications'] ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="email_notifications" class="ml-3 text-sm text-gray-700 dark:text-gray-300">Enable email notifications</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="maintenance_alerts" name="maintenance_alerts" type="checkbox" <?php echo $settings['email']['maintenance_alerts'] ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="maintenance_alerts" class="ml-3 text-sm text-gray-700 dark:text-gray-300">Maintenance request alerts</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="payment_reminders" name="payment_reminders" type="checkbox" <?php echo $settings['email']['payment_reminders'] ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="payment_reminders" class="ml-3 text-sm text-gray-700 dark:text-gray-300">Payment reminders</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input id="new_application_alerts" name="new_application_alerts" type="checkbox" <?php echo $settings['email']['new_application_alerts'] ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="new_application_alerts" class="ml-3 text-sm text-gray-700 dark:text-gray-300">New application alerts</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Appearance Settings -->
    <div id="appearance-panel" class="tab-panel hidden">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-6">Appearance</h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="default_theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Theme</label>
                        <select id="default_theme" name="default_theme" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            <option value="light" <?php echo $settings['appearance']['default_theme'] === 'light' ? 'selected' : ''; ?>>Light</option>
                            <option value="dark" <?php echo $settings['appearance']['default_theme'] === 'dark' ? 'selected' : ''; ?>>Dark</option>
                            <option value="system" <?php echo $settings['appearance']['default_theme'] === 'system' ? 'selected' : ''; ?>>System Default</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Primary Color</label>
                        <input type="color" id="primary_color" name="primary_color" value="<?php echo htmlspecialchars($settings['appearance']['primary_color']); ?>" class="mt-1 block w-full h-10 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <div class="mt-6">
                    <div class="flex items-center space-x-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Logo</label>
                            <div class="flex items-center space-x-3">
                                <img src="<?php echo htmlspecialchars($settings['appearance']['company_logo']); ?>" alt="Company Logo" class="h-12 w-12 rounded-lg border border-gray-300 dark:border-gray-600">
                                <button type="button" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    Change Logo
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Favicon</label>
                            <div class="flex items-center space-x-3">
                                <img src="<?php echo htmlspecialchars($settings['appearance']['favicon']); ?>" alt="Favicon" class="h-8 w-8 rounded border border-gray-300 dark:border-gray-600">
                                <button type="button" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    Change Favicon
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Settings -->
    <div id="security-panel" class="tab-panel hidden">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-6">Security Settings</h3>
                
                <div class="space-y-6">
                    <div>
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Password Policy</h4>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="require_strong_password" name="require_strong_password" type="checkbox" checked class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="require_strong_password" class="ml-3 text-sm text-gray-700 dark:text-gray-300">Require strong passwords</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input id="password_expiry" name="password_expiry" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="password_expiry" class="ml-3 text-sm text-gray-700 dark:text-gray-300">Password expiration (90 days)</label>
                            </div>
                            
                            <div>
                                <label for="min_password_length" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Password Length</label>
                                <input type="number" id="min_password_length" name="min_password_length" value="8" min="6" max="20" class="mt-1 block w-32 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Session Security</h4>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="session_timeout" name="session_timeout" type="checkbox" checked class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="session_timeout" class="ml-3 text-sm text-gray-700 dark:text-gray-300">Session timeout after inactivity</label>
                            </div>
                            
                            <div>
                                <label for="session_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Session Duration (minutes)</label>
                                <input type="number" id="session_duration" name="session_duration" value="30" min="5" max="480" class="mt-1 block w-32 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Two-Factor Authentication</h4>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="enable_2fa" name="enable_2fa" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="enable_2fa" class="ml-3 text-sm text-gray-700 dark:text-gray-300">Enable two-factor authentication</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Tab Navigation -->
<script>
function showTab(tabName) {
    // Hide all panels
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
        button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
    });
    
    // Show selected panel
    document.getElementById(tabName + '-panel').classList.remove('hidden');
    
    // Add active state to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
    activeTab.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
}

function saveSettings() {
    const toast = {
        type: 'success',
        message: 'Settings saved successfully!',
        duration: 3000
    };
    
    if (typeof showToast === 'function') {
        showToast(toast);
    }
    
    // Simulate saving settings
    console.log('Settings saved');
}

// Initialize with general tab shown
document.addEventListener('DOMContentLoaded', function() {
    showTab('general');
});
</script>
