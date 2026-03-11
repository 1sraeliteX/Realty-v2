<?php
// Initialize framework
require_once __DIR__ . '/../../config/init_framework.php';

// Load attachment component
ComponentRegistry::load('attachment-component');

// Get data from DataProvider
$documents = DataProvider::get('documents');

// Set view data
ViewManager::set('title', 'Documents Management');
ViewManager::set('pageTitle', 'Documents');
ViewManager::set('documents', $documents);

$content = ob_start();
?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Documents Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Upload and manage property documents</p>
            </div>
            <a href="/admin/documents/create" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <i class="fas fa-plus mr-2"></i>
                Upload Document
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                    <i class="fas fa-file text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Documents</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">234</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                    <i class="fas fa-home text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Property Docs</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">156</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                    <i class="fas fa-users text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tenant Docs</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">78</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                    <i class="fas fa-database text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Storage Used</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">2.4 GB</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Grid -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Documents</h2>
            <div class="flex items-center space-x-3">
                <select class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="all">All Categories</option>
                    <option value="lease">Lease</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="insurance">Insurance</option>
                    <option value="legal">Legal</option>
                </select>
                <select class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="all">All Properties</option>
                    <option value="1">Sunset Apartments</option>
                    <option value="2">Oak Villa Complex</option>
                    <option value="3">Downtown Office Building</option>
                </select>
            </div>
        </div>
        
        <?php echo AttachmentComponent::renderAttachmentsList($documents, [
            'show_preview' => true,
            'show_download' => true,
            'show_delete' => true,
            'grid_view' => true
        ]); ?>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include JavaScript for attachment functionality
echo AttachmentComponentJS::renderJS();

// Render preview modal
echo AttachmentComponent::renderPreviewModal();

// Include the admin dashboard layout
echo ViewManager::render('admin.dashboard_layout', ['content' => $content]);
?>
