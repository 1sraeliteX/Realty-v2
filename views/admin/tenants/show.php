<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Tenant Details';
$pageTitle = 'Tenant Details';
$pageDescription = 'View comprehensive tenant information and manage lease details';

// Mock tenant data
$tenant = [
    'id' => 1,
    'first_name' => 'John',
    'last_name' => 'Smith',
    'email' => 'john.smith@email.com',
    'phone' => '(555) 123-4567',
    'date_of_birth' => '1985-06-15',
    'ssn' => '***-**-6789',
    'emergency_contact' => 'Jane Smith',
    'emergency_phone' => '(555) 987-6543',
    'status' => 'active',
    'move_in_date' => '2023-01-15',
    'lease_start' => '2023-01-15',
    'lease_end' => '2024-01-14',
    'monthly_rent' => 1200,
    'security_deposit' => 2400,
    'property_name' => 'Sunset Apartments',
    'unit_number' => '101',
    'property_id' => 1,
    'unit_id' => 1,
    'created_at' => '2023-01-10',
    'last_updated' => '2024-01-08'
];

// Mock payment history
$paymentHistory = [
    ['id' => 1, 'date' => '2024-01-01', 'amount' => 1200, 'type' => 'rent', 'status' => 'paid', 'method' => 'bank_transfer'],
    ['id' => 2, 'date' => '2023-12-01', 'amount' => 1200, 'type' => 'rent', 'status' => 'paid', 'method' => 'bank_transfer'],
    ['id' => 3, 'date' => '2023-11-01', 'amount' => 1200, 'type' => 'rent', 'status' => 'paid', 'method' => 'bank_transfer'],
    ['id' => 4, 'date' => '2023-10-01', 'amount' => 1200, 'type' => 'rent', 'status' => 'paid', 'method' => 'bank_transfer'],
];

// Mock documents
$documents = [
    ['id' => 1, 'name' => 'Lease Agreement', 'type' => 'lease', 'upload_date' => '2023-01-10', 'size' => '2.4 MB'],
    ['id' => 2, 'name' => 'ID Verification', 'type' => 'identification', 'upload_date' => '2023-01-10', 'size' => '1.2 MB'],
    ['id' => 3, 'name' => 'Background Check', 'type' => 'background', 'upload_date' => '2023-01-11', 'size' => '856 KB'],
];

// Mock maintenance requests
$maintenanceRequests = [
    ['id' => 1, 'title' => 'Leaky Faucet', 'status' => 'resolved', 'priority' => 'medium', 'created_at' => '2023-12-15', 'resolved_at' => '2023-12-18'],
    ['id' => 2, 'title' => 'AC Not Working', 'status' => 'pending', 'priority' => 'high', 'created_at' => '2024-01-08', 'resolved_at' => null],
];

ob_start();
?>

<!-- Tenant Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <?php echo UIComponents::avatar($tenant['first_name'] . ' ' . $tenant['last_name'], null, 'large'); ?>
                <div>
                    <h1 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?></h1>
                    <p class="text-blue-100"><?php echo htmlspecialchars($tenant['email']); ?></p>
                    <p class="text-blue-100"><?php echo htmlspecialchars($tenant['phone']); ?></p>
                </div>
            </div>
            <div class="flex space-x-2">
                <?php echo UIComponents::button('Edit Tenant', 'primary', 'medium', '/admin/tenants/' . $tenant['id'] . '/edit', 'edit'); ?>
                <?php echo UIComponents::button('Send Message', 'info', 'medium', '#', 'envelope'); ?>
                <?php echo UIComponents::button('Create Invoice', 'success', 'medium', '/admin/invoices/create?tenant_id=' . $tenant['id'], 'file-invoice'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Tenant Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Monthly Rent', '$' . number_format($tenant['monthly_rent']), 'dollar-sign', 'green', '', 'Due on 1st'); ?>
    <?php echo UIComponents::statCard('Lease Status', ucfirst($tenant['status']), 'user-check', 'blue', '', 'Expires ' . date('M j, Y', strtotime($tenant['lease_end']))); ?>
    <?php echo UIComponents::statCard('Security Deposit', '$' . number_format($tenant['security_deposit']), 'shield', 'purple', '', 'Held'); ?>
    <?php echo UIComponents::statCard('Payment History', 'On Time', 'clock', 'orange', 'up', '100% on time'); ?>
</div>

<!-- Tabs Navigation -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button class="tab-button py-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-400" data-tab="overview">
                Overview
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="lease">
                Lease Details
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="payments">
                Payment History
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="maintenance">
                Maintenance
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="documents">
                Documents
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="p-6">
        <!-- Overview Tab -->
        <div id="overview" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['email']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['phone']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($tenant['date_of_birth'])); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SSN</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['ssn']); ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Property Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Property Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Property</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['property_name']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['unit_number']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Move-in Date</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($tenant['move_in_date'])); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd><?php echo UIComponents::badge(ucfirst($tenant['status']), $tenant['status'] === 'active' ? 'success' : 'warning'); ?></dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact</h3>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Name</p>
                            <p class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['emergency_contact']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Phone</p>
                            <p class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['emergency_phone']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lease Details Tab -->
        <div id="lease" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Lease Terms -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lease Terms</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease Start</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($tenant['lease_start'])); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease End</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($tenant['lease_end'])); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease Duration</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">12 months</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Days Remaining</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo max(0, (strtotime($tenant['lease_end']) - time()) / 86400); ?> days</dd>
                        </div>
                    </dl>
                </div>

                <!-- Financial Details -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Financial Details</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Rent</dt>
                            <dd class="text-sm font-bold text-gray-900 dark:text-white">$<?php echo number_format($tenant['monthly_rent']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Security Deposit</dt>
                            <dd class="text-sm font-bold text-gray-900 dark:text-white">$<?php echo number_format($tenant['security_deposit']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Due Date</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">1st of each month</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Late Fee</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">$50 after 5 days</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Lease Actions -->
            <div class="mt-6 flex space-x-4">
                <?php echo UIComponents::button('Renew Lease', 'success', 'medium', '#', 'refresh'); ?>
                <?php echo UIComponents::button('Terminate Lease', 'danger', 'medium', '#', 'times-circle'); ?>
                <?php echo UIComponents::button('Print Lease', 'info', 'medium', '#', 'print'); ?>
            </div>
        </div>

        <!-- Payment History Tab -->
        <div id="payments" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment History</h3>
                <?php echo UIComponents::button('Record Payment', 'primary', 'small', '/admin/payments/create?tenant_id=' . $tenant['id'], 'plus'); ?>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($paymentHistory as $payment): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($payment['date'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo ucfirst($payment['type']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">$<?php echo number_format($payment['amount']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo ucfirst(str_replace('_', ' ', $payment['method'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo UIComponents::badge(ucfirst($payment['status']), $payment['status'] === 'paid' ? 'success' : 'warning'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="/admin/payments/<?php echo $payment['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Maintenance Tab -->
        <div id="maintenance" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Maintenance Requests</h3>
                <?php echo UIComponents::button('Create Request', 'primary', 'small', '/admin/maintenance/create?tenant_id=' . $tenant['id'], 'plus'); ?>
            </div>
            
            <div class="space-y-4">
                <?php foreach ($maintenanceRequests as $request): ?>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['title']); ?></h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Created: <?php echo date('M j, Y', strtotime($request['created_at'])); ?>
                                    <?php if ($request['resolved_at']): ?>
                                        | Resolved: <?php echo date('M j, Y', strtotime($request['resolved_at'])); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php echo UIComponents::badge(ucfirst($request['status']), $request['status'] === 'resolved' ? 'success' : ($request['status'] === 'pending' ? 'warning' : 'info')); ?>
                                <?php echo UIComponents::badge(ucfirst($request['priority']), $request['priority'] === 'high' ? 'danger' : 'warning'); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Documents Tab -->
        <div id="documents" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tenant Documents</h3>
                <?php echo UIComponents::button('Upload Document', 'primary', 'small', '/admin/documents/create?tenant_id=' . $tenant['id'], 'upload'); ?>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Document Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Upload Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($documents as $document): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($document['name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo ucfirst($document['type']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo date('M j, Y', strtotime($document['upload_date'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo $document['size']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 mr-3">Download</a>
                                    <a href="#" class="text-red-600 hover:text-red-900 dark:text-red-400">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.dataset.tab;

            // Update button states
            tabButtons.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            });

            button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            button.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-400');

            // Show/hide content
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            document.getElementById(targetTab).classList.remove('hidden');
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
