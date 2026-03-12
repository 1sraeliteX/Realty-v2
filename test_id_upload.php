<?php
// Test file to verify ID upload functionality
require_once __DIR__ . '/config/bootstrap.php';

// Test AttachmentComponent
ComponentRegistry::load('attachment-component');

echo "<h1>ID Upload Test</h1>";

// Test tenant upload
echo "<h2>Tenant ID Upload</h2>";
echo AttachmentComponent::renderUploadArea([
    'id' => 'test-tenant-upload',
    'name' => 'tenant_docs[]',
    'accept' => '.jpg,.jpeg,.png,.pdf',
    'max_size' => 5,
    'max_files' => 2,
    'preview' => true,
    'class' => 'test-upload'
]);

// Test occupant upload
echo "<h2>Occupant ID Upload</h2>";
echo AttachmentComponent::renderUploadArea([
    'id' => 'test-occupant-upload',
    'name' => 'occupant_docs[]',
    'accept' => '.jpg,.jpeg,.png,.pdf',
    'max_size' => 5,
    'max_files' => 2,
    'preview' => true,
    'class' => 'test-upload'
]);

// Include JavaScript
echo AttachmentComponentJS::renderJS();
echo AttachmentComponent::renderPreviewModal();
?>
