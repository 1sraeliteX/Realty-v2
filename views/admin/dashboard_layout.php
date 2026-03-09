<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = $title ?? 'Admin Dashboard';
$pageTitle = $pageTitle ?? 'Dashboard';
$content = $content ?? '';

// Mock user data for UI
$user = [
    'name' => 'John Doe',
    'email' => 'admin@example.com',
    'avatar' => null,
    'role' => 'Administrator'
];

// Mock notification data
$notifications = [
    ['id' => 1, 'type' => 'info', 'message' => 'New tenant application received', 'time' => '5 min ago'],
    ['id' => 2, 'type' => 'warning', 'message' => 'Rent payment overdue for Unit 3A', 'time' => '1 hour ago'],
    ['id' => 3, 'type' => 'success', 'message' => 'Maintenance request completed', 'time' => '2 hours ago']
];

// Determine active menu item based on current route
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$activeMenu = 'dashboard';

// More precise route matching to avoid false positives
$pathParts = explode('/', trim($currentPath, '/'));
array_shift($pathParts); // Remove 'admin' if present

if (in_array('properties', $pathParts)) $activeMenu = 'properties';
elseif (in_array('tenants', $pathParts)) $activeMenu = 'tenants';
elseif (in_array('occupants', $pathParts)) $activeMenu = 'occupants';
elseif (in_array('units', $pathParts)) $activeMenu = 'units';
elseif (in_array('rooms', $pathParts)) $activeMenu = 'rooms';
elseif (in_array('payments', $pathParts)) $activeMenu = 'payments';
elseif (in_array('invoices', $pathParts)) $activeMenu = 'invoices';
elseif (in_array('maintenance', $pathParts)) $activeMenu = 'maintenance';
elseif (in_array('finances', $pathParts)) $activeMenu = 'finances';
elseif (in_array('reports', $pathParts)) $activeMenu = 'reports';
elseif (in_array('communications', $pathParts)) $activeMenu = 'communications';
elseif (in_array('documents', $pathParts)) $activeMenu = 'documents';
elseif (in_array('team', $pathParts)) $activeMenu = 'team';
elseif (in_array('settings', $pathParts)) $activeMenu = 'settings';
elseif (in_array('profile', $pathParts)) $activeMenu = 'profile';

ob_start();
?>

<!-- Mobile sidebar backdrop -->
<div id="sidebarBackdrop" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden hidden"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:static lg:inset-0">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Cornerstone</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Property Manager</p>
                </div>
            </div>
            <button id="closeSidebar" class="lg:hidden text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <!-- Dashboard -->
            <a href="/admin/dashboard" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'dashboard' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-home mr-3 text-lg <?php echo $activeMenu === 'dashboard' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Dashboard
            </a>

            <!-- Properties -->
            <a href="/admin/dashboard/properties" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'properties' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-building mr-3 text-lg <?php echo $activeMenu === 'properties' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Properties
            </a>

            <!-- Tenants -->
            <a href="/admin/dashboard/tenants" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'tenants' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-users mr-3 text-lg <?php echo $activeMenu === 'tenants' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Tenants
            </a>

            <!-- Occupants -->
            <a href="/admin/dashboard/occupants" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'occupants' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-user-friends mr-3 text-lg <?php echo $activeMenu === 'occupants' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Occupants
            </a>

            <!-- Units -->
            <a href="/admin/dashboard/units" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'units' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-door-open mr-3 text-lg <?php echo $activeMenu === 'units' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Units
            </a>

            <!-- Rooms -->
            <a href="/admin/dashboard/rooms" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'rooms' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-bed mr-3 text-lg <?php echo $activeMenu === 'rooms' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Rooms
            </a>

            <!-- Payments -->
            <a href="/admin/dashboard/payments" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'payments' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-credit-card mr-3 text-lg <?php echo $activeMenu === 'payments' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Payments
            </a>

            <!-- Invoices -->
            <a href="/admin/dashboard/invoices" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'invoices' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-file-invoice mr-3 text-lg <?php echo $activeMenu === 'invoices' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Invoices
            </a>

            <!-- Maintenance -->
            <a href="/admin/dashboard/maintenance" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'maintenance' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-tools mr-3 text-lg <?php echo $activeMenu === 'maintenance' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Maintenance
            </a>

            <!-- Finances -->
            <a href="/admin/dashboard/finances" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'finances' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-chart-line mr-3 text-lg <?php echo $activeMenu === 'finances' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Finances
            </a>

            <!-- Reports -->
            <a href="/admin/dashboard/reports" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'reports' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-chart-bar mr-3 text-lg <?php echo $activeMenu === 'reports' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Reports
            </a>

            <!-- Communications -->
            <a href="/admin/dashboard/communications" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'communications' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-envelope mr-3 text-lg <?php echo $activeMenu === 'communications' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Communications
            </a>

            <!-- Documents -->
            <a href="/admin/dashboard/documents" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'documents' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-folder mr-3 text-lg <?php echo $activeMenu === 'documents' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Documents
            </a>

            <!-- Team -->
            <a href="/admin/dashboard/team" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'team' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-users-cog mr-3 text-lg <?php echo $activeMenu === 'team' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Team
            </a>

            <!-- Settings -->
            <a href="/admin/dashboard/settings" class="nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $activeMenu === 'settings' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-cog mr-3 text-lg <?php echo $activeMenu === 'settings' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'; ?>"></i>
                Settings
            </a>
        </nav>

        <!-- User Profile Section -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center space-x-3">
                <?php echo UIComponents::avatar($user['name'], $user['avatar'], 'small'); ?>
                <div class="flex-1 min-w-0">
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
                            <a href="/admin/dashboard/profile" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="/admin/dashboard/settings" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <hr class="border-gray-200 dark:border-gray-700">
                            <a href="/admin/logout" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Main Content -->
<div class="lg:ml-64 flex flex-col min-h-screen">
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
                            <span class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($pageTitle); ?></span>
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
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Notifications</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <?php if ($notification['type'] === 'success'): ?>
                                                <i class="fas fa-check-circle text-green-500"></i>
                                            <?php elseif ($notification['type'] === 'warning'): ?>
                                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                            <?php else: ?>
                                                <i class="fas fa-info-circle text-blue-500"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?php echo htmlspecialchars($notification['time']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                            <a href="/admin/dashboard/notifications" class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">
                                View all notifications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 bg-gray-50 dark:bg-gray-900">
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($pageTitle); ?></h1>
                <?php if (isset($pageDescription)): ?>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($pageDescription); ?></p>
                <?php endif; ?>
            </div>

            <!-- Dynamic Content -->
            <?php echo $content; ?>
        </div>
    </main>
</div>

<!-- Floating Action Button for DotBot AI Assistant -->
<div class="fixed bottom-6 right-6 z-40">
    <button id="dotbotButton" class="bg-primary-600 hover:bg-primary-700 text-white rounded-full p-4 shadow-lg transition-all duration-200 transform hover:scale-110">
        <i class="fas fa-robot text-xl"></i>
    </button>
</div>

<!-- DotBot Chat Window -->
<div id="dotbotChat" class="hidden fixed bottom-24 right-6 w-96 h-[500px] bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 flex flex-col">
    <!-- Chat Header -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-primary-600 text-white rounded-t-lg">
        <div class="flex items-center">
            <i class="fas fa-robot mr-2"></i>
            <span class="font-medium">DotBot Assistant</span>
        </div>
        <button id="closeDotbot" class="text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Chat Messages -->
    <div class="flex-1 overflow-y-auto p-4 space-y-3">
        <div class="flex items-start">
            <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                <i class="fas fa-robot text-primary-600 dark:text-primary-400 text-xs"></i>
            </div>
            <div class="ml-3 bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-2 max-w-[80%]">
                <p class="text-sm text-gray-900 dark:text-white">Hello! I'm DotBot, your AI assistant. How can I help you today?</p>
            </div>
        </div>
    </div>

    <!-- Chat Input -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-2">
            <input 
                type="text" 
                id="dotbotInput"
                placeholder="Type your message..." 
                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
            >
            <button class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-3 py-2">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
// Sidebar functionality
const sidebar = document.getElementById('sidebar');
const sidebarBackdrop = document.getElementById('sidebarBackdrop');
const openSidebar = document.getElementById('openSidebar');
const closeSidebar = document.getElementById('closeSidebar');

// Restore sidebar state from localStorage
function restoreSidebarState() {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed && window.innerWidth >= 1024) {
        sidebar.classList.add('lg:w-16');
        sidebar.classList.remove('w-64');
        // Hide text elements when collapsed
        const textElements = sidebar.querySelectorAll('.sidebar-text');
        textElements.forEach(el => el.classList.add('hidden'));
    }
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

// Initialize sidebar state
document.addEventListener('DOMContentLoaded', restoreSidebarState);

// User dropdown
const userMenuButton = document.getElementById('userMenuButton');
const userDropdown = document.getElementById('userDropdown');

userMenuButton.addEventListener('click', (e) => {
    e.stopPropagation();
    userDropdown.classList.toggle('hidden');
});

// Notifications dropdown
const notificationButton = document.getElementById('notificationButton');
const notificationsDropdown = document.getElementById('notificationsDropdown');

notificationButton.addEventListener('click', (e) => {
    e.stopPropagation();
    notificationsDropdown.classList.toggle('hidden');
    userDropdown.classList.add('hidden');
});

// Close dropdowns when clicking outside
document.addEventListener('click', () => {
    userDropdown.classList.add('hidden');
    notificationsDropdown.classList.add('hidden');
});

// Dark mode toggle
const darkModeToggle = document.getElementById('darkModeToggle');

darkModeToggle.addEventListener('click', () => {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('darkMode', isDark);
    
    // Dispatch custom event for theme change
    window.dispatchEvent(new CustomEvent('themechange', { detail: { isDark } }));
});

// Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    // Only apply if user hasn't explicitly set a preference
    if (!localStorage.getItem('darkMode')) {
        const isDark = e.matches;
        document.documentElement.classList.toggle('dark', isDark);
        window.dispatchEvent(new CustomEvent('themechange', { detail: { isDark } }));
    }
});

// DotBot Chat
const dotbotButton = document.getElementById('dotbotButton');
const dotbotChat = document.getElementById('dotbotChat');
const closeDotbot = document.getElementById('closeDotbot');
const dotbotInput = document.getElementById('dotbotInput');

dotbotButton.addEventListener('click', () => {
    dotbotChat.classList.toggle('hidden');
    if (!dotbotChat.classList.contains('hidden')) {
        dotbotInput.focus();
    }
});

closeDotbot.addEventListener('click', () => {
    dotbotChat.classList.add('hidden');
});

// DotBot message sending
dotbotInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && dotbotInput.value.trim()) {
        // Add user message
        const messagesContainer = dotbotChat.querySelector('.overflow-y-auto');
        const userMessage = document.createElement('div');
        userMessage.className = 'flex items-start justify-end';
        userMessage.innerHTML = `
            <div class="mr-3 bg-primary-600 text-white rounded-lg px-3 py-2 max-w-[80%]">
                <p class="text-sm">${dotbotInput.value}</p>
            </div>
            <div class="flex-shrink-0 w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-gray-600 text-xs"></i>
            </div>
        `;
        messagesContainer.appendChild(userMessage);
        
        // Clear input
        dotbotInput.value = '';
        
        // Simulate bot response
        setTimeout(() => {
            const botResponse = document.createElement('div');
            botResponse.className = 'flex items-start';
            botResponse.innerHTML = `
                <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                    <i class="fas fa-robot text-primary-600 dark:text-primary-400 text-xs"></i>
                </div>
                <div class="ml-3 bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-2 max-w-[80%]">
                    <p class="text-sm text-gray-900 dark:text-white">I'm processing your request. This is a demo response.</p>
                </div>
            `;
            messagesContainer.appendChild(botResponse);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 1000);
        
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>

<?php
$layoutContent = ob_get_clean();

// Include the main layout
include '../layout.php';
?>
