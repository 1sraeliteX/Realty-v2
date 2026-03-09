<?php
$title = 'Edit Unit';
$pageTitle = 'Edit Unit';

$unit = $unit ?? [];
$properties = $properties ?? [];
$unitTypes = $unitTypes ?? [];

// Parse amenities if they exist
$unitAmenities = [];
if (!empty($unit['amenities'])) {
    $unitAmenities = json_decode($unit['amenities'], true) ?: [];
}

$content = ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Unit: <?php echo htmlspecialchars($unit['unit_number']); ?></h1>
                <a href="/admin/units" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form id="unit-form" class="p-6 space-y-6">
            <!-- Property Selection -->
            <div>
                <label for="property_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Property <span class="text-red-500">*</span>
                </label>
                <select 
                    id="property_id" 
                    name="property_id" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                    <option value="">Select a property</option>
                    <?php foreach ($properties as $property): ?>
                        <option value="<?php echo $property['id']; ?>" <?php echo $property['id'] == $unit['property_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($property['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Unit Number -->
            <div>
                <label for="unit_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Unit Number <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="unit_number" 
                    name="unit_number" 
                    required
                    value="<?php echo htmlspecialchars($unit['unit_number']); ?>"
                    placeholder="e.g., A-101, B-205"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
            </div>

            <!-- Unit Type -->
            <div>
                <label for="unit_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Unit Type <span class="text-red-500">*</span>
                </label>
                <select 
                    id="unit_type" 
                    name="unit_type" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                    <option value="">Select unit type</option>
                    <?php foreach ($unitTypes as $typeKey => $typeName): ?>
                        <option value="<?php echo $typeKey; ?>" <?php echo $typeKey === $unit['type'] ? 'selected' : ''; ?>>
                            <?php echo $typeName; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Bedrooms and Bathrooms Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bedrooms -->
                <div>
                    <label for="bedrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Bedrooms
                    </label>
                    <input 
                        type="number" 
                        id="bedrooms" 
                        name="bedrooms" 
                        min="0"
                        value="<?php echo $unit['bedrooms'] ?: ''; ?>"
                        placeholder="e.g., 1, 2, 3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                </div>

                <!-- Bathrooms -->
                <div>
                    <label for="bathrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Bathrooms
                    </label>
                    <input 
                        type="number" 
                        id="bathrooms" 
                        name="bathrooms" 
                        min="0"
                        step="0.5"
                        value="<?php echo $unit['bathrooms'] ?: ''; ?>"
                        placeholder="e.g., 1, 1.5, 2"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                </div>
            </div>

            <!-- Rent Price -->
            <div>
                <label for="rent_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Monthly Rent Price
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500 dark:text-gray-400">$</span>
                    <input 
                        type="number" 
                        id="rent_price" 
                        name="rent_price" 
                        min="0"
                        step="0.01"
                        value="<?php echo $unit['rent_price'] ?: ''; ?>"
                        placeholder="0.00"
                        class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                </div>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                </label>
                <select 
                    id="status" 
                    name="status" 
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                    <option value="available" <?php echo $unit['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="occupied" <?php echo $unit['status'] === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                    <option value="maintenance" <?php echo $unit['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    <option value="reserved" <?php echo $unit['status'] === 'reserved' ? 'selected' : ''; ?>>Reserved</option>
                </select>
            </div>

            <!-- Unit Information -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit Information</h3>
                <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                    <p>Created: <?php echo date('M j, Y g:i A', strtotime($unit['created_at'])); ?></p>
                    <p>Last Updated: <?php echo date('M j, Y g:i A', strtotime($unit['updated_at'])); ?></p>
                    <p>Property: <?php echo htmlspecialchars($unit['property_name']); ?></p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex space-x-2">
                    <a 
                        href="/admin/units" 
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        Cancel
                    </a>
                    <button 
                        type="button"
                        onclick="deleteUnit(<?php echo $unit['id']; ?>, '<?php echo htmlspecialchars($unit['unit_number']); ?>')"
                        class="px-4 py-2 text-red-700 dark:text-red-300 bg-white dark:bg-gray-700 border border-red-300 dark:border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    id="submit-btn"
                >
                    <i class="fas fa-save mr-2"></i>Update Unit
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../dashboard/layout.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('unit-form');
    const submitBtn = document.getElementById('submit-btn');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        
        // Collect form data
        const formData = new FormData(form);
        const data = {};
        
        // Add regular fields
        for (let [key, value] of formData.entries()) {
            if (key !== 'amenities[]') {
                data[key] = value;
            }
        }
        
        // Add amenities as array
        const amenities = formData.getAll('amenities[]');
        if (amenities.length > 0) {
            data.amenities = amenities;
        }
        
        // Send request
        fetch(`/admin/units/<?php echo $unit['id']; ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(() => {
                    window.location.href = '/admin/units';
                }, 1500);
            } else {
                // Handle validation errors
                if (result.errors) {
                    let errorMessage = 'Please fix the following errors:\n';
                    for (const [field, message] of Object.entries(result.errors)) {
                        errorMessage += `\n• ${message}`;
                    }
                    showToast(errorMessage, 'error');
                } else {
                    showToast(result.message || 'Failed to update unit', 'error');
                }
                
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Update Unit';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while updating the unit', 'error');
            
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Update Unit';
        });
    });
    
    // Form validation feedback
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
            }
        });
        
        field.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('border-red-500');
            }
        });
    });
    
    // Number input validation
    const numberInputs = form.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value && parseFloat(this.value) < 0) {
                this.value = 0;
            }
        });
    });
});

function deleteUnit(id, unitNumber) {
    if (confirm(`Are you sure you want to delete unit "${unitNumber}"? This action cannot be undone.`)) {
        fetch(`/admin/units/${id}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                    window.location.href = '/admin/units';
                }, 1500);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('An error occurred while deleting the unit', 'error');
        });
    }
}

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    
    // Set color based on type
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    toast.className += ' ' + colors[type];
    
    // Handle multi-line error messages
    const formattedMessage = message.replace(/\n/g, '<br>');
    toast.innerHTML = `
        <div class="flex items-start">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2 mt-0.5"></i>
            <div class="text-left">${formattedMessage}</div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 100);
    
    // Remove after 4 seconds for error messages, 3 seconds for others
    const duration = type === 'error' ? 4000 : 3000;
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, duration);
}
</script>
