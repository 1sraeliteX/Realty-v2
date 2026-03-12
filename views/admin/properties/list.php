<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Admin Page');
ViewManager::set('user', [
    'name' => 'Admin User',
    'email' => 'admin@cornerstone.com',
    'avatar' => null
]);
ViewManager::set('notifications', []);

ob_start();
?>


<!-- Header with Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Properties</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage your property portfolio</p>
    </div>
    <div class="mt-4 sm:mt-0 flex space-x-3">
        <button onclick="exportProperties()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-download mr-2"></i>
            Export
        </button>
        <a href="/admin/properties/create" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            <i class="fas fa-plus mr-2"></i>
            Add Property
        </a>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Search -->
        <div class="md:col-span-2">
            <?php echo UIComponents::searchBar('Search properties...', '', 'searchProperties(this.value)'); ?>
        </div>
        
        <!-- Property Type Filter -->
        <?php 
        echo UIComponents::select(
            'type_filter',
            'Property Type',
            [
                '' => 'All Types',
                'Residential' => 'Residential',
                'Commercial' => 'Commercial',
                'Industrial' => 'Industrial'
            ],
            '',
            false,
            'col-span-1'
        ); ?>
        
        <!-- Status Filter -->
        <?php 
        echo UIComponents::select(
            'status_filter',
            'Status',
            [
                '' => 'All Status',
                'occupied' => 'Occupied',
                'available' => 'Available',
                'maintenance' => 'Maintenance'
            ],
            '',
            false,
            'col-span-1'
        ); ?>
    </div>
    
    <!-- Additional Filters -->
    <div class="mt-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <button class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">
                <i class="fas fa-filter mr-1"></i>
                Advanced Filters
            </button>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Showing <?php echo count($properties); ?> properties
            </span>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">View:</span>
            <button id="gridView" class="p-2 text-primary-600 border border-primary-600 rounded">
                <i class="fas fa-th"></i>
            </button>
            <button id="listView" class="p-2 text-gray-400 border border-gray-300 rounded">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>
</div>

<!-- Properties Grid -->
<div id="propertiesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <?php foreach ($properties as $property): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-200">
            <!-- Property Image -->
            <div class="relative h-48 bg-gray-200 dark:bg-gray-700">
                <img src="<?php echo $property['image']; ?>" alt="<?php echo htmlspecialchars($property['name']); ?>" class="w-full h-full object-cover">
                <div class="absolute top-2 right-2">
                    <?php 
                    $statusColor = $property['status'] === 'occupied' ? 'success' : 
                                 ($property['status'] === 'available' ? 'info' : 'warning');
                    echo UIComponents::badge(ucfirst($property['status']), $statusColor, 'small'); 
                    ?>
                </div>
                <div class="absolute top-2 left-2">
                    <?php echo UIComponents::badge($property['type'], 'gray', 'small'); ?>
                </div>
            </div>
            
            <!-- Property Details -->
            <div class="p-6">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars($property['name']); ?></h3>
                    <div class="flex space-x-1">
                        <button onclick="editProperty(<?php echo $property['id']; ?>)" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteProperty(<?php echo $property['id']; ?>)" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    <?php echo htmlspecialchars($property['address']); ?>
                </p>
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo $property['unit_count']; ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Units</p>
                    </div>
                    <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                        <p class="text-lg font-semibold text-green-600 dark:text-green-400"><?php echo $property['occupied_units']; ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Occupied</p>
                    </div>
                </div>
                
                <!-- Revenue Info -->
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Monthly Revenue</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">$<?php echo number_format($property['monthly_revenue'], 0); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Occupancy Rate</p>
                        <p class="text-lg font-semibold text-primary-600 dark:text-primary-400">
                            <?php echo round(($property['occupied_units'] / $property['unit_count']) * 100, 1); ?>%
                        </p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <a href="/admin/properties/<?php echo $property['id']; ?>" class="flex-1 text-center px-3 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">
                        View Details
                    </a>
                    <button onclick="viewUnits(<?php echo $property['id']; ?>)" class="flex-1 text-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Units
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<div class="flex items-center justify-between">
    <div class="text-sm text-gray-700 dark:text-gray-300">
        Showing <span class="font-medium">1</span> to <span class="font-medium"><?php echo count($properties); ?></span> of <span class="font-medium"><?php echo count($properties); ?></span> results
    </div>
    <?php echo UIComponents::pagination(1, 1, 'goToPage'); ?>
</div>

<!-- Delete Confirmation Modal -->
<?php 
echo UIComponents::modal(
    'deleteModal',
    'Delete Property',
    '<p class="text-sm text-gray-600 dark:text-gray-400">Are you sure you want to delete this property? This action cannot be undone and will also delete all associated units and tenant data.</p>',
    '<button onclick="closeModal(\'deleteModal\')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
    <button onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete Property</button>',
    'small'
); ?>

<script>
let propertyToDelete = null;

// Search functionality
function searchProperties(query) {
    // Filter properties based on search query
    const cards = document.querySelectorAll('#propertiesGrid > div');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// View toggle
document.getElementById('gridView').addEventListener('click', function() {
    this.classList.add('text-primary-600', 'border-primary-600');
    this.classList.remove('text-gray-400', 'border-gray-300');
    document.getElementById('listView').classList.add('text-gray-400', 'border-gray-300');
    document.getElementById('listView').classList.remove('text-primary-600', 'border-primary-600');
});

document.getElementById('listView').addEventListener('click', function() {
    this.classList.add('text-primary-600', 'border-primary-600');
    this.classList.remove('text-gray-400', 'border-gray-300');
    document.getElementById('gridView').classList.add('text-gray-400', 'border-gray-300');
    document.getElementById('gridView').classList.remove('text-primary-600', 'border-primary-600');
    // In a real app, this would switch to list view
});

// Property actions
function editProperty(id) {
    window.location.href = `/admin/dashboard/properties/${id}/edit`;
}

function deleteProperty(id) {
    propertyToDelete = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function confirmDelete() {
    if (propertyToDelete) {
        showToast('Property deleted successfully', 'success');
        closeModal('deleteModal');
        // In a real app, this would make an API call
        setTimeout(() => {
            location.reload();
        }, 1500);
    }
}

function viewUnits(id) {
    window.location.href = `/admin/dashboard/properties/${id}/units`;
}

function exportProperties() {
    showToast('Exporting properties data...', 'info');
    // In a real app, this would trigger a download
    setTimeout(() => {
        showToast('Properties exported successfully', 'success');
    }, 2000);
}

// Filter handlers
document.getElementById('type_filter').addEventListener('change', function() {
    filterProperties();
});

document.getElementById('status_filter').addEventListener('change', function() {
    filterProperties();
});

function filterProperties() {
    const typeFilter = document.getElementById('type_filter').value;
    const statusFilter = document.getElementById('status_filter').value;
    
    const cards = document.querySelectorAll('#propertiesGrid > div');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const matchesType = !typeFilter || text.includes(typeFilter.toLowerCase());
        const matchesStatus = !statusFilter || text.includes(statusFilter.toLowerCase());
        
        if (matchesType && matchesStatus) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function goToPage(page) {
    // In a real app, this would load the specified page
    showToast(`Loading page ${page}...`, 'info');
}
</script>

<?php
$content = ob_get_clean();
include '../simple_layout.php';
?>

<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
