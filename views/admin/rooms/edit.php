<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Edit Room';
$pageTitle = 'Edit Room';
$pageDescription = 'Update room information and details';

// Mock room data
$room = [
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
    'description' => 'Spacious master bedroom with en-suite bathroom and walk-in closet.',
    'amenities' => ['Walk-in Closet', 'En-suite Bathroom', 'Balcony Access', 'Hardwood Floors', 'Ceiling Fan', 'Large Windows'],
    'created_at' => '2023-01-10',
    'last_updated' => '2024-01-08'
];

// Mock properties data
$properties = [
    ['id' => 1, 'name' => 'Sunset Apartments'],
    ['id' => 2, 'name' => 'Downtown Plaza'],
    ['id' => 3, 'name' => 'Riverside Complex']
];

// Mock units data
$units = [
    ['id' => 1, 'number' => '101', 'property_id' => 1, 'type' => '1BR'],
    ['id' => 2, 'number' => '102', 'property_id' => 1, 'type' => '1BR'],
    ['id' => 3, 'number' => '103', 'property_id' => 1, 'type' => '2BR'],
    ['id' => 4, 'number' => '201', 'property_id' => 1, 'type' => '2BR'],
];

ob_start();
?>

<!-- Breadcrumb -->
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="/admin/rooms" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <a href="/admin/rooms/<?php echo $room['id']; ?>" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <?php echo htmlspecialchars($room['name']); ?>
                </a>
            </li>
            <li class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Edit</span>
            </li>
        </ol>
    </nav>
</div>

<!-- Edit Form -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    <form id="roomEditForm" onsubmit="submitEditForm(event)">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column - Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Room Name *</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($room['name']); ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Room Type *</label>
                        <select name="type" required onchange="updateRoomTypeFields()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="bedroom" <?php echo $room['type'] === 'bedroom' ? 'selected' : ''; ?>>Bedroom</option>
                            <option value="living" <?php echo $room['type'] === 'living' ? 'selected' : ''; ?>>Living Room</option>
                            <option value="kitchen" <?php echo $room['type'] === 'kitchen' ? 'selected' : ''; ?>>Kitchen</option>
                            <option value="bathroom" <?php echo $room['type'] === 'bathroom' ? 'selected' : ''; ?>>Bathroom</option>
                            <option value="dining" <?php echo $room['type'] === 'dining' ? 'selected' : ''; ?>>Dining Room</option>
                            <option value="office" <?php echo $room['type'] === 'office' ? 'selected' : ''; ?>>Office/Study</option>
                            <option value="storage" <?php echo $room['type'] === 'storage' ? 'selected' : ''; ?>>Storage Room</option>
                            <option value="balcony" <?php echo $room['type'] === 'balcony' ? 'selected' : ''; ?>>Balcony/Patio</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property *</label>
                        <select name="property_id" required onchange="updateUnits()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <?php foreach ($properties as $property): ?>
                                <option value="<?php echo $property['id']; ?>" <?php echo $property['id'] == $room['property_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($property['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit *</label>
                        <select name="unit_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <?php foreach ($units as $unit): ?>
                                <?php if ($unit['property_id'] == $room['property_id']): ?>
                                    <option value="<?php echo $unit['id']; ?>" <?php echo $unit['id'] == $room['unit_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($unit['number']); ?> (<?php echo $unit['type']; ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Size (sq ft) *</label>
                        <input type="number" name="size_sqft" value="<?php echo $room['size_sqft']; ?>" required min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="occupied" <?php echo $room['status'] === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                            <option value="vacant" <?php echo $room['status'] === 'vacant' ? 'selected' : ''; ?>>Vacant</option>
                            <option value="maintenance" <?php echo $room['status'] === 'maintenance' ? 'selected' : ''; ?>>Under Maintenance</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Right Column - Room Details -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Room Details</h3>
                
                <div class="space-y-4">
                    <!-- Beds and Baths -->
                    <div id="bedsBathsSection">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of Beds</label>
                                <input type="number" name="beds" value="<?php echo $room['beds']; ?>" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of Baths</label>
                                <input type="number" name="baths" value="<?php echo $room['baths']; ?>" min="0" step="0.5" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rent Portion ($)</label>
                        <input type="number" name="rent_portion" value="<?php echo $room['rent_portion']; ?>" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Portion of total rent attributed to this room</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Occupant</label>
                        <input type="text" name="occupant" value="<?php echo htmlspecialchars($room['occupant']); ?>" placeholder="e.g., John Smith or Shared" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Describe the room features, layout, etc."><?php echo htmlspecialchars($room['description']); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Amenities Section -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Room Amenities</h3>
            
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <?php
                    $allAmenities = [
                        'Walk-in Closet', 'En-suite Bathroom', 'Balcony Access', 'Large Windows',
                        'Built-in Shelving', 'Ceiling Fan', 'Modern Appliances', 'Granite Countertops',
                        'Pantry', 'Standard Closet', 'Window View', 'Hardwood Floors'
                    ];
                    
                    foreach ($allAmenities as $amenity): ?>
                        <label class="flex items-center">
                            <input type="checkbox" name="amenities[]" value="<?php echo htmlspecialchars($amenity); ?>" 
                                <?php echo in_array($amenity, $room['amenities']) ? 'checked' : ''; ?>
                                class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($amenity); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="flex space-x-4">
                <button type="button" onclick="deleteRoom()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Delete Room
                </button>
            </div>
            
            <div class="flex space-x-4">
                <a href="/admin/rooms/<?php echo $room['id']; ?>" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Mock units data (in a real app, this would come from an API)
const unitsData = <?php echo json_encode($units); ?>;

function updateUnits() {
    const propertyId = document.querySelector('select[name="property_id"]').value;
    const unitSelect = document.querySelector('select[name="unit_id"]');
    const currentUnitId = <?php echo $room['unit_id']; ?>;
    
    // Clear current options
    unitSelect.innerHTML = '';
    
    if (propertyId) {
        // Filter units by property
        const filteredUnits = unitsData.filter(unit => unit.property_id == propertyId);
        
        filteredUnits.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit.id;
            option.textContent = `${unit.number} (${unit.type})`;
            if (unit.id == currentUnitId) {
                option.selected = true;
            }
            unitSelect.appendChild(option);
        });
    }
}

function updateRoomTypeFields() {
    const roomType = document.querySelector('select[name="type"]').value;
    const bedsBathsSection = document.getElementById('bedsBathsSection');
    
    // Show beds/baths for bedrooms and bathrooms
    if (roomType === 'bedroom' || roomType === 'bathroom') {
        bedsBathsSection.classList.remove('hidden');
    } else {
        bedsBathsSection.classList.add('hidden');
        document.querySelector('input[name="beds"]').value = 0;
        document.querySelector('input[name="baths"]').value = 0;
    }
}

function submitEditForm(event) {
    event.preventDefault();
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        showToast('Room updated successfully!', 'success');
        
        // Redirect to room details
        setTimeout(() => {
            window.location.href = '/admin/rooms/<?php echo $room['id']; ?>';
        }, 1500);
    }, 2000);
}

function deleteRoom() {
    if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
        if (confirm('This will permanently remove the room and all associated data. Continue?')) {
            showToast('Deleting room...', 'info');
            setTimeout(() => {
                showToast('Room deleted successfully!', 'success');
                
                // Redirect to rooms list
                setTimeout(() => {
                    window.location.href = '/admin/rooms';
                }, 1500);
            }, 2000);
        }
    }
}

// Initialize room type fields on page load
document.addEventListener('DOMContentLoaded', function() {
    updateRoomTypeFields();
});
</script>

<?php
$content = ob_get_clean();
include '../simple_layout.php';
?>
