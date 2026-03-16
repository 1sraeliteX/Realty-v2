<?php
// Include the app bootstrap so the page renders properly
require_once __DIR__ . '/../config/bootstrap.php';

// Capture the rendered output of the create page
$ch = curl_init('http://localhost:8080/admin/properties/create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
curl_close($ch);

// Save raw HTML to file
file_put_contents(__DIR__ . '/debug_source_output.html', $html);

// Split into lines and show lines 285-315
$lines = explode("\n", $html);
echo "<pre style='background:#1e1e1e;color:#d4d4d4;padding:20px;font-size:12px;'>";
echo "=== LINES 285-315 OF RENDERED PAGE ===\n\n";
for ($i = 284; $i <= 314 && $i < count($lines); $i++) {
    $marker = ($i === 298) ? ' <-- LINE 299 (0-indexed: 298)' : '';
    echo htmlspecialchars(($i + 1) . ': ' . $lines[$i]) . $marker . "\n";
}
echo "</pre>";

// Also show all <script> block boundaries
echo "<pre style='background:#1e1e1e;color:#90ee90;padding:20px;font-size:12px;'>";
echo "=== SCRIPT BLOCK BOUNDARIES ===\n\n";
$scriptCount = 0;
foreach ($lines as $i => $line) {
    if (stripos($line, '<script') !== false || stripos($line, '</script>') !== false) {
        $scriptCount++;
        echo htmlspecialchars(($i + 1) . ': ' . trim($line)) . "\n";
    }
}
echo "Total script-related lines: $scriptCount\n";
echo "</pre>";
?>
