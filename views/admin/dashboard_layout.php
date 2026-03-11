<?php
// Initialize anti-scattering system
require_once __DIR__ . '/../../config/bootstrap.php';

// Get centralized data from DataProvider (anti-scattering compliant)
$user = ViewManager::get('user');
$notifications = ViewManager::get('notifications');
$title = ViewManager::get('title', 'Admin Dashboard');
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Real Estate Management'; ?></title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Local CSS files -->
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <script>
        // Dark mode configuration
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

<!-- Admin Dashboard Layout -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Mobile sidebar backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden hidden"></div>
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
        <!-- Sidebar header -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <i class="fas fa-building text-primary-600 dark:text-primary-400 text-xl mr-3"></i>
                <span class="text-xl font-semibold text-gray-900 dark:text-white">Cornerstone</span>
            </div>
            <button id="close-sidebar" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="p-4 space-y-2">
            <a href="/admin/dashboard" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300">
                <i class="fas fa-home mr-3"></i>
                Dashboard
            </a>
            
            <div class="pt-4 pb-2">
                <span class="px-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Properties</span>
            </div>
            <a href="/admin/properties" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-building mr-3"></i>
                Properties
            </a>
            <a href="/admin/units" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-door-open mr-3"></i>
                Units
            </a>
            
            <div class="pt-4 pb-2">
                <span class="px-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Tenants</span>
            </div>
            <a href="/admin/tenants" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-users mr-3"></i>
                Tenants
            </a>
            <a href="/admin/tenants-occupants" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-user-friends mr-3"></i>
                Tenants & Occupants
            </a>
            
            <div class="pt-4 pb-2">
                <span class="px-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Financial</span>
            </div>
            <a href="/admin/payments" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-credit-card mr-3"></i>
                Payments
            </a>
            <a href="/admin/invoices" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-file-invoice mr-3"></i>
                Invoices
            </a>
            <a href="/admin/finances" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-chart-line mr-3"></i>
                Finances
            </a>
            
            <div class="pt-4 pb-2">
                <span class="px-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Operations</span>
            </div>
            <a href="/admin/maintenance" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-tools mr-3"></i>
                Maintenance
            </a>
            <a href="/admin/communications" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-envelope mr-3"></i>
                Communications
            </a>
            <a href="/admin/documents" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-folder mr-3"></i>
                Documents
            </a>
            
            <div class="pt-4 pb-2">
                <span class="px-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Reports</span>
            </div>
            <a href="/admin/reports" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-chart-bar mr-3"></i>
                Reports
            </a>
            <a href="/admin/dashboard/reports" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-analytics mr-3"></i>
                Dashboard Reports
            </a>
            
            <div class="pt-4 pb-2">
                <span class="px-4 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Settings</span>
            </div>
            <a href="/admin/settings" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-cog mr-3"></i>
                Settings
            </a>
            <a href="/admin/profile" class="nav-item flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-user mr-3"></i>
                Profile
            </a>
        </nav>
    </aside>
    
    <!-- Main content -->
    <div class="lg:pl-64">
        <!-- Top navigation -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                <!-- Left side -->
                <div class="flex items-center">
                    <button id="open-sidebar" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <!-- Breadcrumbs -->
                    <nav class="hidden md:flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="/admin/dashboard" class="text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
                                    Dashboard
                                </a>
                            </li>
                        </ol>
                    </nav>
                </div>
                
                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="hidden md:block">
                        <div class="relative">
                            <input type="text" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Dark mode toggle -->
                    <button id="dark-mode-toggle" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:block"></i>
                    </button>
                    
                    <!-- Notifications -->
                    <div class="relative">
                        <button id="notification-btn" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 relative">
                            <i class="fas fa-bell"></i>
                            <?php if (!empty($notifications)): ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                                    <?php echo count(array_filter($notifications, fn($n) => !$n['read'])); ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        
                        <!-- Notifications dropdown -->
                        <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Notifications</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <?php if (!empty($notifications)): ?>
                                    <?php foreach ($notifications as $notification): ?>
                                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-<?php echo $notification['type'] === 'success' ? 'check-circle text-green-500' : ($notification['type'] === 'warning' ? 'exclamation-triangle text-yellow-500' : ($notification['type'] === 'error' ? 'exclamation-circle text-red-500' : 'info-circle text-blue-500')); ?>"></i>
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <p class="text-sm text-gray-900 dark:text-white"><?php echo $notification['message']; ?></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?php echo $notification['time']; ?></p>
                                                </div>
                                                <?php if (!$notification['read']): ?>
                                                    <div class="w-2 h-2 bg-primary-500 rounded-full flex-shrink-0 mt-2"></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                        No notifications
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User menu -->
                    <div class="relative">
                        <button id="user-menu-btn" class="flex items-center text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            <?php echo UIComponents::avatar($user['name'], $user['avatar'], 'small'); ?>
                            <span class="ml-2 text-sm font-medium hidden md:block"><?php echo $user['name']; ?></span>
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        
                        <!-- User dropdown -->
                        <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo $user['name']; ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo $user['email']; ?></p>
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
        
        <!-- Main content area -->
        <main class="p-4 sm:p-6 lg:p-8">
            <?php echo $content ?? '<div class="text-center py-8"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1><p class="text-gray-600 dark:text-gray-400 mt-2">Welcome to the admin dashboard</p></div>'; ?>
        </main>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const openBtn = document.getElementById('open-sidebar');
    const closeBtn = document.getElementById('close-sidebar');
    
    // Add debug info
    console.log('Sidebar elements found:', {
        sidebar: !!sidebar,
        backdrop: !!backdrop,
        openBtn: !!openBtn,
        closeBtn: !!closeBtn,
        sidebarClasses: sidebar?.className,
        isHidden: sidebar?.classList.contains('-translate-x-full')
    });
    
    // Test if sidebar is working on desktop
    if (window.innerWidth >= 1024) {
        console.log('Desktop detected - sidebar should be visible');
        sidebar?.classList.remove('-translate-x-full');
    }
    
    openBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        console.log('Hamburger menu clicked');
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent body scroll when sidebar is open
    });
    
    closeBtn?.addEventListener('click', closeSidebar);
    backdrop?.addEventListener('click', closeSidebar);
    
    function closeSidebar() {
        console.log('Closing sidebar');
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        document.body.style.overflow = ''; // Restore body scroll
    }
    
    // Dark mode toggle
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    darkModeToggle?.addEventListener('click', () => {
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
    });
    
    // Notifications dropdown
    const notificationBtn = document.getElementById('notification-btn');
    const notificationsDropdown = document.getElementById('notifications-dropdown');
    
    notificationBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationsDropdown.classList.toggle('hidden');
        userDropdown.classList.add('hidden');
    });
    
    // User dropdown
    const userMenuBtn = document.getElementById('user-menu-btn');
    const userDropdown = document.getElementById('user-dropdown');
    
    userMenuBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('hidden');
        notificationsDropdown.classList.add('hidden');
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        notificationsDropdown.classList.add('hidden');
        userDropdown.classList.add('hidden');
    });
    
    // Toast notification system
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `mb-2 px-4 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
        }`;
        toast.textContent = message;
        
        const container = document.getElementById('toast-container');
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                container.removeChild(toast);
            }, 300);
        }, 3000);
    });
</script>
</body>
</html>
