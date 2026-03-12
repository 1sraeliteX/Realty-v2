<?php
// Test notification bell functionality
require_once __DIR__ . '/../config/bootstrap.php';

// Mock user data
ViewManager::set('user', [
    'name' => 'Admin User',
    'email' => 'admin@example.com'
]);

// Mock notification data
ViewManager::set('notifications', [
    ['id' => 1, 'type' => 'info', 'message' => 'New tenant application received', 'time' => '5 min ago', 'read' => false],
    ['id' => 2, 'type' => 'warning', 'message' => 'Rent payment overdue for Unit 3A', 'time' => '1 hour ago', 'read' => false],
    ['id' => 3, 'type' => 'success', 'message' => 'Maintenance request completed', 'time' => '2 hours ago', 'read' => true],
    ['id' => 4, 'type' => 'payment', 'message' => 'Payment received from John Doe', 'time' => '3 hours ago', 'read' => true]
]);

// Mock recent activities data
ViewManager::set('recentActivities', [
    ['id' => 1, 'action' => 'tenant', 'description' => 'New tenant registered', 'created_at' => '2024-01-15 10:30:00', 'property_name' => 'Sunset Apartments'],
    ['id' => 2, 'action' => 'payment', 'description' => 'Rent payment processed', 'created_at' => '2024-01-15 09:15:00', 'property_name' => 'Ocean View Condo'],
    ['id' => 3, 'action' => 'maintenance', 'description' => 'Maintenance request filed', 'created_at' => '2024-01-15 08:45:00', 'property_name' => 'Garden Villa']
]);

ViewManager::set('title', 'Notification Bell Test');

// Simple content for testing
$content = '
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Notification Bell Test</h2>
        <div class="space-y-4">
            <div class="p-4 bg-green-50 rounded-lg">
                <h3 class="text-sm font-medium text-green-800">✅ Test Steps:</h3>
                <ol class="mt-2 text-sm text-green-700 list-decimal list-inside space-y-1">
                    <li>Click the bell icon in the top right - dropdown should open</li>
                    <li>Click outside the dropdown - it should close</li>
                    <li>Click the user profile menu - notification dropdown should close, user dropdown should open</li>
                    <li>Click the bell again - user dropdown should close, notification dropdown should open</li>
                    <li>Verify notification items show correctly with icons, timestamps, and unread indicators</li>
                </ol>
            </div>
            
            <div class="p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800">🔍 Features Implemented:</h3>
                <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                    <li>Bell icon with red notification badge (shows count 3)</li>
                    <li>Dropdown opens/closes on bell click</li>
                    <li>Displays both notifications and recent activities</li>
                    <li>Proper icons for different notification types (user, payment, maintenance, etc.)</li>
                    <li>Timestamp formatting (Just now, X minutes ago, etc.)</li>
                    <li>Unread indicators (blue dot) for unread notifications</li>
                    <li>"View all notifications" link at bottom</li>
                    <li>Proper z-index and positioning</li>
                    <li>Does not interfere with user dropdown</li>
                    <li>Click outside to close functionality</li>
                </ul>
            </div>
            
            <div class="p-4 bg-yellow-50 rounded-lg">
                <h3 class="text-sm font-medium text-yellow-800">📊 Data Sources:</h3>
                <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1">
                    <li>Mock notifications from ViewManager::set("notifications")</li>
                    <li>Mock recent activities from ViewManager::set("recentActivities")</li>
                    <li>Combined and displayed in single dropdown</li>
                    <li>Ready for real data integration</li>
                </ul>
            </div>
        </div>
    </div>
</div>
';

ViewManager::set('content', $content);

// Include the dashboard layout which contains our notification bell
include __DIR__ . '/../views/admin/dashboard_layout.php';
?>
