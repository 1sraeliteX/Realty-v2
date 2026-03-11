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

$title = 'Maintenance Request Details';
$pageTitle = 'Maintenance Details';
$pageDescription = 'View comprehensive maintenance request information and manage repairs';

// Mock maintenance data
$maintenance = [
    'id' => 1,
    'title' => 'Leaky Faucet in Kitchen',
    'description' => 'The kitchen sink faucet has been leaking continuously for the past 2 days. Water is dripping from the base of the faucet and causing water damage to the cabinet below.',
    'priority' => 'medium',
    'status' => 'in_progress',
    'category' => 'plumbing',
    'property_id' => 1,
    'property_name' => 'Sunset Apartments',
    'unit_id' => 1,
    'unit_number' => '101',
    'tenant_id' => 1,
    'tenant_name' => 'John Smith',
    'tenant_email' => 'john.smith@email.com',
    'tenant_phone' => '(555) 123-4567',
    'reported_date' => '2024-01-08',
    'scheduled_date' => '2024-01-10',
    'completed_date' => null,
    'estimated_cost' => 150,
    'actual_cost' => null,
    'assigned_to' => 'Mike Johnson',
    'assigned_date' => '2024-01-09',
    'notes' => 'Tenant has placed a bucket under the sink to catch water. Temporary fix applied but needs professional repair.',
    'created_at' => '2024-01-08 14:30:00',
    'updated_at' => '2024-01-09 09:15:00'
];

// Mock updates history
$updates = [
    [
        'id' => 1,
        'date' => '2024-01-09 09:15:00',
        'type' => 'assignment',
        'description' => 'Assigned to Mike Johnson (Plumbing Specialist)',
        'user' => 'Admin User'
    ],
    [
        'id' => 2,
        'date' => '2024-01-08 16:45:00',
        'type' => 'note',
        'description' => 'Tenant confirmed issue is still active and worsening',
        'user' => 'Admin User'
    ],
    [
        'id' => 3,
        'date' => '2024-01-08 14:30:00',
        'type' => 'creation',
        'description' => 'Maintenance request created by tenant',
        'user' => 'John Smith'
    ]
];

// Mock materials needed
$materials = [
    ['name' => 'Faucet Cartridge', 'quantity' => 1, 'estimated_cost' => 45],
    ['name' => 'Plumbers Tape', 'quantity' => 1, 'estimated_cost' => 5],
    ['name' => 'O-rings (set)', 'quantity' => 1, 'estimated_cost' => 8]
];

ob_start();
?>

<!-- Maintenance Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-orange-600 to-orange-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($maintenance['title']); ?></h1>
                <p class="text-orange-100"><?php echo htmlspecialchars($maintenance['property_name']); ?> • Unit <?php echo htmlspecialchars($maintenance['unit_number']); ?></p>
                <p class="text-orange-100">Reported by <?php echo htmlspecialchars($maintenance['tenant_name']); ?></p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-orange-100 text-sm">Priority</p>
                    <p class="text-lg font-bold text-white"><?php echo ucfirst($maintenance['priority']); ?></p>
                </div>
                <?php echo UIComponents::badge(ucfirst(str_replace('_', ' ', $maintenance['status'])), 
                    $maintenance['status'] === 'completed' ? 'success' : 
                    ($maintenance['status'] === 'in_progress' ? 'info' : 'warning'), 'large'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Category', ucfirst($maintenance['category']), 'wrench', 'blue', '', 'Plumbing issue'); ?>
    <?php echo UIComponents::statCard('Reported', date('M j, Y', strtotime($maintenance['reported_date'])), 'calendar-alt', 'orange', '', 'By tenant'); ?>
    <?php echo UIComponents::statCard('Assigned To', htmlspecialchars($maintenance['assigned_to']), 'user-hard-hat', 'purple', '', 'Specialist'); ?>
    <?php echo UIComponents::statCard('Est. Cost', '$' . number_format($maintenance['estimated_cost']), 'dollar-sign', 'green', '', 'Materials + labor'); ?>
</div>

<!-- Maintenance Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Request Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Request Details</h3>
            
            <div class="space-y-4">
                <!-- Description -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Description</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($maintenance['description']); ?></p>
                </div>

                <!-- Timeline Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Reported Date</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo date('M j, Y H:i', strtotime($maintenance['reported_date'])); ?></p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Scheduled Date</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo date('M j, Y', strtotime($maintenance['scheduled_date'])); ?></p>
                    </div>
                </div>

                <!-- Additional Notes -->
                <?php if (!empty($maintenance['notes'])): ?>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Additional Notes</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($maintenance['notes']); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex space-x-4">
                <?php if ($maintenance['status'] === 'pending'): ?>
                    <?php echo UIComponents::button('Start Work', 'success', 'medium', '#', 'play'); ?>
                <?php endif; ?>
                <?php if ($maintenance['status'] === 'in_progress'): ?>
                    <?php echo UIComponents::button('Complete', 'success', 'medium', '#', 'check'); ?>
                <?php endif; ?>
                <?php echo UIComponents::button('Update Status', 'primary', 'medium', '/admin/maintenance/' . $maintenance['id'] . '/edit', 'edit'); ?>
                <?php echo UIComponents::button('Add Note', 'info', 'medium', '#', 'sticky-note'); ?>
            </div>
        </div>

        <!-- Materials Needed -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Materials Needed</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Material</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Est. Cost</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($materials as $material): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($material['name']); ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white"><?php echo $material['quantity']; ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">$<?php echo number_format($material['estimated_cost']); ?></td>
                                <td class="px-4 py-3"><?php echo UIComponents::badge('Ordered', 'info'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <td colspan="2" class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">Total Estimated Cost</td>
                            <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-white">
                                $<?php echo number_format(array_sum(array_column($materials, 'estimated_cost'))); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Updates Timeline -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Updates Timeline</h3>
            
            <div class="space-y-4">
                <?php foreach ($updates as $update): ?>
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-<?php echo $update['type'] === 'creation' ? 'plus' : ($update['type'] === 'assignment' ? 'user' : 'comment'); ?> text-primary-600 dark:text-primary-400 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($update['description']); ?></p>
                                <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y H:i', strtotime($update['date'])); ?></span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">by <?php echo htmlspecialchars($update['user']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Tenant Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tenant Information</h3>
            <div class="text-center mb-4">
                <?php echo UIComponents::avatar($maintenance['tenant_name'], null, 'large'); ?>
                <h4 class="mt-3 text-lg font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($maintenance['tenant_name']); ?></h4>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($maintenance['tenant_email']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($maintenance['tenant_phone']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Property</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($maintenance['property_name']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Unit</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($maintenance['unit_number']); ?></span>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <?php echo UIComponents::button('Contact Tenant', 'primary', 'small', '#', 'envelope'); ?>
                <?php echo UIComponents::button('View Tenant', 'secondary', 'small', '/admin/tenants/' . $maintenance['tenant_id'], 'user'); ?>
            </div>
        </div>

        <!-- Assignment Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assignment Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Assigned To</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($maintenance['assigned_to']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Assigned Date</p>
                    <p class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($maintenance['assigned_date'])); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Priority Level</p>
                    <div class="mt-1"><?php echo UIComponents::badge(ucfirst($maintenance['priority']), 
                        $maintenance['priority'] === 'high' ? 'danger' : 
                        ($maintenance['priority'] === 'medium' ? 'warning' : 'info')); ?></div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <?php echo UIComponents::button('Schedule Visit', 'info', 'full', '#', 'calendar'); ?>
                <?php echo UIComponents::button('Order Materials', 'warning', 'full', '#', 'shopping-cart'); ?>
                <?php echo UIComponents::button('Upload Photos', 'secondary', 'full', '#', 'camera'); ?>
                <?php echo UIComponents::button('Generate Report', 'primary', 'full', '#', 'file-alt'); ?>
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

// Start work on maintenance
function startWork() {
    if (confirm('Start work on this maintenance request?')) {
        showToast('Work started successfully!', 'success');
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
</script>

<?php
$content = ob_get_clean();
include '../simple_layout.php';
?>
