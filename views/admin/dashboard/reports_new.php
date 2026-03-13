<?php
// Initialize anti-scattering system
require_once __DIR__ . '/../../../config/bootstrap.php';

// Get centralized data from ViewManager (anti-scattering compliant)
$user = ViewManager::get('user');
$stats = ViewManager::get('stats');
$dashboard_trends = ViewManager::get('dashboard_trends');
$recentActivities = ViewManager::get('recentActivities');
$recentProperties = ViewManager::get('recentProperties');
$revenueData = ViewManager::get('revenueData');
$maintenanceRequests = ViewManager::get('maintenanceRequests');
$newApplications = ViewManager::get('newApplications');
$upcomingTasks = ViewManager::get('upcomingTasks');
$title = ViewManager::get('title', 'Dashboard Reports');

// Get current page for navigation highlighting
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$isDashboardReports = strpos($currentPath, '/admin/dashboard/reports') === 0;
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
                        <span class="text-gray-700 dark:text-gray-300 px-3 py-2 rounded-md text-sm font-medium">Reports</span>
                    </nav>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" placeholder="Search reports..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
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
                                <a href="/admin/settings" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
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
                        <a href="/admin/dashboard" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $isDashboard ? 'bg-gray-900 text-white' : ''; ?>">
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
                        <a href="/admin/dashboard/reports" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white <?php echo $isDashboardReports ? 'bg-gray-900 text-white' : ''; ?>">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Reports
                        </a>
                        <a href="/admin/settings" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
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
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Reports & Analytics</h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Comprehensive insights and analytics for your property management business.</p>
                    </div>

                    <!-- Report Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Properties Card -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-primary-500 rounded-md p-3">
                                        <i class="fas fa-home text-white"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Properties</dt>
                                            <dd class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo number_format($stats['total_properties'] ?? 0); ?></dd>
                                        </dl>
                                    </div>
                                    <?php if (isset($dashboard_trends['property_trend']) && $dashboard_trends['property_trend'] != 0): ?>
                                        <div class="ml-auto">
                                            <?php if ($dashboard_trends['property_trend'] > 0): ?>
                                                <span class="text-green-600 text-sm font-medium">+<?php echo number_format($dashboard_trends['property_trend'], 1); ?>%</span>
                                            <?php else: ?>
                                                <span class="text-red-600 text-sm font-medium"><?php echo number_format($dashboard_trends['property_trend'], 1); ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Total Units Card -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                        <i class="fas fa-building text-white"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Units</dt>
                                            <dd class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo number_format($stats['total_units'] ?? 0); ?></dd>
                                        </dl>
                                    </div>
                                    <?php if (isset($dashboard_trends['units_trend']) && $dashboard_trends['units_trend'] != 0): ?>
                                        <div class="ml-auto">
                                            <?php if ($dashboard_trends['units_trend'] > 0): ?>
                                                <span class="text-green-600 text-sm font-medium">+<?php echo number_format($dashboard_trends['units_trend'], 1); ?>%</span>
                                            <?php else: ?>
                                                <span class="text-red-600 text-sm font-medium"><?php echo number_format($dashboard_trends['units_trend'], 1); ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Active Tenants Card -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Tenants</dt>
                                            <dd class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo number_format($stats['active_tenants'] ?? 0); ?></dd>
                                        </dl>
                                    </div>
                                    <?php if (isset($dashboard_trends['tenants_trend']) && $dashboard_trends['tenants_trend'] != 0): ?>
                                        <div class="ml-auto">
                                            <?php if ($dashboard_trends['tenants_trend'] > 0): ?>
                                                <span class="text-green-600 text-sm font-medium">+<?php echo number_format($dashboard_trends['tenants_trend'], 1); ?>%</span>
                                            <?php else: ?>
                                                <span class="text-red-600 text-sm font-medium"><?php echo number_format($dashboard_trends['tenants_trend'], 1); ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Occupancy Rate Card -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                        <i class="fas fa-chart-pie text-white"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Occupancy Rate</dt>
                                            <dd class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo number_format($stats['occupancy_rate'] ?? 0, 1); ?>%</dd>
                                        </dl>
                                    </div>
                                    <?php if (isset($dashboard_trends['occupancy_trend']) && $dashboard_trends['occupancy_trend'] != 0): ?>
                                        <div class="ml-auto">
                                            <?php if ($dashboard_trends['occupancy_trend'] > 0): ?>
                                                <span class="text-green-600 text-sm font-medium">+<?php echo number_format($dashboard_trends['occupancy_trend'], 1); ?>%</span>
                                            <?php else: ?>
                                                <span class="text-red-600 text-sm font-medium"><?php echo number_format($dashboard_trends['occupancy_trend'], 1); ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Chart -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg mb-8">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue Overview</h2>
                            <div class="h-64">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg mb-8">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activities</h2>
                            <div class="space-y-4">
                                <?php if (empty($recentActivities)): ?>
                                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent activities found.</p>
                                <?php else: ?>
                                    <?php foreach ($recentActivities as $activity): ?>
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                    <i class="fas fa-<?php echo $activity['action'] ?? 'circle'; ?> text-gray-500"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($activity['description'] ?? ''); ?></p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo date('M j, Y g:i A', strtotime($activity['created_at'] ?? '')); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Requests -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg mb-8">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Maintenance Requests</h2>
                            <div class="space-y-4">
                                <?php if (empty($maintenanceRequests)): ?>
                                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No maintenance requests found.</p>
                                <?php else: ?>
                                    <?php foreach ($maintenanceRequests as $request): ?>
                                        <div class="border-l-4 border-gray-200 dark:border-gray-700 pl-4">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 mr-3">
                                                    <div class="h-2 w-2 rounded-full bg-<?php echo $request['priority'] === 'urgent' ? 'red' : ($request['priority'] === 'medium' ? 'yellow' : 'blue'); ?> mt-1"></div>
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['title'] ?? ''); ?></h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($request['property_name'] ?? ''); ?> - Unit <?php echo htmlspecialchars($request['unit_number'] ?? ''); ?></p>
                                                    <p class="text-xs text-gray-400 dark:text-gray-500"><?php echo date('M j, Y g:i A', strtotime($request['created_at'] ?? '')); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- New Applications -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg mb-8">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">New Applications</h2>
                            <div class="space-y-4">
                                <?php if (empty($newApplications)): ?>
                                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No new applications found.</p>
                                <?php else: ?>
                                    <?php foreach ($newApplications as $application): ?>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($application['property_name'] ?? ''); ?> - Unit <?php echo htmlspecialchars($application['unit_number'] ?? ''); ?></p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800"><?php echo htmlspecialchars($application['status'] ?? ''); ?></span>
                                                <button class="text-primary-600 hover:text-primary-900 text-sm font-medium">Review</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Tasks -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upcoming Tasks</h2>
                            <div class="space-y-4">
                                <?php if (empty($upcomingTasks)): ?>
                                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No upcoming tasks found.</p>
                                <?php else: ?>
                                    <?php foreach ($upcomingTasks as $task): ?>
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                    <i class="fas fa-<?php echo $task['task_type'] ?? 'circle'; ?> text-gray-500"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($task['title'] ?? ''); ?></h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($task['property_name'] ?? ''); ?> - Unit <?php echo htmlspecialchars($task['unit_number'] ?? ''); ?></p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500">Due: <?php echo date('M j, Y', strtotime($task['due_date'] ?? '')); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = <?php echo json_encode($revenueData ?? []); ?>;
        const months = Object.keys(revenueData);
        const values = Object.values(revenueData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: values,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
