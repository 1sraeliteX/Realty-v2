<?php

/**
 * Framework Initialization
 * Load all core components in the right order
 */

// Load core framework files
require_once __DIR__ . '/components_registry.php';
require_once __DIR__ . '/data_provider.php';
require_once __DIR__ . '/view_manager.php';
require_once __DIR__ . '/ArrayHelper.php';

// Initialize data provider
DataProvider::init();

// Set default view data
ViewManager::set('user', DataProvider::get('user'));
ViewManager::set('notifications', DataProvider::get('notifications'));

/**
 * Helper function for quick view rendering
 */
function view($view, $data = []) {
    return ViewManager::render($view, $data);
}

/**
 * Helper function for component rendering
 */
function component($name, $data = []) {
    return ViewManager::component($name, $data);
}

/**
 * Helper function for getting data
 */
function data($key, $default = null) {
    return DataProvider::get($key, $default);
}

/**
 * Helper function for setting data
 */
function set_data($key, $value) {
    DataProvider::set($key, $value);
}

/**
 * Auto-include in all views
 */
function auto_include_components() {
    // This ensures UIComponents is always available
    ComponentRegistry::load('ui-components');
}

// Register auto-include
auto_include_components();
