<?php
$title = 'Units Management';
$pageTitle = 'Units Management';

// Initialize variables with default values
$stats = $stats ?? [
    'total_units' => 0,
    'occupied_units' => 0,
    'vacant_units' => 0,
    'occupancy_rate' => 0
];

$units = $units ?? [];
$pagination = $pagination ?? [
    'current_page' => 1,
    'per_page' => 12,
    'total' => 0,
    'total_pages' => 1,
    'has_next' => false,
    'has_prev' => false
];

$properties = $properties ?? [];
$unitTypes = $unitTypes ?? [];
$search = $search ?? '';
$status = $status ?? 'all';
$type = $type ?? 'all';
$property_id = $property_id ?? 'all';
$view = $currentView ?? 'grid';

$content = ob_start();
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                <i class="fas fa-building text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Units</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo arr_format($stats, 'total_units'); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                <i class="fas fa-door-open text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Vacant</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo arr_format($stats, 'vacant_units'); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                <i class="fas fa-check-circle text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupied</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo arr_format($stats, 'occupied_units'); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                <i class="fas fa-percentage text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupancy Rate</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo arr_get($stats, 'occupancy_rate', 0); ?>%</p>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Search Bar -->
        <div class="flex-1 max-w-md">
            <div class="relative">
                <input 
                    type="text" 
                    id="search-input"
                    placeholder="Search units..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Status Filter -->
            <select id="status-filter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Status</option>
                <option value="available" <?php echo $status === 'available' ? 'selected' : ''; ?>>Available</option>
                <option value="occupied" <?php echo $status === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                <option value="maintenance" <?php echo $status === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                <option value="reserved" <?php echo $status === 'reserved' ? 'selected' : ''; ?>>Reserved</option>
            </select>

            <!-- Type Filter -->
            <select id="type-filter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="all" <?php echo $type === 'all' ? 'selected' : ''; ?>>All Types</option>
                <?php foreach ($unitTypes as $typeKey => $typeName): ?>
                    <option value="<?php echo $typeKey; ?>" <?php echo $type === $typeKey ? 'selected' : ''; ?>><?php echo $typeName; ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Property Filter -->
            <select id="property-filter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="all" <?php echo $property_id === 'all' ? 'selected' : ''; ?>>All Properties</option>
                <?php foreach ($properties as $property): ?>
                    <option value="<?php echo arr_get($property, 'id'); ?>" <?php echo $property_id == arr_get($property, 'id') ? 'selected' : ''; ?>><?php echo arr_escape($property, 'name'); ?></option>
                <?php endforeach; ?>
            </select>

            <!-- View Toggle -->
            <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button 
                    id="grid-view-btn" 
                    class="px-3 py-1.5 rounded text-sm font-medium transition-colors <?php echo $view === 'grid' ? 'bg-white dark:bg-gray-600 text-primary-600 dark:text-primary-400 shadow' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'; ?>"
                    onclick="setView('grid')"
                >
                    <i class="fas fa-th mr-1"></i> Grid
                </button>
                <button 
                    id="list-view-btn" 
                    class="px-3 py-1.5 rounded text-sm font-medium transition-colors <?php echo $view === 'list' ? 'bg-white dark:bg-gray-600 text-primary-600 dark:text-primary-400 shadow' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'; ?>"
                    onclick="setView('list')"
                >
                    <i class="fas fa-list mr-1"></i> List
                </button>
            </div>

            <!-- Add Unit Button -->
            <button 
                onclick="window.location.href='/admin/units/create'" 
                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            >
                <i class="fas fa-plus mr-2"></i>Add Unit
            </button>
        </div>
    </div>
</div>

<!-- Units Display -->
<div id="units-container">
    <?php if (empty($units)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
            <i class="fas fa-building text-gray-300 dark:text-gray-600 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No units found</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Get started by creating your first unit.</p>
            <button 
                onclick="window.location.href='/admin/units/create'" 
                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            >
                <i class="fas fa-plus mr-2"></i>Create Your First Unit
            </button>
        </div>
    <?php else: ?>
        <?php if ($view === 'grid'): ?>
            <!-- Grid View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($units as $unit): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow cursor-pointer" onclick="viewUnit(<?php echo arr_get($unit, 'id'); ?>)">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo arr_escape($unit, 'unit_number'); ?></h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo getStatusColorClass(arr_get($unit, 'status')); ?>">
                                    <?php echo ucfirst(arr_get($unit, 'status')); ?>
                                </span>
                            </div>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-home mr-2 text-gray-400"></i>
                                    <?php echo htmlspecialchars($unit['property_name']); ?>
                                </div>
                                
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-door-closed mr-2 text-gray-400"></i>
                                    <?php echo $unitTypes[$unit['type']] ?? ucfirst($unit['type']); ?>
                                </div>
                                
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-bed mr-2 text-gray-400"></i>
                                    <?php echo $unit['bedrooms'] ?: '0'; ?> Bedrooms
                                </div>
                                
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-bath mr-2 text-gray-400"></i>
                                    <?php echo $unit['bathrooms'] ?: '0'; ?> Bathrooms
                                </div>
                                
                                <?php if ($unit['rent_price']): ?>
                                    <div class="flex items-center text-gray-900 dark:text-white font-medium">
                                        <i class="fas fa-dollar-sign mr-2 text-green-600"></i>
                                        $<?php echo number_format($unit['rent_price'], 2); ?>/month
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                                <button 
                                    onclick="event.stopPropagation(); editUnit(<?php echo $unit['id']; ?>)" 
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium"
                                >
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <button 
                                    onclick="event.stopPropagation(); deleteUnit(<?php echo $unit['id']; ?>, '<?php echo htmlspecialchars($unit['unit_number']); ?>')" 
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium"
                                >
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- List View -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bedrooms</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bathrooms</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($units as $unit): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="viewUnit(<?php echo $unit['id']; ?>)">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($unit['unit_number']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($unit['property_name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white"><?php echo $unitTypes[$unit['type']] ?? ucfirst($unit['type']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white"><?php echo $unit['bedrooms'] ?: '0'; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white"><?php echo $unit['bathrooms'] ?: '0'; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white"><?php echo $unit['rent_price'] ? '$' . number_format($unit['rent_price'], 2) : '-'; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo getStatusColorClass($unit['status']); ?>">
                                            <?php echo ucfirst($unit['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button 
                                            onclick="event.stopPropagation(); editUnit(<?php echo $unit['id']; ?>)" 
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button 
                                            onclick="event.stopPropagation(); deleteUnit(<?php echo $unit['id']; ?>, '<?php echo htmlspecialchars($unit['unit_number']); ?>')" 
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($pagination['total_pages'] > 1): ?>
    <div class="mt-8 flex items-center justify-between">
        <div class="text-sm text-gray-700 dark:text-gray-300">
            Showing 
            <span class="font-medium"><?php echo (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?></span>
            to 
            <span class="font-medium"><?php echo min($pagination['current_page'] * $pagination['per_page'], $pagination['total']); ?></span>
            of 
            <span class="font-medium"><?php echo $pagination['total']; ?></span>
            results
        </div>
        
        <div class="flex items-center space-x-2">
            <?php if ($pagination['has_prev']): ?>
                <a href="?page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&type=<?php echo $type; ?>&property_id=<?php echo $property_id; ?>&view=<?php echo $view; ?>" class="px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-chevron-left"></i>
                </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&type=<?php echo $type; ?>&property_id=<?php echo $property_id; ?>&view=<?php echo $view; ?>" 
                   class="px-3 py-2 text-sm <?php echo $i === $pagination['current_page'] ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'; ?> rounded-md">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($pagination['has_next']): ?>
                <a href="?page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&type=<?php echo $type; ?>&property_id=<?php echo $property_id; ?>&view=<?php echo $view; ?>" class="px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();

// Helper function for status colors
function getStatusColorClass($status) {
    $classes = [
        'available' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'occupied' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'maintenance' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'reserved' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
    ];
    
    return $classes[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
}

echo ViewManager::render('dashboard.layout', ['content' => ob_get_clean()]);
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('search-input');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 500);
    });
    
    // Filter change handlers
    document.getElementById('status-filter').addEventListener('change', applyFilters);
    document.getElementById('type-filter').addEventListener('change', applyFilters);
    document.getElementById('property-filter').addEventListener('change', applyFilters);
});

function applyFilters() {
    const search = document.getElementById('search-input').value;
    const status = document.getElementById('status-filter').value;
    const type = document.getElementById('type-filter').value;
    const propertyId = document.getElementById('property-filter').value;
    const view = document.getElementById('grid-view-btn').classList.contains('bg-white') ? 'grid' : 'list';
    
    const params = new URLSearchParams({
        search: search,
        status: status,
        type: type,
        property_id: propertyId,
        view: view,
        page: 1
    });
    
    window.location.href = '/admin/units?' + params.toString();
}

function setView(viewType) {
    const gridBtn = document.getElementById('grid-view-btn');
    const listBtn = document.getElementById('list-view-btn');
    
    if (viewType === 'grid') {
        gridBtn.className = 'px-3 py-1.5 rounded text-sm font-medium transition-colors bg-white dark:bg-gray-600 text-primary-600 dark:text-primary-400 shadow';
        listBtn.className = 'px-3 py-1.5 rounded text-sm font-medium transition-colors text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white';
    } else {
        listBtn.className = 'px-3 py-1.5 rounded text-sm font-medium transition-colors bg-white dark:bg-gray-600 text-primary-600 dark:text-primary-400 shadow';
        gridBtn.className = 'px-3 py-1.5 rounded text-sm font-medium transition-colors text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white';
    }
    
    applyFilters();
}

function viewUnit(id) {
    window.location.href = `/admin/units/${id}/edit`;
}

function editUnit(id) {
    window.location.href = `/admin/units/${id}/edit`;
}

function deleteUnit(id, unitNumber) {
    if (confirm(`Are you sure you want to delete unit "${unitNumber}"? This action cannot be undone.`)) {
        fetch(`/admin/units/${id}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('An error occurred while deleting the unit', 'error');
        });
    }
}

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    
    // Set color based on type
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    toast.className += ' ' + colors[type];
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
</script>
