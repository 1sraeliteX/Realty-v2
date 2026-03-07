<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Real Estate Management'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom styles */
        .primary-600 { color: #2563eb; }
        .bg-primary-600 { background-color: #2563eb; }
        .hover\:bg-primary-700:hover { background-color: #1d4ed8; }
        .ring-primary-500:focus { border-color: #3b82f6; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>
    
    <?php echo $content ?? ''; ?>

    <script>
        // Toast notification system
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `mb-2 px-4 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            }`;
            
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
            
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
            }, 100);
            
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Password toggle function
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

        // Quick super admin login
        function quickSuperAdminLogin() {
            document.getElementById('email').value = 'superadmin@cornerstone.com';
            document.getElementById('password').value = 'admin123';
            showToast('Super Admin credentials filled. Click "Sign in" to continue.', 'info');
        }

        // Quick admin login
        function quickAdminLogin() {
            document.getElementById('email').value = 'admin@cornerstone.com';
            document.getElementById('password').value = 'admin123';
            showToast('Admin credentials filled. Click "Sign in" to continue.', 'info');
        }

        // Form submission handlers
        document.addEventListener('DOMContentLoaded', function() {
            // Login form handler
            const loginForm = document.querySelector('form[action="/login"]');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;
                    
                    // Simple demo login
                    if (email === 'superadmin@cornerstone.com' && password === 'admin123') {
                        showToast('Login successful! Redirecting to Super Admin dashboard...', 'success');
                        setTimeout(() => {
                            window.location.href = '/superadmin';
                        }, 2000);
                    } else if (email === 'admin@cornerstone.com' && password === 'admin123') {
                        showToast('Login successful! Redirecting to Admin dashboard...', 'success');
                        setTimeout(() => {
                            window.location.href = '/dashboard';
                        }, 2000);
                    } else {
                        showToast('Invalid credentials. Try the demo accounts.', 'error');
                    }
                });
            }

            // Register form handler
            const registerForm = document.querySelector('form[action="/register"]');
            if (registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showToast('Registration successful! Please login with your new account.', 'success');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                });
            }
        });

        // Dark mode toggle
        if (localStorage.getItem('darkMode') === 'true' || 
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>
