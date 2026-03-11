<?php
// Initialize framework
require_once __DIR__ . '/../config/init_framework.php';

// Set page data
ViewManager::set('title', $title ?? 'Page Title');
ViewManager::set('pageTitle', $pageTitle ?? 'Page Title');

// Get data from provider
$properties = DataProvider::get('properties');
$tenants = DataProvider::get('tenants');

// Process data for this view
$pageData = [
  'properties' => $properties,
  'tenants' => $tenants,
  'stats' => [
    'total' => count($properties)
  ]
];

// Render view
echo ViewManager::render('new_view_example', $pageData);
?>
