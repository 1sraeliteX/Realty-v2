<?php
// Initialize anti-scattering system
require_once __DIR__ . '/../../config/bootstrap.php';

// Get centralized data from DataProvider (anti-scattering compliant)
$user = ViewManager::get('user') ?? ['name' => 'Admin', 'email' => ''];
$notifications = ViewManager::get('notifications');
$title = ViewManager::get('title', 'Admin Dashboard');

// Load currency preference for this admin using CurrencyHelper (anti-scattering compliant)
$currencySymbol = CurrencyHelper::getSymbol('₦');
$currencyCode   = CurrencyHelper::getCode('NGN');

// Get current page for navigation highlighting
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$isDashboard = strpos($currentPath, '/admin/dashboard') === 0 && strpos($currentPath, '/admin/dashboard/') === false;
$isDashboardReports = strpos($currentPath, '/admin/dashboard/reports') === 0;
$isProperties = strpos($currentPath, '/admin/properties') === 0;
$isUnits = strpos($currentPath, '/admin/units') === 0;
$isTenants = strpos($currentPath, '/admin/tenants') === 0 || strpos($currentPath, '/admin/tenants-occupants') === 0;
$isPayments = strpos($currentPath, '/admin/payments') === 0;
$isInvoices = strpos($currentPath, '/admin/invoices') === 0;
$isFinances = strpos($currentPath, '/admin/finances') === 0;
$isMaintenance = strpos($currentPath, '/admin/maintenance') === 0;
$isCommunications = strpos($currentPath, '/admin/communications') === 0;
$isDocuments = strpos($currentPath, '/admin/documents') === 0;
$isSettings = strpos($currentPath, '/admin/settings') === 0;
$isProfile = strpos($currentPath, '/admin/profile') === 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Real Estate Management'; ?></title>
    
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
                        cream: {
                            50: '#F0EAD6',
                            100: '#E8DFC4',
                            200: '#D4C8A8',
                            300: '#C0B18C',
                            400: '#AC9970',
                            500: '#998154',
                            600: '#856A38',
                            700: '#71531C',
                            800: '#5D3C00',
                            900: '#492500',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-cream-50 dark:bg-gray-900">
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

    <div class="flex h-screen bg-cream-50 dark:bg-gray-900">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-cream-50 dark:bg-gray-800 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex flex-col h-full">
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between h-16 px-4 bg-cream-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <i class="fas fa-building text-primary-600 dark:text-primary-400 text-xl mr-3"></i>
                        <span class="text-xl font-semibold text-gray-900 dark:text-white">Cornerstone</span>
                    </div>
                    <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Sidebar Navigation -->
                <div class="flex-1 flex flex-col overflow-y-auto bg-cream-50 dark:bg-gray-800">
                    <nav class="flex-1 px-2 py-4 space-y-1">
                        <!-- Dashboard -->
                        <a href="/admin/dashboard" class="<?php echo $isDashboard ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-home mr-3"></i>
                            Dashboard
                        </a>

                        <!-- Properties Section -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Properties</span>
                        </div>
                        <a href="/admin/properties" class="<?php echo $isProperties ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-building mr-3"></i>
                            Properties
                        </a>
                        <a href="/admin/units" class="<?php echo $isUnits ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-door-open mr-3"></i>
                            Units
                        </a>

                        <!-- Tenants Section -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Tenants</span>
                        </div>
                        <a href="/admin/tenants-occupants" class="<?php echo $isTenants ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-user-friends mr-3"></i>
                            Tenants & Occupants
                        </a>

                        <!-- Financial Section -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Financial</span>
                        </div>
                        <a href="/admin/payments" class="<?php echo $isPayments ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-credit-card mr-3"></i>
                            Payments
                        </a>
                        <a href="/admin/invoices" class="<?php echo $isInvoices ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-file-invoice mr-3"></i>
                            Invoices
                        </a>
                        <a href="/admin/finances" class="<?php echo $isFinances ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-chart-line mr-3"></i>
                            Finances
                        </a>

                        <!-- Operations Section -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Operations</span>
                        </div>
                        <a href="/admin/maintenance" class="<?php echo $isMaintenance ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-tools mr-3"></i>
                            Maintenance
                        </a>
                        <a href="/admin/communications" class="<?php echo $isCommunications ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-envelope mr-3"></i>
                            Communications
                        </a>
                        <a href="/admin/documents" class="<?php echo $isDocuments ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-folder mr-3"></i>
                            Documents
                        </a>

                        <!-- Settings Section -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Settings</span>
                        </div>
                        <a href="/admin/settings" class="<?php echo $isSettings ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-cog mr-3"></i>
                            Settings
                        </a>
                        <a href="/admin/profile" class="<?php echo $isProfile ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-user mr-3"></i>
                            Profile
                        </a>
                        <button onclick="openCalculator()" class="w-full text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-calculator mr-3"></i>
                            Calculator
                        </button>

                        <!-- Dashboard Reports -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Dashboard</span>
                        </div>
                        <a href="/admin/dashboard/reports" class="<?php echo $isDashboardReports ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-chart-pie mr-3"></i>
                            Dashboard Reports
                        </a>

                        <!-- Logout Button -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Account</span>
                        </div>
                        <form method="POST" action="/admin/logout" class="block">
                            <button type="submit" class="w-full text-left text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <i class="fas fa-right-from-bracket mr-3"></i>
                                Logout
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top Header -->
            <header class="bg-cream-50 dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <!-- Left side -->
                    <div class="flex items-center">
                        <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 lg:hidden">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="ml-4 text-xl font-semibold text-gray-800 dark:text-white"><?php echo $title ?? 'Dashboard'; ?></h1>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="hidden md:block">
                            <div class="relative">
                                <input type="text" id="global-search-input" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                
                                <!-- Search Results Dropdown -->
                                <div id="search-results-dropdown" class="hidden absolute left-0 right-0 mt-2 bg-cream-50 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-y-auto">
                                    <div id="search-results-content" class="p-2">
                                        <!-- Search results will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dark mode toggle -->
                        <?php
                        // Use anti-scattering compliant theme toggle
                        if (!class_exists('ComponentRegistry')) {
                            require_once __DIR__ . '/../../config/bootstrap.php';
                        }
                        ComponentRegistry::load('theme-toggle');
                        echo ThemeToggleComponent::render([
                            'size' => 'text-lg',
                            'class' => 'text-gray-500 hover:text-gray-600 dark:hover:text-gray-300',
                            'id' => 'admin-theme-toggle'
                        ]);
                        ?>

                        <!-- Currency Switcher -->
                        <div class="relative" id="currencySwitcherWrapper">
                            <button id="currencySwitcherBtn" onclick="toggleCurrencySwitcher()" title="Switch currency" class="flex items-center gap-1 px-2 py-1.5 rounded-lg text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600">
                                <span id="navCurrencySymbol"><?php echo htmlspecialchars($currencySymbol); ?></span>
                                <span class="text-xs text-gray-400 hidden sm:inline"><?php echo htmlspecialchars($currencyCode); ?></span>
                                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                            </button>

                            <!-- Currency dropdown -->
                            <div id="currencySwitcherDropdown" class="hidden absolute right-0 mt-2 w-52 bg-cream-50 dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                                <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Select Currency</p>
                                </div>
                                <?php
                                $currencies = [
                                    'NGN' => ['symbol' => '₦', 'name' => 'Nigerian Naira'],
                                    'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
                                    'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
                                    'EUR' => ['symbol' => '€', 'name' => 'Euro'],
                                    'GHS' => ['symbol' => '₵', 'name' => 'Ghanaian Cedi'],
                                    'KES' => ['symbol' => 'KSh','name' => 'Kenyan Shilling'],
                                    'ZAR' => ['symbol' => 'R',  'name' => 'South African Rand'],
                                ];
                                foreach ($currencies as $code => $info): ?>
                                    <button type="button" onclick="switchCurrency('<?php echo $code; ?>', '<?php echo $info['symbol']; ?>')" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors <?php echo $currencyCode === $code ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-semibold' : 'text-gray-700 dark:text-gray-300'; ?>">
                                        <span class="w-8 text-center font-bold text-base"><?php echo $info['symbol']; ?></span>
                                        <span class="flex-1 text-left"><?php echo $info['name']; ?></span>
                                        <span class="text-xs text-gray-400"><?php echo $code; ?></span>
                                        <?php if ($currencyCode === $code): ?>
                                            <i class="fas fa-check text-primary-600 text-xs ml-1"></i>
                                        <?php endif; ?>
                                    </button>
                                <?php endforeach; ?>
                                <div class="px-3 py-2 border-t border-gray-100 dark:border-gray-700">
                                    <a href="/admin/settings" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">More settings →</a>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="relative">
                            <button onclick="toggleNotifications()" class="relative text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-bell"></i>
                                <span id="notification-badge" class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center hidden">0</span>
                            </button>
                            
                            <!-- Notifications Dropdown -->
                            <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-cream-50 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Notifications</h3>
                                        <button onclick="markAllAsRead()" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400">Mark all read</button>
                                    </div>
                                </div>
                                <div id="notifications-list" class="max-h-96 overflow-y-auto">
                                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-bell mb-2"></i>
                                        <p class="text-sm">No notifications</p>
                                    </div>
                                </div>
                                <div class="p-3 border-t border-gray-200 dark:border-gray-700">
                                    <a href="/admin/notifications" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400">View all notifications</a>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <button id="user-menu-btn" class="flex items-center text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium"><?php echo strtoupper(substr($user['name'] ?? 'Admin', 0, 1)); ?></span>
                                </div>
                                <span class="ml-2 text-sm font-medium hidden md:block"><?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?></span>
                                <i class="fas fa-chevron-down ml-2 text-xs"></i>
                            </button>
                            
                            <!-- User Dropdown Menu -->
                            <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-cream-50 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                                </div>
                                <div class="py-1">
                                    <a href="/admin/profile" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-user mr-2"></i> Profile
                                    </a>
                                    <a href="/admin/settings" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-cog mr-2"></i> Settings
                                    </a>
                                    <form method="POST" action="/admin/logout" class="block">
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <i class="fas fa-right-from-bracket mr-2"></i> Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto bg-cream-50 dark:bg-gray-900">
                <div class="p-6 max-w-7xl mx-auto">
                    <?php 
                    // Get content from ViewManager (anti-scattering compliant)
                    $content = ViewManager::get('content');
                    echo $content ?? '<div class="text-center py-8"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1><p class="text-gray-600 dark:text-gray-400 mt-2">Welcome to the admin dashboard</p></div>'; 
                    ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden transition-opacity duration-300" onclick="toggleSidebar()"></div>

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `mb-2 px-4 py-3 rounded-lg shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            } transform transition-all duration-300 translate-x-full`;
            
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 
                        type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'
                    } mr-2"></i>
                    <span>${message}</span>
                </div>
            `;

            document.getElementById('toast-container').appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
            }, 100);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Theme toggle functionality
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            
            // Dispatch custom event for components that need to react to theme changes
            window.dispatchEvent(new CustomEvent('themechange', {
                detail: { isDark: !isDark }
            }));
        }
        
        // Sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            // Toggle sidebar visibility
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('translate-x-0');
            overlay.classList.toggle('hidden');
        }

        // Show PHP session messages as toasts
        document.addEventListener('DOMContentLoaded', function() {
            // Notification system
            let notificationsOpen = false;
            
            // Load notification count
            async function loadNotificationCount() {
                try {
                    const response = await fetch('/api/notifications/count');
                    const data = await response.json();
                    if (data.success) {
                        const badge = document.getElementById('notification-badge');
                        if (data.count > 0) {
                            badge.textContent = data.count > 99 ? '99+' : data.count;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    }
                } catch (error) {
                    console.error('Error loading notification count:', error);
                }
            }
            
            // Load recent notifications
            async function loadNotifications() {
                try {
                    const response = await fetch('/api/notifications/recent');
                    const data = await response.json();
                    if (data.success) {
                        renderNotifications(data.notifications);
                    }
                } catch (error) {
                    console.error('Error loading notifications:', error);
                }
            }
            
            // Render notifications in dropdown
            function renderNotifications(notifications) {
                const list = document.getElementById('notifications-list');
                
                if (notifications.length === 0) {
                    list.innerHTML = `
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-bell mb-2"></i>
                            <p class="text-sm">No notifications</p>
                        </div>
                    `;
                    return;
                }
                
                list.innerHTML = notifications.map(notification => `
                    <div class="notification-item p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer ${!notification.is_read ? 'bg-blue-50 dark:bg-blue-900/20' : ''}" 
                         onclick="markAsRead(${notification.id})">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                <i class="fas ${getNotificationIcon(notification.type, notification.activity_type)} text-${getNotificationColor(notification.type)}-500"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white ${!notification.is_read ? 'font-semibold' : ''}">${notification.title}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">${notification.message}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">${notification.time_ago}</p>
                            </div>
                            ${!notification.is_read ? '<div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></div>' : ''}
                        </div>
                    </div>
                `).join('');
            }
            
            // Get notification icon
            function getNotificationIcon(type, activityType) {
                const icons = {
                    'tenant_created': 'fa-user-plus',
                    'payment_received': 'fa-money-bill',
                    'maintenance_request': 'fa-tools',
                    'lease_expiring': 'fa-clock',
                    'unit_status_change': 'fa-home',
                    'property_created': 'fa-building',
                    'invoice_created': 'fa-file-invoice'
                };
                return icons[activityType] || 'fa-info-circle';
            }
            
            // Get notification color
            function getNotificationColor(type) {
                const colors = {
                    'success': 'green',
                    'warning': 'yellow',
                    'error': 'red',
                    'info': 'blue'
                };
                return colors[type] || 'blue';
            }
            
            // Toggle notifications dropdown
            window.toggleNotifications = function() {
                const dropdown = document.getElementById('notifications-dropdown');
                notificationsOpen = !notificationsOpen;
                
                if (notificationsOpen) {
                    dropdown.classList.remove('hidden');
                    loadNotifications();
                } else {
                    dropdown.classList.add('hidden');
                }
            };
            
            // Mark notification as read
            window.markAsRead = async function(id) {
                try {
                    const response = await fetch('/api/notifications/mark-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    });
                    
                    if (response.ok) {
                        loadNotifications();
                        loadNotificationCount();
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            };
            
            // Mark all notifications as read
            window.markAllAsRead = async function() {
                try {
                    const response = await fetch('/api/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    });
                    
                    if (response.ok) {
                        loadNotifications();
                        loadNotificationCount();
                    }
                } catch (error) {
                    console.error('Error marking all notifications as read:', error);
                }
            };
            
            // Close notifications when clicking outside
            document.addEventListener('click', (e) => {
                const dropdown = document.getElementById('notifications-dropdown');
                const button = e.target.closest('button[onclick="toggleNotifications()"]');
                
                if (!button && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                    notificationsOpen = false;
                }
            });
            
            // Load notification count on page load
            loadNotificationCount();
            
            // Refresh notification count every 30 seconds
            setInterval(loadNotificationCount, 30000);

            // User dropdown functionality
            const userMenuBtn = document.getElementById('user-menu-btn');
            const userDropdown = document.getElementById('user-dropdown');
            
            userMenuBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userMenuBtn?.contains(e.target) && !userDropdown?.contains(e.target)) {
                    userDropdown?.classList.add('hidden');
                }
            });

            // Global Search functionality
            let searchTimeout;
            const searchInput = document.getElementById('global-search-input');
            const searchDropdown = document.getElementById('search-results-dropdown');
            const searchContent = document.getElementById('search-results-content');

            // Debounced search function
            function performSearch(query) {
                if (query.length < 2) {
                    searchDropdown.classList.add('hidden');
                    return;
                }

                fetch(`/admin/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchDropdown.classList.add('hidden');
                    });
            }

            // Display search results
            function displaySearchResults(data) {
                const hasResults = data.properties?.length || data.tenants?.length || 
                                 data.units?.length || data.payments?.length;

                if (!hasResults) {
                    searchContent.innerHTML = `
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-search mb-2"></i>
                            <p class="text-sm">No results found</p>
                        </div>
                    `;
                } else {
                    let html = '';
                    
                    // Properties
                    if (data.properties?.length) {
                        html += `
                            <div class="mb-2">
                                <div class="px-2 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Properties</div>
                                ${data.properties.map(prop => `
                                    <a href="/admin/properties/${prop.id}" class="block px-2 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                        <div class="flex items-center">
                                            <i class="fas fa-building text-blue-500 mr-2"></i>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">${prop.name}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">${prop.address}</div>
                                            </div>
                                        </div>
                                    </a>
                                `).join('')}
                            </div>
                        `;
                    }

                    // Tenants
                    if (data.tenants?.length) {
                        html += `
                            <div class="mb-2">
                                <div class="px-2 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tenants</div>
                                ${data.tenants.map(tenant => `
                                    <a href="/admin/tenants/${tenant.id}" class="block px-2 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                        <div class="flex items-center">
                                            <i class="fas fa-user text-green-500 mr-2"></i>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">${tenant.name}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">${tenant.email || tenant.phone}</div>
                                            </div>
                                        </div>
                                    </a>
                                `).join('')}
                            </div>
                        `;
                    }

                    // Units
                    if (data.units?.length) {
                        html += `
                            <div class="mb-2">
                                <div class="px-2 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Units</div>
                                ${data.units.map(unit => `
                                    <a href="/admin/units?id=${unit.id}" class="block px-2 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                        <div class="flex items-center">
                                            <i class="fas fa-door-open text-purple-500 mr-2"></i>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">${unit.unit_number}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">${unit.property_name}</div>
                                            </div>
                                        </div>
                                    </a>
                                `).join('')}
                            </div>
                        `;
                    }

                    // Payments
                    if (data.payments?.length) {
                        html += `
                            <div class="mb-2">
                                <div class="px-2 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Payments</div>
                                ${data.payments.map(payment => `
                                    <a href="/admin/payments?id=${payment.id}" class="block px-2 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                        <div class="flex items-center">
                                            <i class="fas fa-dollar-sign text-yellow-500 mr-2"></i>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">${payment.amount}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">${payment.tenant_name} - ${payment.status}</div>
                                            </div>
                                        </div>
                                    </a>
                                `).join('')}
                            </div>
                        `;
                    }

                    searchContent.innerHTML = html;
                }

                searchDropdown.classList.remove('hidden');
            }

            // Search input event listener
            searchInput?.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                const query = e.target.value.trim();
                
                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => performSearch(query), 300);
                } else {
                    searchDropdown.classList.add('hidden');
                }
            });

            // Close search dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchInput?.contains(e.target) && !searchDropdown?.contains(e.target)) {
                    searchDropdown.classList.add('hidden');
                }
            });

            // Close search dropdown on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    searchDropdown.classList.add('hidden');
                    searchInput?.blur();
                }
            });

            // Currency Switcher functionality
            window.toggleCurrencySwitcher = function() {
                const dropdown = document.getElementById('currencySwitcherDropdown');
                dropdown.classList.toggle('hidden');
            };

            window.switchCurrency = function(code, symbol) {
                // Update button display
                document.getElementById('navCurrencySymbol').textContent = symbol;
                const codeDisplay = document.querySelector('#currencySwitcherBtn .text-xs');
                if (codeDisplay) {
                    codeDisplay.textContent = code;
                }

                // Close dropdown
                document.getElementById('currencySwitcherDropdown').classList.add('hidden');

                // Persist to backend
                fetch('/admin/settings/currency', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ currency: code, symbol: symbol })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Currency updated successfully', 'success');
                        // Reload to propagate symbol app-wide via CurrencyHelper
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast('Failed to update currency', 'error');
                    }
                })
                .catch(err => {
                    console.error('Currency switch failed:', err);
                    showToast('Failed to update currency', 'error');
                });
            };

            // Close currency dropdown when clicking outside
            document.addEventListener('click', (e) => {
                const wrapper = document.getElementById('currencySwitcherWrapper');
                const dropdown = document.getElementById('currencySwitcherDropdown');
                
                if (wrapper && !wrapper.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            <?php if (isset($_SESSION['success'])): ?>
                showToast('<?php echo addslashes($_SESSION['success']); ?>', 'success');
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                showToast('<?php echo addslashes($_SESSION['error']); ?>', 'error');
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['errors'])): ?>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    showToast('<?php echo addslashes($error); ?>', 'error');
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
        });
    </script>
    
    <!-- Include Calculator Component -->
    <?php \ComponentRegistry::load('calculator-component'); ?>
</body>
</html>
