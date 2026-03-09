<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Add Property';
$pageTitle = 'Add New Property';
$pageDescription = 'Add a new property to your portfolio';

ob_start();
?>

<!-- Form Header -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Property</h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Fill in the property details below</p>
</div>

<!-- Progress Indicator -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Basic Information</span>
        </div>
        <div class="flex-1 h-px bg-gray-300 dark:bg-gray-600 mx-4"></div>
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">2</div>
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Property Details</span>
        </div>
        <div class="flex-1 h-px bg-gray-300 dark:bg-gray-600 mx-4"></div>
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">3</div>
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Pricing</span>
        </div>
        <div class="flex-1 h-px bg-gray-300 dark:bg-gray-600 mx-4"></div>
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">4</div>
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Images</span>
        </div>
    </div>
</div>

<!-- Add Property Form -->
<form id="addPropertyForm" class="space-y-8">
    <!-- Step 1: Basic Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Basic Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php echo UIComponents::input('property_name', 'Property Name', 'text', '', 'Enter property name', true); ?>
            <?php echo UIComponents::input('address', 'Address', 'text', '', 'Enter full address', true); ?>
            <?php echo UIComponents::input('city', 'City', 'text', '', 'Enter city', true); ?>
            <?php echo UIComponents::input('state', 'State', 'text', '', 'Enter state', true); ?>
            <?php echo UIComponents::input('zip_code', 'ZIP Code', 'text', '', 'Enter ZIP code', true); ?>
            <?php echo UIComponents::input('country', 'Country', 'text', 'United States', 'Enter country'); ?>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <?php 
            echo UIComponents::select(
                'property_type',
                'Property Type',
                [
                    '' => 'Select type',
                    'Residential' => 'Residential',
                    'Commercial' => 'Commercial',
                    'Industrial' => 'Industrial',
                    'Mixed Use' => 'Mixed Use'
                ],
                '',
                true
            ); ?>
            
            <?php 
            echo UIComponents::select(
                'status',
                'Status',
                [
                    '' => 'Select status',
                    'available' => 'Available',
                    'occupied' => 'Occupied',
                    'maintenance' => 'Under Maintenance'
                ],
                '',
                true
            ); ?>
            
            <?php echo UIComponents::input('year_built', 'Year Built', 'number', '', 'e.g., 2018'); ?>
        </div>
    </div>

    <!-- Step 2: Property Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Property Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php echo UIComponents::input('total_units', 'Total Units', 'number', '', 'Number of units', true); ?>
            <?php echo UIComponents::input('size_sqft', 'Total Size (sq ft)', 'number', '', 'Total square footage'); ?>
            <?php echo UIComponents::input('lot_size', 'Lot Size (acres)', 'number', '', 'Lot size in acres'); ?>
            <?php echo UIComponents::input('parking_spaces', 'Parking Spaces', 'number', '', 'Number of parking spaces'); ?>
        </div>
        
        <div class="mt-6">
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Description
            </label>
            <textarea 
                id="description" 
                name="description" 
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Describe the property, features, location benefits, etc."
            ></textarea>
        </div>
        
        <!-- Amenities -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Amenities
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Swimming Pool" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Swimming Pool</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Fitness Center" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Fitness Center</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Secured Parking" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Secured Parking</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Elevator" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Elevator</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Laundry Room" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Laundry Room</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Pet Friendly" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Pet Friendly</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Air Conditioning" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Air Conditioning</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Heating" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Heating</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Balcony" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Balcony</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Storage" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Storage</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Garden" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Garden</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="amenities[]" value="Security System" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Security System</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Step 3: Pricing -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Pricing Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php echo UIComponents::input('purchase_price', 'Purchase Price', 'number', '', 'e.g., 2500000', true); ?>
            <?php echo UIComponents::input('current_value', 'Current Market Value', 'number', '', 'e.g., 2750000'); ?>
            <?php echo UIComponents::input('monthly_revenue', 'Expected Monthly Revenue', 'number', '', 'e.g., 28800'); ?>
            <?php echo UIComponents::input('annual_expenses', 'Annual Expenses', 'number', '', 'e.g., 50000'); ?>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <?php echo UIComponents::input('property_tax', 'Annual Property Tax', 'number', '', 'e.g., 30000'); ?>
            <?php echo UIComponents::input('insurance', 'Annual Insurance', 'number', '', 'e.g., 12000'); ?>
            <?php echo UIComponents::input('maintenance_fee', 'Monthly Maintenance Fee', 'number', '', 'e.g., 2000'); ?>
        </div>
    </div>

    <!-- Step 4: Images -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Property Images</h3>
        
        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                Click to upload or drag and drop
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                PNG, JPG, GIF up to 10MB each. Maximum 10 images.
            </p>
            <input type="file" id="property_images" name="property_images[]" multiple accept="image/*" class="hidden">
            <button type="button" onclick="document.getElementById('property_images').click()" class="mt-4 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                Select Images
            </button>
        </div>
        
        <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 hidden">
            <!-- Image previews will be added here dynamically -->
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-end space-x-4">
        <a href="/admin/dashboard/properties" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            Cancel
        </a>
        <button type="submit" id="saveBtn" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            <span id="saveBtnText">Save Property</span>
            <div id="saveBtnSpinner" class="hidden ml-2 inline-block">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addPropertyForm');
    const saveBtn = document.getElementById('saveBtn');
    const saveBtnText = document.getElementById('saveBtnText');
    const saveBtnSpinner = document.getElementById('saveBtnSpinner');
    const imageInput = document.getElementById('property_images');
    const imagePreview = document.getElementById('imagePreview');

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const requiredFields = ['property_name', 'address', 'city', 'state', 'zip_code', 'property_type', 'status', 'total_units', 'purchase_price'];
        let isValid = true;
        
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        if (!isValid) {
            showToast('Please fill in all required fields', 'error');
            return;
        }
        
        // Show loading state
        setLoading(true);
        
        // Simulate API call
        setTimeout(function() {
            showToast('Property added successfully!', 'success');
            setTimeout(() => {
                window.location.href = '/admin/dashboard/properties';
            }, 1500);
        }, 2000);
    });

    // Image upload handling
    imageInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        
        if (files.length > 0) {
            imagePreview.classList.remove('hidden');
            imagePreview.innerHTML = '';
            
            files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-32 object-cover rounded-lg">
                            <button type="button" onclick="removeImage(${index})" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        `;
                        imagePreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    function setLoading(isLoading) {
        if (isLoading) {
            saveBtn.disabled = true;
            saveBtnText.textContent = 'Saving...';
            saveBtnSpinner.classList.remove('hidden');
        } else {
            saveBtn.disabled = false;
            saveBtnText.textContent = 'Save Property';
            saveBtnSpinner.classList.add('hidden');
        }
    }

    function removeImage(index) {
        // Remove image from preview
        const previews = imagePreview.querySelectorAll('div');
        if (previews[index]) {
            previews[index].remove();
        }
        
        // Update file input
        const dt = new DataTransfer();
        const files = Array.from(imageInput.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        imageInput.files = dt.files;
        
        // Hide preview if no images
        if (files.length === 0) {
            imagePreview.classList.add('hidden');
        }
    }

    // Auto-format numeric inputs
    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !isNaN(this.value)) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
});

// Drag and drop functionality
const dropZone = document.querySelector('.border-dashed');

dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
    
    const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
    if (files.length > 0) {
        document.getElementById('property_images').files = e.dataTransfer.files;
        document.getElementById('property_images').dispatchEvent(new Event('change'));
    }
});
</script>

<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
