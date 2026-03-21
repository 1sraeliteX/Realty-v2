<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';

// Get data from ViewManager (anti-scattering compliant)
$title = ViewManager::get('title', 'Payments');
$content = ViewManager::get('content', '');

// Load currency preference for this admin using CurrencyHelper (anti-scattering compliant)
$currencySymbol = CurrencyHelper::getSymbol('₦');
$currencyCode   = CurrencyHelper::getCode('NGN');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
    
    <!-- Custom Tailwind Config -->
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
                        cream: {
                            50: '#fffbf5',
                            100: '#fef3e2',
                            200: '#fde8cc',
                            300: '#fbd7a5',
                            400: '#f8bb6c',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Theme initialization script -->
    <script>
        // Apply theme immediately before any CSS loads
        (function() {
            var theme = localStorage.getItem('theme');
            if (theme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Main Content -->
    <main class="min-h-screen p-6">
        <?php echo $content; ?>
    </main>
</body>
</html>
