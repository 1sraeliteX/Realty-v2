<?php
// Anti-scattering compliant framework initialization
require_once __DIR__ . '/../../../config/bootstrap.php';

// Load UIComponents for badge rendering (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get properties data from ViewManager (anti-scattering compliant)
$properties = ViewManager::get('properties', []);
$pagination = ViewManager::get('pagination', []);
$search = ViewManager::get('search', '');
$type = ViewManager::get('type', '');
$category = ViewManager::get('category', '');
$status = ViewManager::get('status', '');

// If no properties data, fetch from database as fallback
if (empty($properties)) {
    $db = \Config\Database::getInstance();
    $adminId = $_SESSION['admin_id'] ?? null;
    if ($adminId) {
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                FROM properties p 
                WHERE p.admin_id = ? AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        $properties = $db->fetchAll($sql, [$adminId]);
    }
}
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
    <div class="flex items-center gap-3 flex-wrap">
        <!-- Search -->
        <div class="flex-1 min-w-[200px]">
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
            'w-40'
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
            'w-40'
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
            <button id="gridView" class="p-2 text-primary-600 border border-primary-600 rounded" data-view-mode="grid">
                <i class="fas fa-th"></i>
            </button>
            <button id="listView" class="p-2 text-gray-400 border border-gray-300 rounded" data-view-mode="list">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>
</div>

<!-- Grid & List Toggle Layout Styles -->
<style>
/* Grid mode — default */
.property-card {
    display: flex;
    flex-direction: column;
}
.property-card .property-image-container {
    position: relative;
    height: 12rem; /* h-48 */
    width: 100%;
    background-color: rgb(229 231 235);
    flex-shrink: 0;
    overflow: hidden;
}
.property-card .property-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* List mode overrides */
.property-card.list-mode {
    flex-direction: row !important;
    align-items: stretch !important;
    min-height: 120px;
}
.property-card.list-mode .property-image-container {
    width: 160px !important;
    min-width: 160px !important;
    height: auto !important;
    min-height: 120px !important;
    flex-shrink: 0 !important;
    position: relative !important;
}
.property-card.list-mode .property-image-container img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    position: absolute !important;
    inset: 0 !important;
}
.property-card.list-mode .property-details {
    flex: 1 !important;
    padding: 1rem !important;
    min-width: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
}

/* List mode: stats row */
.property-card.list-mode .stats-grid {
    display: flex !important;
    flex-direction: row !important;
    gap: 1rem !important;
}
.property-card.list-mode .stats-grid > div {
    flex: 1 !important;
}

/* List mode: revenue row */
.property-card.list-mode .revenue-row {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 1rem !important;
}

/* List mode: hide type badge to prevent overlap */
.property-card.list-mode .property-image-container .absolute.top-2.left-2 {
    display: none !important;
}

/* Dark mode */
.dark .property-card .property-image-container {
    background-color: rgb(55 65 81);
}
</style>

<!-- Properties Container -->
<div class="px-4 md:px-0">
    <div id="properties-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 place-items-center md:place-items-stretch">
        <?php foreach ($properties as $property): ?>
            <div class="property-card w-full max-w-sm md:max-w-none bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-200 cursor-pointer" onclick="previewProperty(<?php echo $property['id']; ?>)" data-view-mode="grid">
            <!-- Property Image -->
            <div class="property-image-container relative h-48 w-full bg-gray-200 dark:bg-gray-700 overflow-hidden flex-shrink-0">
                <?php 
                // Handle image display - use first image from JSON if available, otherwise placeholder
                $imageHtml = '<div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                    <i class="fas fa-image text-4xl"></i>
                    <span class="ml-2 text-sm">No Image</span>
                </div>';
                
                if (!empty($property['images'])) {
                    $images = json_decode($property['images'], true);
                    if (is_array($images) && !empty($images[0])) {
                        $imagePath = '/uploads/properties/' . $images[0];
                        $imageHtml = '<img src="' . $imagePath . '" alt="' . htmlspecialchars($property['name']) . '" class="w-full h-full object-cover">';
                    }
                }
                echo $imageHtml;
                ?>
                <div class="absolute top-2 right-2">
                    <?php 
                    // Map database status to display status
                    $displayStatus = $property['status'] === 'active' ? 'available' : $property['status'];
                    $statusColor = $displayStatus === 'active' ? 'success' : 
                                 ($displayStatus === 'inactive' ? 'info' : 'warning');
                    echo UIComponents::badge(ucfirst($displayStatus), $statusColor, 'small'); 
                    ?>
                </div>
                <div class="absolute top-2 left-2">
                    <?php echo UIComponents::badge(ucfirst($property['type']), 'gray', 'small'); ?>
                </div>
            </div>
            
            <!-- Property Details -->
            <div class="property-details p-6">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate min-w-0 flex-1 pr-2"><?php echo htmlspecialchars($property['name']); ?></h3>
                    <div class="flex space-x-1 flex-shrink-0">
                        <button onclick="editProperty(<?php echo $property['id']; ?>)" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteProperty(<?php echo $property['id']; ?>)" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 truncate">
                    <i class="fas fa-map-marker-alt mr-1 flex-shrink-0"></i>
                    <?php echo htmlspecialchars($property['address']); ?>
                </p>
                
                <!-- Stats Grid -->
                <div class="stats-grid grid grid-cols-2 gap-4 mb-4">
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
                <div class="revenue-row flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Annual Revenue</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            ₦<?php 
                            // Calculate annual revenue from occupied units * rent_price
                            $annualRevenue = 0;
                            if ($property['occupied_units'] > 0 && !empty($property['rent_price'])) {
                                $annualRevenue = (float)$property['rent_price'] * (int)$property['occupied_units'];
                            }
                            echo number_format($annualRevenue, 0);
                            ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Occupancy Rate</p>
                        <p class="text-lg font-semibold text-primary-600 dark:text-primary-400">
                            <?php 
                            $occupancyRate = ($property['unit_count'] > 0) ? 
                                round(($property['occupied_units'] / $property['unit_count']) * 100, 1) : 0;
                            echo $occupancyRate; 
                            ?>%
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

let propertyToDelete = null;

// Initialize view mode from localStorage
document.addEventListener('DOMContentLoaded', function() {
    const savedViewMode = localStorage.getItem('propertiesViewMode') || 'grid';
    setViewMode(savedViewMode);
});

// Search functionality
function searchProperties(query) {
    // Filter properties based on search query
    const cards = document.querySelectorAll('#properties-container > .property-card');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// View toggle functionality
function setViewMode(mode) {
    const container = document.getElementById('properties-container');
    const gridBtn = document.getElementById('gridView');
    const listBtn = document.getElementById('listView');
    const cards = document.querySelectorAll('.property-card');
    
    if (mode === 'grid') {
        container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8';
        gridBtn.className = 'p-2 rounded border border-primary-600 text-primary-600 bg-primary-50 dark:bg-primary-900/30';
        listBtn.className = 'p-2 rounded border border-gray-300 dark:border-gray-600 text-gray-400';
        cards.forEach(card => card.classList.remove('list-mode'));
    } else {
        container.className = 'flex flex-col gap-4 mb-8';
        gridBtn.className = 'p-2 rounded border border-gray-300 dark:border-gray-600 text-gray-400';
        listBtn.className = 'p-2 rounded border border-primary-600 text-primary-600 bg-primary-50 dark:bg-primary-900/30';
        cards.forEach(card => card.classList.add('list-mode'));
    }

    localStorage.setItem('propertiesViewMode', mode);
}

// View toggle event listeners
document.getElementById('gridView').addEventListener('click', function() {
    setViewMode('grid');
});

document.getElementById('listView').addEventListener('click', function() {
    setViewMode('list');
});

// Property actions
function editProperty(id) {
    window.location.href = `/admin/properties/${id}/edit`;
}

function deleteProperty(id) {
    propertyToDelete = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function confirmDelete() {
    if (!propertyToDelete) return;

    // Show loading state on confirm button
    const confirmBtn = document.querySelector('#deleteModal button:last-child');
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Deleting...';
    }

    // Submit DELETE via hidden form POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/properties/${propertyToDelete}/delete`;
    form.style.display = 'none';

    // CSRF token if available
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) {
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = csrfMeta.getAttribute('content');
        form.appendChild(csrf);
    }

    document.body.appendChild(form);
    form.submit();
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('hidden');
}

function viewUnits(id) {
    window.location.href = `/admin/units?property_id=${id}`;
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
    
    const cards = document.querySelectorAll('#properties-container > .property-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const matchesType = !typeFilter || text.includes(typeFilter.toLowerCase());
        const matchesStatus = !statusFilter || text.includes(statusFilter.toLowerCase());
        
        if (matchesType && matchesStatus) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update showing count
    const showingText = document.querySelector('.text-sm.text-gray-500.dark\:text-gray-400');
    if (showingText && showingText.textContent.includes('Showing')) {
        showingText.textContent = `Showing ${visibleCount} properties`;
    }
    
    // Show toast if no results
    if (visibleCount === 0) {
        showToast('No properties match the selected filters', 'info');
    }
}

function goToPage(page) {
    // In a real app, this would load the specified page
    showToast(`Loading page ${page}...`, 'info');
}
</script>
</div>
