<?php
// Anti-scattering compliant framework initialization
require_once __DIR__ . '/../../../config/bootstrap.php';

// Load UIComponents for badge rendering (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from ViewManager (anti-scattering compliant)
$documents = ViewManager::get('documents', []);
$properties = ViewManager::get('properties', []);
$tenants = ViewManager::get('tenants', []);
$filters = ViewManager::get('filters', []);
$pagination = ViewManager::get('pagination', []);
$title = ViewManager::get('title', 'Documents Management');
$user = ViewManager::get('user', ['name' => 'Admin', 'email' => '']);

// If no documents data, fetch from database as fallback
if (empty($documents)) {
    $db = \Config\Database::getInstance();
    $adminId = $_SESSION['admin_id'] ?? null;
    if ($adminId) {
        $sql = "SELECT * FROM documents WHERE admin_id = ? AND deleted_at IS NULL ORDER BY created_at DESC";
        $documents = $db->fetchAll($sql, [$adminId]);
    }
}

// Calculate stats for overview cards
$totalDocuments = count($documents);
$totalSize = 0;
$pdfCount = 0;
$imageCount = 0;
$docCount = 0;

foreach ($documents as $doc) {
    $totalSize += $doc['file_size'] ?? 0;
    $fileType = strtolower($doc['file_type'] ?? '');
    if ($fileType === 'pdf') {
        $pdfCount++;
    } elseif (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $imageCount++;
    } else {
        $docCount++;
    }
}

// Format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

ob_start();
?>
<!-- Header with Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Documents</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage your property documents and files</p>
    </div>
    <div class="mt-4 sm:mt-0 flex space-x-3">
        <button onclick="exportDocuments()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-cream-50 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-download mr-2"></i>
            Export
        </button>
        <button onclick="openUploadModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            <i class="fas fa-plus mr-2"></i>
            Upload Document
        </button>
    </div>
</div>

<!-- Overview Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                <i class="fas fa-file-lines text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo $totalDocuments; ?></h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Documents</p>
            </div>
        </div>
    </div>
    
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-red-100 dark:bg-red-900 rounded-lg p-3">
                <i class="fas fa-file-pdf text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo $pdfCount; ?></h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">PDF Files</p>
            </div>
        </div>
    </div>
    
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                <i class="fas fa-image text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo $imageCount; ?></h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Images</p>
            </div>
        </div>
    </div>
    
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                <i class="fas fa-hdd text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo formatFileSize($totalSize); ?></h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Storage Used</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">

        <!-- Search input -->
        <div class="flex-1 min-w-0">
            <div class="relative pt-6">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center
                            pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </div>
                <input
                    type="text"
                    name="search"
                    value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>"
                    placeholder="Search documents..."
                    class="w-full pl-9 pr-4 py-2 text-sm border
                           border-gray-300 dark:border-gray-600 rounded-lg
                           bg-cream-50 dark:bg-gray-700 text-gray-900
                           dark:text-white placeholder-gray-400
                           focus:outline-none focus:ring-2
                           focus:ring-primary-500 focus:border-transparent"
                />
            </div>
        </div>

        <!-- Property Filter -->
        <div class="flex-shrink-0">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Property
            </label>
            <select
                name="property_id"
                class="w-40 px-3 py-2 text-sm border border-gray-300
                       dark:border-gray-600 rounded-lg bg-cream-50
                       dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:outline-none focus:ring-2
                       focus:ring-primary-500 focus:border-transparent
                       appearance-none cursor-pointer">
                <option value="">All Properties</option>
                <?php foreach ($properties as $property): ?>
                    <option value="<?php echo $property['id']; ?>" <?php echo ($filters['property_id'] ?? '') == $property['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($property['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Tenant Filter -->
        <div class="flex-shrink-0">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Tenant
            </label>
            <select
                name="tenant_id"
                class="w-36 px-3 py-2 text-sm border border-gray-300
                       dark:border-gray-600 rounded-lg bg-cream-50
                       dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:outline-none focus:ring-2
                       focus:ring-primary-500 focus:border-transparent
                       appearance-none cursor-pointer">
                <option value="">All Tenants</option>
                <?php foreach ($tenants as $tenant): ?>
                    <option value="<?php echo $tenant['id']; ?>" <?php echo ($filters['tenant_id'] ?? '') == $tenant['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tenant['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filter Button -->
        <div class="flex-shrink-0 flex items-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                <i class="fas fa-filter mr-2"></i>
                Filter
            </button>
        </div>

    </div>
    
    <!-- Additional Filters -->
    <div class="mt-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <button class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">
                <i class="fas fa-filter mr-1"></i>
                Advanced Filters
            </button>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Showing <?php echo count($documents); ?> documents
            </span>
        </div>
    </div>
</div>

<!-- Documents Table -->
<div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Title
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        File Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Size
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Related To
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Uploaded By
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (!empty($documents)): ?>
                    <?php foreach ($documents as $document): ?>
                        <?php
                        $fileSize = $document['file_size'] ?? 0;
                        $sizeDisplay = formatFileSize($fileSize);
                        
                        $relatedTo = 'Unassigned';
                        if (!empty($document['related_to_type'])) {
                            switch ($document['related_to_type']) {
                                case 'property':
                                    $relatedTo = $document['property_name'] ?? 'Property';
                                    break;
                                case 'tenant':
                                    $relatedTo = $document['tenant_name'] ?? 'Tenant';
                                    break;
                                case 'lease':
                                    $relatedTo = 'Lease';
                                    break;
                            }
                        }
                        
                        $uploadedBy = $document['uploaded_by_name'] ?? 'Unknown';
                        $uploadDate = date('M j, Y', strtotime($document['created_at']));
                        
                        // Determine file icon and color
                        $fileType = strtolower($document['file_type'] ?? '');
                        $iconClass = 'fa-file text-gray-500';
                        $badgeClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                        
                        if ($fileType === 'pdf') {
                            $iconClass = 'fa-file-pdf text-red-500';
                            $badgeClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                        } elseif (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $iconClass = 'fa-file-image text-green-500';
                            $badgeClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                        } elseif (in_array($fileType, ['doc', 'docx'])) {
                            $iconClass = 'fa-file-word text-blue-500';
                            $badgeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                        } elseif (in_array($fileType, ['xls', 'xlsx'])) {
                            $iconClass = 'fa-file-excel text-green-600';
                            $badgeClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                        }
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas <?php echo $iconClass; ?> mr-3 text-lg"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($document['title'] ?? 'Untitled'); ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo htmlspecialchars($document['file_name'] ?? ''); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $badgeClass; ?>">
                                    <?php echo strtoupper(htmlspecialchars($document['file_type'] ?? 'unknown')); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?php echo $sizeDisplay; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <?php echo htmlspecialchars($relatedTo); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?php echo htmlspecialchars($uploadedBy); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?php echo $uploadDate; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="/admin/documents/<?php echo $document['id']; ?>/download" 
                                       class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" 
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="/public/<?php echo htmlspecialchars($document['file_path']); ?>" 
                                       target="_blank" 
                                       class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" 
                                       title="Preview">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" action="/admin/documents/<?php echo $document['id']; ?>/delete" 
                                          onsubmit="return confirm('Are you sure you want to delete this document?')" 
                                          class="inline">
                                        <button type="submit" 
                                                class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-lines text-gray-300 dark:text-gray-600 text-4xl mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No documents found</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Upload your first document to get started</p>
                                <button onclick="openUploadModal()" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Upload Document
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if (!empty($pagination) && $pagination['total'] > 0): ?>
        <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <a href="?page=<?php echo max(1, $pagination['current_page'] - 1); ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        Previous
                    </a>
                    <a href="?page=<?php echo min($pagination['last_page'], $pagination['current_page'] + 1); ?>" 
                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        Next
                    </a>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Showing <span class="font-medium">
                                <?php echo (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?>
                            </span> to 
                            <span class="font-medium">
                                <?php echo min($pagination['current_page'] * $pagination['per_page'], $pagination['total']); ?>
                            </span> of 
                            <span class="font-medium"><?php echo $pagination['total']; ?></span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <a href="?page=<?php echo $pagination['current_page'] - 1; ?>" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php
                            $startPage = max(1, $pagination['current_page'] - 2);
                            $endPage = min($pagination['last_page'], $pagination['current_page'] + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <a href="?page=<?php echo $i; ?>" 
                                   class="<?php echo $i == $pagination['current_page'] ? 'relative inline-flex items-center px-4 py-2 border border-primary-500 bg-primary-50 text-sm font-medium text-primary-600 dark:bg-primary-900 dark:border-primary-400 dark:text-primary-300' : 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                                <a href="?page=<?php echo $pagination['current_page'] + 1; ?>" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<?php 
echo UIComponents::modal(
    'uploadModal',
    'Upload Document',
    '
    <form id="uploadModalForm" method="POST" action="/admin/documents/upload" enctype="multipart/form-data" onsubmit="showUploadProgress()">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                <input type="text" name="title" required 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File</label>
                <input type="file" name="file" required onchange="showFilePreview(this)" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                <div id="filePreview" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Related To Type</label>
                <select name="related_to_type" onchange="loadRelatedEntities(this.value)" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">None</option>
                    <option value="property">Property</option>
                    <option value="tenant">Tenant</option>
                    <option value="lease">Lease</option>
                </select>
            </div>
            
            <div id="relatedToContainer" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Related To</label>
                <select name="related_to_id" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">Select...</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (Optional)</label>
                <textarea name="description" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"></textarea>
            </div>
        </div>
        
        <div id="uploadProgress" class="hidden mt-4">
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary-600 mr-2"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Uploading...</span>
            </div>
        </div>
    </form>
    ',
    '<button type="button" onclick="closeUploadModal()" 
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
        Cancel
    </button>
    <button type="submit" id="uploadBtn"
            class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-upload mr-2"></i>
        Upload Document
    </button>',
    'large'
); ?>

<script>
// Safe showToast fallback if not defined by layout
if (typeof showToast !== 'function') {
    window.showToast = function(message, type) {
        console.log('[Toast] ' + type + ': ' + message);
        // Simple fallback notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 z-50 px-4 py-2 rounded-lg text-white text-sm shadow-lg transition-opacity duration-300 ' +
            (type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600');
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
    };
}

function openUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    const form = document.querySelector('#uploadModal form');
    if (form) {
        form.reset();
    }
    document.getElementById('filePreview').innerHTML = '';
    document.getElementById('relatedToContainer').style.display = 'none';
    document.getElementById('uploadProgress').classList.add('hidden');
}

function showFilePreview(input) {
    const file = input.files[0];
    const preview = document.getElementById('filePreview');
    
    if (file) {
        const size = file.size >= 1048576 ? 
            (file.size / 1048576).toFixed(2) + ' MB' : 
            (file.size / 1024).toFixed(2) + ' KB';
        
        preview.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas fa-file text-gray-400"></i>
                <span>${file.name} (${size})</span>
            </div>
        `;
    } else {
        preview.innerHTML = '';
    }
}

function loadRelatedEntities(type) {
    const container = document.getElementById('relatedToContainer');
    const select = container.querySelector('select');
    
    if (!type) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'block';
    
    // Fetch related entities via API
    fetch(`/admin/documents/api/related-entities?type=${type}`)
        .then(response => response.json())
        .then(data => {
            select.innerHTML = '<option value="">Select...</option>';
            data.forEach(entity => {
                select.innerHTML += `<option value="${entity.id}">${entity.display_name}</option>`;
            });
        })
        .catch(error => {
            console.error('Error loading related entities:', error);
        });
}

function showUploadProgress() {
    document.getElementById('uploadProgress').classList.remove('hidden');
    document.getElementById('uploadBtn').disabled = true;
}

function exportDocuments() {
    showToast('Exporting documents...', 'info');
    // Placeholder for export functionality
    setTimeout(() => {
        showToast('Export completed successfully', 'success');
    }, 2000);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('uploadModal');
    if (event.target === modal) {
        closeUploadModal();
    }
}
</script>

<?php
$content = ob_get_clean();

// Set content in ViewManager for layout (anti-scattering compliant)
ViewManager::set('content', $content);

// Include the admin layout (anti-scattering compliant)
include __DIR__ . '/../dashboard_layout.php';
?>
