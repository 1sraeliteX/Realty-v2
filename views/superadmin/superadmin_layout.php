<?php 
// Initialize variables with default values to prevent undefined variable errors
$admin = $admin ?? getCurrentUser() ?? ['name' => 'Admin User', 'email' => 'admin@example.com'];
$title = $title ?? 'Super Admin Dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Cornerstone Realty Platform</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="flex flex-col w-64 bg-white dark:bg-gray-800 shadow-md">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 px-4 bg-primary-600 dark:bg-primary-800">
                <i class="fas fa-building text-white text-2xl mr-3"></i>
                <div class="text-left">
                    <span class="text-white font-bold text-lg">Cornerstone Realty</span>
                    <p class="text-white text-xs opacity-90">Platform Admin</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="/superadmin" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                    Dashboard
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    Administration
                </div>
                <a href="/superadmin/admins" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-user-shield w-5 mr-3"></i>
                    Admin Management
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    Platform
                </div>
                <a href="/properties" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-home w-5 mr-3"></i>
                    Properties
                </a>
                <a href="/tenants" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-users w-5 mr-3"></i>
                    Tenants & Occupants
                </a>
                <a href="/units" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-door-open w-5 mr-3"></i>
                    Units
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    Financial
                </div>
                <a href="/payments" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-chart-line w-5 mr-3"></i>
                    Finances
                </a>
                <a href="/invoices" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-file-invoice-dollar w-5 mr-3"></i>
                    Invoices
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    Operations
                </div>
                <a href="/maintenance" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-tools w-5 mr-3"></i>
                    Maintenance
                </a>
                <a href="/reports" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    Reports
                </a>
                <a href="/communications" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-envelope w-5 mr-3"></i>
                    Communications
                </a>
                <a href="/documents" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-folder w-5 mr-3"></i>
                    Documents
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    System
                </div>
                <a href="/settings" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-cog w-5 mr-3"></i>
                    Settings
                </a>
            </nav>

            <!-- User menu -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                <div class="flex flex-col items-center mb-3">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    <?php echo isset($admin['name']) ? strtoupper(substr($admin['name'], 0, 1)) : 'A'; ?>
                                </span>
                            </div>
                        </div>
                        <div class="ml-3 text-center">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200"><?php echo isset($admin['name']) ? htmlspecialchars($admin['name']) : 'Admin User'; ?></p>
                            <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Super Admin</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <!-- Theme toggle -->
                    <button onclick="toggleDarkMode()" class="w-full flex items-center justify-center text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 text-sm py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-moon dark:hidden mr-2"></i>
                        <i class="fas fa-sun hidden dark:block mr-2"></i>
                        Theme
                    </button>
                    
                    <!-- Logout -->
                    <form action="/logout" method="POST" class="inline w-full">
                        <button type="submit" class="w-full flex items-center justify-center text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 text-sm py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top header -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 lg:hidden">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="ml-4 text-xl font-semibold text-gray-800 dark:text-white"><?php echo $pageTitle ?? 'Super Admin Dashboard'; ?></h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                        </button>
                        
                        <!-- User Profile -->
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    <?php echo isset($admin['name']) ? strtoupper(substr($admin['name'], 0, 1)) : 'A'; ?>
                                </span>
                            </div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200"><?php echo isset($admin['name']) ? htmlspecialchars($admin['name']) : 'Admin User'; ?></p>
                                <p class="text-xs text-purple-600 dark:text-purple-400">Super Admin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Breadcrumb Navigation -->
            <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-3">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="/superadmin" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            <i class="fas fa-tachometer-alt mr-1"></i>Super Admin
                        </a>
                    </li>
                    <?php
                    // Generate breadcrumb based on current page
                    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
                    $path = parse_url($requestUri, PHP_URL_PATH);
                    $path = rtrim($path, '/');
                    
                    $breadcrumbItems = [];
                    
                    if ($path === '/superadmin') {
                        // Super Admin is already shown as home
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
                        $breadcrumbItems[] = ['name' => 'Tenants & Occupants', 'url' => '/tenants'];
                        if ($path === '/tenants/create') {
                            $breadcrumbItems[] = ['name' => 'Add Tenant', 'url' => null];
                        }
                    } elseif (strpos($path, '/units') === 0) {
                        $breadcrumbItems[] = ['name' => 'Units', 'url' => '/units'];
                    } elseif (strpos($path, '/payments') === 0) {
                        $breadcrumbItems[] = ['name' => 'Finances', 'url' => '/payments'];
                    } elseif (strpos($path, '/invoices') === 0) {
                        $breadcrumbItems[] = ['name' => 'Invoices', 'url' => '/invoices'];
                    } elseif (strpos($path, '/maintenance') === 0) {
                        $breadcrumbItems[] = ['name' => 'Maintenance', 'url' => '/maintenance'];
                    } elseif (strpos($path, '/reports') === 0) {
                        $breadcrumbItems[] = ['name' => 'Reports', 'url' => '/reports'];
                    } elseif (strpos($path, '/communications') === 0) {
                        $breadcrumbItems[] = ['name' => 'Communications', 'url' => '/communications'];
                    } elseif (strpos($path, '/documents') === 0) {
                        $breadcrumbItems[] = ['name' => 'Documents', 'url' => '/documents'];
                    } elseif ($path === '/settings') {
                        $breadcrumbItems[] = ['name' => 'Settings', 'url' => null];
                    } elseif (strpos($path, '/superadmin/admins') === 0) {
                        $breadcrumbItems[] = ['name' => 'Admin Management', 'url' => '/superadmin/admins'];
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

    <!-- DotBot Assistant (hidden by default) -->
    <div id="dotbot-assistant" class="fixed bottom-6 right-6 hidden">
        <button class="w-14 h-14 bg-purple-600 hover:bg-purple-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
            <i class="fas fa-robot text-xl"></i>
        </button>
    </div>

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
            const sidebar = document.querySelector('aside');
            sidebar.classList.toggle('hidden');
        }

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
