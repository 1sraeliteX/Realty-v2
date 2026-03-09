<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Upload Document';
$pageTitle = 'Add Document';

$content = ob_start();
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Upload Document</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Add a new document to the system</p>
            </div>
            <a href="/admin/documents" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Documents
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="/admin/documents" enctype="multipart/form-data" class="space-y-6">
        <!-- Document Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Document Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Name *</label>
                    <input type="text" name="document_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                    <select name="category" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Category</option>
                        <option value="lease">Lease</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="insurance">Insurance</option>
                        <option value="legal">Legal</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property</label>
                    <select name="property_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Property</option>
                        <option value="1">Sunset Apartments</option>
                        <option value="2">Downtown Plaza</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tenant</label>
                    <select name="tenant_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Tenant</option>
                        <option value="1">John Doe</option>
                        <option value="2">Jane Smith</option>
                    </select>
                </div>
            </div>
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File *</label>
                <input type="file" name="document_file" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 10MB)</p>
            </div>
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"></textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <a href="/admin/documents" class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <i class="fas fa-upload mr-2"></i>
                Upload Document
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();

// Include the admin dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
