<?php 
// Initialize variables with default values to prevent undefined variable errors
$admin = $admin ?? (function_exists('getCurrentUser') ? getCurrentUser() : null) ?? ['name' => 'Admin User', 'email' => 'admin@example.com', 'role' => 'admin'];
$title = $title ?? 'Dashboard'; 

// Simple getCurrentUser function if not already defined
if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        return [
            'id' => $_SESSION['admin_id'] ?? 1,
            'name' => $_SESSION['admin_name'] ?? 'Admin User',
            'email' => $_SESSION['admin_email'] ?? 'admin@example.com',
            'role' => $_SESSION['admin_role'] ?? 'admin'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Real Estate Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <style>
        /* Custom styles for responsive sidebar */
        .sidebar-text {
            transition: opacity 0.3s ease-in-out;
        }
        
        /* Ensure proper z-index stacking */
        .sidebar-overlay {
            z-index: 30;
        }
        
        #sidebar {
            z-index: 40;
        }
        
        /* Smooth transitions for blur effect */
        .backdrop-blur-sm {
            transition: backdrop-filter 0.3s ease-in-out;
        }
        
        /* Hide scrollbar on mobile when sidebar is open */
        .sidebar-open {
            overflow: hidden;
        }
        
        /* Ensure main content takes full width on mobile */
        @media (max-width: 1023px) {
            .main-content-mobile {
                margin-left: 0 !important;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed lg:relative lg:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out z-40 flex flex-col w-64 bg-white dark:bg-gray-800 shadow-md h-full">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 bg-primary-600 dark:bg-primary-800">
                <div class="flex items-center">
                    <i class="fas fa-building text-white text-2xl mr-3"></i>
                    <span class="text-white font-bold text-lg">RealEstate</span>
                </div>
                <button onclick="toggleSidebar()" class="lg:hidden text-white hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="/dashboard" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                    <i class="fas fa-dashboard w-5 mr-3"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    <span class="sidebar-text">Properties</span>
                </div>
                <a href="/properties" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-home w-5 mr-3"></i>
                    <span class="sidebar-text">Properties</span>
                </a>
                <a href="/admin/units" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-door-open w-5 mr-3"></i>
                    <span class="sidebar-text">Units</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    <span class="sidebar-text">People</span>
                </div>
                <a href="/admin/tenants" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-users w-5 mr-3"></i>
                    <span class="sidebar-text">Tenants</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    <span class="sidebar-text">Financial</span>
                </div>
                <a href="/admin/finances" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-chart-line w-5 mr-3"></i>
                    <span class="sidebar-text">Finances</span>
                </a>
                <a href="/admin/payments" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-money-bill-wave w-5 mr-3"></i>
                    <span class="sidebar-text">Payments</span>
                </a>
                <a href="/admin/invoices" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-file-invoice w-5 mr-3"></i>
                    <span class="sidebar-text">Invoices</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    <span class="sidebar-text">Operations</span>
                </div>
                <a href="/admin/maintenance" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-tools w-5 mr-3"></i>
                    <span class="sidebar-text">Maintenance</span>
                </a>
                <a href="/admin/communications" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-envelope w-5 mr-3"></i>
                    <span class="sidebar-text">Communications</span>
                </a>
                <a href="/admin/documents" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-folder-open w-5 mr-3"></i>
                    <span class="sidebar-text">Documents</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    <span class="sidebar-text">Reports</span>
                </div>
                <a href="/admin/reports" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    <span class="sidebar-text">Reports</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    <span class="sidebar-text">System</span>
                </div>
                <a href="/admin/profile" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-user-circle w-5 mr-3"></i>
                    <span class="sidebar-text">Profile</span>
                </a>
                <a href="/admin/settings" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-cog w-5 mr-3"></i>
                    <span class="sidebar-text">Settings</span>
                </a>
            </nav>

            <!-- User menu -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                <!-- Profile section -->
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">
                                <?php echo isset($admin['name']) ? strtoupper(substr($admin['name'], 0, 1)) : 'A'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200"><?php echo isset($admin['name']) ? htmlspecialchars($admin['name']) : 'Admin User'; ?></p>
                        <p class="text-xs text-primary-600 dark:text-primary-400 font-medium">
                            <?php echo isset($admin['role']) ? ucfirst($admin['role']) : 'Admin'; ?>
                        </p>
                    </div>
                </div>
                
                <!-- Theme toggle -->
                <div class="bg-gray-700 dark:bg-gray-600 rounded-lg p-3 mb-3 flex items-center justify-between">
                    <span class="text-white text-sm font-medium">Theme</span>
                    <button onclick="toggleDarkMode()" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none bg-blue-500">
                        <span class="sr-only">Toggle theme</span>
                        <span id="theme-toggle-dot" class="inline-block w-4 h-4 transform transition-transform bg-white rounded-full translate-x-6">
                            <i class="fas fa-sun text-yellow-400 text-xs flex items-center justify-center h-full"></i>
                        </span>
                        <span class="absolute left-1 top-1/2 transform -translate-y-1/2 text-white">
                            <i class="fas fa-moon text-xs"></i>
                        </span>
                    </button>
                </div>
                
                <!-- Logout button -->
                <form action="/logout" method="POST">
                    <button type="submit" class="w-full bg-red-900 dark:bg-red-800 hover:bg-red-800 dark:hover:bg-red-700 rounded-lg p-3 flex items-center justify-center text-red-500 dark:text-red-400 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        <span class="text-sm font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden transition-opacity duration-300" onclick="toggleSidebar()"></div>

        <!-- Main content -->
        <div id="main-content" class="flex flex-col flex-1 overflow-hidden lg:ml-0 transition-all duration-300">
            <!-- Top header -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="ml-4 text-xl font-semibold text-gray-800 dark:text-white"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
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
                    </div>
                </div>
            </header>

            <!-- Breadcrumb Navigation -->
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-3">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="/dashboard" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            <i class="fas fa-home mr-1"></i>Dashboard
                        </a>
                    </li>
                    <?php
                    // Generate breadcrumb based on current page
                    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
                    $path = parse_url($requestUri, PHP_URL_PATH);
                    $path = rtrim($path, '/');
                    
                    $breadcrumbItems = [];
                    
                    if ($path === '/dashboard') {
                        // Dashboard is already shown as home
                    } elseif (strpos($path, '/properties') === 0) {
                        $breadcrumbItems[] = ['name' => 'Properties', 'url' => '/properties'];
                        
                        if ($path === '/properties/create') {
                            $breadcrumbItems[] = ['name' => 'Add Property', 'url' => null];
                        } elseif (preg_match('/\/properties\/(\d+)(\/edit)?/', $path, $matches)) {
                            $breadcrumbItems[] = ['name' => 'Property Details', 'url' => '/properties/' . $matches[1]];
                            if (isset($matches[2])) {
                                $breadcrumbItems[] = ['name' => 'Edit', 'url' => null];
                            }
                        }
                    } elseif (strpos($path, '/tenants') === 0) {
                        $breadcrumbItems[] = ['name' => 'Tenants', 'url' => '/tenants'];
                        if ($path === '/tenants/create') {
                            $breadcrumbItems[] = ['name' => 'Add Tenant', 'url' => null];
                        }
                    } elseif (strpos($path, '/units') === 0) {
                        $breadcrumbItems[] = ['name' => 'Units', 'url' => '/units'];
                    } elseif (strpos($path, '/finances') === 0) {
                        $breadcrumbItems[] = ['name' => 'Finances', 'url' => '/finances'];
                    } elseif (strpos($path, '/payments') === 0) {
                        $breadcrumbItems[] = ['name' => 'Payments', 'url' => '/payments'];
                    } elseif (strpos($path, '/invoices') === 0) {
                        $breadcrumbItems[] = ['name' => 'Invoices', 'url' => '/invoices'];
                    } elseif (strpos($path, '/maintenance') === 0) {
                        $breadcrumbItems[] = ['name' => 'Maintenance', 'url' => '/maintenance'];
                    } elseif (strpos($path, '/communications') === 0) {
                        $breadcrumbItems[] = ['name' => 'Communications', 'url' => '/communications'];
                    } elseif (strpos($path, '/documents') === 0) {
                        $breadcrumbItems[] = ['name' => 'Documents', 'url' => '/documents'];
                    } elseif (strpos($path, '/reports') === 0) {
                        $breadcrumbItems[] = ['name' => 'Reports', 'url' => '/reports'];
                    } elseif ($path === '/profile') {
                        $breadcrumbItems[] = ['name' => 'Profile', 'url' => null];
                    } elseif ($path === '/settings') {
                        $breadcrumbItems[] = ['name' => 'Settings', 'url' => null];
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

            <!-- Page content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                <?php echo $content; ?>
            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

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
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
            
            // Update toggle switch position
            const toggleDot = document.getElementById('theme-toggle-dot');
            if (toggleDot) {
                if (isDark) {
                    toggleDot.classList.remove('translate-x-6');
                    toggleDot.classList.add('translate-x-1');
                    toggleDot.innerHTML = '<i class="fas fa-moon text-blue-200 text-xs flex items-center justify-center h-full"></i>';
                } else {
                    toggleDot.classList.remove('translate-x-1');
                    toggleDot.classList.add('translate-x-6');
                    toggleDot.innerHTML = '<i class="fas fa-sun text-yellow-400 text-xs flex items-center justify-center h-full"></i>';
                }
            }
        }

        // Initialize dark mode
        const isDarkMode = localStorage.getItem('darkMode') === 'true' || 
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
        
        if (isDarkMode) {
            document.documentElement.classList.add('dark');
        }
        
        // Initialize toggle switch state
        document.addEventListener('DOMContentLoaded', function() {
            const toggleDot = document.getElementById('theme-toggle-dot');
            if (toggleDot) {
                if (isDarkMode) {
                    toggleDot.classList.remove('translate-x-6');
                    toggleDot.classList.add('translate-x-1');
                    toggleDot.innerHTML = '<i class="fas fa-moon text-blue-200 text-xs flex items-center justify-center h-full"></i>';
                } else {
                    toggleDot.classList.add('translate-x-6');
                    toggleDot.innerHTML = '<i class="fas fa-sun text-yellow-400 text-xs flex items-center justify-center h-full"></i>';
                }
            }
        });

        // Sidebar toggle for mobile and responsive behavior
        let sidebarOpen = false;
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const mainContent = document.getElementById('main-content');
            const body = document.body;
            
            sidebarOpen = !sidebarOpen;
            
            if (sidebarOpen) {
                // Open sidebar
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                
                // Add blur effect to main content on mobile
                if (window.innerWidth < 1024) {
                    mainContent.classList.add('backdrop-blur-sm');
                    body.classList.add('sidebar-open');
                }
            } else {
                // Close sidebar
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                overlay.classList.add('hidden');
                mainContent.classList.remove('backdrop-blur-sm');
                body.classList.remove('sidebar-open');
            }
        }
        
        // Auto-collapse sidebar on screen resize
        function handleResize() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const mainContent = document.getElementById('main-content');
            const body = document.body;
            
            if (window.innerWidth >= 1024) {
                // Desktop: show sidebar permanently
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.add('hidden');
                mainContent.classList.remove('backdrop-blur-sm');
                body.classList.remove('sidebar-open');
                sidebarOpen = false;
            } else {
                // Mobile/tablet: hide sidebar by default
                if (!sidebarOpen) {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                }
            }
        }
        
        // Initialize responsive behavior
        document.addEventListener('DOMContentLoaded', function() {
            handleResize(); // Set initial state
            
            // Add resize listener with debouncing
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(handleResize, 250);
            });
            
            // Close sidebar when clicking navigation links on mobile
            const navLinks = document.querySelectorAll('nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024 && sidebarOpen) {
                        setTimeout(toggleSidebar, 150); // Small delay for navigation
                    }
                });
            });
            
            // Handle escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebarOpen && window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });
        
        // Update the existing toggleSidebar function calls to use the new one
        window.toggleSidebar = toggleSidebar;

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

        // API request helper
        async function apiRequest(endpoint, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            const token = localStorage.getItem('jwt_token');
            if (token) {
                defaultOptions.headers.Authorization = `Bearer ${token}`;
            }

            const finalOptions = {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...options.headers
                }
            };

            try {
                const response = await fetch(endpoint, finalOptions);
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Request failed');
                }

                return data;
            } catch (error) {
                showToast(error.message, 'error');
                throw error;
            }
        }
    </script>
</body>
</html>
