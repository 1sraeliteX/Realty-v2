<?php
// debugchecker.php — temporary diagnostic tool, DELETE after use

// Load autoloader so Components\ namespace resolves
require_once __DIR__ . '/../config/bootstrap.php';

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

// Add try/catch instantiation test
echo "<h3>2.6. AutoFillComponent Instantiation Test</h3>";
try {
    $instance = new \Components\AutoFillComponent();
    echo "<p>✅ AutoFillComponent instantiated successfully</p>";
} catch (\Throwable $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p> | File: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p> | Line: " . $e->getLine() . "</p>";
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

// 3. AutoFillComponent Load Test
echo "<h3>3. AutoFillComponent Load Test</h3>";

// Test 3.1: Was bootstrap.php included?
echo "<h4>3.1 Bootstrap Loading Check</h4>";
$includedFiles = get_included_files();
$bootstrapLoaded = false;
foreach ($includedFiles as $file) {
    if (strpos($file, 'bootstrap.php') !== false) {
        $bootstrapLoaded = true;
        echo "<p>✅ Bootstrap.php loaded: " . htmlspecialchars($file) . "</p>";
        break;
    }
}
if (!$bootstrapLoaded) {
    echo "<p>❌ Bootstrap.php NOT loaded in included files</p>";
}

// Test 3.2: Is the Components\ spl_autoload_register active?
echo "<h4>3.2 Components Autoloader Check</h4>";
$autoloaderActive = false;
$autoloaderFunctions = spl_autoload_functions();
echo "<p>Active autoloaders count: " . count($autoloaderFunctions) . "</p>";

if ($autoloaderFunctions) {
    foreach ($autoloaderFunctions as $index => $function) {
        if (is_array($function) && isset($function[1])) {
            // Check if this is our Components autoloader
            try {
                $reflection = new ReflectionFunction($function);
                $fileName = $reflection->getFileName();
                if ($fileName && strpos($fileName, 'bootstrap.php') !== false) {
                    $autoloaderActive = true;
                    echo "<p>✅ Components namespace autoloader is active (index {$index})</p>";
                    echo "<p>Found in: " . htmlspecialchars($fileName) . "</p>";
                    break;
                }
            } catch (Exception $e) {
                // Skip if we can't reflect
            }
        }
    }
}
if (!$autoloaderActive) {
    echo "<p>❌ Components namespace autoloader NOT found</p>";
}

// Test 3.3: Manual path building and file_exists check
echo "<h4>3.3 Manual Path Resolution Test</h4>";
$expectedFile = __DIR__ . '/../app/components/AutoFillComponent.php';
echo "<p>Target Class: Components\\AutoFillComponent</p>";
echo "<p>Expected File: " . htmlspecialchars($expectedFile) . "</p>";

if (file_exists($expectedFile)) {
    echo "<p>✅ AutoFillComponent.php file exists</p>";
} else {
    echo "<p>❌ AutoFillComponent.php file NOT found</p>";
}

// Test 3.4: Did the class load? (with try/catch)
echo "<h4>3.4 Class Loading Test</h4>";
try {
    $classLoaded = class_exists('\Components\AutoFillComponent', true);
    if ($classLoaded) {
        echo "<p>✅ Components\\AutoFillComponent class exists and loaded</p>";
    } else {
        echo "<p>❌ Components\\AutoFillComponent class NOT found</p>";
    }
} catch (\Throwable $e) {
    echo "<p>❌ Error checking class existence: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p> | File: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p> | Line: " . $e->getLine() . "</p>";
}

// Test 3.5: List all registered autoloaders
echo "<h4>3.5 Registered Autoloaders</h4>";
echo "<p>Total registered autoloaders: " . count($autoloaderFunctions) . "</p>";
foreach ($autoloaderFunctions as $index => $function) {
    if (is_array($function)) {
        $className = is_object($function[0]) ? get_class($function[0]) : $function[0];
        $methodName = $function[1] ?? 'unknown';
        echo "<p>[$index] {$className}::{$methodName}</p>";
    } elseif (is_string($function)) {
        echo "<p>[$index] Function: " . htmlspecialchars($function) . "</p>";
    } else {
        echo "<p>[$index] " . gettype($function) . "</p>";
    }
}

echo "<h3>4. Property Creation Debug</h3>";

// 4.1 Show last 30 lines of PHP error log
echo "<h4>4.1 PHP Error Log (Last 30 lines)</h4>";
$errorLogFile = ini_get('error_log');
echo "<p>Error log file: " . htmlspecialchars($errorLogFile) . "</p>";

if (file_exists($errorLogFile)) {
    $lines = file($errorLogFile);
    $lastLines = array_slice($lines, -30);
    echo "<pre style='background:#f4f4f4;padding:10px;max-height:300px;overflow-y:scroll'>";
    echo htmlspecialchars(implode('', $lastLines));
    echo "</pre>";
} else {
    echo "<p>❌ Error log file not found</p>";
}

// 4.2 Show current POST data
echo "<h4>4.2 Current POST Data</h4>";
if (!empty($_POST)) {
    echo "<pre style='background:#f4f4f4;padding:10px'>";
    echo htmlspecialchars(json_encode($_POST, JSON_PRETTY_PRINT));
    echo "</pre>";
} else {
    echo "<p>❌ No POST data available</p>";
}

// 4.3 Test form submit button
echo "<h4>4.3 Test Property Creation</h4>";
echo "<button onclick='testPropertySubmit()' style='background:#007cba;color:white;padding:10px;border:none;cursor:pointer'>Test Property Submit</button>";
echo "<div id='testResult' style='margin-top:10px;'></div>";

?>
<script>
function testPropertySubmit() {
    const resultDiv = document.getElementById('testResult');
    resultDiv.innerHTML = '<p>🔄 Testing property submission...</p>';
    
    const testData = new FormData();
    testData.append('name', 'Test Property Debug');
    testData.append('address', '123 Test Street');
    testData.append('type', 'apartment');
    testData.append('status', 'active');
    testData.append('water_availability', 'yes');
    testData.append('description', 'Test property for debugging');
    
    fetch('/admin/properties', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: testData
    })
    .then(response => response.text())
    .then(text => {
        resultDiv.innerHTML = '<h5>Raw Response:</h5><pre style="background:#f4f4f4;padding:10px;max-height:400px;overflow-y:scroll">' + 
                              escapeHtml(text) + '</pre>';
        
        try {
            const json = JSON.parse(text);
            resultDiv.innerHTML += '<h5>Parsed JSON:</h5><pre style="background:#e8f5e8;padding:10px">' + 
                                  escapeHtml(JSON.stringify(json, null, 2)) + '</pre>';
        } catch (e) {
            resultDiv.innerHTML += '<p style="color:red">❌ Response is not valid JSON: ' + escapeHtml(e.message) + '</p>';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<p style="color:red">❌ Network error: ' + escapeHtml(error.message) + '</p>';
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Global error handler
window.onerror = function(message, source, lineno, colno, error) {
    const errorBox = document.getElementById('errorBox');
    const errorDetails = document.getElementById('errorDetails');
    
    if (errorBox && errorDetails) {
        errorBox.style.display = 'block';
        errorDetails.innerHTML += 
            '<p><strong>Error:</strong> ' + escapeHtml(message) + '<br>' +
            '<strong>File:</strong> ' + escapeHtml(source) + '<br>' +
            '<strong>Line:</strong> ' + lineno + '<br>' +
            '<strong>Column:</strong> ' + colno + '</p>';
    }
};

// Check button and form elements
function checkButtonElements() {
    const iframe = document.getElementById('addPageFrame');
    const resultDiv = document.getElementById('buttonCheckResult');
    
    iframe.onload = function() {
        try {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            const checks = [];
            
            // Check form
            const form = iframeDoc.getElementById('addPropertyForm');
            checks.push('Form addPropertyForm: ' + (form ? '✅ FOUND' : '❌ NOT FOUND'));
            
            // Check save button
            const saveBtn = iframeDoc.getElementById('saveBtn');
            checks.push('Save button saveBtn: ' + (saveBtn ? '✅ FOUND' : '❌ NOT FOUND'));
            if (saveBtn) {
                checks.push('Save button type: ' + saveBtn.type);
                checks.push('Save button disabled: ' + saveBtn.disabled);
            }
            
            // Check event listeners (basic check)
            if (form) {
                checks.push('Form has event listeners: ' + (form.onsubmit || form.getAttribute('data-has-listener') ? '✅ LIKELY' : '❓ UNKNOWN'));
            }
            
            resultDiv.innerHTML = '<p>' + checks.join('<br>') + '</p>';
            
        } catch (e) {
            resultDiv.innerHTML = '<p style="color:red">❌ Error checking iframe: ' + escapeHtml(e.message) + '</p>';
        }
    };
    
    iframe.src = '/admin/properties/create';
}

// Check global functions
function checkGlobalFunctions() {
    const resultDiv = document.getElementById('functionCheck');
    
    const checks = [];
    
    // Check if functions exist
    checks.push('showToast function: ' + (typeof showToast === 'function' ? '✅ AVAILABLE' : '❌ NOT AVAILABLE'));
    checks.push('setLoading function: ' + (typeof setLoading === 'function' ? '✅ AVAILABLE' : '❌ NOT AVAILABLE'));
    
    // Check if jQuery is loaded (if used)
    checks.push('jQuery loaded: ' + (typeof jQuery !== 'undefined' ? '✅ YES' : '❌ NO'));
    
    // Check if fetch API is available
    checks.push('fetch API available: ' + (typeof fetch === 'function' ? '✅ YES' : '❌ NO'));
    
    resultDiv.innerHTML = '<p>' + checks.join('<br>') + '</p>';
}

// Run checks when page loads
window.addEventListener('load', function() {
    setTimeout(function() {
        checkButtonElements();
        checkGlobalFunctions();
    }, 1000);
});
</script>

<?php

echo "<h3>5. Save Property Button Debug</h3>";

// 5.1 Button Event Listener Check
echo "<h4>5.1 Button Event Listener Check</h4>";
echo "<div id='buttonCheckResult' style='background:#f0f0f0;padding:10px;margin:10px 0;border:1px solid #ccc;'>";
echo "<p>Checking button and form elements...</p>";
echo "</div>";

echo "<iframe id='addPageFrame' style='display:none;width:1px;height:1px;'></iframe>";

// 5.2 JavaScript Error Capture
echo "<h4>5.2 JavaScript Error Capture</h4>";
echo "<div id='errorBox' style='background:#ffebee;border:1px solid #f44336;color:#c62828;padding:10px;margin:10px 0;display:none;'>";
echo "<strong>JavaScript Errors:</strong>";
echo "<div id='errorDetails'></div>";
echo "</div>";

// 5.3 showToast and setLoading availability
echo "<h4>5.3 Global Function Check</h4>";
echo "<div id='functionCheck' style='background:#f0f0f0;padding:10px;margin:10px 0;border:1px solid #ccc;'>";
echo "<p>Checking global function availability...</p>";
echo "</div>";

// 5.4 Test Form Submit (keep existing from section 4.3)
echo "<h4>5.4 Test Form Submit</h4>";
echo "<button onclick='testPropertySubmit()' style='background:#007cba;color:white;padding:10px;border:none;cursor:pointer'>Test Property Submit</button>";
echo "<div id='testResult' style='margin-top:10px;'></div>";

// 5.5 PHP Error Log
echo "<h4>5.5 PHP Error Log (Last 30 lines)</h4>";
$errorLogLocations = [
    'C:\xampp\logs\php_error.log',
    'C:\xampp\php\logs\php_error_log',
    'C:\xampp\apache\logs\error.log',
    ini_get('error_log'),
    sys_get_temp_dir() . '/php_errors.log'
];

$errorLogFound = false;
foreach ($errorLogLocations as $logFile) {
    if ($logFile && file_exists($logFile)) {
        echo "<p>✅ Found error log: " . htmlspecialchars($logFile) . "</p>";
        $lines = file($logFile);
        $lastLines = array_slice($lines, -30);
        echo "<pre style='background:#f4f4f4;padding:10px;max-height:300px;overflow-y:scroll'>";
        echo htmlspecialchars(implode('', $lastLines));
        echo "</pre>";
        $errorLogFound = true;
        break;
    }
}

if (!$errorLogFound) {
    echo "<p>❌ No error log file found in standard locations</p>";
}

// 5.6 JS Syntax Validation
echo "<h4>5.6 JS Syntax Validation</h4>";
echo "<div id='jsSyntaxCheck' style='background:#f0f0f0;padding:10px;margin:10px 0;border:1px solid #ccc;'>";
echo "<p>Checking JavaScript syntax in /admin/properties/create...</p>";

// Fetch the page content
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: DebugChecker/1.0\r\n"
    ]
]);

$pageContent = @file_get_contents('http://127.0.0.1:8080/admin/properties/create', false, $context);

if ($pageContent) {
    // Extract all script blocks
    preg_match_all('/<script[^>]*>(.*?)<\/script>/is', $pageContent, $scriptBlocks);
    
    $issues = [];
    $functionChecks = [];
    
    // Check for required functions
    $functionChecks['autoFillForm'] = strpos($pageContent, 'function autoFillForm') !== false;
    $functionChecks['showToast'] = strpos($pageContent, 'function showToast') !== false;
    $functionChecks['setLoading'] = strpos($pageContent, 'function setLoading') !== false;
    
    $scriptIndex = 0;
    foreach ($scriptBlocks[1] as $index => $scriptContent) {
        $scriptIndex++;
        $scriptLines = explode("\n", $scriptContent);
        $lineNumber = 1;
        
        foreach ($scriptLines as $scriptLine) {
            // Check for unclosed backticks
            $backtickCount = substr_count($scriptLine, '`');
            if ($backtickCount % 2 !== 0) {
                $issues[] = "Script $scriptIndex line $lineNumber: Unclosed backtick detected";
            }
            
            // Check for unclosed quotes (basic check)
            $singleQuoteCount = substr_count($scriptLine, "'") - substr_count($scriptLine, "\\'");
            $doubleQuoteCount = substr_count($scriptLine, '"') - substr_count($scriptLine, '\\"');
            
            if ($singleQuoteCount % 2 !== 0) {
                $issues[] = "Script $scriptIndex line $lineNumber: Unclosed single quote detected";
            }
            
            if ($doubleQuoteCount % 2 !== 0) {
                $issues[] = "Script $scriptIndex line $lineNumber: Unclosed double quote detected";
            }
            
            // Check for PHP variables echoed without json_encode
            if (preg_match('/<\?php.*echo.*\$.*\?>/', $scriptLine)) {
                if (!strpos($scriptLine, 'json_encode')) {
                    $issues[] = "Script $scriptIndex line $lineNumber: PHP variable echoed without json_encode()";
                }
            }
            
            $lineNumber++;
        }
    }
    
    // Display function checks
    echo "<div style='margin-bottom:15px;'>";
    foreach ($functionChecks as $func => $found) {
        $status = $found ? '✅' : '❌';
        $color = $found ? 'green' : 'red';
        echo "<span style='color:$color;font-weight:bold'>$status $func function found in page source</span><br>";
    }
    echo "</div>";
    
    // Display syntax issues
    $backtickIssues = array_filter($issues, function($issue) {
        return strpos($issue, 'backtick') !== false;
    });
    $quoteIssues = array_filter($issues, function($issue) {
        return strpos($issue, 'quote') !== false;
    });
    
    echo "<span style='color:" . (empty($backtickIssues) ? 'green' : 'red') . ";font-weight:bold'>" . 
         (empty($backtickIssues) ? '✅' : '❌') . " No unclosed backticks detected</span><br>";
    echo "<span style='color:" . (empty($quoteIssues) ? 'green' : 'red') . ";font-weight:bold'>" . 
         (empty($quoteIssues) ? '✅' : '❌') . " No unclosed quotes detected</span><br>";
    
    if (empty($issues)) {
        echo "<p style='color:green;font-weight:bold'>✅ No syntax issues detected in JavaScript blocks</p>";
    } else {
        echo "<p style='color:red;font-weight:bold'>❌ Syntax issues found:</p>";
        echo "<ul style='color:red;font-size:12px;max-height:200px;overflow-y:auto;'>";
        foreach ($issues as $issue) {
            echo "<li>" . htmlspecialchars($issue) . "</li>";
        }
        echo "</ul>";
    }
    
    echo "<p><small>Found " . count($scriptBlocks[0]) . " script blocks in total</small></p>";
    
} else {
    echo "<p style='color:red'>❌ Could not fetch page content for syntax checking</p>";
}

echo "</div>";

echo "<hr><p style='color:red'><strong>DELETE this file before going to production.</strong></p>";
?>
