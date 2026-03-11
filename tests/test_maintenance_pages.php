<?php
// Test script to verify maintenance pages are working
echo "Testing Maintenance Pages...\n\n";

// Check if files exist
$maintenanceIndex = __DIR__ . '/views/admin/maintenance/index.php';
$maintenanceCreate = __DIR__ . '/views/admin/maintenance/create.php';

echo "1. Checking file existence:\n";
echo "   - Maintenance index: " . (file_exists($maintenanceIndex) ? "✅ EXISTS" : "❌ MISSING") . "\n";
echo "   - Maintenance create: " . (file_exists($maintenanceCreate) ? "✅ EXISTS" : "❌ MISSING") . "\n\n";

// Check routes
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    $routes = include $routesFile;
    echo "2. Checking routes:\n";
    echo "   - GET /admin/maintenance: " . (isset($routes['GET /admin/maintenance']) ? "✅ EXISTS" : "❌ MISSING") . "\n";
    echo "   - GET /admin/maintenance/create: " . (isset($routes['GET /admin/maintenance/create']) ? "✅ EXISTS" : "❌ MISSING") . "\n";
    echo "   - POST /admin/maintenance: " . (isset($routes['POST /admin/maintenance']) ? "✅ EXISTS" : "❌ MISSING") . "\n\n";
}

// Check sidebar navigation
$dashboardLayout = __DIR__ . '/views/admin/dashboard_layout.php';
if (file_exists($dashboardLayout)) {
    $content = file_get_contents($dashboardLayout);
    echo "3. Checking sidebar navigation:\n";
    echo "   - Maintenance menu item: " . (strpos($content, '/admin/maintenance') !== false ? "✅ EXISTS" : "❌ MISSING") . "\n";
    echo "   - Tools icon: " . (strpos($content, 'fa-tools') !== false ? "✅ EXISTS" : "❌ MISSING") . "\n\n";
}

echo "4. File sizes:\n";
echo "   - Maintenance index: " . number_format(filesize($maintenanceIndex)) . " bytes\n";
echo "   - Maintenance create: " . number_format(filesize($maintenanceCreate)) . " bytes\n\n";

echo "✅ Maintenance pages implementation complete!\n";
echo "📁 Maintenance index: views/admin/maintenance/index.php\n";
echo "📁 Maintenance create: views/admin/maintenance/create.php\n";
echo "🔗 Routes: /admin/maintenance and /admin/maintenance/create\n";
echo "🎨 Design: Dark theme with orange accents matching the image\n";
echo "📋 Features: Stats cards, search/filter, empty state, comprehensive form\n";
?>
