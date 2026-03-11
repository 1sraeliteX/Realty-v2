<?php
// Initialize framework
// Framework initialization handled by controller

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
echo ViewManager::render('admin.new_page_example', $pageData);
?>
