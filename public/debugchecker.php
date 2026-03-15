<?php
// debugchecker.php — temporary diagnostic tool, DELETE after use

echo "<h2>AutoFillComponent Debug Check</h2>";

// 1. Check if the class file exists
$paths = [
    __DIR__ . '/app/components/AutoFillComponent.php',
    __DIR__ . '/../app/components/AutoFillComponent.php',
    dirname(__DIR__) . '/app/components/AutoFillComponent.php',
    dirname(__DIR__) . '/components/AutoFillComponent.php',
];

echo "<h3>1. File Search</h3>";
foreach ($paths as $path) {
    $exists = file_exists($path) ? '✅ FOUND' : '❌ NOT FOUND';
    echo "<p>{$exists} → <code>{$path}</code></p>";
}

// 2. Check if class is already loaded
echo "<h3>2. Class Loaded?</h3>";
echo class_exists('AutoFillComponent') 
    ? "<p>✅ AutoFillComponent is already loaded</p>" 
    : "<p>❌ AutoFillComponent is NOT loaded in current scope</p>";

echo class_exists('Components\AutoFillComponent') 
    ? "<p>✅ Components\AutoFillComponent is already loaded</p>" 
    : "<p>❌ Components\AutoFillComponent is NOT loaded in current scope</p>";

// 2.5. Try to load the class and test
echo "<h3>2.5. Test Class Loading</h3>";
$componentPath = dirname(__DIR__) . '/components/AutoFillComponent.php';
if (file_exists($componentPath)) {
    require_once $componentPath;
    echo class_exists('Components\AutoFillComponent') 
        ? "<p>✅ Components\AutoFillComponent successfully loaded</p>" 
        : "<p>❌ Failed to load Components\AutoFillComponent</p>";
} else {
    echo "<p>❌ Could not find component file to test loading</p>";
}

// 3. Show line 61 context of add.php
echo "<h3>3. add.php Line 61 Context</h3>";
$addView = __DIR__ . '/views/admin/properties/add.php';
if (!file_exists($addView)) {
    $addView = dirname(__DIR__) . '/views/admin/properties/add.php';
}
if (file_exists($addView)) {
    $lines = file($addView);
    $start = max(0, 58); // lines 59–63
    $end   = min(count($lines), 63);
    echo "<pre style='background:#f4f4f4;padding:10px'>";
    for ($i = $start; $i < $end; $i++) {
        $lineNum = $i + 1;
        $marker  = ($lineNum === 61) ? " <-- LINE 61" : "";
        echo htmlspecialchars("{$lineNum}: {$lines[$i]}") . $marker . "\n";
    }
    echo "</pre>";
} else {
    echo "<p>❌ Could not locate add.php</p>";
}

echo "<hr><p style='color:red'><strong>DELETE this file before going to production.</strong></p>";
?>
