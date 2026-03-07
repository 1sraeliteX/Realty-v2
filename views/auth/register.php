<?php $title = 'Register - Real Estate Management'; ?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                <i class="fas fa-user-plus text-primary-600 dark:text-primary-400 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Join Real Estate Management System
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="/register" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        autocomplete="name" 
                        required 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                        placeholder="John Doe"
                        value="<?php echo $_SESSION['old']['name'] ?? ''; ?>"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                        placeholder="john@example.com"
                        value="<?php echo $_SESSION['old']['email'] ?? ''; ?>"
                    >
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Phone Number
                    </label>
                    <input 
                        id="phone" 
                        name="phone" 
                        type="tel" 
                        autocomplete="tel" 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                        placeholder="+1 (555) 123-4567"
                        value="<?php echo $_SESSION['old']['phone'] ?? ''; ?>"
                    >
                </div>

                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Business Name
                    </label>
                    <input 
                        id="business_name" 
                        name="business_name" 
                        type="text" 
                        autocomplete="organization" 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                        placeholder="ABC Properties"
                        value="<?php echo $_SESSION['old']['business_name'] ?? ''; ?>"
                    >
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Account Type <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="role" 
                        name="role" 
                        required 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                    >
                        <option value="">Select account type</option>
                        <option value="admin" <?php echo (($_SESSION['old']['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>
                            Property Admin - Manage my properties
                        </option>
                        <option value="super_admin" <?php echo (($_SESSION['old']['role'] ?? '') === 'super_admin') ? 'selected' : ''; ?>>
                            Super Admin - Manage entire platform
                        </option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Property admins manage their own properties. Super admins can manage all admins and platform settings.
                    </p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="new-password" 
                            required 
                            class="appearance-none relative block w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                            placeholder="••••••••"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password')" 
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Must be at least 8 characters long
                    </p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative">
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            autocomplete="new-password" 
                            required 
                            class="appearance-none relative block w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                            placeholder="••••••••"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password_confirmation')" 
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            <i class="fas fa-eye" id="password_confirmation-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <input 
                    id="agree_terms" 
                    name="agree_terms" 
                    type="checkbox" 
                    required
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
                >
                <label for="agree_terms" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                    I agree to the 
                    <a href="#" class="text-primary-600 hover:text-primary-500 dark:text-primary-400">Terms of Service</a> 
                    and 
                    <a href="#" class="text-primary-600 hover:text-primary-500 dark:text-primary-400">Privacy Policy</a>
                </label>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    id="register-btn"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus group-hover:text-primary-400 h-5 w-5 text-primary-500"></i>
                    </span>
                    <span class="register-text">Create Account</span>
                    <span class="loading-text hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Creating account...
                    </span>
                </button>
            </div>

            <div class="text-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account? 
                </span>
                <a href="/login" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 text-sm">
                    Sign in
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission with loading state
    const registerForm = document.querySelector('form[action="/register"]');
    const registerBtn = document.getElementById('register-btn');
    const registerText = registerBtn.querySelector('.register-text');
    const loadingText = registerBtn.querySelector('.loading-text');

    registerForm.addEventListener('submit', function() {
        registerBtn.disabled = true;
        registerText.classList.add('hidden');
        loadingText.classList.remove('hidden');
    });

    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');

    function validatePasswords() {
        if (passwordConfirmation.value && password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Passwords do not match');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }

    password.addEventListener('change', validatePasswords);
    passwordConfirmation.addEventListener('input', validatePasswords);
});

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
