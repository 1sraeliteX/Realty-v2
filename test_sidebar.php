<?php
require_once __DIR__ . '/config/bootstrap.php';

// Mock user data for testing
ViewManager::set('user', [
    'name' => 'Test Admin',
    'email' => 'test@example.com',
    'avatar' => null
]);

ViewManager::set('title', 'Sidebar Test');
ViewManager::set('content', '
<div class="max-w-7xl mx-auto">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                Sidebar Test Page
            </h2>
            <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                This page tests the sidebar functionality across all screen sizes.
            </div>
            <div class="mt-5">
                <h3 class="text-md font-medium text-gray-900 dark:text-white mb-3">Test Instructions:</h3>
                <ul class="list-disc list-inside space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li><strong>Desktop (lg+):</strong> Sidebar should be visible on the left side</li>
                    <li><strong>Tablet (md):</strong> Sidebar should be hidden, use hamburger menu</li>
                    <li><strong>Mobile (sm):</strong> Sidebar should be hidden, use hamburger menu</li>
                    <li><strong>Dark Mode:</strong> Click moon/sun icon to toggle</li>
                    <li><strong>Logout:</strong> Red logout button at bottom of sidebar</li>
                </ul>
            </div>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <h4 class="font-medium text-blue-900 dark:text-blue-300">Responsive Design</h4>
                    <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">Sidebar adapts to screen size</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                    <h4 class="font-medium text-green-900 dark:text-green-300">Dark Mode</h4>
                    <p class="text-sm text-green-700 dark:text-green-400 mt-1">Toggle with moon/sun icon</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                    <h4 class="font-medium text-red-900 dark:text-red-300">Logout Button</h4>
                    <p class="text-sm text-red-700 dark:text-red-400 mt-1">Always visible at bottom</p>
                </div>
            </div>
        </div>
    </div>
</div>
');

include __DIR__ . '/views/admin/dashboard_layout.php';
?>
