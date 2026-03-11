<?php
// Initialize anti-scattering system
require_once __DIR__ . '/config/bootstrap.php';

// Set up test data
ViewManager::set('user', [
    'name' => 'Test Admin',
    'email' => 'test@example.com',
    'avatar' => null
]);

ViewManager::set('notifications', [
    ['message' => 'Test notification', 'type' => 'info', 'time' => '2 min ago', 'read' => false]
]);

ViewManager::set('title', 'Sidebar Test');

// Test component loading
try {
    ComponentRegistry::load('ui-components');
    echo "✅ UIComponents loaded successfully\n";
} catch (Exception $e) {
    echo "❌ UIComponents loading failed: " . $e->getMessage() . "\n";
}

// Test sidebar HTML
echo "\n=== Testing Sidebar Elements ===\n";

// Check if dashboard layout can be loaded
try {
    ob_start();
    include __DIR__ . '/views/admin/dashboard_layout.php';
    $layout_html = ob_get_clean();
    
    // Check for key sidebar elements
    $sidebar_elements = [
        'sidebar' => 'id="sidebar"',
        'open button' => 'id="open-sidebar"',
        'close button' => 'id="close-sidebar"',
        'backdrop' => 'id="sidebar-backdrop"',
        'navigation items' => 'class="nav-item"'
    ];
    
    foreach ($sidebar_elements as $name => $selector) {
        if (strpos($layout_html, $selector) !== false) {
            echo "✅ {$name}: Found\n";
        } else {
            echo "❌ {$name}: Missing\n";
        }
    }
    
    // Check JavaScript functionality
    if (strpos($layout_html, 'addEventListener(\'click\'') !== false) {
        echo "✅ JavaScript: Event listeners found\n";
    } else {
        echo "❌ JavaScript: Event listeners missing\n";
    }
    
    // Check for debug console logs
    if (strpos($layout_html, 'console.log') !== false) {
        echo "✅ Debug: Console logging enabled\n";
    } else {
        echo "❌ Debug: Console logging missing\n";
    }
    
} catch (Exception $e) {
    echo "❌ Layout loading failed: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Component Registry ===\n";
$components = ComponentRegistry::getInfo();
echo "Registered components: " . count($components) . "\n";

// Check if ui-components is registered
if (isset($components['ui-components'])) {
    echo "✅ UIComponents registered\n";
    echo "   Path: " . $components['ui-components']['path'] . "\n";
} else {
    echo "❌ UIComponents not registered\n";
}

echo "\n=== Testing Data Provider ===\n";
try {
    $user = ViewManager::get('user');
    echo "✅ User data: " . $user['name'] . "\n";
} catch (Exception $e) {
    echo "❌ User data missing: " . $e->getMessage() . "\n";
}

echo "\n=== Sidebar Test Complete ===\n";
?>
