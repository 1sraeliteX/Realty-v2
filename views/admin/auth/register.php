<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
$error = $_SESSION['register_error'] ?? null;
unset($_SESSION['register_error']);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register — Realty</title>
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
            <p class="text-gray-400 mt-1">Create your admin account</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-4 px-4 py-3 bg-red-900/50 border border-red-700 rounded-lg text-red-300 text-sm">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-700">
            <form method="POST" action="/admin/register" class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            First Name
                        </label>
                        <input type="text" name="first_name" required
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Last Name
                        </label>
                        <input type="text" name="last_name" required
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Email Address
                    </label>
                    <input type="email" name="email" required
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="admin@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Password
                    </label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Min. 8 characters">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Confirm Password
                    </label>
                    <input type="password" name="password_confirm" required
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Repeat password">
                </div>
                <button type="submit"
                        class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </button>
            </form>
            <p class="mt-6 text-center text-sm text-gray-400">
                Already have an account?
                <a href="/admin/login" class="text-blue-400 hover:underline">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</body>
</html>
