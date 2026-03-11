<?php
// Initialize framework
require_once __DIR__ . '/config/init_framework.php';

// Load attachment component
ComponentRegistry::load('attachment-component');

// Get data from DataProvider
$documents = DataProvider::get('documents');
$properties = DataProvider::get('properties');
$tenants = DataProvider::get('tenants');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attachment System Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/fontawesome.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Attachment System Test</h1>
            <p class="text-gray-600 dark:text-gray-400">Test the attachment upload, preview, and delete functionality</p>
        </div>

        <!-- Upload Test Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Upload Test</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Test the drag-and-drop upload functionality:</p>
            
            <?php echo AttachmentComponent::renderUploadArea([
                'id' => 'test-upload',
                'name' => 'test_files[]',
                'multiple' => true,
                'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png,.gif',
                'max_size' => 5,
                'max_files' => 3,
                'preview' => true
            ]); ?>
        </div>

        <!-- Existing Attachments Test -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Existing Attachments Test</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Test preview, download, and delete functionality:</p>
            
            <?php echo AttachmentComponent::renderAttachmentsList($documents, [
                'show_preview' => true,
                'show_download' => true,
                'show_delete' => true,
                'grid_view' => true
            ]); ?>
        </div>

        <!-- List View Test -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">List View Test</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Test compact list view:</p>
            
            <?php echo AttachmentComponent::renderAttachmentsList(array_slice($documents, 0, 3), [
                'show_preview' => false,
                'show_download' => true,
                'show_delete' => true,
                'grid_view' => false
            ]); ?>
        </div>

        <!-- Form Integration Test -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Form Integration Test</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Test integration with forms:</p>
            
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Title</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Enter document title">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property</label>
                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Property</option>
                        <?php foreach ($properties as $property): ?>
                        <option value="<?php echo $property['id']; ?>"><?php echo htmlspecialchars($property['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php echo AttachmentComponent::renderUploadArea([
                    'id' => 'form-upload',
                    'name' => 'form_documents[]',
                    'multiple' => true,
                    'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png,.gif',
                    'max_size' => 10,
                    'max_files' => 5,
                    'preview' => true
                ]); ?>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Submit Form
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Include JavaScript -->
    <?php echo AttachmentComponentJS::renderJS(); ?>
    
    <!-- Render Preview Modal -->
    <?php echo AttachmentComponent::renderPreviewModal(); ?>

    <script>
        // Test form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Form submission test - attachments would be processed here');
        });

        // Add dark mode toggle for testing
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.createElement('button');
            darkModeToggle.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
            darkModeToggle.className = 'fixed top-4 right-4 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 z-50';
            darkModeToggle.onclick = function() {
                document.documentElement.classList.toggle('dark');
                this.innerHTML = document.documentElement.classList.contains('dark') ? 
                    '<i class="fas fa-sun"></i> Light Mode' : 
                    '<i class="fas fa-moon"></i> Dark Mode';
            };
            document.body.appendChild(darkModeToggle);
        });
    </script>
</body>
</html>
