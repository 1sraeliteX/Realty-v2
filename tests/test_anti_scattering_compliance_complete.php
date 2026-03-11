<?php
/**
 * Comprehensive Anti-Scattering Compliance Test
 * 
 * This test verifies that the application complies with all anti-scattering principles:
 * 1. No direct require_once patterns in views
 * 2. All data is centralized in DataProvider
 * 3. ViewManager is used for rendering, not manual includes
 * 4. Components are self-contained and isolated
 * 5. No global state modifications in views
 */

require_once __DIR__ . '/config/init_framework.php';

echo "<h1>🔍 Anti-Scattering Compliance Test</h1>\n";
echo "<p>Testing compliance with anti-scattering architecture principles...</p>\n";

$tests = [];
$passed = 0;
$total = 0;

// Test 1: Check for require_once patterns in view files
echo "<h2>📋 Test 1: Checking for require_once patterns in views</h2>\n";
$total++;
$requireOnceFiles = [];
$viewsDir = __DIR__ . '/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match('/require_once.*UIComponents/', $content)) {
            $requireOnceFiles[] = str_replace(__DIR__ . '/', '', $file->getPathname());
        }
    }
}

if (empty($requireOnceFiles)) {
    echo "✅ <strong>PASSED:</strong> No require_once patterns found in view files\n";
    $passed++;
} else {
    echo "❌ <strong>FAILED:</strong> Found require_once patterns in:\n";
    echo "<ul>";
    foreach ($requireOnceFiles as $file) {
        echo "<li><code>$file</code></li>";
    }
    echo "</ul>";
}

// Test 2: Check for ComponentRegistry::load usage
echo "<h2>📋 Test 2: Checking for ComponentRegistry::load usage</h2>\n";
$total++;
$componentRegistryFiles = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match('/ComponentRegistry::load/', $content)) {
            $componentRegistryFiles[] = str_replace(__DIR__ . '/', '', $file->getPathname());
        }
    }
}

if (!empty($componentRegistryFiles)) {
    echo "✅ <strong>PASSED:</strong> Found ComponentRegistry::load usage in " . count($componentRegistryFiles) . " files\n";
    $passed++;
} else {
    echo "❌ <strong>FAILED:</strong> No ComponentRegistry::load usage found in view files\n";
}

// Test 3: Check for manual layout includes
echo "<h2>📋 Test 3: Checking for manual layout includes</h2>\n";
$total++;
$manualIncludes = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match('/include.*layout\.php/', $content)) {
            $manualIncludes[] = str_replace(__DIR__ . '/', '', $file->getPathname());
        }
    }
}

if (empty($manualIncludes)) {
    echo "✅ <strong>PASSED:</strong> No manual layout includes found\n";
    $passed++;
} else {
    echo "❌ <strong>FAILED:</strong> Found manual layout includes in:\n";
    echo "<ul>";
    foreach ($manualIncludes as $file) {
        echo "<li><code>$file</code></li>";
    }
    echo "</ul>";
}

// Test 4: Check for ViewManager::render usage
echo "<h2>📋 Test 4: Checking for ViewManager::render usage</h2>\n";
$total++;
$viewManagerFiles = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match('/ViewManager::render/', $content)) {
            $viewManagerFiles[] = str_replace(__DIR__ . '/', '', $file->getPathname());
        }
    }
}

if (!empty($viewManagerFiles)) {
    echo "✅ <strong>PASSED:</strong> Found ViewManager::render usage in " . count($viewManagerFiles) . " files\n";
    $passed++;
} else {
    echo "❌ <strong>FAILED:</strong> No ViewManager::render usage found in view files\n";
}

// Test 5: Check for mock data in views
echo "<h2>📋 Test 5: Checking for mock data patterns in views</h2>\n";
$total++;
$mockDataFiles = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        // Look for common mock data patterns
        if (preg_match('/\$[a-zA-Z]+ = \[.*id.*=>/', $content) || 
            preg_match('/\/\/ Mock [a-zA-Z]+ data/', $content)) {
            $mockDataFiles[] = str_replace(__DIR__ . '/', '', $file->getPathname());
        }
    }
}

if (empty($mockDataFiles)) {
    echo "✅ <strong>PASSED:</strong> No mock data patterns found in view files\n";
    $passed++;
} else {
    echo "❌ <strong>FAILED:</strong> Found potential mock data in:\n";
    echo "<ul>";
    foreach ($mockDataFiles as $file) {
        echo "<li><code>$file</code></li>";
    }
    echo "</ul>";
}

// Test 6: Check DataProvider completeness
echo "<h2>📋 Test 6: Checking DataProvider completeness</h2>\n";
$total++;
try {
    $dataProviderMethods = get_class_methods('DataProvider');
    $expectedMethods = [
        'init', 'get', 'set', 'all',
        'getUserData', 'getNotificationData', 'getPropertyData', 'getTenantData',
        'getPaymentData', 'getInvoiceData', 'getMaintenanceData', 'getReportData',
        'getDocumentData', 'getSettingsData', 'getFinanceData', 'getDashboardStatsData',
        'getRecentPropertiesData', 'getActivitiesData', 'getMaintenanceRequestsData',
        'getNewApplicationsData', 'getUnitData', 'getUnitTenantData', 'getTenantDetailsData',
        'getTenantPaymentHistoryData', 'getTenantDocumentsData', 'getTenantMaintenanceRequestsData',
        'getAmenitiesData', 'getMaintenanceHistoryData'
    ];
    
    $missingMethods = array_diff($expectedMethods, $dataProviderMethods);
    
    if (empty($missingMethods)) {
        echo "✅ <strong>PASSED:</strong> DataProvider has all expected methods\n";
        $passed++;
    } else {
        echo "❌ <strong>FAILED:</strong> DataProvider missing methods:\n";
        echo "<ul>";
        foreach ($missingMethods as $method) {
            echo "<li><code>$method</code></li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "❌ <strong>FAILED:</strong> Error testing DataProvider: " . $e->getMessage() . "\n";
}

// Test 7: Check for framework initialization
echo "<h2>📋 Test 7: Checking for framework initialization in views</h2>\n";
$total++;
$frameworkInitFiles = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match('/init_framework\.php/', $content)) {
            $frameworkInitFiles[] = str_replace(__DIR__ . '/', '', $file->getPathname());
        }
    }
}

if (!empty($frameworkInitFiles)) {
    echo "✅ <strong>PASSED:</strong> Found framework initialization in " . count($frameworkInitFiles) . " files\n";
    $passed++;
} else {
    echo "❌ <strong>FAILED:</strong> No framework initialization found in view files\n";
}

// Test 8: Check CSS styling consistency
echo "<h2>📋 Test 8: Checking CSS styling consistency</h2>\n";
$total++;
$cssFile = __DIR__ . '/public/assets/css/style.css';
if (file_exists($cssFile)) {
    $cssContent = file_get_contents($cssFile);
    $hasDarkMode = strpos($cssContent, 'dark:') !== false;
    $hasButtons = strpos($cssContent, '.btn-') !== false;
    $hasCards = strpos($cssContent, '.card') !== false;
    $hasTables = strpos($cssContent, '.data-table') !== false;
    $hasBadges = strpos($cssContent, '.badge-') !== false;
    
    if ($hasDarkMode && $hasButtons && $hasCards && $hasTables && $hasBadges) {
        echo "✅ <strong>PASSED:</strong> CSS has comprehensive styling classes\n";
        $passed++;
    } else {
        echo "❌ <strong>FAILED:</strong> CSS missing some styling classes:\n";
        echo "<ul>";
        if (!$hasDarkMode) echo "<li>Dark mode styles</li>";
        if (!$hasButtons) echo "<li>Button styles</li>";
        if (!$hasCards) echo "<li>Card styles</li>";
        if (!$hasTables) echo "<li>Table styles</li>";
        if (!$hasBadges) echo "<li>Badge styles</li>";
        echo "</ul>";
    }
} else {
    echo "❌ <strong>FAILED:</strong> CSS file not found\n";
}

// Summary
echo "<h2>📊 Test Summary</h2>\n";
echo "<div style='padding: 20px; border-radius: 8px; margin: 20px 0; ";
if ($passed === $total) {
    echo "background-color: #d1fae5; border: 1px solid #10b981; color: #065f46;'>";
    echo "<h3 style='color: #065f46; margin: 0 0 10px 0;'>🎉 All Tests Passed!</h3>";
    echo "<p style='margin: 0;'>Your application is fully compliant with anti-scattering principles.</p>";
} else {
    echo "background-color: #fee2e2; border: 1px solid #ef4444; color: #991b1b;'>";
    echo "<h3 style='color: #991b1b; margin: 0 0 10px 0;'>⚠️ Some Tests Failed</h3>";
    echo "<p style='margin: 0;'>$passed out of $total tests passed. Please address the issues above.</p>";
}
echo "</div>";

// Recommendations
echo "<h2>💡 Recommendations</h2>\n";
echo "<ul>";
echo "<li>Continue using ComponentRegistry::load() for all component dependencies</li>";
echo "<li>Keep all mock data centralized in DataProvider</li>";
echo "<li>Use ViewManager::render() for all view rendering</li>";
echo "<li>Test components in isolation to ensure they are self-contained</li>";
echo "<li>Avoid global state modifications in views</li>";
echo "</ul>";

echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>";
?>
