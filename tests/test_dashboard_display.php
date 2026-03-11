<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Display Test</title>
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Dashboard Display Test</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                        <i class="fas fa-home text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Properties</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">15</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                        <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Tenants</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">37</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 p-3 rounded-lg">
                        <i class="fas fa-dollar-sign text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">$25,000</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                        <i class="fas fa-percentage text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Occupancy Rate</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">84.1%</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Properties</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gray-300 dark:bg-gray-600 rounded-lg"></div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Sunset Apartments</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">123 Main St</p>
                                <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400 space-x-3">
                                    <span><i class="fas fa-door-open mr-1"></i>8 units</span>
                                    <span><i class="fas fa-users mr-1"></i>7 occupied</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Occupied</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Activities</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-credit-card text-blue-600 dark:text-blue-400 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 dark:text-white">Rent payment received from Alice Johnson</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Property: Sunset Apartments</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">2 hours ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8">
            <a href="/admin/dashboard" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
    
    <script>
        // Test if dark mode works
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
        
        console.log('Dashboard display test loaded successfully');
        console.log('CSS files loaded:', document.querySelectorAll('link[rel="stylesheet"]').length);
        console.log('Icons test:', document.querySelector('.fas') ? 'Font Awesome loaded' : 'Font Awesome not loaded');
    </script>
</body>
</html>
