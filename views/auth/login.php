<?php $title = 'Login - Real Estate Management'; ?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                <i class="fas fa-building text-primary-600 dark:text-primary-400 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Real Estate Management System
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="/login" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-t-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" 
                        placeholder="Email address"
                        value="<?php echo $_SESSION['old']['email'] ?? ''; ?>"
                    >
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required 
                            class="appearance-none rounded-none relative block w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-b-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" 
                            placeholder="Password"
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
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember-me" 
                        name="remember_me" 
                        type="checkbox" 
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
                    >
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
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
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    id="login-btn"
                >
                    <span class="login-text">Sign in</span>
                    <span class="loading-text hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Signing in...
                    </span>
                </button>
            </div>

            <div class="text-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Don't have an account? 
                </span>
                <a href="/register" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 text-sm">
                    Sign up
                </a>
            </div>

            <!-- Quick Super Admin Login -->
            <div class="mt-6 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
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

        <!-- API Login Section -->
        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-center text-sm font-medium text-gray-900 dark:text-white mb-4">
                Or use API Token
            </h3>
            <form id="api-login-form" class="space-y-4">
                <div>
                    <label for="api-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="api-email" 
                        required
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        placeholder="your@email.com"
                    >
                </div>
                <div>
                    <label for="api-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="api-password" 
                        required
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        placeholder="••••••••"
                    >
                </div>
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                    <i class="fas fa-key mr-2"></i>
                    Get API Token
                </button>
            </form>
            <div id="api-token-result" class="mt-4 hidden">
                <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-md">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Your API Token:</h4>
                    <div class="bg-white dark:bg-gray-900 p-3 rounded border border-gray-300 dark:border-gray-600">
                        <code id="token-display" class="text-xs text-gray-800 dark:text-gray-200 break-all"></code>
                    </div>
                    <button 
                        onclick="copyToken()" 
                        class="mt-2 text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400"
                    >
                        <i class="fas fa-copy mr-1"></i> Copy Token
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission with loading state
    const loginForm = document.querySelector('form[action="/login"]');
    const loginBtn = document.getElementById('login-btn');
    const loginText = loginBtn.querySelector('.login-text');
    const loadingText = loginBtn.querySelector('.loading-text');

    loginForm.addEventListener('submit', function() {
        loginBtn.disabled = true;
        loginText.classList.add('hidden');
        loadingText.classList.remove('hidden');
    });

    // Handle API login
    const apiLoginForm = document.getElementById('api-login-form');
    const apiTokenResult = document.getElementById('api-token-result');
    const tokenDisplay = document.getElementById('token-display');

    apiLoginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('api-email').value;
        const password = document.getElementById('api-password').value;

        try {
            const response = await apiRequest('/api/auth/login', {
                method: 'POST',
                body: JSON.stringify({ email, password })
            });

            // Store token in localStorage
            localStorage.setItem('jwt_token', response.token);
            
            // Display token
            tokenDisplay.textContent = response.token;
            apiTokenResult.classList.remove('hidden');
            
            showToast('API token generated successfully!', 'success');
        } catch (error) {
            // Error is already handled by apiRequest
        }
    });
});

function copyToken() {
    const tokenDisplay = document.getElementById('token-display');
    navigator.clipboard.writeText(tokenDisplay.textContent).then(() => {
        showToast('Token copied to clipboard!', 'success');
    });
}

function quickSuperAdminLogin() {
    // Fill in super admin credentials
    document.getElementById('email').value = 'superadmin@cornerstone.com';
    document.getElementById('password').value = 'admin123';
    
    // Show confirmation
    showToast('Super Admin credentials filled. Click "Sign in" to continue.', 'info');
    
    // Optionally submit the form automatically
    // document.querySelector('form[action="/login"]').submit();
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}
</script>
