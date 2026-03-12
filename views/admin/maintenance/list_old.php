<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$user = DataProvider::get('user');
$notifications = DataProvider::get('notifications');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('user', $user);
ViewManager::set('notifications', $notifications);
ViewManager::set('title', $title ?? 'Maintenance Management');
ViewManager::set('pageTitle', $pageTitle ?? 'Maintenance Management');
ViewManager::set('pageDescription', $pageDescription ?? 'Track and manage maintenance requests');

// Get current page for navigation highlighting
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$isMaintenance = strpos($currentPath, '/admin/maintenance') === 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Maintenance Management'; ?></title>
    
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
        // Tailwind config
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
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

    <!-- Mobile sidebar backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden hidden"></div>

    <!-- Main Flex Container -->
    <div class="flex min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Sidebar -->
        <aside id="sidebar" class="flex-shrink-0 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:static lg:inset-0">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3 sidebar-text">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Cornerstone</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Property Manager</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Collapse Toggle Button -->
                <button id="toggleSidebar" class="hidden lg:block text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-chevron-left text-sidebar-toggle"></i>
                </button>
                <button id="closeSidebar" class="lg:hidden text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600">
            <!-- Dashboard -->
            <a href="/admin/dashboard" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors relative text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" title="Dashboard">
                <i class="fas fa-home mr-3 text-lg text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 sidebar-icon"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <!-- Properties -->
            <a href="/admin/properties" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors relative text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" title="Properties">
                <i class="fas fa-building mr-3 text-lg text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 sidebar-icon"></i>
                <span class="sidebar-text">Properties</span>
            </a>

            <!-- Tenants -->
            <a href="/admin/tenants" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors relative text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" title="Tenants">
                <i class="fas fa-user-friends mr-3 text-lg text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 sidebar-icon"></i>
                <span class="sidebar-text">Tenants</span>
            </a>

            <!-- Maintenance (Active) -->
            <a href="/admin/maintenance" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors relative bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400" title="Maintenance">
                <i class="fas fa-tools mr-3 text-lg text-primary-600 dark:text-primary-400 sidebar-icon"></i>
                <span class="sidebar-text">Maintenance</span>
            </a>

            <!-- Reports -->
            <a href="/admin/dashboard/reports" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors relative text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" title="Reports">
                <i class="fas fa-chart-bar mr-3 text-lg text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 sidebar-icon"></i>
                <span class="sidebar-text">Reports</span>
            </a>

            <!-- Settings -->
            <a href="/admin/settings" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors relative text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" title="Settings">
                <i class="fas fa-cog mr-3 text-lg text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 sidebar-icon"></i>
                <span class="sidebar-text">Settings</span>
            </a>
        </nav>

        <!-- User Profile Section -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center space-x-3">
                <?php echo UIComponents::avatar($user['name'], $user['avatar'], 'small'); ?>
                <div class="flex-1 min-w-0 sidebar-text">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?php echo htmlspecialchars($user['name']); ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="relative">
                    <button id="userMenuButton" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    
                    <!-- User Dropdown Menu -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                        <div class="py-1">
                            <a href="/admin/profile" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="/admin/settings" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <hr class="border-gray-200 dark:border-gray-700">
                            <a href="/admin/logout" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-right-from-bracket mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Main Content -->
<div class="flex-1 overflow-y-auto">
    <!-- Top Navigation -->
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
        <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
            <!-- Left side -->
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button id="openSidebar" class="lg:hidden text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-4">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <!-- Breadcrumb -->
                <nav class="hidden md:flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="/admin/dashboard" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Maintenance</span>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">
                <!-- Search -->
                <div class="hidden md:block">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Search..." 
                            class="w-64 pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline"></i>
                </button>

                <!-- Notifications -->
                <div class="relative">
                    <button id="notificationButton" class="relative text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">
                            <?php echo count($notifications); ?>
                        </span>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div id="notificationsDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Notifications</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <?php foreach ($notifications as $notification): ?>
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?php echo $notification['time']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <!-- Include the content -->
        <?php include __DIR__ . '/list_content.php'; ?>
    </main>
</div>
    <!-- Close Main Flex Container -->
    </div>

<script>
// Toast notification system
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `mb-2 px-4 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    
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
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 100);
    
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Sidebar functionality
const sidebar = document.getElementById('sidebar');
const sidebarBackdrop = document.getElementById('sidebarBackdrop');
const openSidebar = document.getElementById('openSidebar');
const closeSidebar = document.getElementById('closeSidebar');
const toggleSidebar = document.getElementById('toggleSidebar');
if (toggleSidebar) {
    const sidebarToggleIcon = toggleSidebar.querySelector('i');
    
    toggleSidebar.addEventListener('click', () => {
        const sidebarText = document.querySelectorAll('.sidebar-text');
        const sidebarIcons = document.querySelectorAll('.sidebar-icon');
        
        sidebar.classList.toggle('w-64');
        sidebar.classList.toggle('w-16');
        
        sidebarText.forEach(text => {
            text.classList.toggle('hidden');
        });
        
        if (sidebarToggleIcon) {
            sidebarToggleIcon.classList.toggle('fa-chevron-left');
            sidebarToggleIcon.classList.toggle('fa-chevron-right');
        }
    });
}

openSidebar.addEventListener('click', () => {
    sidebar.classList.remove('-translate-x-full');
    sidebarBackdrop.classList.remove('hidden');
});

closeSidebar.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    sidebarBackdrop.classList.add('hidden');
});

sidebarBackdrop.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    sidebarBackdrop.classList.add('hidden');
});

// User dropdown
const userMenuButton = document.getElementById('userMenuButton');
const userDropdown = document.getElementById('userDropdown');

userMenuButton.addEventListener('click', () => {
    userDropdown.classList.toggle('hidden');
});

// Close dropdowns when clicking outside
document.addEventListener('click', (e) => {
    if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
        userDropdown.classList.add('hidden');
    }
});

// Notifications dropdown
const notificationButton = document.getElementById('notificationButton');
const notificationsDropdown = document.getElementById('notificationsDropdown');

notificationButton.addEventListener('click', () => {
    notificationsDropdown.classList.toggle('hidden');
    userDropdown.classList.add('hidden');
});

// Dark mode toggle
const darkModeToggle = document.getElementById('darkModeToggle');
const html = document.documentElement;

darkModeToggle.addEventListener('click', () => {
    html.classList.toggle('dark');
    localStorage.setItem('darkMode', html.classList.contains('dark'));
});

// Check for saved dark mode preference
if (localStorage.getItem('darkMode') === 'true') {
    html.classList.add('dark');
}

function searchMaintenance(query) {
    // Implementation for search functionality
    console.log('Searching for:', query);
}

function exportMaintenance() {
    // Implementation for export functionality
    console.log('Exporting maintenance data');
}

function deleteMaintenance(id) {
    if (confirm('Are you sure you want to delete this maintenance request?')) {
        console.log('Deleting maintenance request:', id);
    }
}
</script>

</body>
</html>
