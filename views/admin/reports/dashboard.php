<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$user = DataProvider::get('user');
$notifications = DataProvider::get('notifications');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('user', $user);
ViewManager::set('notifications', $notifications);
ViewManager::set('title', $title ?? 'Reports & Maintenance');
ViewManager::set('pageTitle', $pageTitle ?? 'Reports & Maintenance');
ViewManager::set('pageDescription', $pageDescription ?? 'Generate reports and manage maintenance requests');

// Add Font Awesome CSS to head (anti-scattering compliant)
ViewManager::set('headCSS', '
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="/assets/css/fontawesome.css">
');

// Mobile viewport fix CSS
ViewManager::set('mobileFixCSS', '
<style>
/* Mobile sidebar overflow fix */
@media (max-width: 1023px) {
    #sidebar {
        height: 100vh;
        max-height: 100vh;
        overflow-y: auto;
        overscroll-behavior: contain;
    }
    
    #sidebar .flex-col {
        min-height: 100vh;
    }
    
    #sidebar nav {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    #sidebar .border-t {
        flex-shrink: 0;
        position: sticky;
        bottom: 0;
        background: white;
    }
}
</style>
');

// Start output buffering for the dashboard layout
ob_start();
?>

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

            <!-- Maintenance -->
            <a href="/admin/maintenance" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors relative text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700" title="Maintenance">
                <i class="fas fa-tools mr-3 text-lg text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 sidebar-icon"></i>
                <span class="sidebar-text">Maintenance</span>
            </a>

            <!-- Reports (Active) -->
            <a href="/admin/dashboard/reports" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors relative bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400" title="Reports">
                <i class="fas fa-chart-bar mr-3 text-lg text-primary-600 dark:text-primary-400 sidebar-icon"></i>
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
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Reports & Maintenance</span>
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
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports & Maintenance</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400"><?php echo $pageDescription; ?></p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                        <i class="fas fa-tools text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Requests</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $maintenanceStats['total']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $maintenanceStats['pending']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                        <i class="fas fa-spinner text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Progress</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $maintenanceStats['in_progress']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $maintenanceStats['completed']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 dark:bg-red-900 rounded-lg p-3">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">High Priority</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $maintenanceStats['high_priority']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Maintenance Request Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Create Maintenance Request</h2>
            </div>
            <div class="p-6">
                <form id="maintenanceRequestForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="property" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property</label>
                            <select id="property" name="property" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Property</option>
                                <?php foreach ($properties as $property): ?>
                                <option value="<?php echo $property['id']; ?>"><?php echo htmlspecialchars($property['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="tenant" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tenant</label>
                            <select id="tenant" name="tenant" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Tenant</option>
                                <?php foreach ($tenants as $tenant): ?>
                                <option value="<?php echo $tenant['id']; ?>"><?php echo htmlspecialchars($tenant['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <select id="category" name="category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['value']; ?>"><?php echo htmlspecialchars($category['label']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                            <select id="priority" name="priority" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Priority</option>
                                <?php foreach ($priorities as $priority): ?>
                                <option value="<?php echo $priority['value']; ?>"><?php echo htmlspecialchars($priority['label']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Describe the maintenance issue..."></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Create Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Maintenance Requests Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Recent Maintenance Requests</h2>
                <button class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
                    View All
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Issue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($maintenanceRequests as $request): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['property']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['unit']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['issue']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $priorityColors = [
                                    'low' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                    'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                ];
                                $color = $priorityColors[$request['priority']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                    <?php echo ucfirst($request['priority']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                ];
                                $statusColor = $statusColors[$request['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusColor; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo $request['reported_date']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 mr-3">View</button>
                                <button class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">Edit</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
    <!-- Close Main Flex Container -->
    </div>

<script>
// Sidebar functionality
const sidebar = document.getElementById('sidebar');
const sidebarBackdrop = document.getElementById('sidebarBackdrop');
const openSidebar = document.getElementById('openSidebar');
const closeSidebar = document.getElementById('closeSidebar');
const toggleSidebar = document.getElementById('toggleSidebar');
const sidebarToggleIcon = toggleSidebar.querySelector('i');

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

// Maintenance request form
const maintenanceRequestForm = document.getElementById('maintenanceRequestForm');
maintenanceRequestForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    // Show success message
    const formData = new FormData(maintenanceRequestForm);
    console.log('Maintenance request submitted:', Object.fromEntries(formData));
    
    // Reset form
    maintenanceRequestForm.reset();
    
    // Show success notification (you can implement this with your notification system)
    alert('Maintenance request created successfully!');
    window.location.href = '/dashboard';
});

</script>

<?php
// Get the dashboard layout content
$dashboardLayout = ob_get_clean();

// Include the layout template
include __DIR__ . '/../../layout.php';
?>
