<?php
// Initialize anti-scattering system
require_once __DIR__ . '/../../../config/bootstrap.php';

// Anti-scattering compliance: Get centralized data from ViewManager
$user = \ViewManager::get('user');
$notifications = \ViewManager::get('notifications');
$settings = \ViewManager::get('settings');

// Set page title
\ViewManager::set('title', 'Settings');

// Include the dashboard layout directly
include __DIR__ . '/../dashboard_layout.php';
?>

<!-- Settings Content -->
<div class="flex-1 p-6 overflow-auto">

