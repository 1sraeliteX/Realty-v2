<?php
require_once __DIR__ . '/../../components/SearchableDropdown.php';
require_once __DIR__ . '/../../config/property_types.php';

$title = 'Edit Property';
$pageTitle = 'Edit Property';

// Get property types configuration
$propertyTypes = include __DIR__ . '/../../config/property_types.php';

// Check if we're editing an existing property
$isEditing = isset($property) && $property;
if (!$isEditing) {
    // Redirect to create page if no property data
    header('Location: /admin/properties/create');
    exit;
}
?>

<!-- Back Navigation -->
<div class="mb-6">
    <a href="/admin/properties" class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Properties
    </a>
</div>

<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $pageTitle; ?></h1>
</div>

<!-- Main Form Container -->
<form id="property-form" class="space-y-8" method="POST" action="/admin/properties/<?php echo $property['id']; ?>" enctype="multipart/form-data">
    <!-- Hidden field for editing -->
    <input type="hidden" name="_method" value="PUT">
    
    <!-- Section 1: Basic Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Basic Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Property Name -->
            <div>
                <label for="property_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Property Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="property_name" 
                    name="property_name" 
                    required
                    value="<?php echo htmlspecialchars($property['name'] ?? ''); ?>"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                    placeholder="e.g. Sunrise Lodge"
                >
                <span class="text-red-500 text-sm mt-1 hidden" id="property_name_error">Property name is required</span>
            </div>
            
            <!-- Address -->
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Address <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="address" 
                    name="address" 
                    required
                    value="<?php echo htmlspecialchars($property['address'] ?? ''); ?>"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                    placeholder="Full address"
                >
                <span class="text-red-500 text-sm mt-1 hidden" id="address_error">Address is required</span>
            </div>
            
            <!-- Property Type -->
            <div>
                <?php
                echo renderSearchableDropdown(
                    $propertyTypes,
                    'property_type',
                    'property_type',
                    'Property Type',
                    'Search or select property type...',
                    $property['type'] ?? '',
                    true,
                    false,
                    ''
                );
                ?>
            </div>
            
            <!-- Yearly Rent -->
            <div>
                <label for="yearly_rent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Yearly Rent <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="yearly_rent" 
                    name="yearly_rent" 
                    required
                    min="0"
                    step="0.01"
                    value="<?php echo htmlspecialchars($property['rent_price'] ?? ''); ?>"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                    placeholder="0.00"
                >
                <span class="text-red-500 text-sm mt-1 hidden" id="yearly_rent_error">Yearly rent is required</span>
            </div>
            
            <!-- Year Built -->
            <div>
                <label for="year_built" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Year Built <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="year_built" 
                    name="year_built" 
                    required
                    min="1900"
                    max="<?php echo date('Y'); ?>"
                    value="<?php echo htmlspecialchars($property['year_built'] ?? ''); ?>"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                    placeholder="2023"
                >
                <span class="text-red-500 text-sm mt-1 hidden" id="year_built_error">Year built is required</span>
            </div>
        </div>
    </div>

    <!-- Section 2: Property Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Property Details</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Number of Rooms -->
            <div>
                <label for="rooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Number of Rooms <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="rooms" 
                    name="rooms" 
                    required
                    min="0"
                    value="<?php echo htmlspecialchars($property['bedrooms'] ?? ''); ?>"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                    placeholder="0"
                >
                <span class="text-red-500 text-sm mt-1 hidden" id="rooms_error">Number of rooms is required</span>
            </div>
            
            <!-- Number of Kitchens -->
            <div>
                <label for="kitchens" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Number of Kitchens <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="kitchens" 
                    name="kitchens" 
                    required
                    min="0"
                    value="<?php echo htmlspecialchars($property['kitchens'] ?? ''); ?>"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                    placeholder="0"
                >
                <span class="text-red-500 text-sm mt-1 hidden" id="kitchens_error">Number of kitchens is required</span>
            </div>
            
            <!-- Number of Bathrooms -->
            <div>
                <label for="bathrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Number of Bathrooms <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="bathrooms" 
                    name="bathrooms" 
                    required
                    min="0"
                    step="0.5"
                    value="<?php echo htmlspecialchars($property['bathrooms'] ?? ''); ?>"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                    placeholder="0"
                >
                <span class="text-red-500 text-sm mt-1 hidden" id="bathrooms_error">Number of bathrooms is required</span>
            </div>
            
            <!-- Water Availability -->
            <div>
                <label for="water_availability" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Water Availability <span class="text-red-500">*</span>
                </label>
                <select 
                    id="water_availability" 
                    name="water_availability" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                    <option value="">Select option</option>
                    <option value="in_building" <?php echo ($property['water_availability'] ?? '') === 'in_building' ? 'selected' : ''; ?>>In Building</option>
                    <option value="borehole" <?php echo ($property['water_availability'] ?? '') === 'borehole' ? 'selected' : ''; ?>>Borehole</option>
                    <option value="water_tank" <?php echo ($property['water_availability'] ?? '') === 'water_tank' ? 'selected' : ''; ?>>Water Tank</option>
                    <option value="none" <?php echo ($property['water_availability'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                </select>
                <span class="text-red-500 text-sm mt-1 hidden" id="water_availability_error">Water availability is required</span>
            </div>
            
            <!-- Parking Spaces -->
            <div>
                <label for="parking" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Parking Spaces <span class="text-red-500">*</span>
                </label>
                <select 
                    id="parking" 
                    name="parking" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                    <option value="">Select option</option>
                    <option value="yes" <?php echo ($property['parking'] ?? '') === 'yes' ? 'selected' : ''; ?>>Yes</option>
                    <option value="no" <?php echo ($property['parking'] ?? '') === 'no' ? 'selected' : ''; ?>>No</option>
                    <option value="limited" <?php echo ($property['parking'] ?? '') === 'limited' ? 'selected' : ''; ?>>Limited</option>
                </select>
                <span class="text-red-500 text-sm mt-1 hidden" id="parking_error">Parking option is required</span>
            </div>
            
            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none" 
                    placeholder="Enter property description..."
                ><?php echo htmlspecialchars($property['description'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4">
        <button type="button" onclick="window.location.href='/admin/properties'" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
            Cancel
        </button>
        <button type="submit" id="submit-btn" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 flex items-center">
            <i class="fas fa-save mr-2"></i>
            <span id="submit-text">Update Property</span>
            <div id="submit-loading" class="hidden ml-2">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </button>
    </div>
</form>

<script>
// Form Submission
document.getElementById('property-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!validateForm()) {
        showToast('Please fill in all required fields', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');
    
    submitBtn.disabled = true;
    submitText.textContent = 'Updating Property...';
    submitLoading.classList.remove('hidden');
    
    // Collect form data
    const formData = new FormData(this);
    
    // Add method override for PUT
    formData.append('_method', 'PUT');
    
    // Submit form using fetch
    fetch('/admin/properties/<?php echo $property['id']; ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else if (response.status === 422) {
            return response.json().then(data => {
                throw { type: 'validation', errors: data.errors };
            });
        } else {
            throw { type: 'server', message: 'Form submission failed' };
        }
    })
    .then(data => {
        showToast('Property updated successfully!', 'success');
        
        // Redirect to properties list
        setTimeout(() => {
            window.location.href = '/admin/properties';
        }, 1500);
    })
    .catch(error => {
        console.error('Error:', error);
        
        if (error.type === 'validation') {
            // Show specific validation errors
            const errorMessages = Object.values(error.errors).flat().join(', ');
            showToast(`Validation errors: ${errorMessages}`, 'error');
        } else {
            showToast('Error updating property. Please try again.', 'error');
        }
        
        // Reset button state
        submitBtn.disabled = false;
        submitText.textContent = 'Update Property';
        submitLoading.classList.add('hidden');
    });
});

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Clear error on input for all fields
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', function() {
            const error = document.getElementById(this.id + '_error');
            if (error) {
                error.classList.add('hidden');
                this.classList.remove('border-red-500');
            }
        });
    });
});

// Form validation
function validateForm() {
    let isValid = true;
    const requiredFields = [
        'property_name', 'address', 'property_type', 'yearly_rent', 'year_built',
        'rooms', 'kitchens', 'bathrooms', 'water_availability', 'parking'
    ];
    
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        const error = document.getElementById(fieldId + '_error');
        
        if (!field.value.trim()) {
            error.classList.remove('hidden');
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            error.classList.add('hidden');
            field.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}
</script>
