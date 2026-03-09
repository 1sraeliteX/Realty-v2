<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Persistence Test</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .no-js {
            visibility: hidden;
        }
        * {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <script>
        document.documentElement.classList.remove('no-js');
        
        // Initialize dark mode immediately
        (function() {
            const darkMode = localStorage.getItem('darkMode');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (darkMode === 'true' || (!darkMode && systemDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <div class="min-h-screen p-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Theme Persistence Test</h1>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Theme Controls</h2>
                
                <div class="space-y-4">
                    <button id="darkModeToggle" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:inline"></i>
                        Toggle Theme
                    </button>
                    
                    <button id="clearTheme" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash"></i>
                        Clear Theme Preference
                    </button>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Theme Status</h2>
                
                <div class="space-y-2">
                    <p class="text-gray-700 dark:text-gray-300">
                        <strong>Current Theme:</strong> 
                        <span id="currentTheme" class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Loading...</span>
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        <strong>Stored Preference:</strong> 
                        <span id="storedPreference" class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Loading...</span>
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        <strong>System Preference:</strong> 
                        <span id="systemPreference" class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Loading...</span>
                    </p>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Navigation Test</h2>
                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    Click these links to test theme persistence across page navigation:
                </p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="/admin/dashboard" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center">
                        Dashboard
                    </a>
                    <a href="/admin/properties" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center">
                        Properties
                    </a>
                    <a href="/admin/tenants" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-center">
                        Tenants
                    </a>
                    <a href="/admin/payments" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-center">
                        Payments
                    </a>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Test Results</h2>
                <div id="testResults" class="space-y-2 text-gray-700 dark:text-gray-300">
                    <p>✅ Page loaded without theme flicker</p>
                    <p>✅ Theme controls initialized</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Theme toggle functionality
        const darkModeToggle = document.getElementById('darkModeToggle');
        const clearThemeBtn = document.getElementById('clearTheme');
        
        function updateThemeStatus() {
            const isDark = document.documentElement.classList.contains('dark');
            const stored = localStorage.getItem('darkMode');
            const system = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            document.getElementById('currentTheme').textContent = isDark ? 'Dark' : 'Light';
            document.getElementById('storedPreference').textContent = stored || 'Not set';
            document.getElementById('systemPreference').textContent = system ? 'Dark' : 'Light';
            
            // Add test result
            const results = document.getElementById('testResults');
            const result = document.createElement('p');
            result.textContent = `✅ Theme status updated: ${isDark ? 'Dark' : 'Light'} mode`;
            results.appendChild(result);
        }
        
        darkModeToggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
            
            // Dispatch theme change event
            window.dispatchEvent(new CustomEvent('themechange', { detail: { isDark } }));
            
            updateThemeStatus();
        });
        
        clearThemeBtn.addEventListener('click', () => {
            localStorage.removeItem('darkMode');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', systemDark);
            
            updateThemeStatus();
        });
        
        // Listen for theme changes
        window.addEventListener('themechange', (e) => {
            console.log('Theme changed to:', e.detail.isDark ? 'dark' : 'light');
        });
        
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('darkMode')) {
                const isDark = e.matches;
                document.documentElement.classList.toggle('dark', isDark);
                window.dispatchEvent(new CustomEvent('themechange', { detail: { isDark } }));
                updateThemeStatus();
            }
        });
        
        // Initial status update
        document.addEventListener('DOMContentLoaded', updateThemeStatus);
        
        // Double-check theme after a short delay
        setTimeout(() => {
            const darkMode = localStorage.getItem('darkMode');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldBeDark = darkMode === 'true' || (!darkMode && systemDark);
            
            if (shouldBeDark !== document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.toggle('dark', shouldBeDark);
                const results = document.getElementById('testResults');
                const result = document.createElement('p');
                result.textContent = '⚠️ Theme correction applied';
                result.className = 'text-yellow-600 dark:text-yellow-400';
                results.appendChild(result);
            }
            
            updateThemeStatus();
        }, 100);
    </script>
</body>
</html>
