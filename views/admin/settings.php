<?php
// Initialize anti-scattering system
require_once __DIR__ . '/../../config/bootstrap.php';

// Get centralized data from ViewManager (anti-scattering compliant)
$user = ViewManager::get('user');
$title = ViewManager::get('title', 'Settings');

// Get current page for navigation highlighting
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$isSettings = strpos($currentPath, '/admin/settings') === 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <!-- Blocking theme script - MUST be first to prevent FOIT -->
    <script>
        // Apply theme immediately before any CSS loads
        (function() {
            var theme = localStorage.getItem('theme');
            // Default to dark if no preference saved (requirement #4)
            if (theme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        dark: {
                            50: '#0f172a',
                            100: '#1e293b',
                            200: '#334155',
                            300: '#475569',
                            400: '#64748b',
                            500: '#6b7280',
                            600: '#7c3aed',
                            700: '#8b5cf6',
                            800: '#94a3b8',
                            900: '#a855f7',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Top Navigation -->
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Cornerstone Realty</h1>
                    </div>
                    
                    <!-- Breadcrumb -->
                    <nav class="ml-8 hidden md:flex space-x-4" aria-label="Breadcrumb">
                        <a href="/admin/dashboard" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                        <span class="text-gray-400 dark:text-gray-600">/</span>
                        <span class="text-gray-700 dark:text-gray-300 px-3 py-2 rounded-md text-sm font-medium">Settings</span>
                    </nav>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" placeholder="Search settings..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Notifications -->
                    <button class="p-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-bell"></i>
                    </button>
                    
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleTheme()" class="p-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:inline"></i>
                    </button>
                    
                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <button onclick="toggleProfile()" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <span class="sr-only">Open user menu</span>
                            <div class="h-8 w-8 rounded-full bg-primary-600 flex items-center justify-center text-white font-medium">
                                <?php echo strtoupper(substr($user['name'] ?? 'A', 0, 1)); ?>
                            </div>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="profileDropdown" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            <div class="py-1">
                                <a href="/admin/profile" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Your Profile</a>
                                <a href="/admin/settings" class="block px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700">Settings</a>
                                <form action="/admin/logout" method="POST">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <!-- Sidebar Content -->
                <div class="flex-1 flex flex-col min-h-0 bg-gray-800">
                    <!-- Navigation -->
                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        <a href="/admin/dashboard" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                        <a href="/admin/properties" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-home mr-3"></i>
                            Properties
                        </a>
                        <a href="/admin/tenants" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-users mr-3"></i>
                            Tenants
                        </a>
                        <a href="/admin/payments" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-credit-card mr-3"></i>
                            Payments
                        </a>
                        <a href="/admin/invoices" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-file-invoice mr-3"></i>
                            Invoices
                        </a>
                        <a href="/admin/maintenance" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-tools mr-3"></i>
                            Maintenance
                        </a>
                        <a href="/admin/communications" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-envelope mr-3"></i>
                            Communications
                        </a>
                        <a href="/admin/documents" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-folder mr-3"></i>
                            Documents
                        </a>
                        <a href="/admin/dashboard/reports" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Reports
                        </a>
                        <a href="/admin/settings" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $isSettings ? 'bg-gray-900 text-white' : ''; ?>">
                            <i class="fas fa-cog mr-3"></i>
                            Settings
                        </a>
                    </nav>
                    
                    <!-- Logout Button -->
                    <div class="border-t border-gray-700 p-4">
                        <form action="/admin/logout" method="POST">
                            <button type="submit" class="w-full flex items-center px-4 py-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-right-from-bracket mr-3"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Settings</h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your account settings and preferences.</p>
                    </div>

                    <!-- Settings Sections -->
                    <div class="space-y-6">
                        <!-- Profile Settings -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
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

                        <!-- Notification Settings -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
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
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
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
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
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
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Data & Privacy</h2>
                                
                                <div class="space-y-4">
                                    <!-- Export Data -->
                                    <div>
                                        <h3 class="text-md font-medium text-gray-900 dark:text-white mb-2">Export Your Data</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Download a copy of your data in various formats.</p>
                                        <div class="mt-3 space-x-3">
                                            <button class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                Export as JSON
                                            </button>
                                            <button class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                Export as CSV
                                            </button>
                                            <button class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
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
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script>
        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = localStorage.getItem('theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.classList.toggle('dark');
            localStorage.setItem('theme', newTheme);
        }

        // Profile Dropdown
        function toggleProfile() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown && !dropdown.contains(event.target) && !event.target.closest('button')) {
                dropdown.classList.add('hidden');
            }
        });

        // Initialize toggle switches
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial toggle states based on current settings
            const toggles = document.querySelectorAll('button[type="button"][class*="translate-x-0"]');
            toggles.forEach(function(toggle) {
                // Add toggle functionality
                toggle.addEventListener('click', function() {
                    const span = toggle.querySelector('span');
                    if (span.classList.contains('translate-x-0')) {
                        span.classList.remove('translate-x-0');
                        span.classList.add('translate-x-5');
                        span.classList.add('bg-primary-600');
                        span.classList.remove('bg-gray-200');
                        span.classList.remove('dark:bg-gray-700');
                    } else {
                        span.classList.add('translate-x-0');
                        span.classList.remove('translate-x-5');
                        span.classList.remove('bg-primary-600');
                        span.classList.add('bg-gray-200');
                        span.classList.add('dark:bg-gray-700');
                    }
                });
            });
        });
    </script>
</body>
</html>
