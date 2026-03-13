<?php
// Initialize anti-scattering system
require_once __DIR__ . '/../../config/bootstrap.php';

// Get centralized data from DataProvider (anti-scattering compliant)
$user = ViewManager::get('user');
$notifications = ViewManager::get('notifications');
$title = ViewManager::get('title', 'Super Admin Dashboard');

// Get current page for navigation highlighting
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$isDashboard = strpos($currentPath, '/superadmin/dashboard') === 0 && strpos($currentPath, '/superadmin/dashboard/') === false;
$isAdmins = strpos($currentPath, '/superadmin/admins') === 0;
$isProperties = strpos($currentPath, '/properties') === 0;
$isUnits = strpos($currentPath, '/units') === 0;
$isTenants = strpos($currentPath, '/tenants') === 0 || strpos($currentPath, '/tenants-occupants') === 0;
$isPayments = strpos($currentPath, '/payments') === 0;
$isInvoices = strpos($currentPath, '/invoices') === 0;
$isFinances = strpos($currentPath, '/finances') === 0;
$isMaintenance = strpos($currentPath, '/maintenance') === 0;
$isCommunications = strpos($currentPath, '/communications') === 0;
$isDocuments = strpos($currentPath, '/documents') === 0;
$isReports = strpos($currentPath, '/reports') === 0;
$isSettings = strpos($currentPath, '/settings') === 0;
$isProfile = strpos($currentPath, '/profile') === 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Super Admin Platform'; ?></title>
    
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
                <div class="flex items-center justify-between h-16 px-4 bg-gradient-to-r from-purple-600 to-purple-700 dark:from-purple-800 dark:to-purple-900">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-white text-xl mr-3"></i>
                        <div class="text-left">
                            <span class="text-white font-bold text-lg">Cornerstone</span>
                            <p class="text-white text-xs opacity-90">Platform Admin</p>
                        </div>
                    </div>
                    <button onclick="toggleSidebar()" class="lg:hidden text-white hover:text-gray-200 p-2 rounded-lg hover:bg-purple-700 dark:hover:bg-purple-800">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Sidebar Navigation -->
                <div class="flex-1 flex flex-col overflow-y-auto bg-white dark:bg-gray-800">
                    <nav class="flex-1 px-2 py-4 space-y-1">
                        <!-- Dashboard -->
                        <a href="/superadmin/dashboard" class="<?php echo $isDashboard ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Platform Dashboard
                        </a>

                        <!-- Administration Section -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Administration</span>
                        </div>
                        <a href="/superadmin/admins" class="<?php echo $isAdmins ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-user-shield mr-3"></i>
                            Admin Management
                        </a>

                        <!-- Platform Overview Section -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Platform Overview</span>
                        </div>
                        <a href="/properties" class="<?php echo $isProperties ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-building mr-3"></i>
                            All Properties
                        </a>
                        <a href="/tenants" class="<?php echo $isTenants ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-users mr-3"></i>
                            All Tenants
                        </a>
                        <a href="/units" class="<?php echo $isUnits ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-door-open mr-3"></i>
                            All Units
                        </a>

                        <!-- Financial Overview -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Financial Overview</span>
                        </div>
                        <a href="/payments" class="<?php echo $isPayments ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-chart-line mr-3"></i>
                            Platform Finances
                        </a>
                        <a href="/invoices" class="<?php echo $isInvoices ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-file-invoice-dollar mr-3"></i>
                            All Invoices
                        </a>

                        <!-- Operations -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Operations</span>
                        </div>
                        <a href="/maintenance" class="<?php echo $isMaintenance ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-tools mr-3"></i>
                            Maintenance Requests
                        </a>
                        <a href="/communications" class="<?php echo $isCommunications ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-envelope mr-3"></i>
                            Communications
                        </a>
                        <a href="/documents" class="<?php echo $isDocuments ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-folder mr-3"></i>
                            Documents
                        </a>
                        <a href="/reports" class="<?php echo $isReports ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Platform Reports
                        </a>

                        <!-- System Settings -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">System</span>
                        </div>
                        <a href="/settings" class="<?php echo $isSettings ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-cog mr-3"></i>
                            System Settings
                        </a>
                        <a href="/profile" class="<?php echo $isProfile ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'; ?> group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-user mr-3"></i>
                            Profile
                        </a>

                        <!-- Logout Button -->
                        <div class="pt-4 pb-2">
                            <span class="px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Account</span>
                        </div>
                        <form method="POST" action="/superadmin/logout" class="block">
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
                        <h1 class="ml-4 text-xl font-semibold text-gray-800 dark:text-white"><?php echo $title ?? 'Super Admin Dashboard'; ?></h1>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="hidden md:block">
                            <div class="relative">
                                <input type="text" placeholder="Search platform..." class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
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
                            'id' => 'superadmin-theme-toggle'
                        ]);
                        ?>

                        <!-- Notifications -->
                        <button class="relative text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                        </button>

                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <button id="user-menu-btn" class="flex items-center text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                                </div>
                                <span class="ml-2 text-sm font-medium hidden md:block"><?php echo htmlspecialchars($user['name']); ?></span>
                                <i class="fas fa-chevron-down ml-2 text-xs"></i>
                            </button>
                            
                            <!-- User Dropdown Menu -->
                            <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($user['name']); ?></p>
                                    <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Super Admin</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                <div class="py-1">
                                    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-user mr-2"></i> Profile
                                    </a>
                                    <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-cog mr-2"></i> Settings
                                    </a>
                                    <form method="POST" action="/superadmin/logout" class="block">
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

            <!-- Breadcrumb Navigation -->
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-3">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="/superadmin/dashboard" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            <i class="fas fa-shield-alt mr-1"></i>Super Admin
                        </a>
                    </li>
                    <?php
                    // Generate breadcrumb based on current page
                    $path = parse_url($currentPath, PHP_URL_PATH);
                    $path = rtrim($path, '/');
                    
                    $breadcrumbItems = [];
                    
                    if ($path === '/superadmin/dashboard') {
                        // Dashboard is already shown as home
                    } elseif (strpos($path, '/superadmin/admins') === 0) {
                        $breadcrumbItems[] = ['name' => 'Admin Management', 'url' => '/superadmin/admins'];
                    } elseif (strpos($path, '/properties') === 0) {
                        $breadcrumbItems[] = ['name' => 'All Properties', 'url' => '/properties'];
                        if ($path === '/properties/create') {
                            $breadcrumbItems[] = ['name' => 'Add Property', 'url' => null];
                        } elseif (preg_match('/\/properties\/(\d+)(\/edit)?/', $path, $matches)) {
                            $breadcrumbItems[] = ['name' => 'Property Details', 'url' => '/properties/' . $matches[1]];
                            if (isset($matches[2])) {
                                $breadcrumbItems[] = ['name' => 'Edit', 'url' => null];
                            }
                        }
                    } elseif (strpos($path, '/tenants') === 0) {
                        $breadcrumbItems[] = ['name' => 'All Tenants', 'url' => '/tenants'];
                        if ($path === '/tenants/create') {
                            $breadcrumbItems[] = ['name' => 'Add Tenant', 'url' => null];
                        }
                    } elseif (strpos($path, '/units') === 0) {
                        $breadcrumbItems[] = ['name' => 'All Units', 'url' => '/units'];
                    } elseif (strpos($path, '/payments') === 0) {
                        $breadcrumbItems[] = ['name' => 'Platform Finances', 'url' => '/payments'];
                    } elseif (strpos($path, '/invoices') === 0) {
                        $breadcrumbItems[] = ['name' => 'All Invoices', 'url' => '/invoices'];
                    } elseif (strpos($path, '/maintenance') === 0) {
                        $breadcrumbItems[] = ['name' => 'Maintenance Requests', 'url' => '/maintenance'];
                    } elseif (strpos($path, '/reports') === 0) {
                        $breadcrumbItems[] = ['name' => 'Platform Reports', 'url' => '/reports'];
                    } elseif (strpos($path, '/communications') === 0) {
                        $breadcrumbItems[] = ['name' => 'Communications', 'url' => '/communications'];
                    } elseif (strpos($path, '/documents') === 0) {
                        $breadcrumbItems[] = ['name' => 'Documents', 'url' => '/documents'];
                    } elseif ($path === '/settings') {
                        $breadcrumbItems[] = ['name' => 'System Settings', 'url' => null];
                    } elseif ($path === '/profile') {
                        $breadcrumbItems[] = ['name' => 'Profile', 'url' => null];
                    }
                    
                    foreach ($breadcrumbItems as $index => $item):
                        if ($index < count($breadcrumbItems) - 1):
                    ?>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <a href="<?php echo $item['url']; ?>" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <span class="text-gray-900 dark:text-white font-medium">
                                <?php echo htmlspecialchars($item['name']); ?>
                            </span>
                        </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                <?php echo $content ?? '<div class="text-center py-8"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Super Admin Dashboard</h1><p class="text-gray-600 dark:text-gray-400 mt-2">Welcome to the platform administration dashboard</p></div>'; ?>
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
