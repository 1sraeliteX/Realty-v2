<?php
// Initialize anti-scattering system
require_once __DIR__ . '/../../config/bootstrap.php';

// Get centralized data from DataProvider (anti-scattering compliant)
$user = ViewManager::get('user');
$notifications = ViewManager::get('notifications');
$title = ViewManager::get('title', 'Admin Dashboard');

// Get current page for navigation highlighting
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$isDashboard = strpos($currentPath, '/admin/dashboard') === 0 && strpos($currentPath, '/admin/dashboard/') === false;
$isProperties = strpos($currentPath, '/admin/properties') === 0;
$isUnits = strpos($currentPath, '/admin/units') === 0;
$isTenants = strpos($currentPath, '/admin/tenants') === 0;
$isPayments = strpos($currentPath, '/admin/payments') === 0;
$isInvoices = strpos($currentPath, '/admin/invoices') === 0;
$isFinances = strpos($currentPath, '/admin/finances') === 0;
$isMaintenance = strpos($currentPath, '/admin/maintenance') === 0;
$isCommunications = strpos($currentPath, '/admin/communications') === 0;
$isDocuments = strpos($currentPath, '/admin/documents') === 0;
$isReports = strpos($currentPath, '/admin/reports') === 0;
$isSettings = strpos($currentPath, '/admin/settings') === 0;
$isProfile = strpos($currentPath, '/admin/profile') === 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Real Estate Management'; ?></title>
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
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex flex-col h-full">
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between h-16 px-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <i class="fas fa-building text-primary-600 dark:text-primary-400 text-xl mr-3"></i>
                        <span class="text-xl font-semibold text-gray-900 dark:text-white">Cornerstone</span>
                    </div>
                    <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Sidebar Navigation -->
                <div class="flex-1 flex flex-col overflow-y-auto bg-white dark:bg-gray-800">
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
                        <a href="/admin/tenants" class="<?php echo $isTenants ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-users mr-3"></i>
                            Tenants
                        </a>
                        <a href="/admin/tenants-occupants" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
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

                        <!-- Reports Section -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Reports</span>
                        </div>
                        <a href="/admin/reports" class="<?php echo $isReports ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Reports
                        </a>
                        <a href="/admin/dashboard/reports" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-analytics mr-3"></i>
                            Dashboard Reports
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
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
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
                                <input type="text" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Dark mode toggle -->
                        <button onclick="toggleDarkMode()" class="text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-moon dark:hidden"></i>
                            <i class="fas fa-sun hidden dark:block"></i>
                        </button>

                        <!-- Notifications -->
                        <button class="relative text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                        </button>

                        <!-- User Profile -->
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                            </div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200"><?php echo htmlspecialchars($user['name']); ?></p>
                                <p class="text-xs text-primary-600 dark:text-primary-400">Administrator</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                <?php echo $content ?? '<div class="text-center py-8"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1><p class="text-gray-600 dark:text-gray-400 mt-2">Welcome to the admin dashboard</p></div>'; ?>
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

        // Dark mode toggle
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }

        // Initialize dark mode
        if (localStorage.getItem('darkMode') === 'true' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
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
</body>
</html>
