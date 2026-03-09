<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Document Details';
$pageTitle = 'Document Details';
$pageDescription = 'View comprehensive document information and manage file details';

// Mock document data
$document = [
    'id' => 1,
    'name' => 'Lease Agreement - John Smith',
    'description' => 'Standard 12-month lease agreement for residential unit 101 at Sunset Apartments. Includes all terms and conditions, rent amount, security deposit, and tenant responsibilities.',
    'type' => 'lease',
    'category' => 'legal',
    'file_name' => 'lease_agreement_john_smith_2024.pdf',
    'file_size' => 2456789,
    'file_type' => 'application/pdf',
    'file_path' => '/uploads/documents/leases/',
    'tenant_id' => 1,
    'tenant_name' => 'John Smith',
    'property_id' => 1,
    'property_name' => 'Sunset Apartments',
    'unit_id' => 1,
    'unit_number' => '101',
    'uploaded_by' => 'Admin User',
    'upload_date' => '2023-01-10 14:30:00',
    'last_modified' => '2023-01-10 14:30:00',
    'expiry_date' => '2024-01-14',
    'status' => 'active',
    'tags' => ['lease', '2024', 'unit-101', 'john-smith'],
    'access_level' => 'restricted',
    'version' => 1,
    'is_signed' => true,
    'signature_date' => '2023-01-15',
    'download_count' => 12,
    'shared_with' => [
        ['id' => 1, 'name' => 'John Smith', 'email' => 'john.smith@email.com', 'permission' => 'view'],
        ['id' => 2, 'name' => 'Property Manager', 'email' => 'manager@cornerstone.com', 'permission' => 'edit']
    ]
];

// Mock document versions
$versions = [
    ['version' => 1, 'uploaded_by' => 'Admin User', 'upload_date' => '2023-01-10 14:30:00', 'file_size' => 2456789, 'notes' => 'Initial lease agreement'],
];

// Mock activity log
$activityLog = [
    [
        'id' => 1,
        'action' => 'uploaded',
        'description' => 'Document uploaded by Admin User',
        'user' => 'Admin User',
        'date' => '2023-01-10 14:30:00'
    ],
    [
        'id' => 2,
        'action' => 'signed',
        'description' => 'Document signed by John Smith',
        'user' => 'John Smith',
        'date' => '2023-01-15 10:00:00'
    ],
    [
        'id' => 3,
        'action' => 'viewed',
        'description' => 'Document viewed by Property Manager',
        'user' => 'Property Manager',
        'date' => '2023-12-01 09:15:00'
    ],
    [
        'id' => 4,
        'action' => 'downloaded',
        'description' => 'Document downloaded by John Smith',
        'user' => 'John Smith',
        'date' => '2024-01-05 14:30:00'
    ]
];

ob_start();
?>

<!-- Document Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($document['name']); ?></h1>
                <p class="text-indigo-100"><?php echo htmlspecialchars($document['tenant_name']); ?> • Unit <?php echo htmlspecialchars($document['unit_number']); ?></p>
                <p class="text-indigo-100"><?php echo htmlspecialchars($document['property_name']); ?></p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-indigo-100 text-sm">File Size</p>
                    <p class="text-lg font-bold text-white"><?php echo number_format($document['file_size'] / 1024 / 1024, 2); ?> MB</p>
                </div>
                <?php echo UIComponents::badge(ucfirst($document['status']), $document['status'] === 'active' ? 'success' : 'warning', 'large'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Document Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Type', ucfirst($document['type']), 'file', 'blue', '', ucfirst($document['category'])); ?>
    <?php echo UIComponents::statCard('Uploaded', date('M j, Y', strtotime($document['upload_date'])), 'calendar', 'green', '', 'By ' . $document['uploaded_by']); ?>
    <?php echo UIComponents::statCard('Downloads', $document['download_count'], 'download', 'purple', '', 'Total views'); ?>
    <?php echo UIComponents::statCard('Expires', date('M j, Y', strtotime($document['expiry_date'])), 'clock', 'orange', '', 'In ' . max(0, (strtotime($document['expiry_date']) - time()) / 86400) . ' days'); ?>
</div>

<!-- Document Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Document Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Document Information</h3>
            
            <div class="space-y-4">
                <!-- Description -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Description</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($document['description']); ?></p>
                </div>

                <!-- File Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">File Details</h4>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">File Name</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo htmlspecialchars($document['file_name']); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">File Type</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo htmlspecialchars($document['file_type']); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">File Size</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo number_format($document['file_size'] / 1024 / 1024, 2); ?> MB</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Dates</h4>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Upload Date</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo date('M j, Y H:i', strtotime($document['upload_date'])); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Last Modified</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo date('M j, Y H:i', strtotime($document['last_modified'])); ?></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-xs text-gray-500 dark:text-gray-400">Expiry Date</dt>
                                <dd class="text-xs text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($document['expiry_date'])); ?></dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Tags -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Tags</h4>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($document['tags'] as $tag): ?>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-full">
                                <?php echo htmlspecialchars($tag); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex space-x-4">
                <?php echo UIComponents::button('Download', 'primary', 'medium', '#', 'download'); ?>
                <?php echo UIComponents::button('Print', 'info', 'medium', '#', 'print'); ?>
                <?php echo UIComponents::button('Share', 'secondary', 'medium', '#', 'share'); ?>
                <?php echo UIComponents::button('Edit', 'warning', 'medium', '/admin/documents/' . $document['id'] . '/edit', 'edit'); ?>
            </div>
        </div>

        <!-- Document Preview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Document Preview</h3>
            
            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-8 text-center">
                <i class="fas fa-file-pdf text-6xl text-red-500 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400 mb-4">PDF Document Preview</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6"><?php echo htmlspecialchars($document['file_name']); ?></p>
                <?php echo UIComponents::button('Open Full Preview', 'primary', 'medium', '#', 'external-link-alt'); ?>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activity Log</h3>
            
            <div class="space-y-4">
                <?php foreach ($activityLog as $activity): ?>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-<?php echo $activity['action'] === 'uploaded' ? 'green' : ($activity['action'] === 'signed' ? 'blue' : ($activity['action'] === 'viewed' ? 'yellow' : 'purple')); ?>-100 dark:bg-<?php echo $activity['action'] === 'uploaded' ? 'green' : ($activity['action'] === 'signed' ? 'blue' : ($activity['action'] === 'viewed' ? 'yellow' : 'purple')); ?>-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-<?php echo $activity['action'] === 'uploaded' ? 'upload' : ($activity['action'] === 'signed' ? 'signature' : ($activity['action'] === 'viewed' ? 'eye' : 'download')); ?> text-<?php echo $activity['action'] === 'uploaded' ? 'green' : ($activity['action'] === 'signed' ? 'blue' : ($activity['action'] === 'viewed' ? 'yellow' : 'purple')); ?>-600 dark:text-<?php echo $activity['action'] === 'uploaded' ? 'green' : ($activity['action'] === 'signed' ? 'blue' : ($activity['action'] === 'viewed' ? 'yellow' : 'purple')); ?>-400 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($activity['description']); ?></p>
                                <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y H:i', strtotime($activity['date'])); ?></span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">by <?php echo htmlspecialchars($activity['user']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Tenant Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Associated Tenant</h3>
            <div class="text-center mb-4">
                <?php echo UIComponents::avatar($document['tenant_name'], null, 'large'); ?>
                <h4 class="mt-3 text-lg font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($document['tenant_name']); ?></h4>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Property</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($document['property_name']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Unit</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($document['unit_number']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Signed</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo $document['is_signed'] ? 'Yes' : 'No'; ?></span>
                </div>
                <?php if ($document['is_signed']): ?>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Signature Date</span>
                        <span class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($document['signature_date'])); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-4">
                <?php echo UIComponents::button('View Tenant', 'primary', 'small', '/admin/tenants/' . $document['tenant_id'], 'user'); ?>
            </div>
        </div>

        <!-- Document Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Document Settings</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Access Level</p>
                    <div class="mt-1"><?php echo UIComponents::badge(ucfirst($document['access_level']), 
                        $document['access_level'] === 'restricted' ? 'warning' : 'success'); ?></div>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Version</p>
                    <p class="text-sm text-gray-900 dark:text-white">v<?php echo $document['version']; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <div class="mt-1"><?php echo UIComponents::badge(ucfirst($document['status']), 
                        $document['status'] === 'active' ? 'success' : 'warning'); ?></div>
                </div>
            </div>
        </div>

        <!-- Shared With -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shared With</h3>
            <div class="space-y-3">
                <?php foreach ($document['shared_with'] as $share): ?>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($share['name']); ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($share['email']); ?></p>
                        </div>
                        <div><?php echo UIComponents::badge(ucfirst($share['permission']), 'info'); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-4">
                <?php echo UIComponents::button('Share Document', 'primary', 'small', '#', 'share'); ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <?php echo UIComponents::button('Upload New Version', 'success', 'full', '#', 'upload'); ?>
                <?php echo UIComponents::button('Duplicate Document', 'info', 'full', '#', 'copy'); ?>
                <?php echo UIComponents::button('Move to Folder', 'warning', 'full', '#', 'folder'); ?>
                <?php echo UIComponents::button('Delete Document', 'danger', 'full', '#', 'trash'); ?>
            </div>
        </div>
    </div>
</div>

<script>
// Download document
function downloadDocument() {
    showToast('Downloading document...', 'info');
    setTimeout(() => {
        showToast('Document downloaded successfully!', 'success');
    }, 2000);
}

// Share document
function shareDocument() {
    showToast('Opening share dialog...', 'info');
}

// Upload new version
function uploadNewVersion() {
    showToast('Opening file uploader...', 'info');
}

// Duplicate document
function duplicateDocument() {
    if (confirm('Create a duplicate of this document?')) {
        showToast('Duplicating document...', 'info');
        setTimeout(() => {
            showToast('Document duplicated successfully!', 'success');
        }, 2000);
    }
}

// Delete document
function deleteDocument() {
    if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        showToast('Deleting document...', 'info');
        setTimeout(() => {
            showToast('Document deleted successfully!', 'success');
            // Redirect to documents list
            window.location.href = '/admin/documents';
        }, 2000);
    }
}

// Print document
function printDocument() {
    window.print();
}
</script>

<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
