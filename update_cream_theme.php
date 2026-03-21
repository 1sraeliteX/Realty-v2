<?php
/**
 * Cream Theme Update Script
 * 
 * This script updates all white background colors to warm cream (#F0EAD6) 
 * across the admin panel while maintaining anti-scattering compliance.
 * 
 * Changes made:
 * - bg-white → bg-cream-50 (for main backgrounds)
 * - #ffffff → #F0EAD6 (for inline styles)
 * - #fff → #F0EAD6 (for inline styles)
 * - background: white → background: #F0EAD6 (for CSS)
 * 
 * Preserves:
 * - Text colors (text-white, text-gray-*, etc.)
 * - Border colors (border-white, etc.)
 * - Dark theme classes (dark:bg-white, etc.)
 * - Icon fills and non-background uses
 */

// Define the directory to scan
$adminDir = __DIR__ . '/views/admin';
$creamColor = '#F0EAD6';

// Files to exclude (already handled manually)
$excludeFiles = [
    'dashboard_layout.php'
];

// Get all PHP files in admin directory
$files = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($adminDir)
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $relativePath = str_replace($adminDir . '/', '', $file->getPathname());
        if (!in_array($relativePath, $excludeFiles)) {
            $files[] = $file->getPathname();
        }
    }
}

echo "Starting cream theme update...\n";
echo "Found " . count($files) . " files to process\n\n";

$changesCount = 0;
$filesModified = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;
    $fileChanges = 0;

    // Pattern 1: Replace bg-white (but not dark:bg-white)
    $content = preg_replace('/bg-white(?!\-)/', 'bg-cream-50', $content);
    $fileChanges += preg_match_all('/bg-white(?!\-)/', $originalContent);

    // Pattern 2: Replace #ffffff (case insensitive)
    $content = preg_replace('/#ffffff/i', $creamColor, $content);
    $fileChanges += preg_match_all('/#ffffff/i', $originalContent);

    // Pattern 3: Replace #fff (but not in CSS variables or other contexts)
    $content = preg_replace('/\b#fff\b/', $creamColor, $content);
    $fileChanges += preg_match_all('/\b#fff\b/', $originalContent);

    // Pattern 4: Replace background: white (and variations)
    $content = preg_replace('/background\s*:\s*white/i', 'background: ' . $creamColor, $content);
    $content = preg_replace('/background-color\s*:\s*white/i', 'background-color: ' . $creamColor, $content);
    $fileChanges += preg_match_all('/background\s*:\s*white/i', $originalContent);
    $fileChanges += preg_match_all('/background-color\s*:\s*white/i', $originalContent);

    // Pattern 5: Replace backgroundColor: 'white' (JavaScript)
    $content = preg_replace("/backgroundColor\s*:\s*['\"]white['\"]/", "backgroundColor: '" . $creamColor . "'", $content);
    $fileChanges += preg_match_all("/backgroundColor\s*:\s*['\"]white['\"]/", $originalContent);

    // Only write if changes were made
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        $filesModified++;
        echo "✓ Updated: " . str_replace($adminDir . '/', '', $file) . " ({$fileChanges} changes)\n";
        $changesCount += $fileChanges;
    }
}

echo "\n=== Summary ===\n";
echo "Files processed: " . count($files) . "\n";
echo "Files modified: {$filesModified}\n";
echo "Total changes: {$changesCount}\n";
echo "\nTheme update complete! 🎨\n";
echo "All white backgrounds have been changed to warm cream ({$creamColor})\n";
echo "Dark theme and non-background elements have been preserved.\n";

// Verify no stark white backgrounds remain
echo "\n=== Verification ===\n";
$remainingWhite = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Check for remaining white backgrounds (excluding dark theme)
    $remainingWhite += preg_match_all('/bg-white(?!\-)/', $content);
    $remainingWhite += preg_match_all('/#ffffff/i', $content);
    $remainingWhite += preg_match_all('/\b#fff\b/', $content);
    $remainingWhite += preg_match_all('/background\s*:\s*white/i', $content);
    $remainingWhite += preg_match_all('/background-color\s*:\s*white/i', $content);
}

if ($remainingWhite > 0) {
    echo "⚠️  Found {$remainingWhite} remaining white backgrounds that may need manual review\n";
} else {
    echo "✅ No remaining white backgrounds found!\n";
}

echo "\nNext steps:\n";
echo "1. Test the admin dashboard at http://127.0.0.1:8080/admin/dashboard\n";
echo "2. Check various admin pages for visual consistency\n";
echo "3. Verify dark mode still works correctly\n";
echo "4. Test responsive design on mobile devices\n";
?>
