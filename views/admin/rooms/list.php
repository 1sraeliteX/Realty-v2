<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Rooms Management';
$pageTitle = 'Rooms';
$pageDescription = 'Manage individual rooms within units';

// Mock rooms data
$rooms = [
    [
        'id' => 1,
        'name' => 'Master Bedroom',
        'unit_id' => 1,
        'unit_number' => '101',
        'property_name' => 'Sunset Apartments',
        'type' => 'bedroom',
        'size_sqft' => 180,
        'beds' => 1,
        'baths' => 1,
        'status' => 'occupied',
        'occupant' => 'John Smith',
        'rent_portion' => 600,
        'amenities' => ['Walk-in Closet', 'En-suite Bathroom', 'Balcony Access'],
        'created_at' => '2023-01-10'
    ],
    [
        'id' => 2,
        'name' => 'Living Room',
        'unit_id' => 1,
        'unit_number' => '101',
        'property_name' => 'Sunset Apartments',
        'type' => 'living',
        'size_sqft' => 250,
        'beds' => 0,
        'baths' => 0,
        'status' => 'occupied',
        'occupant' => 'Shared',
        'rent_portion' => 300,
        'amenities' => ['Large Windows', 'Built-in Shelving', 'Ceiling Fan'],
        'created_at' => '2023-01-10'
    ],
    [
        'id' => 3,
        'name' => 'Bedroom 2',
        'unit_id' => 1,
        'unit_number' => '101',
        'property_name' => 'Sunset Apartments',
        'type' => 'bedroom',
        'size_sqft' => 120,
        'beds' => 1,
        'baths' => 0,
        'status' => 'vacant',
        'occupant' => null,
        'rent_portion' => 300,
        'amenities' => ['Standard Closet', 'Window View'],
        'created_at' => '2023-01-10'
    ],
    [
        'id' => 4,
        'name' => 'Kitchen',
        'unit_id' => 2,
        'unit_number' => '102',
        'property_name' => 'Sunset Apartments',
        'type' => 'kitchen',
        'size_sqft' => 150,
        'beds' => 0,
        'baths' => 0,
        'status' => 'occupied',
        'occupant' => 'Sarah Johnson',
        'rent_portion' => 400,
        'amenities' => ['Modern Appliances', 'Granite Countertops', 'Pantry'],
        'created_at' => '2023-01-15'
    ]
];

ob_start();
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php 
    $totalRooms = count($rooms);
    $occupiedRooms = count(array_filter($rooms, fn($r) => $r['status'] === 'occupied'));
    $vacantRooms = count(array_filter($rooms, fn($r) => $r['status'] === 'vacant'));
    $bedrooms = count(array_filter($rooms, fn($r) => $r['type'] === 'bedroom'));
    ?>
    
    <?php echo UIComponents::statCard('Total Rooms', $totalRooms, 'door-open', 'blue', '', 'All room types'); ?>
    <?php echo UIComponents::statCard('Occupied', $occupiedRooms, 'users', 'green', '', round(($occupiedRooms / $totalRooms) * 100, 1) . '% occupied'); ?>
    <?php echo UIComponents::statCard('Vacant', $vacantRooms, 'home', 'orange', '', 'Available for rent'); ?>
    <?php echo UIComponents::statCard('Bedrooms', $bedrooms, 'bed', 'purple', '', 'Sleeping rooms'); ?>
</div>

<!-- Actions Bar -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center space-x-3 mb-3 sm:mb-0">
            <?php echo UIComponents::button('Add Room', 'primary', 'medium', '/admin/rooms/create', 'plus'); ?>
            <?php echo UIComponents::button('Import Rooms', 'secondary', 'medium', '#', 'upload'); ?>
            <?php echo UIComponents::button('Export List', 'info', 'medium', '#', 'download'); ?>
        </div>
        
        <div class="flex items-center space-x-3">
            <div class="relative">
                <input 
                    type="text" 
                    id="searchRooms"
                    placeholder="Search rooms..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                    onkeyup="searchRooms(this.value)"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            
            <select id="typeFilter" onchange="filterRooms()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">All Types</option>
                <option value="bedroom">Bedroom</option>
                <option value="living">Living Room</option>
                <option value="kitchen">Kitchen</option>
                <option value="bathroom">Bathroom</option>
                <option value="dining">Dining Room</option>
            </select>
            
            <select id="statusFilter" onchange="filterRooms()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">All Statuses</option>
                <option value="occupied">Occupied</option>
                <option value="vacant">Vacant</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
    </div>
</div>

<!-- Rooms Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="roomsGrid">
    <?php foreach ($rooms as $room): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow" data-type="<?php echo $room['type']; ?>" data-status="<?php echo $room['status']; ?>">
            <!-- Room Header -->
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white"><?php echo htmlspecialchars($room['name']); ?></h3>
                        <p class="text-indigo-100 text-sm"><?php echo htmlspecialchars($room['unit_number']); ?> • <?php echo htmlspecialchars($room['property_name']); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-indigo-100 text-xs"><?php echo ucfirst($room['type']); ?></p>
                        <p class="text-white font-bold"><?php echo $room['size_sqft']; ?> sq ft</p>
                    </div>
                </div>
            </div>
            
            <!-- Room Content -->
            <div class="p-4">
                <!-- Status and Occupant -->
                <div class="flex items-center justify-between mb-3">
                    <?php echo UIComponents::badge(ucfirst($room['status']), 
                        $room['status'] === 'occupied' ? 'success' : 
                        ($room['status'] === 'vacant' ? 'warning' : 'info')); ?>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        <?php echo $room['occupant'] ? htmlspecialchars($room['occupant']) : 'Unoccupied'; ?>
                    </span>
                </div>
                
                <!-- Room Details -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Beds/Baths:</span>
                        <span class="text-gray-900 dark:text-white"><?php echo $room['beds']; ?>/<?php echo $room['baths']; ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Rent Portion:</span>
                        <span class="text-gray-900 dark:text-white">$<?php echo number_format($room['rent_portion']); ?></span>
                    </div>
                </div>
                
                <!-- Amenities -->
                <div class="mb-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Amenities:</p>
                    <div class="flex flex-wrap gap-1">
                        <?php foreach (array_slice($room['amenities'], 0, 2) as $amenity): ?>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded-full">
                                <?php echo htmlspecialchars($amenity); ?>
                            </span>
                        <?php endforeach; ?>
                        <?php if (count($room['amenities']) > 2): ?>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded-full">
                                +<?php echo count($room['amenities']) - 2; ?> more
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex space-x-2">
                    <a href="/admin/rooms/<?php echo $room['id']; ?>" class="flex-1 text-center px-3 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                    <a href="/admin/rooms/<?php echo $room['id']; ?>/edit" class="flex-1 text-center px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Empty State (hidden by default) -->
<div id="emptyState" class="hidden text-center py-12">
    <i class="fas fa-door-open text-4xl text-gray-400 mb-4"></i>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No rooms found</h3>
    <p class="text-gray-500 dark:text-gray-400 mb-6">Try adjusting your search or filters</p>
    <button onclick="clearFilters()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
        Clear Filters
    </button>
</div>

<!-- Quick Actions Modal -->
<?php echo UIComponents::modal('quickActionsModal', 'Quick Actions', '
    <div class="space-y-3">
        <button onclick="bulkUpdate()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-edit mr-3 text-blue-600"></i>
            Bulk Update Rooms
        </button>
        <button onclick="generateRoomReport()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-chart-bar mr-3 text-green-600"></i>
            Generate Room Report
        </button>
        <button onclick="importRooms()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-upload mr-3 text-purple-600"></i>
            Import Rooms
        </button>
        <button onclick="exportRooms()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-download mr-3 text-orange-600"></i>
            Export Rooms List
        </button>
    </div>
', 'medium'); ?>

<script>
// Search functionality
function searchRooms(query) {
    const cards = document.querySelectorAll('#roomsGrid > div');
    let hasResults = false;
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
            card.style.display = '';
            hasResults = true;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    document.getElementById('emptyState').classList.toggle('hidden', hasResults);
    document.getElementById('roomsGrid').classList.toggle('hidden', !hasResults);
}

// Filter functionality
function filterRooms() {
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const cards = document.querySelectorAll('#roomsGrid > div');
    let hasResults = false;
    
    cards.forEach(card => {
        const type = card.dataset.type;
        const status = card.dataset.status;
        
        const matchesType = !typeFilter || type === typeFilter;
        const matchesStatus = !statusFilter || status === statusFilter;
        
        if (matchesType && matchesStatus) {
            card.style.display = '';
            hasResults = true;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    document.getElementById('emptyState').classList.toggle('hidden', hasResults);
    document.getElementById('roomsGrid').classList.toggle('hidden', !hasResults);
}

// Clear filters
function clearFilters() {
    document.getElementById('searchRooms').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    
    const cards = document.querySelectorAll('#roomsGrid > div');
    cards.forEach(card => {
        card.style.display = '';
    });
    
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('roomsGrid').classList.remove('hidden');
}

// Quick actions
function bulkUpdate() {
    closeModal('quickActionsModal');
    showToast('Opening bulk update wizard...', 'info');
}

function generateRoomReport() {
    closeModal('quickActionsModal');
    showToast('Generating room report...', 'info');
    setTimeout(() => {
        showToast('Room report generated successfully!', 'success');
    }, 2000);
}

function importRooms() {
    closeModal('quickActionsModal');
    showToast('Opening import wizard...', 'info');
}

function exportRooms() {
    closeModal('quickActionsModal');
    showToast('Exporting rooms list...', 'info');
    setTimeout(() => {
        showToast('Rooms exported successfully!', 'success');
    }, 2000);
}

// Add quick actions button to header
document.addEventListener('DOMContentLoaded', function() {
    const headerActions = document.querySelector('.bg-white.dark\\:bg-gray-800.rounded-lg.shadow-lg.p-4.mb-6 .flex.flex-col.sm\\:flex-row.sm\\:items-center.sm\\:justify-between .flex.items-center.space-x-3.mb-3.sm\\:mb-0');
    if (headerActions) {
        const quickActionsBtn = document.createElement('button');
        quickActionsBtn.className = 'inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700';
        quickActionsBtn.innerHTML = '<i class="fas fa-bolt mr-2"></i>Quick Actions';
        quickActionsBtn.onclick = () => document.getElementById('quickActionsModal').classList.remove('hidden');
        headerActions.appendChild(quickActionsBtn);
    }
});
</script>

<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
