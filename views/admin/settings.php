<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from ViewManager (anti-scattering compliant)
$user = ViewManager::get('user');
$currency = ViewManager::get('currency');
$currency_symbol = ViewManager::get('currency_symbol');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Settings');
ViewManager::set('pageTitle', 'Settings');
ViewManager::set('pageDescription', 'Manage your account settings and preferences');
ViewManager::set('activeMenu', 'settings');

ob_start();
?>
                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Settings</h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your account settings and preferences.</p>
                    </div>

                    <!-- Settings Sections -->
                    <div class="space-y-6">
                        <!-- Profile Settings -->
                        <div class="bg-cream-50 dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Settings</h2>
                                
                                <form class="space-y-6">
                                    <!-- Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    
                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    
                                    <!-- Phone -->
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    
                                    <!-- Business Name -->
                                    <div>
                                        <label for="business_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Business Name</label>
                                        <input type="text" id="business_name" name="business_name" value="<?php echo htmlspecialchars($user['business_name'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    
                                    <!-- Save Button -->
                                    <div class="flex justify-end">
                                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Currency & Regional Settings -->
                        <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center">
                                <i class="fas fa-coins mr-2 text-primary-600"></i>
                                Currency & Regional
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                Choose your preferred currency for displaying financial data across your dashboard.
                            </p>

                            <form method="POST" action="/admin/settings">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Currency selector -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Display Currency
                                        </label>
                                        <select name="currency" id="currencySelect" onchange="previewCurrency(this.value)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                            <option value="NGN" <?php echo ($currency ?? 'NGN') === 'NGN' ? 'selected' : ''; ?>>
                                                ₦ — Nigerian Naira (NGN)
                                            </option>
                                            <option value="USD" <?php echo ($currency ?? '') === 'USD' ? 'selected' : ''; ?>>
                                                $ — US Dollar (USD)
                                            </option>
                                            <option value="GBP" <?php echo ($currency ?? '') === 'GBP' ? 'selected' : ''; ?>>
                                                £ — British Pound (GBP)
                                            </option>
                                            <option value="EUR" <?php echo ($currency ?? '') === 'EUR' ? 'selected' : ''; ?>>
                                                € — Euro (EUR)
                                            </option>
                                            <option value="GHS" <?php echo ($currency ?? '') === 'GHS' ? 'selected' : ''; ?>>
                                                ₵ — Ghanaian Cedi (GHS)
                                            </option>
                                            <option value="KES" <?php echo ($currency ?? '') === 'KES' ? 'selected' : ''; ?>>
                                                KSh — Kenyan Shilling (KES)
                                            </option>
                                            <option value="ZAR" <?php echo ($currency ?? '') === 'ZAR' ? 'selected' : ''; ?>>
                                                R — South African Rand (ZAR)
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Live preview -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Preview
                                        </label>
                                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Rent price example:
                                            </p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1" id="currencyPreview">
                                                <?php echo $currency_symbol ?? '₦'; ?>1,200,000
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                Symbol: <span id="symbolPreview" class="font-semibold text-primary-600">
                                                    <?php echo $currency_symbol ?? '₦'; ?>
                                                </span>
                                                · Code: <span id="codePreview" class="font-semibold">
                                                    <?php echo $currency ?? 'NGN'; ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        Save Currency Settings
                                    </button>
                                </div>
                            </form>

                            <script>
                            const currencySymbols = {
                                NGN: '₦', USD: '$', GBP: '£',
                                EUR: '€', GHS: '₵', KES: 'KSh', ZAR: 'R'
                            };
                            function previewCurrency(code) {
                                const sym = currencySymbols[code] || '₦';
                                document.getElementById('currencyPreview').textContent = sym + '1,200,000';
                                document.getElementById('symbolPreview').textContent = sym;
                                document.getElementById('codePreview').textContent = code;
                            }
                            </script>
                        </div>

                        <!-- Notification Settings -->
                        <div class="bg-cream-50 dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notification Preferences</h2>
                                
                                <div class="space-y-4">
                                    <!-- Email Notifications -->
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label for="email_notifications" class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Notifications</label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Receive email updates for important activities</p>
                                        </div>
                                        <button class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" type="button">
                                            <span class="sr-only">Toggle email notifications</span>
                                            <span class="translate-x-0 inline-flex h-6 w-11 rounded-full bg-gray-200 dark:bg-gray-700"></span>
                                        </button>
                                    </div>
                                    
                                    <!-- SMS Notifications -->
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label for="sms_notifications" class="text-sm font-medium text-gray-700 dark:text-gray-300">SMS Notifications</label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Get SMS alerts for urgent matters</p>
                                        </div>
                                        <button class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" type="button">
                                            <span class="sr-only">Toggle SMS notifications</span>
                                            <span class="translate-x-0 inline-flex h-6 w-11 rounded-full bg-gray-200 dark:bg-gray-700"></span>
                                        </button>
                                    </div>
                                    
                                    <!-- Push Notifications -->
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label for="push_notifications" class="text-sm font-medium text-gray-700 dark:text-gray-300">Push Notifications</label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Browser push notifications for real-time updates</p>
                                        </div>
                                        <button class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" type="button">
                                            <span class="sr-only">Toggle push notifications</span>
                                            <span class="translate-x-0 inline-flex h-6 w-11 rounded-full bg-gray-200 dark:bg-gray-700"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Appearance Settings -->
                        <div class="bg-cream-50 dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appearance</h2>
                                
                                <div class="space-y-4">
                                    <!-- Theme -->
                                    <div>
                                        <label for="theme" class="text-sm font-medium text-gray-700 dark:text-gray-300">Theme Preference</label>
                                        <select id="theme" name="theme" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                            <option value="light">Light</option>
                                            <option value="dark">Dark</option>
                                            <option value="auto">System Default</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Language -->
                                    <div>
                                        <label for="language" class="text-sm font-medium text-gray-700 dark:text-gray-300">Language</label>
                                        <select id="language" name="language" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                            <option value="en">English</option>
                                            <option value="es">Español</option>
                                            <option value="fr">Français</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Timezone -->
                                    <div>
                                        <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Timezone</label>
                                        <select id="timezone" name="timezone" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">Eastern Time (ET)</option>
                                            <option value="America/Chicago">Central Time (CT)</option>
                                            <option value="America/Denver">Mountain Time (MT)</option>
                                            <option value="America/Los_Angeles">Pacific Time (PT)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="bg-cream-50 dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Security</h2>
                                
                                <div class="space-y-4">
                                    <!-- Change Password -->
                                    <div>
                                        <h3 class="text-md font-medium text-gray-900 dark:text-white mb-2">Change Password</h3>
                                        <div class="space-y-3">
                                            <div>
                                                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                                                <input type="password" id="current_password" name="current_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                                            <div>
                                                <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                                                <input type="password" id="new_password" name="new_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                                            <div>
                                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                                                <input type="password" id="confirm_password" name="confirm_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="button" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                Update Password
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Two-Factor Authentication -->
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-md font-medium text-gray-900 dark:text-white">Two-Factor Authentication</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Add an extra layer of security to your account</p>
                                        </div>
                                        <button class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" type="button">
                                            <span class="sr-only">Toggle two-factor authentication</span>
                                            <span class="translate-x-0 inline-flex h-6 w-11 rounded-full bg-gray-200 dark:bg-gray-700"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data & Privacy -->
                        <div class="bg-cream-50 dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Data & Privacy</h2>
                                
                                <div class="space-y-4">
                                    <!-- Export Data -->
                                    <div>
                                        <h3 class="text-md font-medium text-gray-900 dark:text-white mb-2">Export Your Data</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Download a copy of your data in various formats.</p>
                                        <div class="mt-3 space-x-3">
                                            <button class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-cream-50 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                Export as JSON
                                            </button>
                                            <button class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-cream-50 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                Export as CSV
                                            </button>
                                            <button class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-cream-50 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                Export as PDF
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Delete Account -->
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                        <h3 class="text-md font-medium text-red-600 dark:text-red-400 mb-2">Delete Account</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Permanently delete your account and all associated data. This action cannot be undone.</p>
                                        <div class="mt-3">
                                            <button type="button" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Delete My Account
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
<?php
$content = ob_get_clean();

// Set content for layout (anti-scattering compliant)
ViewManager::set('content', $content);

// Include layout directly (anti-scattering compliant)
include __DIR__ . '/../../views/admin/dashboard_layout.php';
?>
