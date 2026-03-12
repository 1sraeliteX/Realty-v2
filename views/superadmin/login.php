<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../config/bootstrap.php';

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Super Admin Login - Cornerstone Realty');
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 to-purple-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo and Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-purple-600 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-user-shield text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                Super Admin Portal
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Sign in to your super admin account
            </p>
        </div>

        <!-- Login Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8">
            <form id="loginForm" class="space-y-6" action="/superadmin/login" method="POST">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            autocomplete="email" 
                            required
                            class="appearance-none relative block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700"
                            placeholder="superadmin@example.com"
                        >
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required
                            class="appearance-none relative block w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700"
                            placeholder="Enter your password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password')" 
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember-me" 
                            name="remember_me" 
                            type="checkbox" 
                            class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 rounded"
                        >
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-purple-600 hover:text-purple-500 dark:text-purple-400">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <!-- Error Message (if any) -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 dark:text-red-300">
                                    <?php 
                                    echo htmlspecialchars($_SESSION['error']); 
                                    unset($_SESSION['error']);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        id="login-btn"
                    >
                        <span class="login-text">Sign in as Super Admin</span>
                        <span class="loading-text hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Signing in...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Demo Account Info -->
            <div class="mt-6 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-purple-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-purple-800 dark:text-purple-200">Demo Account</h3>
                        <div class="mt-2 text-sm text-purple-700 dark:text-purple-300">
                            <p class="font-mono">superadmin@cornerstone.com</p>
                            <p class="font-mono">admin123</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Admin Login -->
        <div class="text-center">
            <a href="/admin/login" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Admin Login
            </a>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const eye = document.getElementById(inputId + '-eye');
    
    if (input.type === 'password') {
        input.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}

// Handle form submission
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('login-btn');
    const loginText = btn.querySelector('.login-text');
    const loadingText = btn.querySelector('.loading-text');
    
    // Show loading state
    btn.disabled = true;
    loginText.classList.add('hidden');
    loadingText.classList.remove('hidden');
});

// Auto-fill demo credentials on double click
document.addEventListener('dblclick', function(e) {
    if (e.target.closest('.bg-purple-50, .dark\\:bg-purple-900\\/20')) {
        document.getElementById('email').value = 'superadmin@cornerstone.com';
        document.getElementById('password').value = 'admin123';
    }
});
</script>
