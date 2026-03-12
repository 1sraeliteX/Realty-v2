<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', $title ?? 'Create Maintenance Request');
ViewManager::set('pageTitle', $pageTitle ?? 'Create Maintenance Request');
ViewManager::set('pageDescription', $pageDescription ?? 'Create a new maintenance request or work order');

// Set data for the view
ViewManager::set('properties', $properties ?? []);
ViewManager::set('tenants', $tenants ?? []);
ViewManager::set('contractors', $contractors ?? []);
ViewManager::set('categories', $categories ?? []);
ViewManager::set('priorities', $priorities ?? []);
ViewManager::set('statuses', $statuses ?? []);

// Capture page content
ob_start();
?>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Maintenance Request</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1"><?php echo htmlspecialchars($pageDescription ?? 'Create a new maintenance request or work order'); ?></p>
        </div>
        <a href="/admin/maintenance" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Maintenance
        </a>
    </div>
</div>

<!-- Form -->
<form method="POST" action="/admin/maintenance" class="space-y-6">
    <?php 
    // Get data from ViewManager (anti-scattering compliant)
    $properties = $properties ?? [];
    $tenants = $tenants ?? [];
    $contractors = $contractors ?? [];
    $categories = $categories ?? [];
    $priorities = $priorities ?? [];
    $statuses = $statuses ?? [];
    
    // Load UIComponents (anti-scattering compliant)
    ComponentRegistry::load('ui-components');
    
    // Build form content dynamically to avoid syntax errors
    $basicInfoContent = '<div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title/Request Summary -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title / Request Summary *</label>
                    <input 
                        type="text" 
                        name="title" 
                        required
                        placeholder="Brief summary of the maintenance issue"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Property/Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property / Unit *</label>
                    <select name="property_unit" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Property/Unit</option>';
    
    foreach ($properties as $property) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($property['id']) . '">' . htmlspecialchars($property['name']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Tenant -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tenant</label>
                    <select name="tenant" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Tenant (Optional)</option>';
    
    foreach ($tenants as $tenant) {
        $tenantName = ($tenant['first_name'] ?? '') . ' ' . ($tenant['last_name'] ?? '');
        $basicInfoContent .= '<option value="' . htmlspecialchars($tenant['id']) . '">' . htmlspecialchars(trim($tenantName)) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Category</option>';
    
    foreach ($categories as $category) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($category['value']) . '">' . htmlspecialchars($category['label']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority *</label>
                    <select name="priority" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Priority</option>';
    
    foreach ($priorities as $priority) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($priority['value']) . '">' . htmlspecialchars($priority['label']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">';
    
    foreach ($statuses as $status) {
        $selected = $status['value'] === 'pending' ? ' selected' : '';
        $basicInfoContent .= '<option value="' . htmlspecialchars($status['value']) . '"' . $selected . '>' . htmlspecialchars($status['label']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
            </div>
            
            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description *</label>
                <textarea 
                    name="description" 
                    required 
                    rows="4" 
                    placeholder="Detailed description of the maintenance issue..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                ></textarea>
            </div>
        </div>';
    
    echo UIComponents::card(
        $basicInfoContent,
        '<div class="flex items-center">
            <i class="fas fa-info-circle mr-2 text-primary-600"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Basic Information</h3>
        </div>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    );
    ?>

    <!-- Assignment & Scheduling -->
    <?php 
    // Build assignment content dynamically to avoid syntax errors
    $assignmentContent = '<div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Assigned To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assigned To</label>
                    <select name="assigned_to" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Contractor/Staff</option>';
    
    foreach ($contractors as $contractor) {
        $assignmentContent .= '<option value="' . htmlspecialchars($contractor['id']) . '">' . htmlspecialchars($contractor['name']) . '</option>';
    }
    
    $assignmentContent .= '</select>
                </div>
                
                <!-- Scheduled Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scheduled Date</label>
                    <input 
                        type="date" 
                        name="scheduled_date"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Estimated Cost -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estimated Cost</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center justify-center w-8 pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400">$</span>
                        </div>
                        <input 
                            type="number" 
                            name="estimated_cost"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                    </div>
                </div>
                
                <!-- Attachments -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attachments</label>
                    <div class="relative">
                        <input 
                            type="file" 
                            name="attachments"
                            multiple
                            accept="image/*,.pdf,.doc,.docx"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-600 file:text-white hover:file:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Upload images or documents (optional)</p>
                </div>
            </div>
        </div>';
    
    echo UIComponents::card(
        $assignmentContent,
        '<div class="flex items-center">
            <i class="fas fa-calendar-alt mr-2 text-primary-600"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Assignment & Scheduling</h3>
        </div>',
        null,
        'bg-white dark:bg-gray-800 rounded-lg shadow'
    );
    ?>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-4">
        <a href="/admin/maintenance" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            Cancel
        </a>
        <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-900 transition-colors duration-200">
            <i class="fas fa-paper-plane mr-2"></i>
            Submit Request
        </button>
    </div>
</form>

<?php
// Capture content and set for layout
$content = ob_get_clean();

// Use the admin dashboard layout (anti-scattering compliant)
include __DIR__ . '/../dashboard_layout.php';
?>
