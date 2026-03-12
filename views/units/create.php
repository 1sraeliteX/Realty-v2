<?php
// Framework components are auto-loaded by ViewManager (anti-scattering compliant)

// Get data from ViewManager (anti-scattering compliant)
$properties = ViewManager::get('properties', []);
$unitTypes = ViewManager::get('unitTypes', []);
?>

<!-- Create Unit Content -->
<div class="space-y-6">
    <!-- Page Header with Breadcrumb -->
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/admin/dashboard" class="text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="/admin/units" class="text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
                            Units
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500 dark:text-gray-400">Create Unit</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Unit</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Add a new unit to your property portfolio</p>
            </div>
            <a href="/admin/units" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Units
            </a>
        </div>
    </div>

    <!-- Create Unit Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
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
                        <option value="<?php echo $property['id']; ?>"><?php echo htmlspecialchars($property['name']); ?></option>
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
                        <option value="<?php echo $typeKey; ?>"><?php echo $typeName; ?></option>
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
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="reserved">Reserved</option>
                </select>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a 
                    href="/admin/units" 
                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    id="submit-btn"
                >
                    <i class="fas fa-plus mr-2"></i>Create Unit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('unit-form');
    const submitBtn = document.getElementById('submit-btn');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
        
        // Collect form data
        const formData = new FormData(form);
        const data = {};
        
        // Add regular fields
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        // Send request
        fetch('/admin/units', {
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
                    showToast(result.message || 'Failed to create unit', 'error');
                }
                
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-plus mr-2"></i>Create Unit';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while creating the unit', 'error');
            
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-plus mr-2"></i>Create Unit';
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
