<?php
// Anti-Scattering Compliance: Use framework bootstrap
require_once __DIR__ . '/../../../config/bootstrap.php';

// Load UIComponents for form rendering (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Load AutoFillComponent using ComponentRegistry
ComponentRegistry::load('autofill-component');

// Set up view data
ViewManager::set('title', 'Add Property');
ViewManager::set('user', $admin);
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
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Revenue & Expenses</span>
        </div>
        <div class="flex-1 h-px bg-gray-300 dark:bg-gray-600 mx-4"></div>
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">4</div>
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Rent Record</span>
        </div>
        <div class="flex-1 h-px bg-gray-300 dark:bg-gray-600 mx-4"></div>
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">5</div>
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Images</span>
        </div>
    </div>
</div>

<?php
// Get admin user
$admin = $admin ?? null;
if (!$admin) {
    header('Location: /admin/login');
    exit;
}
?>

<!-- Add Property Form -->
<form id="addPropertyForm" action="/admin/properties" method="POST" enctype="multipart/form-data" class="space-y-8">
    
    <?php
    // Add auto-fill button at the top
    try {
        echo "<!-- DEBUG: About to call AutoFillComponent -->\n";
        
        // Check if class exists in current scope
        if (class_exists('Components\AutoFillComponent')) {
            echo "<!-- DEBUG: AutoFillComponent class exists -->\n";
            \Components\AutoFillComponent::generateAutoFillButton(
                'addPropertyForm', 
                \Components\AutoFillComponent::getPropertyFillData(),
                'Auto-Fill Property Form',
                'bg-purple-600 hover:bg-purple-700 text-white'
            );
            echo "<!-- DEBUG: AutoFillComponent call completed -->\n";
        } else {
            echo "<!-- DEBUG: AutoFillComponent class does not exist, trying to load again -->\n";
            ComponentRegistry::load('autofill-component');
            if (class_exists('Components\AutoFillComponent')) {
                echo "<!-- DEBUG: AutoFillComponent class exists after reload -->\n";
                \Components\AutoFillComponent::generateAutoFillButton(
                    'addPropertyForm', 
                    \Components\AutoFillComponent::getPropertyFillData(),
                    'Auto-Fill Property Form',
                    'bg-purple-600 hover:bg-purple-700 text-white'
                );
                echo "<!-- DEBUG: AutoFillComponent call completed after reload -->\n";
            } else {
                echo "<!-- DEBUG: AutoFillComponent class still does not exist -->\n";
            }
        }
    } catch (Exception $e) {
        echo "<!-- DEBUG: AutoFillComponent error: " . $e->getMessage() . " -->\n";
    } catch (Error $e) {
        echo "<!-- DEBUG: AutoFillComponent fatal error: " . $e->getMessage() . " -->\n";
    }
    ?>
    <!-- Step 1: Basic Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-info-circle mr-2 text-primary-600"></i>
                Basic Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php echo UIComponents::input('name', 'Property Name', 'text', '', 'Enter property name', true); ?>
                <?php echo UIComponents::input('address', 'Address', 'text', '', 'Enter full address', true); ?>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <?php 
                // Load property types from configuration
                $propertyTypes = require_once __DIR__ . '/../../../config/property_types.php';
                $typeOptions = ['' => 'Select type'];
                foreach ($propertyTypes as $type) {
                    $typeOptions[$type['value']] = $type['label'];
                }
                
                echo UIComponents::select(
                    'type',
                    'Property Type',
                    $typeOptions,
                    '',
                    true
                ); ?>
                
                <?php 
                echo UIComponents::select(
                    'status',
                    'Status',
                    [
                        '' => 'Select status',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'maintenance' => 'Maintenance'
                    ],
                    'active',
                    true
                ); ?>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <?php echo UIComponents::input('year_built', 'Year Built', 'number', '', 'e.g., 2018'); ?>
                
                <?php 
                echo UIComponents::select(
                    'water_availability',
                    'Water Availability',
                    [
                        '' => 'Select option',
                        'yes' => 'Available',
                        'no' => 'Not Available'
                    ],
                    '',
                    true
                ); ?>
            </div>
            
            <div class="mt-6">
                <?php echo UIComponents::textarea('description', 'Description', '', 'Property description and features'); ?>
            </div>
        </div>
    </div>

    <!-- Step 2: Property Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-building mr-2 text-primary-600"></i>
                Property Details
                <button type="button" id="propertyDetailsToggle" class="ml-auto text-primary-600 hover:text-primary-700">
                    <i class="fas fa-chevron-down transition-transform" id="propertyDetailsIcon"></i>
                </button>
            </h3>
            
            <div id="propertyDetailsContent" class="hidden">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php echo UIComponents::input('bedrooms', 'Bedrooms', 'number', '', 'Number of bedrooms'); ?>
                <?php echo UIComponents::input('bathrooms', 'Bathrooms', 'number', '', 'Number of bathrooms'); ?>
                <?php echo UIComponents::input('kitchens', 'Kitchens', 'number', '', 'Number of kitchens'); ?>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <?php 
                echo UIComponents::select(
                    'parking',
                    'Parking Available',
                    [
                        '' => 'Select option',
                        'yes' => 'Yes',
                        'no' => 'No'
                    ],
                    '',
                    true
                ); ?>
                <?php echo UIComponents::input('category', 'Category', 'text', '', 'Property category'); ?>
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                    <i class="fas fa-star mr-2 text-yellow-500"></i>
                    Property Amenities
                </label>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Swimming Pool" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Swimming Pool</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Fitness Center" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Fitness Center</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Secured Parking" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Secured Parking</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Elevator" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Elevator</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Laundry Room" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Laundry Room</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Pet Friendly" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Pet Friendly</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Air Conditioning" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Air Conditioning</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Heating" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Heating</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Balcony" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Balcony</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Storage" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Storage</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Garden" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Garden</span>
                        </label>
                        <label class="flex items-center p-3 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500 hover:border-primary-500 dark:hover:border-primary-400 transition-colors cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Security System" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 font-medium">Security System</span>
                        </label>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Revenue and Expenses (Optional) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-dollar-sign mr-2 text-primary-600"></i>
                    Revenue and Expenses
                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Optional</span>
                </h3>
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="skipPricing" name="skip_pricing" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Skip revenue and expenses for now</span>
                    </label>
                    <button type="button" id="pricingToggle" class="ml-4 text-primary-600 hover:text-primary-700">
                        <i class="fas fa-chevron-down transition-transform" id="pricingIcon"></i>
                    </button>
                </div>
            </div>
            
            <div id="pricingContent">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php echo UIComponents::input('monthly_revenue', 'Expected Yearly Revenue', 'number', '', 'e.g., 28800'); ?>
                <?php echo UIComponents::input('annual_expenses', 'Annual Expenses', 'number', '', 'e.g., 50000'); ?>
            </div>
            
            <div id="pricingAdditionalContent" class="hidden pb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <?php echo UIComponents::input('property_tax', 'Annual Property Tax', 'number', '', 'e.g., 30000'); ?>
                <?php echo UIComponents::input('insurance', 'Annual Insurance', 'number', '', 'e.g., 12000'); ?>
                <?php echo UIComponents::input('maintenance_fee', 'Monthly Maintenance Fee', 'number', '', 'e.g., 2000'); ?>
            </div>
            </div>
            </div>
        </div>
    </div>

    <!-- Rent Record Information Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-money-bill-wave mr-2 text-primary-600"></i>
                    Rent Record Information
                </h3>
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="skipRentRecord" name="skip_rent_record" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Skip rent record for now</span>
                    </label>
                    <button type="button" id="rentRecordToggle" class="ml-4 text-primary-600 hover:text-primary-700">
                        <i class="fas fa-chevron-down transition-transform" id="rentRecordIcon"></i>
                    </button>
                </div>
            </div>
            
            <div id="rentRecordContent">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Rent Price -->
                    <div>
                        <label for="monthly_rent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rent Price
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500 dark:text-gray-400">₦</span>
                            <input 
                                type="number" 
                                id="monthly_rent" 
                                name="monthly_rent" 
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                        </div>
                    </div>

                    <!-- Rent Payment Frequency -->
                    <div>
                        <label for="rent_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Payment Frequency
                        </label>
                        <select 
                            id="rent_frequency" 
                            name="rent_frequency" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="annually">Annually</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Security Deposit -->
                <div>
                    <label for="security_deposit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Security Deposit
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500 dark:text-gray-400">₦</span>
                        <input 
                            type="number" 
                            id="security_deposit" 
                            name="security_deposit" 
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 4: Images -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-images mr-2 text-primary-600"></i>
                Property Images
            </h3>
            
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                    Click to upload or drag and drop
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    PNG, JPG, GIF up to 10MB each. Maximum 10 images.
                </p>
                <input type="file" id="property_images" name="images[]" multiple accept="image/*" class="hidden">
                <button type="button" onclick="document.getElementById('property_images').click()" class="mt-4 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                    Select Images
                </button>
            </div>
            
            <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 hidden">
                <!-- Image previews will be added here dynamically -->
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-end space-x-4 mx-4 md:mx-6 my-6">
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
        
        console.log('=== PROPERTY CREATION DEBUG ===');
        console.log('Form submission started');
        
        // Clear previous errors
        document.querySelectorAll('.border-red-500').forEach(field => {
            field.classList.remove('border-red-500');
        });
        
        // Basic validation
        const requiredFields = ['name', 'address', 'type', 'status'];
        let isValid = true;
        let validationErrors = [];
        
        // Check if pricing is skipped
        const skipPricing = document.querySelector('#skipPricing').checked;
        
        // Check if rent record is skipped
        const skipRentRecord = document.querySelector('#skipRentRecord').checked;
        
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field || !field.value.trim()) {
                if (field) {
                    field.classList.add('border-red-500');
                }
                validationErrors.push(`${fieldName} is required`);
                isValid = false;
            } else {
                console.log(`Field ${fieldName}:`, field.value);
                if (field) {
                    field.classList.remove('border-red-500');
                }
            }
        });
        
        // Validate numeric fields only if pricing is not skipped
        const numericFields = skipPricing ? ['bedrooms', 'bathrooms', 'kitchens'] : ['bedrooms', 'bathrooms', 'kitchens', 'monthly_revenue', 'annual_expenses'];
        
        // Validate rent record fields only if rent record is not skipped
        const rentRecordFields = skipRentRecord ? [] : ['monthly_rent', 'security_deposit'];
        
        // Combine all numeric fields for validation
        const allNumericFields = [...numericFields, ...rentRecordFields];
        
        allNumericFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field && field.value && isNaN(field.value)) {
                field.classList.add('border-red-500');
                validationErrors.push(`${fieldName} must be a valid number`);
                isValid = false;
            }
        });
        
        if (!isValid) {
            console.error('Validation errors:', validationErrors);
            showToast('Please fix the following errors: ' + validationErrors.join(', '), 'error');
            return;
        }
        
        // Show loading state
        setLoading(true);
        
        // Create FormData for file upload
        const formData = new FormData(form);
        
        // Log form data for debugging
        console.log('Form data being submitted:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}:`, value);
        }
        
        // Submit to server
        fetch('/admin/properties', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.text().then(text => {
                console.log('Raw response:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    throw new Error('Invalid JSON response');
                }
            });
        })
        .then(data => {
            console.log('Parsed response data:', data);
            setLoading(false);
            
            if (data.errors) {
                console.error('Server validation errors:', data.errors);
                // Handle validation errors
                Object.keys(data.errors).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('border-red-500');
                        console.error(`Error on field ${field}:`, data.errors[field]);
                    }
                });
                showToast('Please correct the errors and try again', 'error');
            } else if (data.success) {
                console.log('Property created successfully:', data);
                showToast('Property added successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '/admin/properties';
                }, 1500);
            } else {
                console.error('Unexpected response format:', data);
                showToast('Unexpected response from server', 'error');
            }
        })
        .catch(error => {
            console.error('=== SUBMISSION ERROR ===');
            console.error('Error details:', error);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            
            setLoading(false);
            showToast('Error adding property: ' + error.message, 'error');
            
            // Show detailed error in console for debugging
            const errorDetails = {
                message: error.message,
                timestamp: new Date().toISOString(),
                formData: Array.from(formData.entries())
            };
            console.error('Full error details:', errorDetails);
        });
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

    // Toggle property details section
    document.getElementById('propertyDetailsToggle').addEventListener('click', function() {
        const content = document.getElementById('propertyDetailsContent');
        const icon = document.getElementById('propertyDetailsIcon');
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            content.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    });
    
    // Toggle pricing section
    document.getElementById('pricingToggle').addEventListener('click', function() {
        const content = document.getElementById('pricingContent');
        const additionalContent = document.getElementById('pricingAdditionalContent');
        const icon = document.getElementById('pricingIcon');
        const skipCheckbox = document.getElementById('skipPricing');
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            additionalContent.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
            skipCheckbox.checked = false;
        } else {
            content.classList.add('hidden');
            additionalContent.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
            skipCheckbox.checked = true;
        }
    });
    
    // Handle skip pricing checkbox
    document.getElementById('skipPricing').addEventListener('change', function() {
        const content = document.getElementById('pricingContent');
        const additionalContent = document.getElementById('pricingAdditionalContent');
        const icon = document.getElementById('pricingIcon');
        
        if (this.checked) {
            content.classList.add('hidden');
            additionalContent.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
            
            // Clear pricing field validation errors
            const pricingFields = ['monthly_revenue', 'annual_expenses', 'property_tax', 'insurance', 'maintenance_fee', 'monthly_rent', 'security_deposit'];
            pricingFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.classList.remove('border-red-500');
                    field.removeAttribute('required');
                }
            });
        } else {
            content.classList.remove('hidden');
            additionalContent.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        }
    });
    
    // Toggle rent record section
    document.getElementById('rentRecordToggle').addEventListener('click', function() {
        const content = document.getElementById('rentRecordContent');
        const icon = document.getElementById('rentRecordIcon');
        const skipCheckbox = document.getElementById('skipRentRecord');
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
            skipCheckbox.checked = false;
        } else {
            content.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
            skipCheckbox.checked = true;
        }
    });
    
    // Handle skip rent record checkbox
    document.getElementById('skipRentRecord').addEventListener('change', function() {
        const content = document.getElementById('rentRecordContent');
        const icon = document.getElementById('rentRecordIcon');
        
        if (this.checked) {
            content.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
            
            // Clear rent record field validation errors
            const rentRecordFields = ['monthly_rent', 'security_deposit'];
            rentRecordFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.classList.remove('border-red-500');
                    field.removeAttribute('required');
                }
            });
        } else {
            content.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        }
    });
    
    // Initialize with property details collapsed and pricing optional
    document.getElementById('propertyDetailsContent').classList.add('hidden');
    document.getElementById('skipPricing').checked = false;
    document.getElementById('pricingContent').classList.remove('hidden');
    document.getElementById('pricingAdditionalContent').classList.remove('hidden');
    document.getElementById('pricingIcon').classList.remove('fa-chevron-down');
    document.getElementById('pricingIcon').classList.add('fa-chevron-up');
    
    // Initialize rent record section
    document.getElementById('skipRentRecord').checked = false;
    document.getElementById('rentRecordContent').classList.remove('hidden');
    document.getElementById('rentRecordIcon').classList.remove('fa-chevron-down');
    document.getElementById('rentRecordIcon').classList.add('fa-chevron-up');

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
