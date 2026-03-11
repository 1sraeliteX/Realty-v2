<?php
// Test anti-scattering system and hamburger menu
require_once __DIR__ . '/config/bootstrap.php';

// Set up test data
ViewManager::set('content', '<h1>Testing Hamburger Menu</h1><p>Click the hamburger menu to test functionality.</p>');

// Render the dashboard layout
ViewManager::render('admin.dashboard_layout', [
    'content' => '<h1>Testing Hamburger Menu</h1><p>Click the hamburger menu to test functionality.</p>'
]);
?>
