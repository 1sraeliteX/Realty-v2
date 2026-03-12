<?php
require_once __DIR__ . '/../../config/property_types.php';

$title = 'Add Property';
$pageTitle = 'Add New Property';

// Check if we're editing an existing property
$isEditing = isset($property) && $property;
if ($isEditing) {
    $title = 'Edit Property';
    $pageTitle = 'Edit Property';
}

// Get property types configuration
$propertyTypes = include __DIR__ . '/../../config/property_types.php';
?>

<!-- Back Navigation -->
<div class="mb-6">
    <a href="/properties" class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Properties
    </a>
</div>

<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $pageTitle; ?></h1>
</div>

<!-- Main Form Container -->
<form id="property-form" class="space-y-8" method="POST" action="/admin/properties" enctype="multipart/form-data">
    
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
                <label for="property_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Property Type <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="property_type_search" 
                        name="property_type_search" 
                        required
                        value="<?php 
                            $selectedType = $property['type'] ?? '';
                            if ($selectedType) {
                                foreach ($propertyTypes as $type) {
                                    if ($type['value'] === $selectedType) {
                                        echo htmlspecialchars($type['label']);
                                        break;
                                    }
                                }
                            }
                        ?>"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                        placeholder="Search or select property type..."
                        autocomplete="off"
                    >
                    <input type="hidden" id="property_type" name="property_type" required value="<?php echo htmlspecialchars($property['type'] ?? ''); ?>">
                    <button type="button" id="property_type_toggle" class="absolute right-2 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    
                    <!-- Custom Dropdown -->
                    <div id="property_type_dropdown" class="absolute z-[100] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl max-h-80 overflow-y-auto hidden">
                        <!-- Categories will be dynamically populated -->
                    </div>
                </div>
                <span class="text-red-500 text-sm mt-1 hidden" id="property_type_error">Property type is required</span>
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
                    <option value="in_building">In Building</option>
                    <option value="borehole">Borehole</option>
                    <option value="water_tank">Water Tank</option>
                    <option value="none">None</option>
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
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="limited">Limited</option>
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
                ></textarea>
            </div>
        </div>
    </div>

    <!-- Section 3: Property Images -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Property Images</h2>
        
        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8">
            <div class="text-center mb-6">
                <div class="flex justify-center space-x-4">
                    <!-- Upload from Device -->
                    <button type="button" onclick="document.getElementById('image-upload').click()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-upload mr-2"></i>Upload from Device
                    </button>
                    
                    <!-- Take Photo -->
                    <button type="button" onclick="takePhoto()" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <i class="fas fa-camera mr-2"></i>Take Photo
                    </button>
                </div>
                
                <input type="file" id="image-upload" class="hidden" accept="image/*" multiple onchange="handleImageSelect(event)">
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                    Supported formats: JPG, PNG, GIF. Maximum file size: 5MB per image.
                </p>
            </div>
            
            <!-- Image Preview Grid -->
            <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Image previews will be dynamically added here -->
            </div>
        </div>
    </div>

    <!-- Section 4: Amenities -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Amenities</h2>
        
        <div class="space-y-4">
            <!-- Amenity Input -->
            <div class="flex gap-4">
                <input 
                    type="text" 
                    id="amenity-input" 
                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                    placeholder="Security, Generator, Water Tank"
                >
                <button type="button" onclick="addAmenity()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <i class="fas fa-plus mr-2"></i>Add
                </button>
            </div>
            
            <!-- Amenities List -->
            <div id="amenities-list" class="flex flex-wrap gap-2">
                <!-- Amenities will be dynamically added here -->
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4">
        <button type="button" onclick="window.location.href='/properties'" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
            Cancel
        </button>
        <button type="submit" id="submit-btn" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 flex items-center">
            <i class="fas fa-<?php echo $isEditing ? 'save' : 'plus'; ?> mr-2"></i>
            <span id="submit-text"><?php echo $isEditing ? 'Update Property' : 'Add Property'; ?></span>
            <div id="submit-loading" class="hidden ml-2">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </button>
    </div>
</form>

<script>
let selectedImages = [];
let amenities = [];

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    
    // Set styles based on type
    const typeStyles = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-black',
        info: 'bg-blue-500 text-white'
    };
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    toast.className = `${typeStyles[type]} px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-[300px] transform transition-all duration-300 translate-x-full`;
    toast.innerHTML = `
        <i class="fas ${icons[type]}"></i>
        <span>${message}</span>
    `;
    
    // Add to container
    toastContainer.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 5000);
}

// Image Upload Functions
function handleImageSelect(event) {
    const files = event.target.files;
    const preview = document.getElementById('image-preview');
    
    for (let file of files) {
        if (file.type.startsWith('image/')) {
            if (file.size > 5 * 1024 * 1024) { // 5MB limit
                showToast('File size must be less than 5MB', 'error');
                continue;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageContainer = document.createElement('div');
                imageContainer.className = 'relative group';
                imageContainer.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                    <button type="button" onclick="removeImage(${selectedImages.length})" class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                preview.appendChild(imageContainer);
                selectedImages.push(file);
            };
            reader.readAsDataURL(file);
        } else {
            showToast('Please select only image files', 'error');
        }
    }
}

function removeImage(index) {
    selectedImages.splice(index, 1);
    updateImagePreview();
}

function updateImagePreview() {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    selectedImages.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imageContainer = document.createElement('div');
            imageContainer.className = 'relative group';
            imageContainer.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                <button type="button" onclick="removeImage(${index})" class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-times text-xs"></i>
                </button>
            `;
            preview.appendChild(imageContainer);
        };
        reader.readAsDataURL(file);
    });
}

function takePhoto() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
                modal.innerHTML = `
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl">
                        <video id="camera-video" class="w-full rounded-lg mb-4" autoplay></video>
                        <div class="flex justify-center space-x-4">
                            <button type="button" onclick="capturePhoto()" class="px-4 py-2 bg-green-600 text-white rounded-lg">
                                <i class="fas fa-camera mr-2"></i>Capture
                            </button>
                            <button type="button" onclick="closeCamera()" class="px-4 py-2 bg-gray-600 text-white rounded-lg">
                                Cancel
                            </button>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
                
                const video = document.getElementById('camera-video');
                video.srcObject = stream;
                window.currentStream = stream;
            })
            .catch(function(err) {
                showToast('Camera access denied or not available', 'error');
            });
    } else {
        showToast('Camera not available on this device', 'error');
    }
}

function capturePhoto() {
    const video = document.getElementById('camera-video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);
    
    canvas.toBlob(function(blob) {
        const file = new File([blob], 'photo.jpg', { type: 'image/jpeg' });
        selectedImages.push(file);
        updateImagePreview();
        closeCamera();
        showToast('Photo captured successfully', 'success');
    }, 'image/jpeg');
}

function closeCamera() {
    if (window.currentStream) {
        window.currentStream.getTracks().forEach(track => track.stop());
    }
    const modal = document.querySelector('.fixed.inset-0');
    if (modal) {
        modal.remove();
    }
}

// Amenity Functions
function addAmenity() {
    const input = document.getElementById('amenity-input');
    const value = input.value.trim();
    
    if (value) {
        if (amenities.includes(value)) {
            showToast('Amenity already added', 'warning');
            return;
        }
        amenities.push(value);
        updateAmenitiesList();
        input.value = '';
    }
}

function updateAmenitiesList() {
    const list = document.getElementById('amenities-list');
    list.innerHTML = amenities.map((amenity, index) => `
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
            ${amenity}
            <button type="button" onclick="removeAmenity(${index})" class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                <i class="fas fa-times"></i>
            </button>
        </span>
    `).join('');
}

function removeAmenity(index) {
    amenities.splice(index, 1);
    updateAmenitiesList();
}

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
    const isEditing = <?php echo $isEditing ? 'true' : 'false'; ?>;
    
    submitBtn.disabled = true;
    submitText.textContent = isEditing ? 'Updating Property...' : 'Adding Property...';
    submitLoading.classList.remove('hidden');
    
    // Collect form data
    const formData = new FormData(this);
    
    // Add images
    selectedImages.forEach((image, index) => {
        formData.append(`images[${index}]`, image);
    });
    
    // Add amenities as JSON string
    formData.append('amenities', JSON.stringify(amenities));
    
    // Get authentication token (for API requests)
    const token = localStorage.getItem('jwt_token') || getCookie('jwt_token');
    
    // If no token, we need to handle this differently for web requests
    const isWebRequest = !token;
    
    // Map form fields to API field names
    const apiFormData = new FormData();
    apiFormData.append('name', formData.get('property_name'));
    apiFormData.append('address', formData.get('address'));
    apiFormData.append('type', formData.get('property_type'));
    apiFormData.append('rent_price', formData.get('yearly_rent'));
    apiFormData.append('year_built', formData.get('year_built'));
    apiFormData.append('bedrooms', formData.get('rooms'));
    apiFormData.append('bathrooms', formData.get('bathrooms'));
    apiFormData.append('kitchens', formData.get('kitchens'));
    apiFormData.append('parking', formData.get('parking'));
    apiFormData.append('water_availability', formData.get('water_availability'));
    apiFormData.append('description', formData.get('description'));
    
    // Parse amenities JSON and re-encode for API
    try {
        const amenitiesData = JSON.parse(formData.get('amenities'));
        apiFormData.append('amenities', JSON.stringify(amenitiesData));
    } catch (e) {
        apiFormData.append('amenities', '[]');
    }
    
    // Add images
    selectedImages.forEach((image, index) => {
        apiFormData.append(`images[${index}]`, image);
    });
    
    // Submit form using fetch to web endpoint
    const requestOptions = {
        method: 'POST',
        body: apiFormData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    // Add Authorization header only if we have a token
    if (token) {
        requestOptions.headers.Authorization = `Bearer ${token}`;
    }
    
    fetch('/properties', requestOptions)
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(data => {
                if (!response.ok) {
                    throw { type: 'validation', errors: data.errors || data.message || 'Form submission failed' };
                }
                return data;
            });
        } else {
            // Handle HTML response (likely an error page)
            return response.text().then(html => {
                if (!response.ok) {
                    throw { type: 'server', message: 'Server returned HTML response instead of JSON', html: html };
                }
                // Try to extract JSON from HTML if possible
                try {
                    const jsonMatch = html.match(/<script[^>]*>.*?window\.__INITIAL_STATE__\s*=\s*({.*?});/s);
                    if (jsonMatch) {
                        return JSON.parse(jsonMatch[1]);
                    }
                } catch (e) {
                    // Fallback to success
                }
                return { message: 'Property saved successfully' };
            });
        }
    })
    .then(data => {
        showToast(isEditing ? 'Property updated successfully!' : 'Property added successfully!', 'success');
        
        // Redirect to properties list
        setTimeout(() => {
            window.location.href = '/properties';
        }, 1500);
    })
    .catch(error => {
        console.error('Error:', error);
        
        if (error.type === 'validation') {
            // Show specific validation errors
            const errorMessages = Array.isArray(error.errors) ? error.errors.join(', ') : 
                                 typeof error.errors === 'object' ? Object.values(error.errors).flat().join(', ') : 
                                 error.errors;
            showToast(`Validation errors: ${errorMessages}`, 'error');
        } else {
            showToast('Error saving property. Please try again.', 'error');
            
            // If we have HTML response, log it for debugging
            if (error.html) {
                console.log('Server HTML response:', error.html);
            }
        }
        
        // Reset button state
        submitBtn.disabled = false;
        submitText.textContent = isEditing ? 'Update Property' : 'Add Property';
        submitLoading.classList.add('hidden');
    });
});

// Allow Enter key to add amenities
document.getElementById('amenity-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addAmenity();
    }
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

// Update form validation
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

// Property Type Dropdown Functionality
(function() {
    const propertyTypes = <?php echo json_encode($propertyTypes); ?>;
    const searchInput = document.getElementById('property_type_search');
    const hiddenInput = document.getElementById('property_type');
    const dropdown = document.getElementById('property_type_dropdown');
    const dropdownToggle = document.getElementById('property_type_toggle');
    const errorElement = document.getElementById('property_type_error');
    
    if (!searchInput || !hiddenInput || !dropdown || !dropdownToggle) {
        console.error('Property type dropdown elements not found');
        return;
    }
    
    // Get categories from property_type_helper
    const categories = {
        'residential': {
            label: 'Residential',
            types: ['apartment', 'flat', 'studio_apartment', 'duplex', 'triplex', 'quadplex', 'detached_house', 'semi_detached_house', 'bungalow', 'terrace_house', 'townhouse', 'condominium', 'penthouse', 'loft', 'cottage', 'villa', 'mansion', 'mobile_home', 'tiny_home', 'serviced_apartment', 'student_housing', 'co_living_space', 'lodge', 'self_contain', 'mini_flat', 'room_and_parlor', 'apartment_building', 'residential_complex', 'block_of_flats', 'hostel', 'dormitory', 'boarding_house']
        },
        'commercial': {
            label: 'Commercial',
            types: ['office_building', 'office_space', 'office_suite', 'co_working_space', 'retail_shop', 'shop', 'shopping_mall', 'strip_mall', 'supermarket', 'restaurant', 'cafe', 'bar', 'lounge', 'hotel', 'motel', 'guest_house', 'event_center', 'cinema', 'bank_building', 'clinic', 'hospital', 'pharmacy', 'school', 'training_center']
        },
        'industrial': {
            label: 'Industrial',
            types: ['warehouse', 'factory', 'manufacturing_plant', 'distribution_center', 'cold_storage_facility', 'assembly_plant', 'industrial_yard']
        },
        'land': {
            label: 'Land',
            types: ['residential_land', 'commercial_land', 'industrial_land', 'agricultural_land', 'farm_land', 'ranch_land', 'undeveloped_land', 'development_site', 'estate_plot']
        },
        'special': {
            label: 'Special',
            types: ['church', 'mosque', 'temple', 'cemetery', 'government_building', 'military_facility', 'prison', 'stadium', 'sports_complex', 'convention_center', 'library', 'museum']
        },
        'mixed': {
            label: 'Mixed Use',
            types: ['mixed_use_building', 'shop_and_apartment', 'office_and_retail_building', 'mixed_use_tower']
        }
    };
    
    let isDropdownOpen = false;
    let searchTimeout;
    
    // Initialize dropdown
    renderCategories(propertyTypes);
    
    function renderCategories(types) {
        dropdown.innerHTML = '';
        
        Object.entries(categories).forEach(([categoryKey, categoryData]) => {
            const categoryTypes = types.filter(type => categoryData.types.includes(type.value));
            
            if (categoryTypes.length > 0) {
                const categorySection = document.createElement('div');
                categorySection.className = 'mb-2';
                categorySection.innerHTML = `
                    <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        ${categoryData.label}
                    </div>
                    <div class="category-types">
                        ${categoryTypes.map(type => `
                            <div class="property-type-option px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 rounded" 
                                 data-value="${type.value}" data-label="${type.label}">
                                <div class="font-medium text-gray-900 dark:text-white">${type.label}</div>
                                ${type.description ? `<div class="text-sm text-gray-500 dark:text-gray-400">${type.description}</div>` : ''}
                            </div>
                        `).join('')}
                    </div>
                `;
                dropdown.appendChild(categorySection);
            }
        });
        
        // Add click handlers to all options
        dropdown.querySelectorAll('.property-type-option').forEach(option => {
            option.addEventListener('click', function() {
                selectPropertyType(this.dataset.value, this.dataset.label);
            });
        });
    }
    
    function filterPropertyTypes(searchTerm) {
        const allOptions = dropdown.querySelectorAll('.property-type-option');
        const categorySections = dropdown.querySelectorAll('.category-types');
        
        if (!searchTerm) {
            // Show all
            allOptions.forEach(option => option.classList.remove('hidden'));
            categorySections.forEach(section => section.classList.remove('hidden'));
            
            // Hide empty categories
            dropdown.querySelectorAll('.mb-2').forEach(categorySection => {
                const visibleOptions = categorySection.querySelectorAll('.property-type-option:not(.hidden)');
                const categoryHeader = categorySection.querySelector('.bg-gray-50, .dark\\:bg-gray-700');
                const categoryTypes = categorySection.querySelector('.category-types');
                
                if (visibleOptions.length === 0) {
                    categoryHeader.classList.add('hidden');
                    categoryTypes.classList.add('hidden');
                } else {
                    categoryHeader.classList.remove('hidden');
                    categoryTypes.classList.remove('hidden');
                }
            });
            return;
        }
        
        // Filter options
        allOptions.forEach(option => {
            const label = option.dataset.label.toLowerCase();
            const description = option.querySelector('.text-gray-500, .dark\\:text-gray-400')?.textContent.toLowerCase() || '';
            
            if (label.includes(searchTerm.toLowerCase()) || description.includes(searchTerm.toLowerCase())) {
                option.classList.remove('hidden');
            } else {
                option.classList.add('hidden');
            }
        });
        
        // Hide empty categories
        dropdown.querySelectorAll('.mb-2').forEach(categorySection => {
            const visibleOptions = categorySection.querySelectorAll('.property-type-option:not(.hidden)');
            const categoryHeader = categorySection.querySelector('.bg-gray-50, .dark\\:bg-gray-700');
            const categoryTypes = categorySection.querySelector('.category-types');
            
            if (visibleOptions.length === 0) {
                categoryHeader.classList.add('hidden');
                categoryTypes.classList.add('hidden');
            } else {
                categoryHeader.classList.remove('hidden');
                categoryTypes.classList.remove('hidden');
            }
        });
    }
    
    function toggleDropdown() {
        isDropdownOpen = !isDropdownOpen;
        if (isDropdownOpen) {
            dropdown.classList.remove('hidden');
            filterPropertyTypes(searchInput.value);
        } else {
            dropdown.classList.add('hidden');
        }
        dropdownToggle.innerHTML = isDropdownOpen ? '<i class="fas fa-chevron-up"></i>' : '<i class="fas fa-chevron-down"></i>';
    }
    
    function selectPropertyType(value, label) {
        hiddenInput.value = value;
        searchInput.value = label;
        isDropdownOpen = false;
        dropdown.classList.add('hidden');
        dropdownToggle.innerHTML = '<i class="fas fa-chevron-down"></i>';
        
        // Clear validation error
        if (errorElement) {
            errorElement.classList.add('hidden');
            searchInput.classList.remove('border-red-500');
        }
    }
    
    // Event listeners
    dropdownToggle.addEventListener('click', toggleDropdown);
    
    searchInput.addEventListener('focus', () => {
        if (!isDropdownOpen) {
            toggleDropdown();
        }
    });
    
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterPropertyTypes(e.target.value);
        }, 150);
        
        // Clear hidden input if search doesn't match selected value
        if (hiddenInput.value && e.target.value !== searchInput.defaultValue) {
            hiddenInput.value = '';
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.relative')) {
            isDropdownOpen = false;
            dropdown.classList.add('hidden');
            dropdownToggle.innerHTML = '<i class="fas fa-chevron-down"></i>';
        }
    });
    
    // Keyboard navigation
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            isDropdownOpen = false;
            dropdown.classList.add('hidden');
            searchInput.blur();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const firstVisible = dropdown.querySelector('.property-type-option:not(.hidden)');
            if (firstVisible) {
                selectPropertyType(firstVisible.dataset.value, firstVisible.dataset.label);
            }
        } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            const visibleOptions = Array.from(dropdown.querySelectorAll('.property-type-option:not(.hidden)'));
            const currentIndex = visibleOptions.findIndex(opt => opt.classList.contains('bg-primary-50'));
            
            let nextIndex;
            if (e.key === 'ArrowDown') {
                nextIndex = currentIndex < visibleOptions.length - 1 ? currentIndex + 1 : 0;
            } else {
                nextIndex = currentIndex > 0 ? currentIndex - 1 : visibleOptions.length - 1;
            }
            
            visibleOptions.forEach(opt => opt.classList.remove('bg-primary-50', 'dark:bg-primary-900'));
            visibleOptions[nextIndex].classList.add('bg-primary-50', 'dark:bg-primary-900');
            visibleOptions[nextIndex].scrollIntoView({ block: 'nearest' });
        }
    });
})();
</script>
