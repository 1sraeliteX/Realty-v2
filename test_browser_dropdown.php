<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Types Dropdown Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Property Types Dropdown Browser Test</h1>
        
        <?php
        require_once __DIR__ . '/config/property_types.php';
        require_once __DIR__ . '/components/SearchableDropdown.php';
        
        $propertyTypes = include __DIR__ . '/config/property_types.php';
        ?>
        
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-4">Property Types Available: <?php echo count($propertyTypes); ?></h2>
            <p class="text-sm text-gray-600 mb-4">Testing the dropdown component directly</p>
        </div>
        
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-4">Dropdown Component</h2>
            <?php
            echo renderSearchableDropdown(
                $propertyTypes,
                'test_property_type',
                'test_property_type',
                'Property Type',
                'Search or select property type...',
                'apartment',
                true,
                false,
                ''
            );
            ?>
        </div>
        
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-4">Debug Information</h2>
            <div class="bg-gray-100 p-4 rounded">
                <h3 class="font-semibold mb-2">Browser Console Check:</h3>
                <p class="text-sm">Open browser developer tools (F12) and check the Console tab for any JavaScript errors</p>
                
                <h3 class="font-semibold mb-2 mt-4">Expected Behavior:</h3>
                <ul class="text-sm list-disc list-inside">
                    <li>Click on the dropdown to open it</li>
                    <li>Type "apartment" to search for apartment options</li>
                    <li>Use arrow keys to navigate</li>
                    <li>Press Enter to select highlighted option</li>
                    <li>Click outside to close dropdown</li>
                </ul>
                
                <h3 class="font-semibold mb-2 mt-4">If Issues Found:</h3>
                <p class="text-sm text-red-600">Check if:</p>
                <ul class="text-sm list-disc list-inside">
                    <li>JavaScript errors in console</li>
                    <li>CSS conflicts preventing display</li>
                    <li>Dropdown not appearing</li>
                    <li>Options not showing</li>
                </ul>
            </div>
        </div>
        
        <div class="mt-8">
            <a href="/properties/create" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Property Creation Form
            </a>
        </div>
    </div>
</body>
</html>
