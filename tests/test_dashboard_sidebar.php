<?php
// Simple test to check if admin dashboard loads
require_once __DIR__ . '/config/bootstrap.php';

// Mock admin session
$_SESSION['admin'] = [
    'id' => 1,
    'name' => 'Test Admin',
    'email' => 'test@example.com',
    'role' => 'admin'
];

// Set up view data
ViewManager::set('user', [
    'name' => 'Test Admin',
    'email' => 'test@example.com',
    'avatar' => null
]);

ViewManager::set('notifications', []);
ViewManager::set('title', 'Admin Dashboard - Sidebar Test');

// Test sidebar functionality
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Sidebar Test</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
</head>
<body class='bg-gray-50'>
    <h1 class='text-2xl font-bold p-4'>Sidebar Test - Loading Dashboard Layout</h1>";

// Load the dashboard layout
try {
    include __DIR__ . '/views/admin/dashboard_layout.php';
    echo "<div class='p-4 bg-green-100 text-green-800 m-4 rounded'>✅ Dashboard layout loaded successfully</div>";
} catch (Exception $e) {
    echo "<div class='p-4 bg-red-100 text-red-800 m-4 rounded'>❌ Error loading layout: " . $e->getMessage() . "</div>";
}

echo "<script>
// Test sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const openBtn = document.getElementById('open-sidebar');
    const closeBtn = document.getElementById('close-sidebar');
    
    console.log('Sidebar Test Results:');
    console.log('- Sidebar element:', !!sidebar);
    console.log('- Open button:', !!openBtn);
    console.log('- Close button:', !!closeBtn);
    console.log('- Sidebar classes:', sidebar?.className);
    
    // Test mobile toggle
    if (openBtn && sidebar) {
        openBtn.addEventListener('click', function() {
            console.log('Open button clicked - testing sidebar toggle');
            sidebar.classList.toggle('-translate-x-full');
        });
    }
    
    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', function() {
            console.log('Close button clicked - testing sidebar toggle');
            sidebar.classList.add('-translate-x-full');
        });
    }
    
    // Add test instructions
    const instructions = document.createElement('div');
    instructions.className = 'fixed bottom-4 left-4 bg-blue-100 text-blue-800 p-4 rounded-lg max-w-md';
    instructions.innerHTML = '<h3 class=\"font-bold mb-2\">Sidebar Test Instructions:</h3><ul class=\"text-sm\"><li>• On desktop: Sidebar should be visible on the left</li><li>• On mobile: Click hamburger menu to toggle</li><li>• Check browser console for debug info</li></ul>';
    document.body.appendChild(instructions);
});
</script>
</body>
</html>";
?>
