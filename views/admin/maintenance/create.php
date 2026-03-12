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
ViewManager::set('title', $title ?? 'Create Maintenance Request');
ViewManager::set('pageTitle', $pageTitle ?? 'Create Maintenance Request');

// Get current page for navigation highlighting
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$isMaintenance = strpos($currentPath, '/admin/maintenance') === 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Create Maintenance Request'; ?></title>
    
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
                        <a href="/admin/dashboard" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-home mr-3"></i>
                            Dashboard
                        </a>

                        <!-- Properties -->
                        <a href="/admin/properties" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-building mr-3"></i>
                            Properties
                        </a>

                        <!-- Tenants -->
                        <a href="/admin/tenants" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-user-friends mr-3"></i>
                            Tenants
                        </a>

                        <!-- Maintenance (Active) -->
                        <a href="/admin/maintenance" class="bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-tools mr-3"></i>
                            Maintenance
                        </a>

                        <!-- Reports -->
                        <a href="/admin/reports" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Reports
                        </a>

                        <!-- Settings -->
                        <a href="/admin/settings" class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-cog mr-3"></i>
                            Settings
                        </a>
                    </nav>

                    <!-- User Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-center">
                            <?php echo UIComponents::avatar($user['name'], $user['avatar'], 'small'); ?>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($user['name']); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <button class="ml-auto text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:pl-64 flex flex-col flex-1">
            <!-- Top Header -->
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
                    <!-- Left side -->
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 mr-4">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Breadcrumb -->
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2">
                                <li>
                                    <a href="/admin/dashboard" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <i class="fas fa-home"></i>
                                    </a>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                                    <a href="/admin/maintenance" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        Maintenance
                                    </a>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Create Request</span>
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
                        <button onclick="toggleDarkMode()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-moon dark:hidden"></i>
                            <i class="fas fa-sun hidden dark:inline"></i>
                        </button>

                        <!-- Notifications -->
                        <div class="relative">
                            <button onclick="toggleNotifications()" class="relative text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">
                                    <?php echo count($notifications); ?>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Maintenance Request</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1"><?php echo htmlspecialchars($pageDescription ?? 'Create a new maintenance request or work order'); ?></p>
        </div>
        <a href="/admin/maintenance" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Maintenance
        </a>
    </div>
</div>

<!-- Form -->
<form method="POST" action="/admin/maintenance" class="space-y-6">
    <!-- Basic Information -->
    <?php 
    // Build form content dynamically to avoid syntax errors
    $basicInfoContent = '<div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title/Request Summary -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title / Request Summary *</label>
                    <input 
                        type="text" 
                        name="title" 
                        required
                        placeholder="Brief summary of the maintenance issue"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Property/Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property / Unit *</label>
                    <select name="property_unit" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Property/Unit</option>';
    
    foreach ($properties ?? [] as $property) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($property['id']) . '">' . htmlspecialchars($property['name']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Tenant -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tenant</label>
                    <select name="tenant" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Tenant (Optional)</option>';
    
    foreach ($tenants ?? [] as $tenant) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($tenant['id']) . '">' . htmlspecialchars($tenant['name']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Category</option>';
    
    foreach ($categories ?? [] as $category) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($category['value']) . '">' . htmlspecialchars($category['label']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority *</label>
                    <select name="priority" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Priority</option>';
    
    foreach ($priorities ?? [] as $priority) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($priority['value']) . '">' . htmlspecialchars($priority['label']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">';
    
    foreach ($statuses ?? [] as $status) {
        $selected = $status['value'] === 'pending' ? ' selected' : '';
        $basicInfoContent .= '<option value="' . htmlspecialchars($status['value']) . '"' . $selected . '>' . htmlspecialchars($status['label']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
            </div>
            
            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description *</label>
                <textarea 
                    name="description" 
                    required 
                    rows="4" 
                    placeholder="Detailed description of the maintenance issue..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                ></textarea>
            </div>
        </div>';
    
    echo UIComponents::card(
        $basicInfoContent,
        '<div class="flex items-center">
            <i class="fas fa-info-circle mr-2 text-primary-600"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Basic Information</h3>
        </div>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    );
    ?>

    <!-- Assignment & Scheduling -->
    <?php 
    // Build assignment content dynamically to avoid syntax errors
    $assignmentContent = '<div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Assigned To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assigned To</label>
                    <select name="assigned_to" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Contractor/Staff</option>';
    
    foreach ($contractors ?? [] as $contractor) {
        $assignmentContent .= '<option value="' . htmlspecialchars($contractor['id']) . '">' . htmlspecialchars($contractor['name']) . '</option>';
    }
    
    $assignmentContent .= '</select>
                </div>
                
                <!-- Scheduled Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scheduled Date</label>
                    <input 
                        type="date" 
                        name="scheduled_date"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Estimated Cost -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estimated Cost</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                        <input 
                            type="number" 
                            name="estimated_cost"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                    </div>
                </div>
                
                <!-- Attachments -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attachments</label>
                    <div class="relative">
                        <input 
                            type="file" 
                            name="attachments"
                            multiple
                            accept="image/*,.pdf,.doc,.docx"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-600 file:text-white hover:file:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Upload images or documents (optional)</p>
                </div>
            </div>
        </div>';
    
    echo UIComponents::card(
        $assignmentContent,
        '<div class="flex items-center">
            <i class="fas fa-calendar-alt mr-2 text-primary-600"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Assignment & Scheduling</h3>
        </div>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    );
    ?>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-4">
        <a href="/admin/maintenance" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            Cancel
        </a>
        <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-900 transition-colors duration-200">
            <i class="fas fa-paper-plane mr-2"></i>
            Submit Request
        </button>
    </div>
</form>

</div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile sidebar backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden hidden" onclick="toggleSidebar()"></div>

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

        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        // Toggle dark mode
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }

        // Toggle notifications
        function toggleNotifications() {
            // Implementation for notifications dropdown
            console.log('Toggle notifications');
        }

        // Initialize dark mode
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>
