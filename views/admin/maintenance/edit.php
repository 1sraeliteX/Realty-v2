<?php
// Get data from ViewManager (anti-scattering compliant)
$request = ViewManager::get('request', []);
$properties = ViewManager::get('properties', []);
$tenants = ViewManager::get('tenants', []);
$contractors = ViewManager::get('contractors', []);
$categories = ViewManager::get('categories', []);
$priorities = ViewManager::get('priorities', []);
$completion_statuses = ViewManager::get('completion_statuses', []);

// Define default values if not provided
if (empty($categories)) {
    $categories = ['plumbing', 'electrical', 'hvac', 'appliance', 'structural', 'pest_control', 'landscaping', 'other'];
}

if (empty($priorities)) {
    $priorities = ['low', 'medium', 'high', 'urgent'];
}
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Maintenance Request</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Update maintenance request information</p>
            </div>
            <a href="/admin/maintenance" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Cancel
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="/admin/maintenance/<?php echo $request['id'] ?? $_GET['id']; ?>" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center mb-6">
                <i class="fas fa-info-circle mr-2 text-primary-600"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Request Information</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                    <input type="text" 
                           name="title" 
                           required
                           value="<?php echo htmlspecialchars($request['title'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                
                <!-- Property/Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property/Unit *</label>
                    <select name="property_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Property</option>
                        <?php foreach ($properties as $property): ?>
                            <option value="<?php echo htmlspecialchars($property['id']); ?>" 
                                    <?php echo ($request['property_id'] ?? '') == $property['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($property['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Tenant -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tenant</label>
                    <select name="tenant_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Tenant (Optional)</option>
                        <?php foreach ($tenants as $tenant): ?>
                            <?php 
                            $tenantName = ($tenant['first_name'] ?? '') . ' ' . ($tenant['last_name'] ?? '');
                            $tenantName = trim($tenantName);
                            ?>
                            <option value="<?php echo htmlspecialchars($tenant['id']); ?>" 
                                    <?php echo ($request['tenant_id'] ?? '') == $tenant['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tenantName); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority *</label>
                    <select name="priority" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <?php foreach ($priorities as $priority): ?>
                            <option value="<?php echo htmlspecialchars($priority); ?>" 
                                    <?php echo ($request['priority'] ?? '') === $priority ? 'selected' : ''; ?>>
                                <?php echo ucfirst($priority); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="pending" <?php echo ($request['status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo ($request['status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="completed" <?php echo ($request['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo ($request['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>" 
                                    <?php echo ($request['category'] ?? '') === $category ? 'selected' : ''; ?>>
                                <?php echo ucfirst($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Assigned To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assigned To</label>
                    <select name="assigned_to" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Contractor/Staff</option>
                        <?php foreach ($contractors as $contractor): ?>
                            <option value="<?php echo htmlspecialchars($contractor['id']); ?>" 
                                    <?php echo ($request['assigned_to'] ?? '') == $contractor['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($contractor['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Issue Description *</label>
                <textarea name="description" required rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"><?php echo htmlspecialchars($request['description'] ?? ''); ?></textarea>
            </div>
            
            <!-- Additional Notes -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Notes</label>
                <textarea name="notes" rows="3" 
                          placeholder="Any additional notes or updates..."
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"><?php echo htmlspecialchars($request['notes'] ?? ''); ?></textarea>
            </div>
        </div>

        <!-- Financial Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center mb-6">
                <i class="fas fa-dollar-sign mr-2 text-green-600"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Financial Information</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Estimated Cost -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estimated Cost</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center justify-center w-8 pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400">$</span>
                        </div>
                        <input type="number" 
                               name="cost_estimate"
                               value="<?php echo htmlspecialchars($request['cost_estimate'] ?? ''); ?>"
                               placeholder="0.00"
                               step="0.01"
                               min="0"
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <!-- Actual Cost -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Actual Cost</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center justify-center w-8 pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400">$</span>
                        </div>
                        <input type="number" 
                               name="actual_cost"
                               value="<?php echo htmlspecialchars($request['actual_cost'] ?? ''); ?>"
                               placeholder="0.00"
                               step="0.01"
                               min="0"
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                
                <!-- Scheduled Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scheduled Date</label>
                    <input type="date" 
                           name="scheduled_date"
                           value="<?php echo htmlspecialchars($request['scheduled_date'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                
                <!-- Completion Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Completion Date</label>
                    <input type="date" 
                           name="completion_date"
                           value="<?php echo htmlspecialchars($request['completion_date'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <a href="/admin/maintenance" class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-save mr-2"></i>
                Update Request
            </button>
        </div>
    </form>
</div>

