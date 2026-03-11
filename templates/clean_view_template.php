<?php

/**
 * Clean View Template
 * Use this template for all new views to prevent scattering
 */

// 1. Initialize framework (always first)
require_once __DIR__ . '/../config/init_framework.php';

// 2. Set page-specific data (isolated from other pages)
ViewManager::set('title', $title ?? 'Page Title');
ViewManager::set('pageTitle', $pageTitle ?? 'Page Title');

// 3. Get data from centralized provider (don't create mock data here)
$properties = DataProvider::get('properties');
$tenants = DataProvider::get('tenants');
$user = DataProvider::get('user');

// 4. Process data only for this view (no side effects)
$pageData = [
    'filtered_properties' => array_filter($properties, function($p) use ($statusFilter) {
        return !$statusFilter || $p['status'] === $statusFilter;
    }),
    'stats' => [
        'total' => count($properties),
        'active' => count(array_filter($properties, fn($p) => $p['status'] === 'active'))
    ]
];

// 5. Render view (components auto-loaded)
echo ViewManager::render('admin.your_view_name', $pageData);

/**
 * DO NOT DO THESE THINGS (they cause scattering):
 * 
 * ❌ Don't include components directly:
 *    require_once __DIR__ . '/../../components/UIComponents.php';
 * 
 * ❌ Don't include layout files:
 *    include __DIR__ . '/../layout.php';
 * 
 * ❌ Don't create mock data in views:
 *    $mockData = [...];
 * 
 * ❌ Don't modify global state:
 *    $_SESSION['something'] = 'value';
 * 
 * ❌ Don't mix business logic with presentation:
 *    if ($_POST['action']) { ... }
 * 
 * ✅ INSTEAD, DO THESE:
 * 
 * ✅ Use the framework
 * ✅ Keep data in DataProvider
 * ✅ Use ViewManager for rendering
 * ✅ Make components self-contained
 * ✌ Keep views focused on presentation only
 */
