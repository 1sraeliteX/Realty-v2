<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get documents data from controller/DataProvider (anti-scattering compliant)
$documents = ViewManager::get('documents') ?? DataProvider::get('documents');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Documents Management');
ViewManager::set('user', DataProvider::get('user'));
ViewManager::set('notifications', DataProvider::get('notifications'));

ob_start();
?>

<!-- Documents Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php echo UIComponents::statsCard('Total Documents', count($documents), 'file-lines', 12.5, 'primary'); ?>
    <?php echo UIComponents::statsCard('Property Docs', '156', 'home', 8.3, 'blue'); ?>
    <?php echo UIComponents::statsCard('Tenant Docs', '78', 'users', 15.2, 'green'); ?>
    <?php echo UIComponents::statsCard('Storage Used', '2.4 GB', 'database', 2.1, 'yellow'); ?>
</div>

<!-- Documents Management Section -->
<div class="grid grid-cols-1 lg:grid-cols-1 gap-8 mb-8">
    <?php 
    $documentsContent = '
        <!-- Header with Actions -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Documents Management</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Upload and manage property documents</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="/admin/documents/create" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Upload Document
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" placeholder="Search documents..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
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

        <!-- Documents Table -->
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Document Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Category
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Property
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Size
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Upload Date
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
    ';

    if (!empty($documents)) {
        foreach ($documents as $document) {
            $type = $document['type'] ?? 'Unknown';
            $name = $document['name'] ?? 'Unknown Document';
            $category = $document['category'] ?? 'General';
            $property = $document['property'] ?? 'Unassigned';
            $size = $document['size'] ?? '0 KB';
            $uploadDate = $document['upload_date'] ?? 'Unknown';
            
            $iconClass = $type === 'PDF' ? 'fa-file-pdf text-red-500' : 
                        ($type === 'DOCX' ? 'fa-file-word text-blue-500' : 'fa-file text-gray-500');
            
            $documentsContent .= '
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas ' . $iconClass . ' mr-3"></i>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">' . htmlspecialchars($name) . '</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">' . htmlspecialchars($type) . '</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                ' . htmlspecialchars($category) . '
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            ' . htmlspecialchars($property) . '
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            ' . htmlspecialchars($size) . '
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            ' . htmlspecialchars($uploadDate) . '
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
            ';
        }
    } else {
        $documentsContent .= '
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i class="fas fa-file-lines text-gray-300 dark:text-gray-600 text-4xl mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">No documents found</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Upload your first document to get started</p>
                        </td>
                    </tr>
        ';
    }

    $documentsContent .= '
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex justify-between sm:hidden">
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                    Previous
                </a>
                <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                    Next
                </a>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">' . count($documents) . '</span> of <span class="font-medium">' . count($documents) . '</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="#" aria-current="page" class="relative inline-flex items-center px-4 py-2 border border-primary-500 bg-primary-50 text-sm font-medium text-primary-600 dark:bg-primary-900 dark:border-primary-400 dark:text-primary-300">
                            1
                        </a>
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    ';

    echo UIComponents::card(
        $documentsContent,
        null,
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    );
    ?>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <?php 
    echo UIComponents::card(
        '<div class="space-y-4">
            <a href="/admin/documents/create" class="block p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-2">
                        <i class="fas fa-upload text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Upload Document</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Add new documents</p>
                    </div>
                </div>
            </a>
            <a href="/admin/documents/categories" class="block p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-2">
                        <i class="fas fa-folder text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Manage Categories</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Organize document types</p>
                    </div>
                </div>
            </a>
            <a href="/admin/documents/storage" class="block p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-2">
                        <i class="fas fa-hdd text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Storage Settings</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Manage storage limits</p>
                    </div>
                </div>
            </a>
        </div>',
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    );
    ?>

    <?php 
    echo UIComponents::card(
        '<div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Storage Used</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">2.4 GB / 10 GB</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-primary-600 h-2 rounded-full" style="width: 24%"></div>
            </div>
            <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">PDF Files</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">1.2 GB</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Images</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">856 MB</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Other</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">344 MB</span>
                </div>
            </div>
        </div>',
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">Storage Overview</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    );
    ?>

    <?php 
    echo UIComponents::card(
        '<div class="space-y-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-2">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">All Documents Synced</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Last sync: 2 minutes ago</p>
                </div>
            </div>
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-2">
                    <i class="fas fa-shield-alt text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Backup Enabled</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Daily automatic backups</p>
                </div>
            </div>
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-2">
                    <i class="fas fa-lock text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Security Active</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Encryption enabled</p>
                </div>
            </div>
        </div>',
        '<h3 class="text-lg font-medium text-gray-900 dark:text-white">System Status</h3>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    );
    ?>
</div>

<?php
$content = ob_get_clean();

// Include the admin dashboard layout (anti-scattering compliant)
echo ViewManager::render('admin.dashboard_layout', ['content' => $content]);
?>
