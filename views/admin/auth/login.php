<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Realty</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-600 rounded-2xl mb-4">
                <i class="fas fa-building text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Realty Admin</h1>
            <p class="text-gray-400 mt-1">Sign in to your dashboard</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-4 px-4 py-3 bg-red-900/50 border border-red-700 rounded-lg text-red-300 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-700">
            <form method="POST" action="/admin/login" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Email Address
                    </label>
                    <input type="email" name="email" required autofocus
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="admin@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="passwordInput" required
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-12"
                               placeholder="•••••••">
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-200">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                        <input type="checkbox" name="remember" 
                               class="rounded border-gray-600 bg-gray-700 text-blue-500">
                        Remember me
                    </label>
                </div>
                <button type="submit"
                        class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                    <i class="fas fa-right-to-bracket mr-2"></i>
                    Sign In
                </button>
            </form>
            <p class="mt-6 text-center text-sm text-gray-400">
                Don't have an account?
                <a href="/admin/register" class="text-blue-400 hover:underline">
                    Register here
                </a>
            </p>
        </div>
    </div>
    <script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
    </script>
</body>
</html>
