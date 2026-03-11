<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$user = DataProvider::get('user');
$notifications = DataProvider::get('notifications');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('user', $user);
ViewManager::set('notifications', $notifications);
ViewManager::set('title', $title ?? 'Create Maintenance Request');
ViewManager::set('pageTitle', $pageTitle ?? 'Create Maintenance Request');

// Add Font Awesome CSS to head (anti-scattering compliant)
ViewManager::set('headCSS', '
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="/assets/css/fontawesome.css">
');

// Mobile viewport fix CSS
ViewManager::set('mobileFixCSS', '
<style>
/* Mobile sidebar overflow fix */
@media (max-width: 1023px) {
    #sidebar {
        height: 100vh;
        max-height: 100vh;
        overflow-y: auto;
        overscroll-behavior: contain;
    }
    
    #sidebar .flex-col {
        min-height: 100vh;
    }
    
    #sidebar nav {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    #sidebar .border-t {
        flex-shrink: 0;
        position: sticky;
        bottom: 0;
        background: inherit;
    }
}

/* Ensure proper scrolling on iOS */
@supports (-webkit-touch-callout: none) {
    #sidebar {
        -webkit-overflow-scrolling: touch;
    }
}
</style>
');

// Mobile viewport fix JavaScript
ViewManager::set('mobileFixJS', '
<script>
// Mobile sidebar viewport fix
function fixMobileSidebarViewport() {
    const sidebar = document.getElementById("sidebar");
    const isMobile = window.innerWidth < 1024;
    
    if (isMobile && sidebar) {
        // Ensure full viewport height on mobile
        sidebar.style.height = "100vh";
        sidebar.style.maxHeight = "100vh";
        
        // Handle viewport changes on iOS Safari
        const handleViewportChange = () => {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty("--vh", `${vh}px`);
            sidebar.style.height = "calc(var(--vh, 1vh) * 100)";
        };
        
        // Initial setup
        handleViewportChange();
        
        // Listen for orientation changes and viewport adjustments
        window.addEventListener("resize", handleViewportChange);
        window.addEventListener("orientationchange", handleViewportChange);
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", fixMobileSidebarViewport);
</script>
');

// Determine active menu item based on current route
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$activeMenu = 'maintenance';

// Set active menu for template use
ViewManager::set('activeMenu', $activeMenu);

// Output head CSS first (anti-scattering compliant)
echo ViewManager::get('headCSS');

// Start output buffering for the page content
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
    <!-- Basic Information -->
    <?php 
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
    
    foreach ($properties ?? [] as $property) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($property['id']) . '">' . htmlspecialchars($property['name']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Tenant -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tenant</label>
                    <select name="tenant" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Tenant (Optional)</option>';
    
    foreach ($tenants ?? [] as $tenant) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($tenant['id']) . '">' . htmlspecialchars($tenant['name']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Category</option>';
    
    foreach ($categories ?? [] as $category) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($category['value']) . '">' . htmlspecialchars($category['label']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority *</label>
                    <select name="priority" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Priority</option>';
    
    foreach ($priorities ?? [] as $priority) {
        $basicInfoContent .= '<option value="' . htmlspecialchars($priority['value']) . '">' . htmlspecialchars($priority['label']) . '</option>';
    }
    
    $basicInfoContent .= '</select>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">';
    
    foreach ($statuses ?? [] as $status) {
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
    
    foreach ($contractors ?? [] as $contractor) {
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
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
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
$content = ob_get_clean();
include __DIR__ . '/../simple_layout.php';
?>
