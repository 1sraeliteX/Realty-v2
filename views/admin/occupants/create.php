<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Add Occupant';
$pageTitle = 'Add Occupant';
$pageDescription = 'Add a new occupant to the system';

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

<!-- Progress Indicator -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Personal Information</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Basic details and contact</p>
            </div>
        </div>
        <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 mx-4"></div>
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium">2</div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Property Assignment</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">Unit and parking details</p>
            </div>
        </div>
        <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 mx-4"></div>
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium">3</div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Additional Details</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">Emergency contact and notes</p>
            </div>
        </div>
    </div>
</div>

<!-- Form Container -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    <form id="occupantForm" onsubmit="submitOccupantForm(event)">
        <!-- Step 1: Personal Information -->
        <div id="step1" class="step-content">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Personal Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                    <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                    <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                    <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Occupant Type *</label>
                    <select name="type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Type</option>
                        <option value="tenant">Tenant</option>
                        <option value="family_member">Family Member</option>
                        <option value="guest">Guest</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="nextStep(2)" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Next Step <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Property Assignment -->
        <div id="step2" class="step-content hidden">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Property Assignment</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Move-in Date *</label>
                    <input type="date" name="move_in_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parking Space</label>
                    <input type="text" name="parking_space" placeholder="e.g., P-101, Guest, None" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vehicle Information</label>
                    <input type="text" name="vehicle_info" placeholder="e.g., Tesla Model 3 - ABC123" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="temporary">Temporary</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex justify-between">
                <button type="button" onclick="previousStep(1)" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <button type="button" onclick="nextStep(3)" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Next Step <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Additional Details -->
        <div id="step3" class="step-content hidden">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Additional Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Phone</label>
                    <input type="tel" name="emergency_phone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Additional notes about the occupant..."></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-between">
                <button type="button" onclick="previousStep(2)" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-check mr-2"></i> Create Occupant
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let currentStep = 1;

// Mock units data (in a real app, this would come from an API)
const unitsData = <?php echo json_encode($units); ?>;

function nextStep(step) {
    // Validate current step
    if (!validateStep(currentStep)) {
        return;
    }
    
    // Hide current step
    document.getElementById(`step${currentStep}`).classList.add('hidden');
    
    // Show next step
    document.getElementById(`step${step}`).classList.remove('hidden');
    
    // Update progress indicator
    updateProgressIndicator(step);
    
    currentStep = step;
}

function previousStep(step) {
    // Hide current step
    document.getElementById(`step${currentStep}`).classList.add('hidden');
    
    // Show previous step
    document.getElementById(`step${step}`).classList.remove('hidden');
    
    // Update progress indicator
    updateProgressIndicator(step);
    
    currentStep = step;
}

function updateProgressIndicator(step) {
    // Update progress circles
    for (let i = 1; i <= 3; i++) {
        const circle = document.querySelector(`.flex.items-center:nth-child(${i * 2 - 1}) .w-8`);
        const title = document.querySelector(`.flex.items-center:nth-child(${i * 2 - 1}) h3`);
        const subtitle = document.querySelector(`.flex.items-center:nth-child(${i * 2 - 1}) p`);
        
        if (i < step) {
            circle.className = 'w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium';
            circle.innerHTML = '<i class="fas fa-check"></i>';
            title.className = 'text-sm font-medium text-gray-900 dark:text-white';
            subtitle.className = 'text-xs text-gray-500 dark:text-gray-400';
        } else if (i === step) {
            circle.className = 'w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-medium';
            circle.innerHTML = i;
            title.className = 'text-sm font-medium text-gray-900 dark:text-white';
            subtitle.className = 'text-xs text-gray-500 dark:text-gray-400';
        } else {
            circle.className = 'w-8 h-8 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium';
            circle.innerHTML = i;
            title.className = 'text-sm font-medium text-gray-500 dark:text-gray-400';
            subtitle.className = 'text-xs text-gray-400 dark:text-gray-500';
        }
    }
}

function validateStep(step) {
    const stepElement = document.getElementById(`step${step}`);
    const requiredFields = stepElement.querySelectorAll('[required]');
    
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            field.focus();
            showToast(`Please fill in ${field.previousElementSibling.textContent.replace('*', '').trim()}`, 'error');
            return false;
        }
    }
    
    return true;
}

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
            option.textContent = unit.number;
            unitSelect.appendChild(option);
        });
    }
}

function submitOccupantForm(event) {
    event.preventDefault();
    
    if (!validateStep(3)) {
        return;
    }
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        showToast('Occupant created successfully!', 'success');
        
        // Redirect to occupants list
        setTimeout(() => {
            window.location.href = '/admin/occupants';
        }, 1500);
    }, 2000);
}
</script>

<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
