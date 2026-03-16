<?php
// Load framework bootstrap — MUST be first
if (!class_exists('ComponentRegistry')) {
    require_once __DIR__ . '/../config/bootstrap.php';
}

// Load database config if not already loaded
if (!class_exists('Config\Database')) {
    $dbConfigPath = __DIR__ . '/../config/database.php';
    if (file_exists($dbConfigPath)) {
        require_once $dbConfigPath;
    }
}

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details Page Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Property Details Page Debug</h1>';

try {

// Section 1: Image path check for property ID 40
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">1. Image Path Check (Property ID 40)</h2>';

// Safe DB instantiation — works regardless of namespace
$db  = null;
$pdo = null;

$dbCandidates = [
    'Config\Database',
    'Database',
    '\Config\Database',
];

foreach ($dbCandidates as $candidate) {
    if (class_exists($candidate)) {
        try {
            $db  = $candidate::getInstance();
            $pdo = $db->getConnection();
            break;
        } catch (\Throwable $e) {
            $db  = null;
            $pdo = null;
        }
    }
}

if (!$db || !$pdo) {
    // Last resort: try manual require
    $dbFile = __DIR__ . '/../config/database.php';
    if (file_exists($dbFile)) {
        require_once $dbFile;
        if (class_exists('Config\Database')) {
            try {
                $db  = \Config\Database::getInstance();
                $pdo = $db->getConnection();
            } catch (\Throwable $e) {
                $db  = null;
                $pdo = null;
            }
        }
    }
}

if (!$pdo) {
    echo '<p style="color:red">❌ Database not available — skipping this section</p>';
} else {
    try {
        $stmt = $pdo->prepare(
            "SELECT id, name, images
             FROM properties
             WHERE id = ?
             LIMIT 1"
        );
        $stmt->execute([40]);
        $prop = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$prop) {
            echo '<p>⚠️ Property ID 40 not found in database.
                    Try another property ID.</p>';

            // Show available property IDs instead
            $all = $pdo->query(
                "SELECT id, name FROM properties
                 WHERE deleted_at IS NULL
                 ORDER BY id DESC LIMIT 5"
            )->fetchAll(\PDO::FETCH_ASSOC);

            if ($all) {
                echo '<p>Available properties:</p><ul>';
                foreach ($all as $p) {
                    echo '<li>ID: ' . $p['id']
                         . ' — ' . htmlspecialchars($p['name']) . '</li>';
                }
                echo '</ul>';
            }
        } else {
            echo '<p>Property: <strong>'
                 . htmlspecialchars($prop['name']) . '</strong></p>';
            echo '<p>Raw images field: <code>'
                 . htmlspecialchars($prop['images'] ?? 'NULL') . '</code></p>';

            $images = json_decode($prop['images'] ?? '[]', true);

            if (empty($images)) {
                echo '<p>⚠️ No images stored for this property</p>';
            } else {
                foreach ($images as $filename) {
                    $pubPath     = __DIR__ . '/uploads/properties/'
                                   . $filename;
                    $storagePath = __DIR__ . '/../storage/uploads/properties/'
                                   . $filename;

                    $inPublic  = file_exists($pubPath);
                    $inStorage = file_exists($storagePath);

                    echo '<p>Filename: <code>'
                         . htmlspecialchars($filename) . '</code></p>';
                    echo '<p>'
                         . ($inPublic
                             ? '✅ FILE EXISTS in public/uploads/properties/'
                             : '❌ FILE MISSING from public/uploads/properties/')
                         . '</p>';
                    echo '<p>'
                         . ($inStorage
                             ? '✅ FILE EXISTS in storage/uploads/properties/'
                             : '❌ FILE MISSING from storage/uploads/properties/')
                         . '</p>';
                    echo '<p>Web path: <code>/uploads/properties/'
                         . htmlspecialchars($filename) . '</code></p>';
                }
            }
        }
    } catch (\Throwable $e) {
        echo '<p style="color:red">❌ Query failed: '
             . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

echo '</div>';

// Section 2: Currency symbol audit
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">2. Currency Symbol Audit</h2>';

$filesToCheck = [
    'app/controllers/PropertyController.php' => 'renderPropertyDetails method',
    'views/admin/properties/list.php' => 'properties list view',
    'views/admin/units/list.php' => 'units list view'
];

foreach ($filesToCheck as $file => $description) {
    echo '<div class="mb-4">
            <h3 class="font-medium">' . htmlspecialchars($description) . ' (' . htmlspecialchars($file) . ')</h3>';
    
    $filePath = __DIR__ . '/../' . $file;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        // Count dollar signs in currency context (not PHP variables)
        $dollarCount = preg_match_all('/\$\s*<?php.*number_format/', $content);
        $nairaCount = substr_count($content, '₦');
        
        echo '<p><strong>$ currency symbols:</strong> ' . $dollarCount . ' (should be 0)</p>';
        echo '<p><strong>₦ currency symbols:</strong> ' . $nairaCount . '</p>';
        
        if ($dollarCount === 0) {
            echo '<p class="text-green-600">✅ No dollar currency symbols found</p>';
        } else {
            echo '<p class="text-red-600">❌ Found ' . $dollarCount . ' dollar currency symbols</p>';
        }
    } else {
        echo '<p class="text-red-600">File not found</p>';
    }
    echo '</div>';
}

echo '</div>';

// Section 3: URL fix check
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">3. URL Fix Check</h2>';

// Read the full controller file
$controllerFile = __DIR__ . '/../app/controllers/PropertyController.php';
$controllerSrc = file_exists($controllerFile)
    ? file_get_contents($controllerFile)
    : '';

if (!$controllerSrc) {
    echo '<p style="color:red">❌ Could not read PropertyController.php</p>';
} else {
    // Check 3 — URL fixes
    echo '<h4><strong>3. URL Fix Check</strong></h4>';

    $hasAdminEditUrl = strpos($controllerSrc, '/admin/properties/') !== false;
    $hasAdminUnitUrl = strpos($controllerSrc, '/admin/units/create') !== false;
    $hasOldEditUrl = strpos($controllerSrc, "'/properties/") !== false
        || strpos($controllerSrc, '"/properties/') !== false;
    $hasOldUnitUrl = strpos($controllerSrc, "'/units/create") !== false
        || strpos($controllerSrc, '"/units/create') !== false;

    echo '<p>'
        . ($hasAdminEditUrl ? '✅' : '❌')
        . ' /admin/properties/ in edit link — '
        . ($hasAdminEditUrl ? 'CORRECT' : 'MISSING') . '</p>';

    echo '<p>'
        . ($hasAdminUnitUrl ? '✅' : '❌')
        . ' /admin/units/create in add unit link — '
        . ($hasAdminUnitUrl ? 'CORRECT' : 'MISSING') . '</p>';

    echo '<p>'
        . (!$hasOldEditUrl ? '✅' : '⚠️')
        . ' Old /properties/ URL — '
        . (!$hasOldEditUrl ? 'REMOVED' : 'STILL PRESENT') . '</p>';

    echo '<p>'
        . (!$hasOldUnitUrl ? '✅' : '⚠️')
        . ' Old /units/create URL — '
        . (!$hasOldUnitUrl ? 'REMOVED' : 'STILL PRESENT') . '</p>';

    // Check 4 — Overflow protection
    echo '<h4><strong>4. Overflow Protection Check</strong></h4>';

    $hasTruncate = strpos($controllerSrc, 'truncate') !== false;
    $hasMinW0 = strpos($controllerSrc, 'min-w-0') !== false;
    $hasStoragePath = strpos($controllerSrc, '/storage/uploads/properties/') !== false;
    $hasCorrectPath = strpos($controllerSrc, '/uploads/properties/') !== false;

    echo '<p>'
        . ($hasTruncate ? '✅' : '❌')
        . ' truncate class on rent price — '
        . ($hasTruncate ? 'PRESENT' : 'MISSING') . '</p>';

    echo '<p>'
        . ($hasMinW0 ? '✅' : '❌')
        . ' min-w-0 on stat card — '
        . ($hasMinW0 ? 'PRESENT' : 'MISSING') . '</p>';

    echo '<p>'
        . (!$hasStoragePath ? '✅' : '❌')
        . ' /storage/uploads/properties/ path — '
        . (!$hasStoragePath ? 'REMOVED ✅' : 'STILL PRESENT ❌ needs fix') . '</p>';

    echo '<p>'
        . ($hasCorrectPath ? '✅' : '❌')
        . ' /uploads/properties/ correct path — '
        . ($hasCorrectPath ? 'PRESENT' : 'MISSING') . '</p>';
}

echo '</div>';

// Section 5: Property Card Buttons Debug
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">5. Property Card Buttons Debug</h2>';

$propertiesListFile = __DIR__ . '/../views/admin/properties/list.php';
if (file_exists($propertiesListFile)) {
    $content = file_get_contents($propertiesListFile);
    
    $hasGridButtons = strpos($content, 'grid grid-cols-2 gap-2') !== false;
    $hasEditButton = strpos($content, 'fas fa-edit') !== false && strpos($content, 'Edit') !== false;
    $hasDeleteButton = strpos($content, 'fas fa-trash') !== false && strpos($content, 'Delete') !== false;
    $hasOldIconButtons = strpos($content, 'text-gray-400 hover:text-blue-600') !== false;
    
    echo '<p><strong>Grid Buttons Layout:</strong> ' . ($hasGridButtons ? '✅ GRID BUTTONS' : '❌ MISSING') . '</p>';
    echo '<p><strong>Edit Button with Text:</strong> ' . ($hasEditButton ? '✅' : '❌') . '</p>';
    echo '<p><strong>Delete Button with Text:</strong> ' . ($hasDeleteButton ? '✅' : '❌') . '</p>';
    echo '<p><strong>Old Icon-Only Buttons:</strong> ' . ($hasOldIconButtons ? '⚠️ OLD BUTTONS STILL THERE' : '✅ REMOVED') . '</p>';
} else {
    echo '<p class="text-red-600">views/admin/properties/list.php not found</p>';
}

echo '</div>';

// Section 6: Broken Require/Include Path Audit
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">6. Broken Path Audit — require/include errors</h2>';

$projectRoot = __DIR__ . '/..';
$scanDirs = [
    $projectRoot . '/app/models',
    $projectRoot . '/app/controllers',
    $projectRoot . '/app/components',
    $projectRoot . '/config',
];

$brokenPaths  = [];
$checkedFiles = 0;
$totalRequires = 0;

// Recursive file scanner
function scanPhpFiles(string $dir): array {
    $files = [];
    if (!is_dir($dir)) return $files;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

foreach ($scanDirs as $dir) {
    $files = scanPhpFiles($dir);
    foreach ($files as $filepath) {
        $checkedFiles++;
        $content = file_get_contents($filepath);
        $lines   = explode("\n", $content);

        foreach ($lines as $lineNum => $line) {
            // Match require/include with __DIR__ paths
            if (preg_match(
                '/(?:require_once|require|include_once|include)\s*[(\s]'
                . '[\'"]?(__DIR__|dirname\(__FILE__\))'
                . '\s*\.\s*[\'"]([^\'"]+)[\'"]/',
                $line, $matches
            )) {
                $totalRequires++;
                $relPath  = $matches[2];
                $fileDir  = dirname($filepath);
                $resolved = realpath($fileDir . '/' . ltrim($relPath, '/'));

                // Also try resolving manually if realpath fails
                if (!$resolved) {
                    $manual = $fileDir . $relPath;
                    // Normalize .. segments
                    $parts  = explode('/', str_replace('\\', '/', $manual));
                    $stack  = [];
                    foreach ($parts as $part) {
                        if ($part === '..') {
                            array_pop($stack);
                        } elseif ($part !== '.') {
                            $stack[] = $part;
                        }
                    }
                    $manual = implode(DIRECTORY_SEPARATOR, $stack);
                    $exists = file_exists($manual);
                } else {
                    $exists = true;
                }

                if (!$resolved && !($exists ?? false)) {
                    $brokenPaths[] = [
                        'file'     => str_replace(
                            $projectRoot, '', $filepath),
                        'line'     => $lineNum + 1,
                        'require'  => trim($line),
                        'resolved' => $fileDir . $relPath,
                    ];
                }
            }
        }
    }
}

// Output results
echo '<p>📂 Files scanned: <strong>' . $checkedFiles . '</strong></p>';
echo '<p>🔍 require/include statements found: <strong>'
     . $totalRequires . '</strong></p>';

if (empty($brokenPaths)) {
    echo '<p style="color:green">✅ No broken require/include paths found</p>';
} else {
    echo '<p style="color:red"><strong>❌ '
         . count($brokenPaths)
         . ' broken path(s) found:</strong></p>';

    echo '<table border="1" cellpadding="6"
           style="border-collapse:collapse;width:100%;
                  font-family:monospace;font-size:12px">';
    echo '<tr style="background:#fee2e2">
            <th>File</th>
            <th>Line</th>
            <th>Broken Path</th>
            <th>Suggested Fix</th>
          </tr>';

    foreach ($brokenPaths as $b) {
        // Suggest the correct path
        $broken   = $b['resolved'];
        $filename = basename($broken);

        // Try to find the file in the project
        $suggestion = 'File not found in project';
        $searchDirs = [
            $projectRoot . '/config/',
            $projectRoot . '/app/',
            $projectRoot . '/public/',
        ];
        foreach ($searchDirs as $sd) {
            $found = glob($sd . '**/' . $filename, GLOB_BRACE);
            if (empty($found)) {
                // Try direct
                $direct = $sd . $filename;
                if (file_exists($direct)) {
                    $suggestion = "__DIR__ . '/"
                        . str_replace(
                            str_replace('\\','/',
                                dirname($projectRoot
                                    . $b['file'])) . '/',
                            '',
                            str_replace('\\', '/', $direct))
                        . "'";
                    break;
                }
            }
        }

        echo '<tr>'
             . '<td style="color:#dc2626">'
             . htmlspecialchars($b['file']) . '</td>'
             . '<td style="text-align:center">'
             . $b['line'] . '</td>'
             . '<td style="color:#9a3412;word-break:break-all">'
             . htmlspecialchars(trim($b['require'])) . '</td>'
             . '<td style="color:#166534">'
             . htmlspecialchars($suggestion) . '</td>'
             . '</tr>';
    }
    echo '</table>';

    // Also show the correct root path for reference
    echo '<p style="margin-top:10px">
            <strong>Project root:</strong>
            <code>' . htmlspecialchars($projectRoot) . '</code>
          </p>';
    echo '<p>
            <strong>config/database.php full path:</strong>
            <code>'
         . htmlspecialchars($projectRoot . '/config/database.php')
         . '</code> — '
         . (file_exists($projectRoot . '/config/database.php')
             ? '<span style="color:green">✅ EXISTS</span>'
             : '<span style="color:red">❌ MISSING</span>')
         . '</p>';
}

echo '</div>';

// Section 7: Known Broken File Check
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">7. Known Broken File Check</h2>';

$knownBroken = [
    $projectRoot . '/app/models/PaymentModel.php',
];

foreach ($knownBroken as $kf) {
    if (!file_exists($kf)) {
        echo '<p>⚠️ ' . basename($kf) . ' — FILE NOT FOUND</p>';
        continue;
    }

    $content = file_get_contents($kf);
    $lines   = explode("\n", $content);

    // Show lines 1-10 for quick inspection
    echo '<p><strong>' . basename($kf) . '</strong> — '
         . 'First 10 lines:</p>';
    echo '<pre style="background:#f3f4f6;padding:10px;
                      font-size:11px;overflow-x:auto">';
    for ($i = 0; $i < min(10, count($lines)); $i++) {
        $marker = ($i === 5) ? ' ← Line ' . ($i+1) . ' (error here)' : '';
        echo htmlspecialchars(($i+1) . ': ' . $lines[$i]) . $marker . "\n";
    }
    echo '</pre>';

    // Check if the path it requires actually exists
    if (preg_match(
        '/require_once\s+__DIR__\s*\.\s*[\'"]([^\'"]+)[\'"]/',
        $content, $m)) {
        $reqPath = dirname($kf) . $m[1];
        echo '<p>Requires: <code>'
             . htmlspecialchars($m[1]) . '</code></p>';
        echo '<p>Resolves to: <code>'
             . htmlspecialchars($reqPath) . '</code></p>';
        echo '<p>'
             . (file_exists($reqPath)
                 ? '✅ FILE EXISTS'
                 : '❌ FILE MISSING — <strong>this is the bug</strong>')
             . '</p>';

        if (!file_exists($reqPath)) {
            $correctPath = $projectRoot . '/config/database.php';
            echo '<p>✅ Correct path should be: <code>'
                 . htmlspecialchars(
                     '__DIR__ . \'/../../config/database.php\'')
                 . '</code></p>';
            echo '<p>'
                 . (file_exists($correctPath)
                     ? '✅ Correct file EXISTS at: '
                       . htmlspecialchars($correctPath)
                     : '❌ Even correct path is missing')
                 . '</p>';
        }
    }
}

echo '</div>';

// Section 10: Payments Table Schema Debug
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">10. Payments Table Schema Debug</h2>';

// 1. Actual columns check
echo '<h3 class="font-medium mb-2">1. Actual columns check</h3>';
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM payments");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo '<table border="1" cellpadding="4" style="border-collapse:collapse;width:100%;font-size:12px">';
        echo '<tr style="background:#f3f4f6"><th>Column</th><th>Status</th></tr>';
        
        $expectedColumns = [
            'deleted_at' => '✅ or ❌',
            'unit_id' => '✅ or ❌', 
            'receipt_reference' => '✅ (should exist already)',
            'paystack_reference' => 'should NOT exist → ✅ ABSENT or ⚠️ PRESENT'
        ];
        
        $foundColumns = [];
        foreach ($columns as $col) {
            $foundColumns[$col['Field']] = $col;
        }
        
        foreach ($expectedColumns as $col => $expected) {
            $exists = isset($foundColumns[$col]);
            $status = $exists ? '✅ EXISTS' : '❌ MISSING';
            $color = $exists ? 'green' : 'red';
            
            echo '<tr>';
            echo '<td style="font-family:monospace">' . htmlspecialchars($col) . '</td>';
            echo '<td style="color:' . $color . '">' . $status . '</td>';
            echo '</tr>';
        }
        
        // Check for unwanted paystack_reference
        if (isset($foundColumns['paystack_reference'])) {
            echo '<tr>';
            echo '<td style="font-family:monospace">paystack_reference</td>';
            echo '<td style="color:orange">⚠️ UNWANTED COLUMN EXISTS</td>';
            echo '</tr>';
        } else {
            echo '<tr>';
            echo '<td style="font-family:monospace">paystack_reference</td>';
            echo '<td style="color:green">✅ CORRECTLY ABSENT</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        
    } catch (Throwable $e) {
        echo '<p style="color:red">❌ Error checking columns: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
} else {
    echo '<p style="color:red">❌ Database not available</p>';
}

// 2. PaymentModel.php column reference audit
echo '<h3 class="font-medium mb-2 mt-4">2. PaymentModel.php column reference audit</h3>';
$paymentModelFile = $projectRoot . '/app/models/PaymentModel.php';
if (file_exists($paymentModelFile)) {
    $modelContent = file_get_contents($paymentModelFile);
    
    $checks = [
        'paystack_reference' => '❌ STILL WRONG or ✅ REMOVED',
        'receipt_reference' => '✅ or ❌',
        'deleted_at' => '✅ or ❌',
        'ensurePaymentsSchema' => '✅ or ❌'
    ];
    
    echo '<table border="1" cellpadding="4" style="border-collapse:collapse;width:100%;font-size:12px">';
    echo '<tr style="background:#f3f4f6"><th>Check</th><th>Result</th></tr>';
    
    foreach ($checks as $check => $expected) {
        $found = strpos($modelContent, $check) !== false;
        $result = '';
        
        if ($check === 'paystack_reference') {
            $result = $found ? '❌ STILL WRONG' : '✅ REMOVED';
        } elseif ($check === 'receipt_reference') {
            $result = $found ? '✅ PRESENT' : '❌ MISSING';
        } elseif ($check === 'deleted_at') {
            $result = $found ? '✅ USED IN QUERIES' : '❌ NOT FOUND';
        } elseif ($check === 'ensurePaymentsSchema') {
            $result = $found ? '✅ METHOD EXISTS' : '❌ METHOD MISSING';
        }
        
        $color = strpos($result, '✅') !== false ? 'green' : 'red';
        
        echo '<tr>';
        echo '<td style="font-family:monospace">' . htmlspecialchars($check) . '</td>';
        echo '<td style="color:' . $color . '">' . $result . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
} else {
    echo '<p style="color:red">❌ PaymentModel.php not found</p>';
}

// 3. Live query test
echo '<h3 class="font-medium mb-2 mt-4">3. Live query test</h3>';
if ($pdo) {
    try {
        $testQuery = "
            SELECT p.id, p.amount, p.status,
                   p.receipt_reference, p.deleted_at,
                   t.first_name as tenant_name
            FROM payments p
            LEFT JOIN tenants t ON t.id = p.tenant_id
            WHERE p.admin_id IS NOT NULL
            LIMIT 3
        ";
        $stmt = $pdo->prepare($testQuery);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo '<p style="color:green">✅ QUERY WORKS - Returned ' . count($results) . ' rows</p>';
        
        if (!empty($results)) {
            echo '<table border="1" cellpadding="4" style="border-collapse:collapse;width:100%;font-size:11px">';
            echo '<tr style="background:#f3f4f6">';
            foreach (array_keys($results[0]) as $key) {
                echo '<th>' . htmlspecialchars($key) . '</th>';
            }
            echo '</tr>';
            
            foreach ($results as $row) {
                echo '<tr>';
                foreach ($row as $value) {
                    echo '<td>' . htmlspecialchars($value ?? 'NULL') . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
        
    } catch (Throwable $e) {
        echo '<p style="color:red">❌ QUERY ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
} else {
    echo '<p style="color:red">❌ Database not available</p>';
}

// 4. Page load simulation
echo '<h3 class="font-medium mb-2 mt-4">4. Page load simulation</h3>';
try {
    require_once $projectRoot . '/app/models/PaymentModel.php';
    echo '<p style="color:green">✅ PaymentModel.php loads without fatal error</p>';
} catch (Throwable $e) {
    echo '<p style="color:red">❌ PaymentModel fatal: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

echo '</div>';

// Section 12: PaymentsController Namespace Fix Verification
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">12. PaymentsController Namespace Fix Verification</h2>';

// Namespace prefix check
$pcSrc = file_get_contents($projectRoot . '/app/controllers/PaymentsController.php');

$bareVM = substr_count($pcSrc, 'ViewManager::') - substr_count($pcSrc, '\\ViewManager::');
$bareDP = substr_count($pcSrc, 'DataProvider::') - substr_count($pcSrc, '\\DataProvider::');
$bareCR = substr_count($pcSrc, 'ComponentRegistry::') - substr_count($pcSrc, '\\ComponentRegistry::');

echo '<p>'
     . ($bareVM === 0 ? '✅' : '❌')
     . ' ViewManager:: namespace — '
     . ($bareVM === 0 ? 'CORRECT' : $bareVM . ' bare calls remaining')
     . '</p>';
echo '<p>'
     . ($bareDP === 0 ? '✅' : '❌')
     . ' DataProvider:: namespace — '
     . ($bareDP === 0 ? 'CORRECT' : $bareDP . ' bare calls remaining')
     . '</p>';
echo '<p>'
     . ($bareCR === 0 ? '✅' : '❌')
     . ' ComponentRegistry:: namespace — '
     . ($bareCR === 0 ? 'CORRECT' : $bareCR . ' bare calls remaining')
     . '</p>';

// Check other affected controllers
$affectedControllers = [
    'InvoiceController', 'MaintenanceController', 
    'ReportController', 'CommunicationController'
];

echo '<h3 class="font-medium mb-2 mt-4">Other Controllers Check</h3>';
foreach ($affectedControllers as $controller) {
    $controllerFile = $projectRoot . '/app/controllers/' . $controller . '.php';
    if (file_exists($controllerFile)) {
        $content = file_get_contents($controllerFile);
        $bare = substr_count($content, 'ViewManager::') - substr_count($content, '\\ViewManager::');
        echo '<p>'
             . ($bare === 0 ? '✅' : '❌')
             . ' ' . $controller . ' — '
             . ($bare === 0 ? 'CORRECT' : $bare . ' bare calls')
             . '</p>';
    }
}

echo '</div>';

// Section 11: Payments Fix Verification
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">11. Payments Fix Verification</h2>';

// 1. paystack_reference final check
echo '<h3 class="font-medium mb-2">1. paystack_reference final check</h3>';
$paymentModelFile = $projectRoot . '/app/models/PaymentModel.php';
if (file_exists($paymentModelFile)) {
    $modelContent = file_get_contents($paymentModelFile);
    $paystackCount = substr_count($modelContent, 'paystack_reference');
    
    if ($paystackCount === 0) {
        echo '<p style="color:green">✅ FULLY REMOVED - 0 occurrences of paystack_reference</p>';
    } else {
        echo '<p style="color:red">❌ STILL PRESENT - ' . $paystackCount . ' occurrences of paystack_reference</p>';
    }
} else {
    echo '<p style="color:red">❌ PaymentModel.php not found</p>';
}

// 2. t.first_name fix check
echo '<h3 class="font-medium mb-2 mt-4">2. t.first_name fix check</h3>';
if (file_exists($paymentModelFile)) {
    $hasFirstName = strpos($modelContent, 't.first_name') !== false;
    $hasNameField = strpos($modelContent, 't.name as tenant_name') !== false;
    
    if (!$hasFirstName && $hasNameField) {
        echo '<p style="color:green">✅ FIXED - Using t.name as tenant_name</p>';
    } else {
        echo '<p style="color:red">❌ STILL WRONG - ';
        if ($hasFirstName) echo 't.first_name still present';
        if (!$hasNameField) echo 't.name as tenant_name missing';
        echo '</p>';
    }
} else {
    echo '<p style="color:red">❌ PaymentModel.php not found</p>';
}

// 3. Missing pages re-check
echo '<h3 class="font-medium mb-2 mt-4">3. Missing pages re-check</h3>';
$expectedNewPages = [
    'views/admin/auth/login.php',
    'views/admin/auth/register.php', 
    'views/admin/dashboard.php',
    'views/admin/payments/list.php',
    'views/admin/reports/index.php',
    'views/admin/profile.php'
];

$newPagesExist = 0;
$newPagesMissing = [];
foreach ($expectedNewPages as $page) {
    $fullPath = $projectRoot . '/' . $page;
    if (file_exists($fullPath)) {
        $newPagesExist++;
    } else {
        $newPagesMissing[] = $page;
    }
}

echo '<p><strong>New Pages Status:</strong> ' . $newPagesExist . '/6 exist, ' . count($newPagesMissing) . ' missing</p>';
if (!empty($newPagesMissing)) {
    echo '<p style="color:red">❌ Missing pages:</p><ul>';
    foreach ($newPagesMissing as $missing) {
        echo '<li style="color:red">' . htmlspecialchars($missing) . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p style="color:green">✅ All 6 new pages created successfully</p>';
}

// 4. Live payments query test
echo '<h3 class="font-medium mb-2 mt-4">4. Live payments query test</h3>';
if ($pdo) {
    try {
        $testQuery = "
            SELECT p.id, p.amount, p.status,
                   p.receipt_reference,
                   t.name as tenant_name
            FROM payments p
            LEFT JOIN tenants t ON t.id = p.tenant_id
            WHERE p.deleted_at IS NULL
            LIMIT 3
        ";
        $stmt = $pdo->prepare($testQuery);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo '<p style="color:green">✅ QUERY WORKS - Returned ' . count($results) . ' rows</p>';
        
        if (!empty($results)) {
            echo '<table border="1" cellpadding="4" style="border-collapse:collapse;width:100%;font-size:11px">';
            echo '<tr style="background:#f3f4f6">';
            foreach (array_keys($results[0]) as $key) {
                echo '<th>' . htmlspecialchars($key) . '</th>';
            }
            echo '</tr>';
            
            foreach ($results as $row) {
                echo '<tr>';
                foreach ($row as $value) {
                    echo '<td>' . htmlspecialchars($value ?? 'NULL') . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
        
    } catch (Throwable $e) {
        echo '<p style="color:red">❌ QUERY ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
} else {
    echo '<p style="color:red">❌ Database not available</p>';
}

echo '</div>';

// Section 8: Fix Verification
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">8. Fix Verification</h2>';

$fixChecks = [
    [
        'label' => 'PaymentModel.php path fixed',
        'file'  => $projectRoot . '/app/models/PaymentModel.php',
        'check' => function($src) {
            return strpos($src, "'/../config/") === false
                && strpos($src, "'/../../config/") !== false;
        }
    ],
    [
        'label' => 'views/errors/500.php created',
        'file'  => $projectRoot . '/views/errors/500.php',
        'check' => fn($src) => strlen($src) > 100
    ],
    [
        'label' => 'views/errors/404.php created',
        'file'  => $projectRoot . '/views/errors/404.php',
        'check' => fn($src) => strlen($src) > 100
    ],
    [
        'label' => 'views/admin/invoices/index.php created',
        'file'  => $projectRoot . '/views/admin/invoices/index.php',
        'check' => fn($src) => strlen($src) > 100
    ],
    [
        'label' => 'views/admin/invoices/show.php created',
        'file'  => $projectRoot . '/views/admin/invoices/show.php',
        'check' => fn($src) => strlen($src) > 100
    ],
    [
        'label' => 'views/admin/maintenance/index.php created',
        'file'  => $projectRoot . '/views/admin/maintenance/index.php',
        'check' => fn($src) => strlen($src) > 100
    ],
    [
        'label' => 'views/admin/reports/create.php created',
        'file'  => $projectRoot . '/views/admin/reports/create.php',
        'check' => fn($src) => strlen($src) > 100
    ],
    [
        'label' => 'views/admin/reports/edit.php created',
        'file'  => $projectRoot . '/views/admin/reports/edit.php',
        'check' => fn($src) => strlen($src) > 100
    ],
    [
        'label' => 'views/admin/communications/bulk.php created',
        'file'  => $projectRoot
                   . '/views/admin/communications/bulk.php',
        'check' => fn($src) => strlen($src) > 100
    ],
];

foreach ($fixChecks as $fc) {
    if (!file_exists($fc['file'])) {
        echo '<p>❌ ' . $fc['label'] . ' — FILE MISSING</p>';
        continue;
    }
    $src    = file_get_contents($fc['file']);
    $passed = ($fc['check'])($src);
    echo '<p>'
         . ($passed ? '✅' : '❌')
         . ' ' . $fc['label']
         . ' — ' . ($passed ? 'PASS' : 'FAIL')
         . '</p>';
}

echo '</div>';

// Section 9: Missing Pages Audit
echo '<div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">9. Missing Pages Audit</h2>';
echo '<p style="color:#6b7280;font-size:13px">
        Checking all routes against their expected view files...
        </p>';

$projectRoot = realpath(__DIR__ . '/..');

// Complete map of: Route => Expected view file path
// (relative to project root)
$routeViewMap = [

    // ── Admin Auth ──────────────────────────────────────────────
    'GET /admin/login'
        => 'views/auth/login.php',
    'GET /admin/register'
        => 'views/auth/register.php',

    // ── Admin Dashboard ─────────────────────────────────────────
    'GET /admin/dashboard'
        => 'views/admin/dashboard_enhanced.php',

    // ── Superadmin ──────────────────────────────────────────────
    'GET /superadmin/dashboard'
        => 'views/superadmin/dashboard.php',
    'GET /superadmin/admins'
        => 'views/superadmin/admins.php',
    'GET /superadmin/login'
        => 'views/superadmin/login.php',

    // ── Properties ──────────────────────────────────────────────
    'GET /admin/properties'
        => 'views/admin/properties/list.php',
    'GET /admin/properties/create'
        => 'views/admin/properties/add.php',
    'GET /admin/properties/{id}'
        => 'views/admin/properties/show.php',
    'GET /admin/properties/{id}/edit'
        => 'views/admin/properties/edit.php',

    // ── Units ───────────────────────────────────────────────────
    'GET /admin/units'
        => 'views/admin/units/list.php',
    'GET /admin/units/create'
        => 'views/admin/units/create.php',
    'GET /admin/units/{id}/edit'
        => 'views/admin/units/edit.php',

    // ── Tenants ─────────────────────────────────────────────────
    'GET /admin/tenants'
        => 'views/admin/tenants/list.php',
    'GET /admin/tenants/create'
        => 'views/admin/tenants/create.php',
    'GET /admin/tenants/{id}'
        => 'views/admin/tenants/show.php',
    'GET /admin/tenants/{id}/edit'
        => 'views/admin/tenants/edit.php',

    // ── Tenants & Occupants ─────────────────────────────────────
    'GET /admin/tenants-occupants'
        => 'views/admin/tenants_occupants/index.php',
    'GET /admin/occupants/create'
        => 'views/admin/occupants/create.php',

    // ── Payments ────────────────────────────────────────────────
    'GET /admin/payments'
        => 'views/admin/payments/index.php',
    'GET /admin/payments/create'
        => 'views/admin/payments/create.php',
    'GET /admin/payments/{id}'
        => 'views/admin/payments/show.php',
    'GET /admin/payments/{id}/edit'
        => 'views/admin/payments/edit.php',

    // ── Invoices ────────────────────────────────────────────────
    'GET /admin/invoices'
        => 'views/admin/invoices/index.php',
    'GET /admin/invoices/create'
        => 'views/admin/invoices/create.php',
    'GET /admin/invoices/{id}'
        => 'views/admin/invoices/show.php',
    'GET /admin/invoices/{id}/edit'
        => 'views/admin/invoices/edit.php',

    // ── Finances ────────────────────────────────────────────────
    'GET /admin/finances'
        => 'views/admin/finances/index.php',

    // ── Maintenance ─────────────────────────────────────────────
    'GET /admin/maintenance'
        => 'views/admin/maintenance/index.php',
    'GET /admin/maintenance/create'
        => 'views/admin/maintenance/create.php',
    'GET /admin/maintenance/{id}/edit'
        => 'views/admin/maintenance/edit.php',

    // ── Communications ──────────────────────────────────────────
    'GET /admin/communications'
        => 'views/admin/communications/index.php',
    'GET /admin/communications/create'
        => 'views/admin/communications/create.php',
    'GET /admin/communications/{id}/edit'
        => 'views/admin/communications/edit.php',
    'POST /admin/communications (bulk)'
        => 'views/admin/communications/bulk.php',

    // ── Documents ───────────────────────────────────────────────
    'GET /admin/documents'
        => 'views/admin/documents/index.php',
    'GET /admin/documents/create'
        => 'views/admin/documents/create.php',
    'GET /admin/documents/{id}/edit'
        => 'views/admin/documents/edit.php',

    // ── Reports ─────────────────────────────────────────────────
    'GET /admin/reports'
        => 'views/admin/dashboard_reports.php',
    'GET /admin/reports/create'
        => 'views/admin/reports/create.php',
    'GET /admin/reports/{id}/edit'
        => 'views/admin/reports/edit.php',

    // ── Settings & Profile ───────────────────────────────────────
    'GET /admin/settings'
        => 'views/admin/settings.php',
    'GET /admin/profile'
        => 'views/admin/profile/index.php',

    // ── Error Pages ─────────────────────────────────────────────
    '404 error page'
        => 'views/errors/404.php',
    '500 error page'
        => 'views/errors/500.php',
];

$existingPages = [];
$missingPages  = [];

foreach ($routeViewMap as $route => $viewPath) {
    $fullPath = $projectRoot . '/' . $viewPath;
    if (file_exists($fullPath)) {
        $existingPages[] = [
            'route'    => $route,
            'view'     => $viewPath,
            'size'     => filesize($fullPath),
            'modified' => date('Y-m-d H:i', filemtime($fullPath)),
        ];
    } else {
        $missingPages[] = [
            'route' => $route,
            'view'  => $viewPath,
        ];
    }
}

// Summary counts
$total   = count($routeViewMap);
$present = count($existingPages);
$missing = count($missingPages);
$pct     = round(($present / $total) * 100);

echo '<div style="display:flex;gap:16px;margin:12px 0;
                  flex-wrap:wrap">';
echo '<div style="padding:12px 20px;background:#dcfce7;
                  border-radius:8px;text-align:center">
      <div style="font-size:1.8rem;font-weight:800;
                  color:#16a34a">' . $present . '</div>
      <div style="font-size:12px;color:#166534">
          Pages Exist</div>
    </div>';
echo '<div style="padding:12px 20px;background:#fee2e2;
                  border-radius:8px;text-align:center">
      <div style="font-size:1.8rem;font-weight:800;
                  color:#dc2626">' . $missing . '</div>
      <div style="font-size:12px;color:#991b1b">
          Pages Missing</div>
    </div>';
echo '<div style="padding:12px 20px;background:#dbeafe;
                  border-radius:8px;text-align:center">
      <div style="font-size:1.8rem;font-weight:800;
                  color:#2563eb">' . $pct . '%</div>
      <div style="font-size:12px;color:#1e40af">
          Complete</div>
    </div>';
echo '</div>';

// ── Missing pages table ────────────────────────────────────────
if (!empty($missingPages)) {
    echo '<h4 style="color:#dc2626;margin:16px 0 8px">
            ❌ Missing Pages (' . $missing . ')
          </h4>';

    // Group by section
    $grouped = [];
    foreach ($missingPages as $mp) {
        preg_match(
            '/views\/(?:admin|superadmin|errors)\/([^\/]+)/',
            $mp['view'], $m);
        $section = ucfirst($m[1] ?? 'other');
        $grouped[$section][] = $mp;
    }

    foreach ($grouped as $section => $pages) {
        echo '<p style="font-weight:600;margin:10px 0 4px;
                        color:#374151">' . $section . '</p>';
        echo '<table border="1" cellpadding="6"
               style="border-collapse:collapse;width:100%;
                      font-size:12px;margin-bottom:8px">';
        echo '<tr style="background:#fef2f2">
                <th style="text-align:left">Route</th>
                <th style="text-align:left">Expected File</th>
                <th style="text-align:left">Priority</th>
              </tr>';
        foreach ($pages as $p) {
            // Assign priority based on route type
            $priority = 'Medium';
            $pColor   = '#f59e0b';
            if (strpos($p['route'], 'create') !== false
                || strpos($p['route'], 'index') !== false
                || strpos($p['route'], 'list') !== false
                || strpos($p['route'], 'dashboard') !== false) {
                $priority = 'High';
                $pColor   = '#dc2626';
            }
            if (strpos($p['route'], 'edit') !== false
                || strpos($p['route'], 'show') !== false) {
                $priority = 'Medium';
                $pColor   = '#f59e0b';
            }
            if (strpos($p['route'], 'error') !== false) {
                $priority = 'Low';
                $pColor   = '#6b7280';
            }
            echo '<tr>'
                 . '<td style="color:#6b7280">'
                 . htmlspecialchars($p['route']) . '</td>'
                 . '<td style="color:#dc2626;font-family:monospace">'
                 . htmlspecialchars($p['view']) . '</td>'
                 . '<td style="color:' . $pColor
                 . ';font-weight:600">' . $priority . '</td>'
                 . '</tr>';
        }
        echo '</table>';
    }
}

// ── Existing pages table ───────────────────────────────────────
echo '<h4 style="color:#16a34a;margin:16px 0 8px">
        ✅ Existing Pages (' . $present . ')
      </h4>';
echo '<table border="1" cellpadding="6"
       style="border-collapse:collapse;width:100%;
              font-size:12px">';
echo '<tr style="background:#f0fdf4">
        <th style="text-align:left">Route</th>
        <th style="text-align:left">View File</th>
        <th style="text-align:left">Size</th>
        <th style="text-align:left">Last Modified</th>
      </tr>';
foreach ($existingPages as $ep) {
    echo '<tr>'
         . '<td style="color:#6b7280">'
         . htmlspecialchars($ep['route']) . '</td>'
         . '<td style="font-family:monospace;color:#166534">'
         . htmlspecialchars($ep['view']) . '</td>'
         . '<td>' . number_format($ep['size'] / 1024, 1)
         . ' KB</td>'
         . '<td style="color:#6b7280">'
         . $ep['modified'] . '</td>'
         . '</tr>';
}
echo '</table>';

// ── Proceed prompt ─────────────────────────────────────────────
if (!empty($missingPages)) {
    echo '<div style="margin-top:20px;padding:16px;
                      background:#fffbeb;border:1px solid #f59e0b;
                      border-radius:8px">';
    echo '<p style="font-weight:700;color:#92400e;margin-bottom:8px">
            ⚠️ ' . $missing . ' pages are missing.
          </p>';
    echo '<p style="color:#78350f;font-size:13px;margin-bottom:12px">
            Review the missing pages above. To create all missing
            pages with proper structure, run the page creation
            prompt in your IDE.
          </p>';
    echo '<p style="color:#78350f;font-size:12px">
            <strong>High priority:</strong> These pages will cause
            fatal errors when visited.<br>
            <strong>Medium priority:</strong> These are detail/edit
            pages that degrade functionality.<br>
            <strong>Low priority:</strong> Error pages and minor
            supporting views.
          </p>';
    echo '</div>';
} else {
    echo '<div style="margin-top:16px;padding:16px;
                      background:#f0fdf4;border:1px solid #86efac;
                      border-radius:8px">
            <p style="color:#166534;font-weight:700">
                🎉 All pages exist! No missing views found.
            </p>
          </div>';
}

echo '</div>';

echo '</div>
</body>
</html>';

} catch (\Throwable $e) {
    echo '<div style="color:red;padding:20px;font-family:monospace">';
    echo '<strong>❌ Debugchecker Fatal Error:</strong><br>';
    echo htmlspecialchars($e->getMessage()) . '<br>';
    echo 'File: ' . htmlspecialchars($e->getFile())
         . ' Line: ' . $e->getLine();
    echo '</div>';
}
?>
