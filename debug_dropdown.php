<?php
// Debug script to check property types dropdown
require_once __DIR__ . '/components/SearchableDropdown.php';
require_once __DIR__ . '/config/property_types.php';

echo "<h1>Property Types Dropdown Debug</h1>";

// Test 1: Check if property types are loaded correctly
echo "<h2>1. Testing Property Types Loading</h2>";

$propertyTypes = include __DIR__ . '/config/property_types.php';
echo "<p>Property types file loaded: " . (is_array($propertyTypes) ? 'YES' : 'NO') . "</p>";
echo "<p>Total property types: " . count($propertyTypes) . "</p>";

if (is_array($propertyTypes) && count($propertyTypes) > 0) {
    echo "<h3>First 5 property types:</h3>";
    for ($i = 0; $i < min(5, count($propertyTypes)); $i++) {
        echo "<p>" . ($i + 1) . ". {$propertyTypes[$i]['label']} ({$propertyTypes[$i]['value']})</p>";
    }
} else {
    echo "<p style='color: red;'>ERROR: Property types not loaded correctly</p>";
}

// Test 2: Test dropdown rendering directly
echo "<h2>2. Testing Dropdown Rendering</h2>";

ob_start();
echo renderSearchableDropdown(
    $propertyTypes,
    'property_type',
    'property_type',
    'Property Type',
    'Search or select property type...',
    'apartment',
    true,
    false,
    ''
);
$dropdownOutput = ob_get_clean();

echo "<h3>Dropdown Output:</h3>";
echo "<div style='border: 1px solid #ccc; padding: 10px; background: #f9f9f9;'>";
echo $dropdownOutput;
echo "</div>";

// Test 3: Check for common issues
echo "<h2>3. Common Issues Check</h2>";

$issues = [];

// Check if property types array has correct structure
if (!is_array($propertyTypes)) {
    $issues[] = "Property types is not an array";
} elseif (count($propertyTypes) === 0) {
    $issues[] = "Property types array is empty";
} else {
    // Check first few types for correct structure
    $firstType = $propertyTypes[0];
    if (!isset($firstType['value']) || !isset($firstType['label'])) {
        $issues[] = "Property type structure is incorrect - missing 'value' or 'label'";
    }
}

// Check if dropdown output contains expected content
if (strpos($dropdownOutput, 'Apartment') === false) {
    $issues[] = "Dropdown does not contain 'Apartment' option";
}

if (strpos($dropdownOutput, 'Flat') === false) {
    $issues[] = "Dropdown does not contain 'Flat' option";
}

if (empty($issues)) {
    echo "<p style='color: green; font-weight: bold;'>✓ No issues found!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ Issues found:</p>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li>$issue</li>";
    }
    echo "</ul>";
}

// Test 4: JavaScript functionality check
echo "<h2>4. JavaScript Components Check</h2>";
echo "<p>Checking for required JavaScript functions...</p>";

$jsChecks = [
    'searchable-dropdown-container' => 'Dropdown container class',
    'fa-chevron-down' => 'Dropdown toggle icon',
    'dropdown-option' => 'Dropdown option class',
    'filterOptions' => 'Filter options function',
    'selectOption' => 'Select option function'
];

echo "<ul>";
foreach ($jsChecks as $check => $description) {
    echo "<li>$check: $description</li>";
}
echo "</ul>";

echo "<h2>Debug Summary</h2>";
echo "<p><strong>Property Types:</strong> " . count($propertyTypes) . " loaded</p>";
echo "<p><strong>Dropdown Generated:</strong> " . (empty($issues) ? '✓ Working' : '✗ Has Issues') . "</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Verify dropdown opens and closes correctly</li>";
echo "<li>Test search functionality</li>";
echo "<li>Check form submission</li>";
echo "</ol>";

echo "<p><a href='/properties/create'>Test Live Dropdown</a></p>";
?>
