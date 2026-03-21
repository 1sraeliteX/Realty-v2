<?php
// Get data from ViewManager (anti-scattering compliant)
$request = ViewManager::get('request', []);
$contractors = ViewManager::get('contractors', []);
?>


<!-- Maintenance Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-orange-600 to-orange-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($request['title'] ?? 'Maintenance Request'); ?></h1>
                <p class="text-orange-100"><?php echo htmlspecialchars($request['property_name'] ?? 'Unknown Property'); ?> <?php echo !empty($request['unit_number']) ? '• Unit ' . htmlspecialchars($request['unit_number']) : ''; ?></p>
                <p class="text-orange-100">Reported by <?php echo htmlspecialchars($request['tenant_name'] ?? 'Unknown Tenant'); ?></p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-orange-100 text-sm">Priority</p>
                    <p class="text-lg font-bold text-white"><?php echo ucfirst($request['priority'] ?? 'medium'); ?></p>
                </div>
                <div class="px-3 py-1 rounded-full text-sm font-semibold 
                    <?php echo match($request['status'] ?? 'pending') {
                        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    }; ?>">
                    <?php echo ucwords(str_replace('_', ' ', $request['status'] ?? 'pending')); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-wrench text-2xl text-blue-500"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo ucfirst($request['category'] ?? 'Other'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-orange-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar-alt text-2xl text-orange-500"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Reported</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($request['created_at'] ?? 'now')); ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-user text-2xl text-purple-500"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Assigned To</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['assigned_to_name'] ?? 'Unassigned'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-dollar-sign text-2xl text-green-500"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Est. Cost</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">$<?php echo number_format($request['cost_estimate'] ?? 0, 2); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Request Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Request Details</h3>
            
            <div class="space-y-4">
                <!-- Description -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Description</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo nl2br(htmlspecialchars($request['description'] ?? 'No description provided.')); ?></p>
                </div>

                <!-- Timeline Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Reported Date</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo date('M j, Y H:i', strtotime($request['created_at'] ?? 'now')); ?></p>
                    </div>
                    <?php if (!empty($request['scheduled_date'])): ?>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Scheduled Date</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo date('M j, Y', strtotime($request['scheduled_date'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Additional Notes -->
                <?php if (!empty($request['notes'])): ?>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Additional Notes</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo nl2br(htmlspecialchars($request['notes'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-wrap gap-3">
                <?php if ($request['status'] === 'pending'): ?>
                    <button onclick="updateStatus('in_progress')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>Start Work
                    </button>
                <?php endif; ?>
                <?php if ($request['status'] === 'in_progress'): ?>
                    <button onclick="completeMaintenance()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>Complete
                    </button>
                <?php endif; ?>
                <a href="/admin/maintenance/<?php echo $request['id']; ?>/edit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Update Status
                </a>
                <button onclick="addNote()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sticky-note mr-2"></i>Add Note
                </button>
            </div>
        </div>

        <!-- Updates Timeline -->
        <?php if (!empty($request['updates'])): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Updates Timeline</h3>
            
            <div class="space-y-4">
                <?php foreach ($request['updates'] as $update): ?>
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-comment text-primary-600 dark:text-primary-400 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($update['notes'] ?? 'Status updated'); ?></p>
                                <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y H:i', strtotime($update['created_at'])); ?></span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Status: <?php echo ucfirst($update['status']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Tenant Information -->
        <?php if (!empty($request['tenant_name'])): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tenant Information</h3>
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-xl font-semibold text-gray-600 dark:text-gray-300">
                        <?php echo substr($request['tenant_name'], 0, 2); ?>
                    </span>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['tenant_name']); ?></h4>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['tenant_email'] ?? 'N/A'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['tenant_phone'] ?? 'N/A'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Property</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['property_name'] ?? 'N/A'); ?></span>
                </div>
                <?php if (!empty($request['unit_number'])): ?>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Unit</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['unit_number']); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-4 space-y-2">
                <button onclick="contactTenant()" class="w-full px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-sm">
                    <i class="fas fa-envelope mr-2"></i>Contact Tenant
                </button>
                <?php if (!empty($request['tenant_id'])): ?>
                <a href="/admin/tenants/<?php echo $request['tenant_id']; ?>" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm text-center">
                    <i class="fas fa-user mr-2"></i>View Tenant
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Assignment Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assignment Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Assigned To</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['assigned_to_name'] ?? 'Unassigned'); ?></p>
                </div>
                <?php if (!empty($request['assigned_date'])): ?>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Assigned Date</p>
                    <p class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($request['assigned_date'])); ?></p>
                </div>
                <?php endif; ?>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Priority Level</p>
                    <div class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            <?php echo match($request['priority'] ?? 'medium') {
                                'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                default => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            }; ?>">
                            <?php echo ucfirst($request['priority'] ?? 'medium'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <button onclick="scheduleVisit()" class="w-full px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    <i class="fas fa-calendar mr-2"></i>Schedule Visit
                </button>
                <button onclick="orderMaterials()" class="w-full px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                    <i class="fas fa-shopping-cart mr-2"></i>Order Materials
                </button>
                <button onclick="uploadPhotos()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                    <i class="fas fa-camera mr-2"></i>Upload Photos
                </button>
                <button onclick="generateReport()" class="w-full px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-sm">
                    <i class="fas fa-file-alt mr-2"></i>Generate Report
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Complete maintenance request
function completeMaintenance() {
    if (confirm('Are you sure this maintenance request is completed?')) {
        // Show modal for actual cost and completion notes
        showToast('Opening completion form...', 'info');
    }
}

// Update status
function updateStatus(newStatus) {
    if (confirm(`Update status to ${newStatus}?`)) {
        showToast('Status updated successfully!', 'success');
        setTimeout(() => {
            location.reload();
        }, 2000);
    }
}

// Add note to maintenance
function addNote() {
    const note = prompt('Enter your note:');
    if (note) {
        showToast('Note added successfully!', 'success');
        setTimeout(() => {
            location.reload();
        }, 2000);
    }
}

// Contact tenant
function contactTenant() {
    showToast('Opening communication panel...', 'info');
}

// Schedule visit
function scheduleVisit() {
    showToast('Opening scheduling form...', 'info');
}

// Order materials
function orderMaterials() {
    showToast('Opening materials form...', 'info');
}

// Upload photos
function uploadPhotos() {
    showToast('Opening photo upload...', 'info');
}

// Generate report
function generateReport() {
    showToast('Generating report...', 'info');
}
</script>

