<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance — Realty</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    
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
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Local CSS files -->
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center">
    <?php
    // Admin bypass check
    if (isset($_GET['bypass']) && $_GET['bypass'] === '1') {
        echo '<div class="text-center p-8">';
        echo '<h1 class="text-2xl font-bold text-green-600 dark:text-green-400 mb-4">Site is live for admin</h1>';
        echo '<p class="text-gray-600 dark:text-gray-400">Maintenance bypass is active.</p>';
        echo '</div>';
        exit;
    }
    ?>
    
    <div class="max-w-md w-full mx-auto p-6 text-center">
        <!-- Logo/App Name -->
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-600 dark:bg-primary-500 rounded-full mb-4">
                <i class="fas fa-building text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Realty</h1>
        </div>
        
        <!-- Maintenance Icon -->
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
                <i class="fas fa-tools text-yellow-600 dark:text-yellow-400 text-3xl animate-pulse-slow"></i>
            </div>
        </div>
        
        <!-- Main Content -->
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
            Under Maintenance
        </h2>
        
        <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
            We're making improvements to serve you better. 
            Check back shortly.
        </p>
        
        <!-- Optional: Estimated return time -->
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 mb-8">
            <div class="flex items-center justify-center text-sm text-gray-600 dark:text-gray-400">
                <i class="far fa-clock mr-2"></i>
                <span>Expected to be back online soon</span>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <p class="text-sm text-gray-500 dark:text-gray-500 mb-3">
                Need immediate assistance?
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="mailto:support@realty.com" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors duration-200">
                    <i class="fas fa-envelope mr-2"></i>
                    Email Support
                </a>
                <a href="tel:+1234567890" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors duration-200">
                    <i class="fas fa-phone mr-2"></i>
                    Call Us
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-xs text-gray-400 dark:text-gray-600">
            <p>Thank you for your patience</p>
        </div>
    </div>
    
    <!-- Theme toggle script (consistent with main app) -->
    <script>
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
    </script>
</body>
</html>
