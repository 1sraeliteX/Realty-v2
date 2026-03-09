<?php 
$title = 'Login - Real Estate Management';
$content = '
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <!-- Main Card Container -->
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900 mb-4">
                <i class="fas fa-building text-primary-600 dark:text-primary-400 text-xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2">
                Sign in to your account
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Real Estate Management System
            </p>
        </div>
        
        <!-- Demo Accounts Info -->
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Demo Accounts</h4>
            <div class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                <p><strong>Super Admin:</strong> superadmin@cornerstone.com / admin123</p>
                <p><strong>Regular Admin:</strong> admin@cornerstone.com / admin123</p>
            </div>
        </div>
        
        <!-- Form Section -->
        <form class="space-y-6" action="/admin/login" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email Address
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm" 
                        placeholder="Enter your email"
                    >
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required 
                            class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm" 
                            placeholder="Enter your password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword(\'password\')" 
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember-me" 
                        name="remember_me" 
                        type="checkbox" 
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
                    >
                    <label for="remember-me" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    id="login-btn"
                >
                    <span class="login-text">Sign in</span>
                    <span class="loading-text hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Signing in...
                    </span>
                </button>
            </div>

            <!-- Sign Up Link -->
            <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Don\'t have an account? 
                </span>
                <a href="/register" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 text-sm">
                    Sign up
                </a>
            </div>

            <!-- Quick Admin Login -->
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100">Admin Access</h4>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">Quick login for administrators</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="quickAdminLogin()" 
                        class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors"
                    >
                        <i class="fas fa-user mr-2"></i>Login as Admin
                    </button>
                </div>
            </div>

            <!-- Quick Super Admin Login -->
            <div class="mt-4 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-purple-900 dark:text-purple-100">Super Admin Access</h4>
                        <p class="text-xs text-purple-700 dark:text-purple-300 mt-1">Quick login for platform administrators</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="quickSuperAdminLogin()" 
                        class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition-colors"
                    >
                        <i class="fas fa-user-shield mr-2"></i>Login as Super Admin
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
';

// JavaScript functions
echo '
<script>
document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.querySelector("form[action=\\"/login\\"]");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    
    // Quick Admin Login
    function quickAdminLogin() {
        console.log("Quick Admin Login clicked");
        emailInput.value = "admin@cornerstone.com";
        passwordInput.value = "admin123";
        
        // Add visual feedback
        emailInput.classList.add("ring-2", "ring-blue-500");
        passwordInput.classList.add("ring-2", "ring-blue-500");
        
        setTimeout(() => {
            emailInput.classList.remove("ring-2", "ring-blue-500");
            passwordInput.classList.remove("ring-2", "ring-blue-500");
        }, 1000);
        
        // Submit form
        loginForm.submit();
    }
    
    // Quick Super Admin Login
    function quickSuperAdminLogin() {
        console.log("Quick Super Admin Login clicked");
        emailInput.value = "superadmin@cornerstone.com";
        passwordInput.value = "admin123";
        
        // Add visual feedback
        emailInput.classList.add("ring-2", "ring-purple-500");
        passwordInput.classList.add("ring-2", "ring-purple-500");
        
        setTimeout(() => {
            emailInput.classList.remove("ring-2", "ring-purple-500");
            passwordInput.classList.remove("ring-2", "ring-purple-500");
        }, 1000);
        
        // Submit form
        loginForm.submit();
    }
    
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const eye = document.getElementById(fieldId + "-eye");
        
        if (field.type === "password") {
            field.type = "text";
            eye.classList.remove("fa-eye");
            eye.classList.add("fa-eye-slash");
        } else {
            field.type = "password";
            eye.classList.remove("fa-eye-slash");
            eye.classList.add("fa-eye");
        }
    }
    
    // Handle form submission
    loginForm.addEventListener("submit", function(e) {
        const submitBtn = document.getElementById("login-btn");
        const loginText = submitBtn.querySelector(".login-text");
        const loadingText = submitBtn.querySelector(".loading-text");
        
        // Show loading state
        submitBtn.disabled = true;
        loginText.classList.add("hidden");
        loadingText.classList.remove("hidden");
        
        // Reset loading state after 3 seconds
        setTimeout(() => {
            submitBtn.disabled = false;
            loginText.classList.remove("hidden");
            loadingText.classList.add("hidden");
        }, 3000);
    });
    
    // Display errors if any
    ';
    
    if (isset($_SESSION["error"])) {
        echo 'const errorDiv = document.createElement("div");
        errorDiv.className = "mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded";
        errorDiv.innerHTML = "' . $_SESSION["error"] . '";
        loginForm.parentNode.insertBefore(errorDiv, loginForm);
        unset($_SESSION["error"]);
        ';
    }
    
    echo '});
</script>';

include __DIR__ . '/../simple_layout.php';
?>
