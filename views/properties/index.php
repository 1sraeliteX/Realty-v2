<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$properties = ViewManager::get('properties') ?? [];
$pagination = ViewManager::get('pagination') ?? [];
$search = ViewManager::get('search') ?? '';
$type = ViewManager::get('type') ?? '';
$category = ViewManager::get('category') ?? '';
$status = ViewManager::get('status') ?? '';

// Get property type data from DataProvider
$categoryOptions = DataProvider::get('property_category_options') ?? [];
$allPropertyTypes = DataProvider::get('property_type_options') ?? [];

// Set page data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Properties');
ViewManager::set('pageTitle', 'Properties Management');
?>

<!-- Header Actions -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Properties</h1>
    <a href="/properties/create" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-plus mr-2"></i>Add Property
    </a>
</div>

<!-- Search and Filters -->
<form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <div class="relative">
                <input type="text" name="search" placeholder="Search properties..." value="<?php echo htmlspecialchars($search); ?>" 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>
        <div class="flex gap-2">
            <select name="category" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <?php foreach ($categoryOptions as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo $category === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
            <select name="type" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">All Types</option>
                <?php
                if ($category) {
                    require_once __DIR__ . '/../../config/property_type_helper.php';
                    $categoryTypes = getPropertiesByCategory($category);
                    foreach ($categoryTypes as $type): ?>
                        <option value="<?php echo $type['value']; ?>" <?php echo $type === $type['value'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($type['label']); ?></option>
                    <?php endforeach;
                } else {
                    // Show all types if no category selected
                    foreach ($allPropertyTypes as $type): ?>
                        <option value="<?php echo $type['value']; ?>" <?php echo $type === $type['value'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($type['label']); ?></option>
                    <?php endforeach;
                }
                ?>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-search mr-2"></i>Search
            </button>
        </div>
    </div>
</form>

<!-- Properties Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">All Properties</h3>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            <?php echo count($properties ?? []); ?> properties found
            <?php if (isset($pagination['total']) && $pagination['total'] > count($properties ?? [])): ?>
                (<?php echo $pagination['total']; ?> total)
            <?php endif; ?>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Property</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Units</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Occupancy</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($properties ?? [])): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-16">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                                    <i class="fas fa-home text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Properties Found</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                                    You haven't added any properties yet. Start by adding your first property.
                                </p>
                                <a href="/properties/create" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Your First Property
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($properties as $property): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                                            <i class="fas fa-building text-primary-600 dark:text-primary-300"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($property['name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo htmlspecialchars($property['address']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <?php
// Find the specific type label for display
$typeLabel = '';
foreach ($allPropertyTypes as $type) {
    if ($type['value'] === $property['type']) {
        $typeLabel = $type['label'];
        break;
    }
}
?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $property['type'] === 'residential' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                               ($property['type'] === 'commercial' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 
                                               'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'); ?>">
                                        <?php echo ucfirst($property['type']); ?>
                                    </span>
                                    <?php if ($typeLabel): ?>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1"><?php echo htmlspecialchars($typeLabel); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <div class="flex items-center">
                                    <i class="fas fa-door-open text-gray-400 mr-2"></i>
                                    <?php echo $property['unit_count']; ?> units
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-900 dark:text-white mr-3">
                                        <?php echo $property['occupied_units']; ?>/<?php echo $property['unit_count']; ?>
                                    </div>
                                    <?php if ($property['unit_count'] > 0): ?>
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                                                 style="width: <?php echo round(($property['occupied_units'] / $property['unit_count']) * 100); ?>%"></div>
                                        </div>
                                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                                            <?php echo round(($property['occupied_units'] / $property['unit_count']) * 100); ?>%
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $property['status'] === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                           ($property['status'] === 'inactive' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : 
                                           'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'); ?>">
                                    <span class="w-2 h-2 mr-1 rounded-full inline-block 
                                        <?php echo $property['status'] === 'active' ? 'bg-green-400' : 
                                               ($property['status'] === 'inactive' ? 'bg-gray-400' : 'bg-yellow-400'); ?>"></span>
                                    <?php echo ucfirst($property['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewProperty(<?php echo $property['id']; ?>)" 
                                            class="text-primary-600 hover:text-primary-900 dark:text-primary-400 transition-colors"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editProperty(<?php echo $property['id']; ?>)" 
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 transition-colors"
                                            title="Edit Property">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteProperty(<?php echo $property['id']; ?>, '<?php echo htmlspecialchars($property['name']); ?>')" 
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 transition-colors"
                                            title="Delete Property">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <div class="relative">
                                        <button onclick="toggleMenu(<?php echo $property['id']; ?>)" 
                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                                title="More Options">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="menu-<?php echo $property['id']; ?>" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10">
                                            <a href="/properties/<?php echo $property['id']; ?>/units" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <i class="fas fa-door-open mr-2"></i>Manage Units
                                            </a>
                                            <a href="/properties/<?php echo $property['id']; ?>/tenants" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <i class="fas fa-users mr-2"></i>View Tenants
                                            </a>
                                            <a href="/properties/<?php echo $property['id']; ?>/payments" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <i class="fas fa-dollar-sign mr-2"></i>Payment History
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <?php if ($pagination['has_prev']): ?>
                    <a href="?page=<?php echo $pagination['current_page'] - 1; ?><?php echo !empty($search) ? '&search=' . urlencode((string)$search) : ''; ?><?php echo !empty($type) ? '&type=' . urlencode((string)$type) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode((string)$category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode((string)$status) : ''; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">Previous</a>
                <?php endif; ?>
                <?php if ($pagination['has_next']): ?>
                    <a href="?page=<?php echo $pagination['current_page'] + 1; ?><?php echo !empty($search) ? '&search=' . urlencode((string)$search) : ''; ?><?php echo !empty($type) ? '&type=' . urlencode((string)$type) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode((string)$category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode((string)$status) : ''; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">Next</a>
                <?php endif; ?>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium"><?php echo ($pagination['current_page'] - 1) * $pagination['per_page'] + 1; ?></span> to 
                        <span class="font-medium"><?php echo min($pagination['current_page'] * $pagination['per_page'], $pagination['total']); ?></span> of 
                        <span class="font-medium"><?php echo $pagination['total']; ?></span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <?php if ($pagination['has_prev']): ?>
                            <a href="?page=<?php echo $pagination['current_page'] - 1; ?><?php echo !empty($search) ? '&search=' . urlencode((string)$search) : ''; ?><?php echo !empty($type) ? '&type=' . urlencode((string)$type) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode((string)$category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode((string)$status) : ''; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode((string)$search) : ''; ?><?php echo !empty($type) ? '&type=' . urlencode((string)$type) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode((string)$category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode((string)$status) : ''; ?>" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium 
                                <?php echo $i === $pagination['current_page'] ? 
                                    'z-10 bg-primary-50 border-primary-500 text-primary-600 dark:bg-primary-900 dark:border-primary-400 dark:text-primary-300' : 
                                    'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <a href="?page=<?php echo $pagination['current_page'] + 1; ?><?php echo !empty($search) ? '&search=' . urlencode((string)$search) : ''; ?><?php echo !empty($type) ? '&type=' . urlencode((string)$type) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode((string)$category) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode((string)$status) : ''; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">Next</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript for interactive functionality -->
<script>
function viewProperty(id) {
    window.location.href = '/properties/' + id;
}

function editProperty(id) {
    window.location.href = '/properties/' + id + '/edit';
}

function deleteProperty(id, name) {
    if (confirm('Are you sure you want to delete "' + name + '"? This action cannot be undone.')) {
        // Create form for deletion
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/properties/' + id + '/delete';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleMenu(id) {
    const menu = document.getElementById('menu-' + id);
    
    // Close all other menus
    document.querySelectorAll('[id^="menu-"]').forEach(m => {
        if (m.id !== 'menu-' + id) {
            m.classList.add('hidden');
        }
    });
    
    // Toggle current menu
    menu.classList.toggle('hidden');
    
    // Close menu when clicking outside
    document.addEventListener('click', function closeMenu(e) {
        if (!e.target.closest('#menu-' + id) && !e.target.closest('button')) {
            menu.classList.add('hidden');
            document.removeEventListener('click', closeMenu);
        }
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects for table rows
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(2px)';
            this.style.transition = 'transform 0.2s ease';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});
</script>
