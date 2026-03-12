<?php
// Anti-scattering compliant theme toggle partial
// Uses ComponentRegistry to load the theme toggle component

// Initialize framework if not already loaded
if (!class_exists('ComponentRegistry')) {
    require_once __DIR__ . '/../config/bootstrap.php';
}

ComponentRegistry::load('theme-toggle');

// Render the theme toggle button
echo ThemeToggleComponent::render([
    'size' => 'text-lg',
    'class' => 'text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700',
    'id' => 'theme-toggle'
]);
?>
