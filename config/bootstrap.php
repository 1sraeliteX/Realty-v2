<?php

/**
 * Bootstrap - Application initialization
 * Initializes the anti-scattering system before any views are loaded
 */

// Load core anti-scattering components
require_once __DIR__ . '/components_registry.php';
require_once __DIR__ . '/view_manager.php';
require_once __DIR__ . '/data_provider.php';
require_once __DIR__ . '/ArrayHelper.php';

// Initialize the system
ComponentRegistry::init();
DataProvider::init();

// Set up common view data using DataProvider
ViewManager::set('user', DataProvider::get('user'));
ViewManager::set('notifications', DataProvider::get('notifications'));
ViewManager::set('title', 'Admin Dashboard');

// Make sure UIComponents are available
ComponentRegistry::load('ui-components');

?>
