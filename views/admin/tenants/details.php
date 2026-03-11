<?php
// Include UI Components
require_once __DIR__ . '/../../../components/UIComponents.php';

$title = 'Tenant Details';
$pageTitle = 'Tenant Information';

$content = ob_start();
?>

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tenant Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View and manage tenant information</p>
            </div>
            <div class="flex space-x-3">
                <a href="/admin/tenants" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Tenants
                </a>
                <a href="/admin/tenants/<?php echo $tenant['id']; ?>/edit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Tenant
                </a>
            </div>
        </div>
    </div>

    <!-- Tenant Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['email']); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['phone']); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <?php 
                        $statusColor = $tenant['lease_status'] === 'active' ? 'success' : 
                                     ($tenant['lease_status'] === 'expiring' ? 'warning' : 'danger');
                        echo UIComponents::badge(ucfirst($tenant['lease_status']), $statusColor, 'small'); 
                        ?>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Address</h2>
                <div class="space-y-2">
                    <?php if (isset($tenant['address'])): ?>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['address']); ?></p>
                        <p class="text-gray-900 dark:text-white">
                            <?php echo htmlspecialchars($tenant['city'] . ', ' . $tenant['state'] . ' ' . $tenant['zip_code']); ?>
                        </p>
                    <?php else: ?>
                        <p class="text-gray-500 dark:text-gray-400">No address on file</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Lease Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lease Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Property</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['property_name']); ?> - Unit <?php echo htmlspecialchars($tenant['unit_number']); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Rent</label>
                        <p class="text-gray-900 dark:text-white">$<?php echo number_format($tenant['rent_amount'], 2); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease Start</label>
                        <p class="text-gray-900 dark:text-white"><?php echo date('F j, Y', strtotime($tenant['lease_start'])); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease End</label>
                        <p class="text-gray-900 dark:text-white"><?php echo date('F j, Y', strtotime($tenant['lease_end'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment History</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($payment_history as $payment): ?>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <?php echo date('M j, Y', strtotime($payment['date'])); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        $<?php echo number_format($payment['amount'], 2); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($payment['method']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <?php 
                                        $color = $payment['status'] === 'paid' ? 'success' : 'warning';
                                        echo UIComponents::badge(ucfirst($payment['status']), $color, 'small'); 
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Maintenance Requests -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Maintenance Requests</h2>
                <div class="space-y-3">
                    <?php foreach ($maintenanceRequests as $request): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['type']); ?></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($request['description']); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y', strtotime($request['date'])); ?></p>
                            </div>
                            <?php 
                            $color = $request['status'] === 'completed' ? 'success' : 
                                    ($request['status'] === 'pending' ? 'warning' : 'info');
                            echo UIComponents::badge(ucfirst($request['status']), $color, 'small'); 
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="/admin/payments/create?tenant_id=<?php echo $tenant['id']; ?>" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        Record Payment
                    </a>
                    <a href="/admin/maintenance/create?tenant_id=<?php echo $tenant['id']; ?>" class="block w-full text-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-tools mr-2"></i>
                        Create Maintenance Request
                    </a>
                    <a href="/admin/communications/create?tenant_id=<?php echo $tenant['id']; ?>" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Message
                    </a>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact</h3>
                <div class="space-y-2">
                    <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['emergency_contact_name']); ?></p>
                    <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['emergency_contact_phone']); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
?>
