<?php
// Initialize variables with default values to prevent undefined variable errors
$admin = $admin ?? ['name' => 'Admin User', 'email' => 'admin@example.com', 'role' => 'admin'];
$title = $title ?? 'Page';
$message = $message ?? 'This page is under construction.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Real Estate Management</title>
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
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="hidden lg:flex flex-col w-64 bg-white dark:bg-gray-800 shadow-md h-full">
            <!-- Logo -->
            <div class="flex items-center h-16 px-4 bg-primary-600 dark:bg-primary-800">
                <div class="flex items-center">
                    <i class="fas fa-building text-white text-2xl mr-3"></i>
                    <span class="text-white font-bold text-lg">RealEstate</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="/admin/dashboard" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-dashboard w-5 mr-3"></i>
                    <span>Dashboard</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    Properties
                </div>
                <a href="/admin/properties" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-home w-5 mr-3"></i>
                    <span>Properties</span>
                </a>
                <a href="/admin/units" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-door-open w-5 mr-3"></i>
                    <span>Units</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    People
                </div>
                <a href="/admin/tenants" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-users w-5 mr-3"></i>
                    <span>Tenants</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    Financial
                </div>
                <a href="/admin/finances" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-chart-line w-5 mr-3"></i>
                    <span>Finances</span>
                </a>
                <a href="/admin/payments" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-money-bill-wave w-5 mr-3"></i>
                    <span>Payments</span>
                </a>
                <a href="/admin/invoices" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-file-invoice w-5 mr-3"></i>
                    <span>Invoices</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    Operations
                </div>
                <a href="/admin/maintenance" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-tools w-5 mr-3"></i>
                    <span>Maintenance</span>
                </a>
                <a href="/admin/communications" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-envelope w-5 mr-3"></i>
                    <span>Communications</span>
                </a>
                <a href="/admin/documents" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-folder-open w-5 mr-3"></i>
                    <span>Documents</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    Reports
                </div>
                <a href="/admin/reports" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    <span>Reports</span>
                </a>
                
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 mt-4 mb-2">
                    System
                </div>
                <a href="/admin/profile" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-user-circle w-5 mr-3"></i>
                    <span>Profile</span>
                </a>
                <a href="/admin/settings" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-cog w-5 mr-3"></i>
                    <span>Settings</span>
                </a>
            </nav>

            <!-- User menu -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">
                                <?php echo strtoupper(substr($admin['name'], 0, 1)); ?>
                            </span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200"><?php echo htmlspecialchars($admin['name']); ?></p>
                        <p class="text-xs text-primary-600 dark:text-primary-400 font-medium">
                            <?php echo ucfirst($admin['role']); ?>
                        </p>
                    </div>
                </div>
                
                <form action="/admin/logout" method="POST">
                    <button type="submit" class="w-full bg-red-900 dark:bg-red-800 hover:bg-red-800 dark:hover:bg-red-700 rounded-lg p-3 flex items-center justify-center text-red-500 dark:text-red-400 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        <span class="text-sm font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden lg:ml-0">
            <!-- Top header -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-800 dark:text-white"><?php echo $title; ?></h1>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                        <div class="text-center">
                            <div class="mb-6">
                                <i class="fas fa-tools text-6xl text-primary-500 mb-4"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                <?php echo $title; ?>
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 text-lg mb-6">
                                <?php echo $message; ?>
                            </p>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <p class="text-blue-800 dark:text-blue-200 text-sm">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    This module is currently under development. Full functionality will be available in a future update.
                                </p>
                            </div>
                            <div class="mt-8">
                                <a href="/admin/dashboard" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
                type === 'warning' ? 'bg-yellow-500' : 
                type === 'info' ? 'bg-blue-500' : 'bg-gray-500'
            } transform transition-all duration-300 translate-x-full`;
            
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 
                        type === 'warning' ? 'fa-exclamation-triangle' : 
                        type === 'info' ? 'fa-info-circle' : 'fa-circle'
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

            <?php if (isset($_SESSION['info'])): ?>
                showToast('<?php echo addslashes($_SESSION['info']); ?>', 'info');
                <?php unset($_SESSION['info']); ?>
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
