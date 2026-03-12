<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Real Estate Management'; ?></title>
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
<body class="bg-gray-50 dark:bg-gray-900">
    <?php echo $content; ?>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

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

        // Helper function to get cookies
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

        // CSRF token for AJAX requests
        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
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
    </script>
</body>
</html>
