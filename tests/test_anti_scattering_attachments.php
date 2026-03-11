<?php
// Anti-Scattering Compliance Test for Attachment System

require_once __DIR__ . '/config/init_framework.php';

echo "=== Anti-Scattering Compliance Test ===\n\n";

// Test 1: Component Registry Loading
echo "1. Testing Component Registry Loading:\n";
try {
    ComponentRegistry::load('attachment-component');
    echo "   ✅ AttachmentComponent loaded successfully via ComponentRegistry\n";
} catch (Exception $e) {
    echo "   ❌ Failed to load AttachmentComponent: " . $e->getMessage() . "\n";
}

// Test 2: Data Provider Integration
echo "\n2. Testing Data Provider Integration:\n";
try {
    $documents = DataProvider::get('documents');
    if (is_array($documents) && count($documents) > 0) {
        echo "   ✅ Documents data retrieved from DataProvider: " . count($documents) . " items\n";
    } else {
        echo "   ❌ No documents data found in DataProvider\n";
    }
} catch (Exception $e) {
    echo "   ❌ Failed to get documents from DataProvider: " . $e->getMessage() . "\n";
}

// Test 3: Component Independence
echo "\n3. Testing Component Independence:\n";
try {
    // Test if AttachmentComponent can be used without direct includes
    $uploadHtml = AttachmentComponent::renderUploadArea(['id' => 'test']);
    if (strpos($uploadHtml, 'attachment-upload-container') !== false) {
        echo "   ✅ AttachmentComponent renders upload area independently\n";
    } else {
        echo "   ❌ AttachmentComponent upload area rendering failed\n";
    }
    
    $listHtml = AttachmentComponent::renderAttachmentsList($documents);
    if (strpos($listHtml, 'attachments-list') !== false) {
        echo "   ✅ AttachmentComponent renders attachment list independently\n";
    } else {
        echo "   ❌ AttachmentComponent list rendering failed\n";
    }
} catch (Exception $e) {
    echo "   ❌ Component independence test failed: " . $e->getMessage() . "\n";
}

// Test 4: No Direct Includes Check
echo "\n4. Testing No Direct Includes:\n";
$filesToCheck = [
    'views/admin/documents/list.php',
    'views/admin/documents/create.php',
    'components/AttachmentComponent.php'
];

foreach ($filesToCheck as $file) {
    $content = file_get_contents(__DIR__ . '/' . $file);
    $hasBadInclude = strpos($content, 'require_once') !== false && 
                     strpos($content, 'init_framework.php') === false;
    if (!$hasBadInclude) {
        echo "   ✅ $file - No direct require_once found (only init_framework.php allowed)\n";
    } else {
        echo "   ❌ $file - Contains direct require_once (anti-scattering violation)\n";
    }
}

// Test 5: Data Centralization Check
echo "\n5. Testing Data Centralization:\n";
try {
    // Check if documents are centralized in DataProvider
    $documents = DataProvider::get('documents');
    if (isset($documents[0]['file_name']) && isset($documents[0]['file_path'])) {
        echo "   ✅ Document data is centralized in DataProvider\n";
    } else {
        echo "   ❌ Document data structure not properly centralized\n";
    }
} catch (Exception $e) {
    echo "   ❌ Data centralization test failed: " . $e->getMessage() . "\n";
}

// Test 6: View Manager Usage
echo "\n6. Testing View Manager Usage:\n";
$viewFiles = [
    'views/admin/documents/list.php',
    'views/admin/documents/create.php'
];

foreach ($viewFiles as $file) {
    $content = file_get_contents(__DIR__ . '/' . $file);
    if (strpos($content, 'ViewManager::') !== false) {
        echo "   ✅ $file - Uses ViewManager\n";
    } else {
        echo "   ❌ $file - Does not use ViewManager (anti-scattering violation)\n";
    }
}

// Test 7: Component Registration
echo "\n7. Testing Component Registration:\n";
try {
    $componentInfo = ComponentRegistry::getInfo('attachment-component');
    if ($componentInfo && isset($componentInfo['path'])) {
        echo "   ✅ AttachmentComponent is registered in ComponentRegistry\n";
        echo "      Path: " . $componentInfo['path'] . "\n";
        echo "      Dependencies: " . implode(', ', $componentInfo['dependencies']) . "\n";
    } else {
        echo "   ❌ AttachmentComponent not found in ComponentRegistry\n";
    }
} catch (Exception $e) {
    echo "   ❌ Component registration test failed: " . $e->getMessage() . "\n";
}

// Test 8: JavaScript Functionality
echo "\n8. Testing JavaScript Functionality:\n";
try {
    $jsCode = AttachmentComponentJS::renderJS();
    if (strpos($jsCode, 'class AttachmentComponent') !== false) {
        echo "   ✅ JavaScript class generated successfully\n";
    } else {
        echo "   ❌ JavaScript class generation failed\n";
    }
} catch (Exception $e) {
    echo "   ❌ JavaScript functionality test failed: " . $e->getMessage() . "\n";
}

echo "\n=== Test Summary ===\n";
echo "Attachment System Anti-Scattering Compliance Test Complete.\n";
echo "Access test_attachments.php for visual testing.\n";
echo "Access /admin/documents for production testing.\n";
?>
