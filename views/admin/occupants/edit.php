<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Edit Occupant';
$pageTitle = 'Edit Occupant';
$pageDescription = 'Update occupant information and details';

// Mock occupant data (same as show page)
$occupant = [
    'id' => 1,
    'first_name' => 'John',
    'last_name' => 'Smith',
    'email' => 'john.smith@email.com',
    'phone' => '(555) 123-4567',
    'date_of_birth' => '1985-06-15',
    'type' => 'tenant',
    'property_id' => 1,
    'property_name' => 'Sunset Apartments',
    'unit_id' => 1,
    'unit_number' => '101',
    'move_in_date' => '2023-01-15',
    'status' => 'active',
    'emergency_contact' => 'Jane Smith',
    'emergency_phone' => '(555) 987-6543',
    'emergency_relationship' => 'Spouse',
    'vehicle_info' => 'Tesla Model 3 - ABC123',
    'parking_space' => 'P-101',
    'storage_unit' => 'S-101',
    'notes' => 'Primary leaseholder. Works as software engineer. Quiet resident, pays rent on time.',
    'created_at' => '2023-01-10',
    'last_updated' => '2024-01-08',
    'profile_photo' => null
];

// Mock properties data
$properties = [
    ['id' => 1, 'name' => 'Sunset Apartments'],
    ['id' => 2, 'name' => 'Downtown Plaza'],
    ['id' => 3, 'name' => 'Riverside Complex']
];

// Mock units data
$units = [
    ['id' => 1, 'number' => '101', 'property_id' => 1],
    ['id' => 2, 'number' => '102', 'property_id' => 1],
    ['id' => 3, 'number' => '103', 'property_id' => 1],
    ['id' => 4, 'number' => '201', 'property_id' => 1],
];

ob_start();
?>

<!-- Breadcrumb -->
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="/admin/occupants" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <a href="/admin/occupants/<?php echo $occupant['id']; ?>" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <?php echo htmlspecialchars($occupant['first_name'] . ' ' . $occupant['last_name']); ?>
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
    <form id="occupantEditForm" onsubmit="submitEditForm(event)">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Personal Information -->
            <div class="lg:col-span-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($occupant['first_name']); ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($occupant['last_name']); ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($occupant['email']); ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($occupant['phone']); ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="<?php echo $occupant['date_of_birth']; ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Occupant Type *</label>
                        <select name="type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="tenant" <?php echo $occupant['type'] === 'tenant' ? 'selected' : ''; ?>>Tenant</option>
                            <option value="family_member" <?php echo $occupant['type'] === 'family_member' ? 'selected' : ''; ?>>Family Member</option>
                            <option value="guest" <?php echo $occupant['type'] === 'guest' ? 'selected' : ''; ?>>Guest</option>
                            <option value="staff" <?php echo $occupant['type'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="active" <?php echo $occupant['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $occupant['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="temporary" <?php echo $occupant['status'] === 'temporary' ? 'selected' : ''; ?>>Temporary</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Middle Column - Property & Assignment -->
            <div class="lg:col-span-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Property Assignment</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property *</label>
                        <select name="property_id" required onchange="updateUnits()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <?php foreach ($properties as $property): ?>
                                <option value="<?php echo $property['id']; ?>" <?php echo $property['id'] == $occupant['property_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($property['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit *</label>
                        <select name="unit_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <?php foreach ($units as $unit): ?>
                                <?php if ($unit['property_id'] == $occupant['property_id']): ?>
                                    <option value="<?php echo $unit['id']; ?>" <?php echo $unit['id'] == $occupant['unit_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($unit['number']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Move-in Date *</label>
                        <input type="date" name="move_in_date" value="<?php echo $occupant['move_in_date']; ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parking Space</label>
                        <input type="text" name="parking_space" value="<?php echo htmlspecialchars($occupant['parking_space']); ?>" placeholder="e.g., P-101, Guest, None" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Storage Unit</label>
                        <input type="text" name="storage_unit" value="<?php echo htmlspecialchars($occupant['storage_unit']); ?>" placeholder="e.g., S-101, None" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vehicle Information</label>
                        <input type="text" name="vehicle_info" value="<?php echo htmlspecialchars($occupant['vehicle_info']); ?>" placeholder="e.g., Tesla Model 3 - ABC123" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Right Column - Emergency Contact & Notes -->
            <div class="lg:col-span-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact" value="<?php echo htmlspecialchars($occupant['emergency_contact']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Phone</label>
                        <input type="tel" name="emergency_phone" value="<?php echo htmlspecialchars($occupant['emergency_phone']); ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Relationship</label>
                        <input type="text" name="emergency_relationship" value="<?php echo htmlspecialchars($occupant['emergency_relationship']); ?>" placeholder="e.g., Spouse, Parent, Friend" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                </div>
                
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 mt-6">Notes</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Notes</label>
                    <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Additional notes about the occupant..."><?php echo htmlspecialchars($occupant['notes']); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="flex space-x-4">
                <button type="button" onclick="deleteOccupant()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Delete Occupant
                </button>
            </div>
            
            <div class="flex space-x-4">
                <a href="/admin/occupants/<?php echo $occupant['id']; ?>" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
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
    const currentUnitId = <?php echo $occupant['unit_id']; ?>;
    
    // Clear current options
    unitSelect.innerHTML = '';
    
    if (propertyId) {
        // Filter units by property
        const filteredUnits = unitsData.filter(unit => unit.property_id == propertyId);
        
        filteredUnits.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit.id;
            option.textContent = unit.number;
            if (unit.id == currentUnitId) {
                option.selected = true;
            }
            unitSelect.appendChild(option);
        });
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
        showToast('Occupant updated successfully!', 'success');
        
        // Redirect to occupant details
        setTimeout(() => {
            window.location.href = '/admin/occupants/<?php echo $occupant['id']; ?>';
        }, 1500);
    }, 2000);
}

function deleteOccupant() {
    if (confirm('Are you sure you want to delete this occupant? This action cannot be undone.')) {
        if (confirm('This will permanently remove the occupant and all associated data. Continue?')) {
            showToast('Deleting occupant...', 'info');
            setTimeout(() => {
                showToast('Occupant deleted successfully!', 'success');
                
                // Redirect to occupants list
                setTimeout(() => {
                    window.location.href = '/admin/occupants';
                }, 1500);
            }, 2000);
        }
    }
}
</script>

<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
