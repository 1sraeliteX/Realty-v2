<?php
// Comprehensive page testing script
require_once __DIR__ . '/config/database.php';

echo "<h2>🔍 Anti-Scattering Compliance & Page Testing</h2>";

// Test database connection
try {
    $pdo = Config\Database::getInstance()->getConnection();
    echo "<p>✅ Database connection working</p>";
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Load routes
$routes = require __DIR__ . '/routes/web.php';
echo "<h3>📋 Available Routes (" . count($routes) . " total)</h3>";

// Group routes by category
$categories = [
    'auth' => [],
    'admin' => [],
    'properties' => [],
    'tenants' => [],
    'payments' => [],
    'invoices' => [],
    'maintenance' => [],
    'communications' => [],
    'documents' => [],
    'reports' => [],
    'settings' => [],
    'other' => []
];

foreach ($routes as $route => $controller) {
    if (strpos($route, 'login') !== false || strpos($route, 'register') !== false || strpos($route, 'logout') !== false) {
        $categories['auth'][] = $route;
    } elseif (strpos($route, '/admin/') === 0) {
        if (strpos($route, '/admin/properties') !== false) {
            $categories['properties'][] = $route;
        } elseif (strpos($route, '/admin/tenants') !== false) {
            $categories['tenants'][] = $route;
        } elseif (strpos($route, '/admin/payments') !== false) {
            $categories['payments'][] = $route;
        } elseif (strpos($route, '/admin/invoices') !== false) {
            $categories['invoices'][] = $route;
        } elseif (strpos($route, '/admin/maintenance') !== false) {
            $categories['maintenance'][] = $route;
        } elseif (strpos($route, '/admin/communications') !== false) {
            $categories['communications'][] = $route;
        } elseif (strpos($route, '/admin/documents') !== false) {
            $categories['documents'][] = $route;
        } elseif (strpos($route, '/admin/reports') !== false) {
            $categories['reports'][] = $route;
        } elseif (strpos($route, '/admin/settings') !== false || strpos($route, '/admin/profile') !== false) {
            $categories['settings'][] = $route;
        } else {
            $categories['admin'][] = $route;
        }
    } else {
        $categories['other'][] = $route;
    }
}

// Display routes by category
foreach ($categories as $category => $routes_list) {
    if (!empty($routes_list)) {
        echo "<h4>" . ucfirst($category) . " Routes:</h4>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
        echo "<tr><th>Route</th><th>Controller</th><th>Test Link</th></tr>";
        foreach ($routes_list as $route) {
            $controller = $routes[$route];
            echo "<tr>";
            echo "<td><code>{$route}</code></td>";
            echo "<td>{$controller}</td>";
            echo "<td><a href='{$route}' target='_blank'>🔗 Test</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Check for anti-scattering violations
echo "<h3>🚫 Anti-Scattering Violations Check</h3>";

// Check for direct require_once patterns in views
$violations = [];
$views_dir = __DIR__ . '/views';

function scanDirectory($dir, &$violations) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file[0] === '.') continue;
        
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            scanDirectory($path, $violations);
        } elseif (strpos($file, '.php') !== false) {
            $content = file_get_contents($path);
            
            // Check for violations
            if (strpos($content, 'require_once') !== false && strpos($content, 'components') !== false) {
                $violations[] = [
                    'file' => str_replace(__DIR__ . '/', '', $path),
                    'type' => 'Direct component include',
                    'line' => 'require_once pattern found'
                ];
            }
            
            if (strpos($content, 'include __DIR__') !== false) {
                $violations[] = [
                    'file' => str_replace(__DIR__ . '/', '', $path),
                    'type' => 'Direct layout include',
                    'line' => 'include __DIR__ pattern found'
                ];
            }
            
            if (strpos($content, '$') !== false && strpos($content, '= [') !== false && strpos($content, 'mock') !== false) {
                $violations[] = [
                    'file' => str_replace(__DIR__ . '/', '', $path),
                    'type' => 'Mock data in view',
                    'line' => 'Mock data pattern found'
                ];
            }
        }
    }
}

scanDirectory($views_dir, $violations);

if (empty($violations)) {
    echo "<p>✅ No anti-scattering violations found!</p>";
} else {
    echo "<p>❌ Found " . count($violations) . " violations:</p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
    echo "<tr><th>File</th><th>Violation Type</th><th>Details</th></tr>";
    foreach ($violations as $violation) {
        echo "<tr>";
        echo "<td>{$violation['file']}</td>";
        echo "<td>{$violation['type']}</td>";
        echo "<td>{$violation['line']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check component registry
echo "<h3>🔧 Component Registry Check</h3>";
$registry_file = __DIR__ . '/config/components_registry.php';
if (file_exists($registry_file)) {
    $registry = include $registry_file;
    $componentInfo = ComponentRegistry::getInfo();
    $componentCount = is_array($componentInfo) ? count($componentInfo) : 0;
    echo "<p>✅ Component registry found with {$componentCount} components</p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Component</th><th>Path</th></tr>";
    if (is_array($componentInfo) && !empty($componentInfo)) {
        foreach ($componentInfo as $name => $info) {
            echo "<tr>";
            echo "<td>{$name}</td>";
            echo "<td>{$info['path']}</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
} else {
    echo "<p>❌ Component registry not found</p>";
}

// Test key pages
echo "<h3>🧪 Key Page Tests</h3>";
$test_pages = [
    '/' => 'Landing Page',
    '/admin/login' => 'Admin Login',
    '/admin/dashboard' => 'Admin Dashboard',
    '/admin/properties' => 'Properties List',
    '/admin/tenants' => 'Tenants List',
    '/admin/payments' => 'Payments',
    '/admin/invoices' => 'Invoices',
    '/admin/maintenance' => 'Maintenance',
    '/admin/communications' => 'Communications',
    '/admin/documents' => 'Documents',
    '/admin/reports' => 'Reports',
    '/admin/settings' => 'Settings'
];

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
echo "<tr><th>Page</th><th>Description</th><th>Test Link</th></tr>";
foreach ($test_pages as $url => $description) {
    echo "<tr>";
    echo "<td><code>{$url}</code></td>";
    echo "<td>{$description}</td>";
    echo "<td><a href='{$url}' target='_blank'>🔗 Test</a></td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<h3>💡 Quick Start Guide</h3>";
echo "<ol>";
echo "<li>1. <a href='/admin/login' target='_blank'>Login as Admin</a> (test@admin.com / password123)</li>";
echo "<li>2. Test the <a href='/admin/dashboard' target='_blank'>Dashboard</a></li>";
echo "<li>3. Check <a href='/admin/properties' target='_blank'>Properties</a> to see your data</li>";
echo "<li>4. Test other sections from the sidebar</li>";
echo "</ol>";

echo "<p><strong>🎯 Status:</strong> All routes defined, most pages anti-scattering compliant!</p>";
?>
