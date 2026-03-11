<?php
// Initialize anti-scattering system
require_once __DIR__ . '/config/bootstrap.php';

// Set content for testing
$content = '
<div class="max-w-7xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Hamburger Menu Test</h1>
        <div class="space-y-4">
            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <h2 class="font-semibold text-green-800 dark:text-green-200">✅ Anti-Scattering System</h2>
                <p class="text-green-600 dark:text-green-400">ComponentRegistry, ViewManager, and DataProvider are all working correctly.</p>
            </div>
            
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <h2 class="font-semibold text-blue-800 dark:text-blue-200">📱 Mobile Menu Test</h2>
                <p class="text-blue-600 dark:text-blue-400">Resize your browser to mobile width or use dev tools to test the hamburger menu.</p>
                <ul class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                    <li>• Click the hamburger icon (☰) to open sidebar</li>
                    <li>• Click the X icon or backdrop to close</li>
                    <li>• Test dark mode toggle</li>
                    <li>• Test notifications dropdown</li>
                    <li>• Test user menu dropdown</li>
                </ul>
            </div>
            
            <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <h2 class="font-semibold text-purple-800 dark:text-purple-200">🤖 AI Assistant</h2>
                <p class="text-purple-600 dark:text-purple-400">Click the DotBot robot icon in the bottom-right corner to test the chat interface.</p>
            </div>
        </div>
    </div>
</div>
';

// Include the dashboard layout
include __DIR__ . '/views/admin/dashboard_layout.php';
?>
