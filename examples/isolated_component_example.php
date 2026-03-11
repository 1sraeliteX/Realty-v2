<?php

/**
 * Example: How to build isolated components that don't break other parts
 * This demonstrates the new architecture that prevents scattering
 */

// Initialize framework (do this once in your bootstrap)
require_once __DIR__ . '/../config/init_framework.php';

// Example 1: Isolated View Rendering
function renderDashboardPage() {
    // Set page-specific data
    ViewManager::set('title', 'Dashboard');
    ViewManager::set('pageTitle', 'Dashboard Overview');
    
    // Add page-specific data without affecting other pages
    $pageData = [
        'stats' => DataProvider::get('reports'),
        'recent_properties' => array_slice(DataProvider::get('properties'), 0, 3),
        'recent_tenants' => array_slice(DataProvider::get('tenants'), 0, 3)
    ];
    
    // Render view - components are auto-loaded
    return ViewManager::render('admin.dashboard_enhanced', $pageData);
}

// Example 2: Isolated Component
function renderPropertyCard($property) {
    // Components are self-contained
    return ViewManager::component('ui-components.card', [
        'content' => "
            <div class='property-card'>
                <h3>{$property['name']}</h3>
                <p>{$property['address']}</p>
                <p>Status: {$property['status']}</p>
            </div>
        ",
        'class' => 'property-card-wrapper'
    ]);
}

// Example 3: Safe Data Manipulation
function addNewProperty($propertyData) {
    // This won't affect other parts of the system
    $currentProperties = DataProvider::get('properties');
    $newProperty = array_merge([
        'id' => count($currentProperties) + 1,
        'status' => 'pending',
        'occupied_units' => 0
    ], $propertyData);
    
    // Update only this specific data
    DataProvider::set('properties', array_merge($currentProperties, [$newProperty]));
    
    return $newProperty;
}

// Example 4: Component Isolation
function createCustomComponent($name, $template) {
    // Register new component without affecting existing ones
    ComponentRegistry::register($name, $template, ['ui-components']);
    
    // Now it can be used independently
    return ViewManager::component($name, ['data' => 'example']);
}

// Example usage:
echo renderDashboardPage();
echo renderPropertyCard(DataProvider::get('properties')[0]);

// Add new property without breaking existing views
$newProp = addNewProperty([
    'name' => 'New Building',
    'address' => '123 New St',
    'type' => 'Mixed Use'
]);

// Create custom component
createCustomComponent('custom-widget', __DIR__ . '/../components/CustomWidget.php');
