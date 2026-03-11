<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Unit Details');
ViewManager::set('pageTitle', 'Unit Details');
ViewManager::set('pageDescription', 'View comprehensive unit information and manage tenant assignments');

// Get data from DataProvider (anti-scattering compliant)
$unit = DataProvider::get('unit');
$tenant = DataProvider::get('tenant');
$amenities = DataProvider::get('amenities');
$maintenanceHistory = DataProvider::get('maintenanceHistory');

ob_start();
?>

<!-- Unit Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Unit <?php echo htmlspecialchars($unit['unit_number']); ?></h1>
                <p class="text-purple-100"><?php echo htmlspecialchars($unit['property_name']); ?></p>
                <p class="text-purple-100"><?php echo htmlspecialchars($unit['type']); ?> • <?php echo $unit['sqft']; ?> sq ft</p>
            </div>
            <div class="flex space-x-2">
                <?php echo UIComponents::button('Edit Unit', 'primary', 'medium', '/admin/units/' . $unit['id'] . '/edit', 'edit'); ?>
                <?php echo UIComponents::button('Add Tenant', 'success', 'medium', '/admin/tenants/create?unit_id=' . $unit['id'], 'user-plus'); ?>
                <?php echo UIComponents::button('Maintenance', 'info', 'medium', '/admin/maintenance/create?unit_id=' . $unit['id'], 'tools'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Unit Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Monthly Rent', '$' . number_format($unit['rent']), 'dollar-sign', 'green', '', 'Market rate'); ?>
    <?php echo UIComponents::statCard('Status', ucfirst($unit['status']), 'home', 'blue', '', $unit['status'] === 'occupied' ? 'Tenant assigned' : 'Vacant'); ?>
    <?php echo UIComponents::statCard('Size', $unit['sqft'] . ' sq ft', 'expand', 'purple', '', $unit['bedrooms'] . 'BR/' . $unit['bathrooms'] . 'BA'); ?>
    <?php echo UIComponents::statCard('Security Deposit', '$' . number_format($unit['security_deposit']), 'shield', 'orange', '', 'Required'); ?>
</div>

<!-- Tabs Navigation -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button class="tab-button py-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-400" data-tab="overview">
                Overview
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="tenant">
                Tenant
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="amenities">
                Amenities
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="maintenance">
                Maintenance
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="financials">
                Financials
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="p-6">
        <!-- Overview Tab -->
        <div id="overview" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Unit Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Unit Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit Number</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($unit['unit_number']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($unit['type']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Size</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $unit['sqft']; ?> sq ft</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bedrooms</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $unit['bedrooms']; ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bathrooms</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $unit['bathrooms']; ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Floor</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $unit['floor']; ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Section</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($unit['section']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd><?php echo UIComponents::badge(ucfirst($unit['status']), $unit['status'] === 'occupied' ? 'success' : 'warning'); ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Additional Features -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Features</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Parking Space</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($unit['parking_space']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Storage Unit</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($unit['storage_unit']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Rent</dt>
                            <dd class="text-sm font-bold text-gray-900 dark:text-white">$<?php echo number_format($unit['rent']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Security Deposit</dt>
                            <dd class="text-sm font-bold text-gray-900 dark:text-white">$<?php echo number_format($unit['security_deposit']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Property</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($unit['property_name']); ?></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Tenant Tab -->
        <div id="tenant" class="tab-content hidden">
            <?php if ($unit['status'] === 'occupied'): ?>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Tenant</h3>
                    <div class="flex items-center space-x-4">
                        <?php echo UIComponents::avatar($tenant['name'], null, 'large'); ?>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['name']); ?></h4>
                            <p class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($tenant['email']); ?></p>
                            <p class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($tenant['phone']); ?></p>
                        </div>
                        <div class="ml-auto">
                            <?php echo UIComponents::button('View Tenant', 'primary', 'medium', '/admin/tenants/' . $tenant['id'], 'user'); ?>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease Period</p>
                            <p class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($tenant['lease_start'])); ?> - <?php echo date('M j, Y', strtotime($tenant['lease_end'])); ?></p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Rent</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">$<?php echo number_format($tenant['monthly_rent']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <?php echo UIComponents::button('Extend Lease', 'success', 'medium', '#', 'calendar-plus'); ?>
                    <?php echo UIComponents::button('Terminate Lease', 'danger', 'medium', '#', 'times-circle'); ?>
                    <?php echo UIComponents::button('Send Notice', 'info', 'medium', '#', 'envelope'); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-user-slash text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Tenant Assigned</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">This unit is currently vacant</p>
                    <?php echo UIComponents::button('Assign Tenant', 'primary', 'medium', '/admin/tenants/create?unit_id=' . $unit['id'], 'user-plus'); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Amenities Tab -->
        <div id="amenities" class="tab-content hidden">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Unit Amenities</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($amenities as $amenity): ?>
                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($amenity); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-6">
                <?php echo UIComponents::button('Edit Amenities', 'primary', 'medium', '/admin/units/' . $unit['id'] . '/edit', 'edit'); ?>
            </div>
        </div>

        <!-- Maintenance Tab -->
        <div id="maintenance" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Maintenance History</h3>
                <?php echo UIComponents::button('Schedule Maintenance', 'primary', 'small', '/admin/maintenance/create?unit_id=' . $unit['id'], 'plus'); ?>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($maintenanceHistory as $maintenance): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($maintenance['date'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($maintenance['title']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">$<?php echo number_format($maintenance['cost']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo UIComponents::badge(ucfirst($maintenance['status']), $maintenance['status'] === 'completed' ? 'success' : 'warning'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="/admin/maintenance/<?php echo $maintenance['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Financials Tab -->
        <div id="financials" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Income -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Income</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Base Rent</span>
                            <span class="text-sm font-bold text-green-600 dark:text-green-400">$<?php echo number_format($unit['rent']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Additional Fees</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">$0</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border-t-2 border-green-200 dark:border-green-800">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Total Income</span>
                            <span class="text-sm font-bold text-green-600 dark:text-green-400">$<?php echo number_format($unit['rent']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Expenses -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Expenses</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Maintenance</span>
                            <span class="text-sm font-bold text-red-600 dark:text-red-400">$50</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Utilities</span>
                            <span class="text-sm font-bold text-red-600 dark:text-red-400">$75</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border-t-2 border-red-200 dark:border-red-800">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Total Expenses</span>
                            <span class="text-sm font-bold text-red-600 dark:text-red-400">$125</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="mt-6 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Net Monthly Profit</h3>
                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        $<?php echo number_format($unit['rent'] - 125); ?>
                    </span>
                </div>
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
echo ViewManager::render('admin.simple_layout', ['content' => $content]);
?>
