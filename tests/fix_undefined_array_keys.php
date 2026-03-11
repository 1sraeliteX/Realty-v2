<?php

/**
 * Mass Fix Script for "Undefined array key" errors
 * This script will automatically fix unsafe array accesses across the entire codebase
 */

require_once __DIR__ . '/config/ArrayHelper.php';

// Files to fix (priority order)
$filesToFix = [
    'views/units/index.php',
    'views/admin/properties/list.php',
    'views/admin/properties/details.php',
    'views/admin/tenants/list.php',
    'views/admin/tenants/details.php',
    'views/admin/dashboard_enhanced.php',
    'views/admin/login.php',
    'views/landing.php'
];

// Patterns to replace
$replacements = [
    // Basic array access -> arr_get()
    '/\$([a-zA-Z_]\w*)\[\'([^\']+)\'\](?!\s*\?\?)/' => 'arr_get($$1, \'$2\')',
    
    // htmlspecialchars -> arr_escape()
    '/htmlspecialchars\(\$([a-zA-Z_]\w*)\[\'([^\']+)\'\](?!\s*\?\?)/' => 'arr_escape($$1, \'$2\')',
    
    // number_format -> arr_format()
    '/number_format\(\$([a-zA-Z_]\w*)\[\'([^\']+)\'\](?!\s*\?\?)/' => 'arr_format($$1, \'$2\')',
    
    // $_POST access -> ArrayHelper::post()
    '/\$_POST\[\'([^\']+)\'\](?!\s*\?\?)/' => 'ArrayHelper::post(\'$1\')',
    
    // $_GET access -> ArrayHelper::getParam()
    '/\$_GET\[\'([^\']+)\'\](?!\s*\?\?)/' => 'ArrayHelper::getParam(\'$1\')',
    
    // $_SESSION access -> ArrayHelper::session()
    '/\$_SESSION\[\'([^\']+)\'\](?!\s*\?\?)/' => 'ArrayHelper::session(\'$1\')',
    
    // $_REQUEST access -> ArrayHelper::request()
    '/\$_REQUEST\[\'([^\']+)\'\](?!\s*\?\?)/' => 'ArrayHelper::request(\'$1\')'
];

echo "Starting mass fix for undefined array key errors...\n\n";

foreach ($filesToFix as $file) {
    $filePath = __DIR__ . '/' . $file;
    
    if (!file_exists($filePath)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    echo "🔧 Fixing: $file\n";
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $changesCount = 0;
    
    // Apply replacements
    foreach ($replacements as $pattern => $replacement) {
        $matches = [];
        preg_match_all($pattern, $content, $matches);
        
        if (!empty($matches[0])) {
            $content = preg_replace($pattern, $replacement, $content);
            $changesCount += count($matches[0]);
            echo "   ✓ Replaced " . count($matches[0]) . " instances\n";
        }
    }
    
    // Add ArrayHelper include if not present
    if (strpos($content, 'ArrayHelper') !== false && strpos($content, 'require_once') === false) {
        $content = "<?php\nrequire_once __DIR__ . '/../../config/ArrayHelper.php';\n\n" . substr($content, 6);
        echo "   ✓ Added ArrayHelper include\n";
    }
    
    // Save changes if any were made
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "   ✅ Saved $changesCount fixes\n";
    } else {
        echo "   ℹ️  No fixes needed\n";
    }
    
    echo "\n";
}

echo "🎉 Mass fix completed!\n";
echo "\nNext steps:\n";
echo "1. Test the application to ensure fixes work correctly\n";
echo "2. Check error logs for any remaining issues\n";
echo "3. Update remaining files manually if needed\n";

// Create verification script
$verificationScript = '<?php
/**
 * Verification script to check for remaining undefined array key patterns
 */

echo "Scanning for remaining unsafe array accesses...\n\n";

$directory = __DIR__;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

$unsafePatterns = [
    '/\$[a-zA-Z_]\w*\[\'[^\']+\'\](?!\s*\?\?)/',
    '/\$_POST\[\'[^\']+\'\](?!\s*\?\?)/',
    '/\$_GET\[\'[^\']+\'\](?!\s*\?\?)/',
    '/\$_SESSION\[\'[^\']+\'\](?!\s*\?\?)/'
];

$totalIssues = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === \'php\') {
        $content = file_get_contents($file->getPathname());
        
        foreach ($unsafePatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                echo "⚠️  Unsafe array access found: " . str_replace(__DIR__, \'\', $file->getPathname()) . "\n";
                $totalIssues++;
                break;
            }
        }
    }
}

if ($totalIssues === 0) {
    echo "✅ No unsafe array accesses found!\n";
} else {
    echo "\n❌ Found $totalIssues files with unsafe array accesses\n";
}
?>';

file_put_contents(__DIR__ . '/verify_array_fixes.php', $verificationScript);
echo "\n📝 Created verification script: verify_array_fixes.php\n";
?>
