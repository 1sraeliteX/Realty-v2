<?php
// Test dashboard styling and anti-scattering compliance
echo "=== Dashboard Styling & Anti-Scattering Test ===\n";

// Test 1: CSS files exist
$cssFiles = [
    'public/assets/css/tailwind.css',
    'public/assets/css/fontawesome.css', 
    'public/assets/css/style.css'
];

foreach ($cssFiles as $cssFile) {
    if (file_exists(__DIR__ . '/' . $cssFile)) {
        echo "✅ $cssFile exists\n";
    } else {
        echo "❌ $cssFile missing\n";
    }
}

// Test 2: Check layout includes CSS
$layoutContent = file_get_contents(__DIR__ . '/views/simple_layout.php');
if (strpos($layoutContent, 'tailwind.css') !== false) {
    echo "✅ Tailwind CSS included in layout\n";
} else {
    echo "❌ Tailwind CSS not included in layout\n";
}

// Test 3: Check anti-scattering compliance
$controllerContent = file_get_contents(__DIR__ . '/app/controllers/AdminDashboardController.php');
$viewContent = file_get_contents(__DIR__ . '/views/admin/dashboard_enhanced.php');

$compliance = [];

// Check ComponentRegistry usage
if (strpos($controllerContent, 'ComponentRegistry::load') !== false) {
    $compliance[] = "✅ Uses ComponentRegistry::load()";
} else {
    $compliance[] = "❌ Missing ComponentRegistry::load()";
}

// Check ViewManager usage
if (strpos($controllerContent, 'ViewManager::render') !== false) {
    $compliance[] = "✅ Uses ViewManager::render()";
} else {
    $compliance[] = "❌ Missing ViewManager::render()";
}

// Check for no mock data in view
if (strpos($viewContent, '=> [') === false) {
    $compliance[] = "✅ No mock data in view";
} else {
    $compliance[] = "❌ Mock data found in view";
}

// Check for ComponentRegistry usage (anti-scattering compliant)
if (strpos($viewContent, 'ComponentRegistry::load') !== false) {
    $compliance[] = "✅ Uses ComponentRegistry::load() (anti-scattering compliant)";
} else {
    $compliance[] = "❌ Missing ComponentRegistry::load()";
}

foreach ($compliance as $check) {
    echo $check . "\n";
}

echo "\n=== Test Complete ===\n";
echo "Dashboard should now display with proper styling at:\n";
echo "http://localhost:8000/admin/dashboard\n";
?>
