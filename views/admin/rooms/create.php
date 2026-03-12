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


<!-- Form Container -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    <form id="roomForm" onsubmit="submitRoomForm(event)">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column - Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Room Name *</label>
                        <input type="text" name="name" required placeholder="e.g., Master Bedroom, Living Room" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Room Type *</label>
                        <select name="type" required onchange="updateRoomTypeFields()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Select Room Type</option>
                            <option value="bedroom">Bedroom</option>
                            <option value="living">Living Room</option>
                            <option value="kitchen">Kitchen</option>
                            <option value="bathroom">Bathroom</option>
                            <option value="dining">Dining Room</option>
                            <option value="office">Office/Study</option>
                            <option value="storage">Storage Room</option>
                            <option value="balcony">Balcony/Patio</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property *</label>
                        <select name="property_id" required onchange="updateUnits()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Select Property</option>
                            <?php foreach ($properties as $property): ?>
                                <option value="<?php echo $property['id']; ?>"><?php echo htmlspecialchars($property['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit *</label>
                        <select name="unit_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Select Unit</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Size (sq ft) *</label>
                        <input type="number" name="size_sqft" required min="1" placeholder="e.g., 150" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Select Status</option>
                            <option value="occupied">Occupied</option>
                            <option value="vacant">Vacant</option>
                            <option value="maintenance">Under Maintenance</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Right Column - Room Details -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Room Details</h3>
                
                <div class="space-y-4">
                    <!-- Beds and Baths (conditional based on room type) -->
                    <div id="bedsBathsSection" class="hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of Beds</label>
                                <input type="number" name="beds" min="0" placeholder="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of Baths</label>
                                <input type="number" name="baths" min="0" step="0.5" placeholder="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rent Portion ($)</label>
                        <input type="number" name="rent_portion" min="0" step="0.01" placeholder="e.g., 600.00" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Portion of total rent attributed to this room</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Occupant</label>
                        <input type="text" name="occupant" placeholder="e.g., John Smith or Shared" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" rows="3" placeholder="Describe the room features, layout, etc." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Amenities Section -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Room Amenities</h3>
            
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Walk-in Closet" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Walk-in Closet</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="En-suite Bathroom" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">En-suite Bathroom</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Balcony Access" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Balcony Access</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Large Windows" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Large Windows</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Built-in Shelving" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Built-in Shelving</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Ceiling Fan" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Ceiling Fan</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Modern Appliances" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Modern Appliances</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Granite Countertops" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Granite Countertops</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Pantry" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Pantry</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Standard Closet" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Standard Closet</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Window View" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Window View</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="amenities[]" value="Hardwood Floors" class="mr-2 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Hardwood Floors</span>
                    </label>
                </div>
                
                <div class="mt-4">
                    <input type="text" name="custom_amenity" placeholder="Add custom amenity..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="/admin/rooms" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Create Room
            </button>
        </div>
    </form>
</div>

<script>
// Mock units data (in a real app, this would come from an API)
const unitsData = <?php echo json_encode($units); ?>;

function updateUnits() {
    const propertyId = document.querySelector('select[name="property_id"]').value;
    const unitSelect = document.querySelector('select[name="unit_id"]');
    
    // Clear current options
    unitSelect.innerHTML = '<option value="">Select Unit</option>';
    
    if (propertyId) {
        // Filter units by property
        const filteredUnits = unitsData.filter(unit => unit.property_id == propertyId);
        
        filteredUnits.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit.id;
            option.textContent = `${unit.number} (${unit.type})`;
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
        
        // Set defaults based on room type
        if (roomType === 'bedroom') {
            document.querySelector('input[name="beds"]').value = 1;
            document.querySelector('input[name="baths"]').value = 0;
        } else if (roomType === 'bathroom') {
            document.querySelector('input[name="beds"]').value = 0;
            document.querySelector('input[name="baths"]').value = 1;
        }
    } else {
        bedsBathsSection.classList.add('hidden');
        document.querySelector('input[name="beds"]').value = 0;
        document.querySelector('input[name="baths"]').value = 0;
    }
}

function submitRoomForm(event) {
    event.preventDefault();
    
    // Validate form
    const requiredFields = event.target.querySelectorAll('[required]');
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            field.focus();
            showToast(`Please fill in ${field.previousElementSibling.textContent.replace('*', '').trim()}`, 'error');
            return;
        }
    }
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...';
    submitBtn.disabled = true;
    
    // Collect amenities
    const amenities = [];
    const checkedAmenities = document.querySelectorAll('input[name="amenities[]"]:checked');
    checkedAmenities.forEach(checkbox => {
        amenities.push(checkbox.value);
    });
    
    const customAmenity = document.querySelector('input[name="custom_amenity"]').value.trim();
    if (customAmenity) {
        amenities.push(customAmenity);
    }
    
    // Simulate API call
    setTimeout(() => {
        showToast('Room created successfully!', 'success');
        
        // Redirect to rooms list
        setTimeout(() => {
            window.location.href = '/admin/rooms';
        }, 1500);
    }, 2000);
}
</script>


<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
