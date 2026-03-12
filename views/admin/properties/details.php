<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Admin Page');
ViewManager::set('user', [
    'name' => 'Admin User',
    'email' => 'admin@cornerstone.com',
    'avatar' => null
]);
ViewManager::set('notifications', []);

ob_start();
?>


<!-- Back Button and Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div class="flex items-center space-x-4 mb-4 sm:mb-0">
        <a href="/admin/dashboard/properties" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Properties
        </a>
        <div class="flex items-center space-x-2">
            <?php 
            $statusColor = $property['status'] === 'occupied' ? 'success' : 
                         ($property['status'] === 'available' ? 'info' : 'warning');
            echo UIComponents::badge(ucfirst($property['status']), $statusColor); 
            ?>
            <?php echo UIComponents::badge($property['type'], 'gray'); ?>
        </div>
    </div>
    <div class="flex space-x-3">
        <a href="/admin/properties" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</a>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-print mr-2"></i>
            Print
        </button>
        <a href="/admin/properties/<?php echo $property['id']; ?>/edit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            <i class="fas fa-edit mr-2"></i>
            Edit Property
        </a>
    </div>
</div>

<!-- Property Header with Image -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
    <div class="relative h-64 md:h-96">
        <img src="<?php echo $property['image']; ?>" alt="<?php echo htmlspecialchars($property['name']); ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-6 text-white">
            <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($property['name']); ?></h1>
            <p class="text-lg opacity-90">
                <i class="fas fa-map-marker-alt mr-2"></i>
                <?php echo htmlspecialchars($property['address']); ?>
            </p>
        </div>
    </div>
</div>

<!-- Property Information Tabs -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button onclick="switchTab('overview')" class="tab-btn py-4 px-1 border-b-2 font-medium text-sm border-primary-500 text-primary-600" data-tab="overview">
                Overview
            </button>
            <button onclick="switchTab('units')" class="tab-btn py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-tab="units">
                Units
            </button>
            <button onclick="switchTab('tenants')" class="tab-btn py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-tab="tenants">
                Tenants
            </button>
            <button onclick="switchTab('financials')" class="tab-btn py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-tab="financials">
                Financials
            </button>
            <button onclick="switchTab('maintenance')" class="tab-btn py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-tab="maintenance">
                Maintenance
            </button>
            <button onclick="switchTab('documents')" class="tab-btn py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-tab="documents">
                Documents
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="p-6">
        <!-- Overview Tab -->
        <div id="overview-tab" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Property Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Property Information</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Property Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($property['type']); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Year Built</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo $property['year_built']; ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Size</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo number_format($property['size_sqft']); ?> sq ft</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Units</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo $property['unit_count']; ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Purchase Price</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">$<?php echo number_format($property['purchase_price'], 0); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Value</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">$<?php echo number_format($property['current_value'], 0); ?></dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Description</h3>
                        <p class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($property['description']); ?></p>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Amenities</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <?php foreach ($property['amenities'] as $amenity): ?>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-check text-green-500"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($amenity); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Performance Stats -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Performance</h3>
                        <div class="space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupancy Rate</span>
                                    <span class="text-lg font-bold text-primary-600 dark:text-primary-400"><?php echo round(($property['occupied_units'] / $property['unit_count']) * 100, 1); ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-primary-600 h-2 rounded-full" style="width: <?php echo ($property['occupied_units'] / $property['unit_count']) * 100; ?>%"></div>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Monthly Revenue</span>
                                    <span class="text-lg font-bold text-green-600 dark:text-green-400">$<?php echo number_format($property['monthly_revenue'], 0); ?></span>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Annual Revenue</span>
                                    <span class="text-lg font-bold text-green-600 dark:text-green-400">$<?php echo number_format($property['monthly_revenue'] * 12, 0); ?></span>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">ROI</span>
                                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">13.8%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Units Tab -->
        <div id="units-tab" class="tab-content hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Units Overview</h3>
                <a href="/admin/dashboard/units/new?property_id=<?php echo $property['id']; ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Add Unit
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tenant</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($unit['unit_number']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo htmlspecialchars($unit['type']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo number_format($unit['size_sqft']); ?> sq ft
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    $<?php echo number_format($unit['rent'], 0); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $statusColor = $unit['status'] === 'occupied' ? 'success' : 
                                                 ($unit['status'] === 'vacant' ? 'warning' : 'info');
                                    echo UIComponents::badge(ucfirst($unit['status']), $statusColor, 'small'); 
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo $unit['tenant'] ? htmlspecialchars($unit['tenant']) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="/admin/dashboard/units/<?php echo $unit['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tenants Tab -->
        <div id="tenants-tab" class="tab-content hidden">
            <div class="text-center py-12">
                <i class="fas fa-users text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tenant Management</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">View and manage all tenants for this property</p>
                <a href="/admin/dashboard/tenants?property_id=<?php echo $property['id']; ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-users mr-2"></i>
                    View Tenants
                </a>
            </div>
        </div>

        <!-- Financials Tab -->
        <div id="financials-tab" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Revenue Summary</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Monthly Revenue</span>
                            <span class="text-lg font-bold text-green-600 dark:text-green-400">$<?php echo number_format($property['monthly_revenue'], 0); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Annual Revenue</span>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">$<?php echo number_format($property['monthly_revenue'] * 12, 0); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Average Rent per Unit</span>
                            <span class="text-lg font-bold text-purple-600 dark:text-purple-400">$<?php echo number_format($property['monthly_revenue'] / $property['unit_count'], 0); ?></span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Property Value</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Purchase Price</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">$<?php echo number_format($property['purchase_price'], 0); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Value</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">$<?php echo number_format($property['current_value'], 0); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Appreciation</span>
                            <span class="text-lg font-bold text-green-600 dark:text-green-400">+$<?php echo number_format($property['current_value'] - $property['purchase_price'], 0); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Tab -->
        <div id="maintenance-tab" class="tab-content hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Maintenance Requests</h3>
                <a href="/admin/dashboard/maintenance/new?property_id=<?php echo $property['id']; ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                    <i class="fas fa-plus mr-2"></i>
                    New Request
                </a>
            </div>

            <div class="space-y-4">
                <?php foreach ($maintenanceRequests as $request): ?>
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['issue']); ?></h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Unit <?php echo htmlspecialchars($request['unit']); ?> • <?php echo htmlspecialchars($request['date']); ?></p>
                                </div>
                                <?php 
                                $priorityColor = $request['priority'] === 'high' ? 'danger' : 
                                               ($request['priority'] === 'medium' ? 'warning' : 'info');
                                echo UIComponents::badge(ucfirst($request['priority']), $priorityColor, 'small'); 
                                ?>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php 
                                $statusColor = $request['status'] === 'completed' ? 'success' : 
                                             ($request['status'] === 'in_progress' ? 'warning' : 'info');
                                echo UIComponents::badge(ucfirst($request['status']), $statusColor, 'small'); 
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Documents Tab -->
        <div id="documents-tab" class="tab-content hidden">
            <div class="text-center py-12">
                <i class="fas fa-folder text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Document Management</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Upload and manage property documents</p>
                <button class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-upload mr-2"></i>
                    Upload Documents
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active state from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-primary-500', 'text-primary-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active state to selected tab button
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-primary-500', 'text-primary-600');
}
</script>


<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
