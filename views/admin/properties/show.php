<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Property Details';
$pageTitle = 'Property Details';
$pageDescription = 'View comprehensive property information and manage related data';

// Mock property data
$property = [
    'id' => 1,
    'name' => 'Sunset Apartments',
    'address' => '123 Main St, Los Angeles, CA 90001',
    'type' => 'Residential',
    'status' => 'occupied',
    'unit_count' => 24,
    'occupied_units' => 22,
    'vacant_units' => 2,
    'monthly_revenue' => 28800,
    'purchase_price' => 2500000,
    'current_value' => 2800000,
    'image' => 'https://picsum.photos/seed/sunset/800/400.jpg',
    'year_built' => 2018,
    'size_sqft' => 15000,
    'lot_size_acres' => 2.5,
    'parking_spaces' => 48,
    'created_at' => '2023-01-15',
    'last_updated' => '2024-01-10',
    'description' => 'Modern residential apartment complex located in the heart of Los Angeles. Features include swimming pool, fitness center, and secure parking.',
    'amenities' => ['Swimming Pool', 'Fitness Center', 'Secure Parking', 'Elevator', 'Laundry Facilities', 'Pet Friendly', 'Package Room', 'BBQ Area'],
    'expenses' => [
        'mortgage' => 12000,
        'insurance' => 2000,
        'taxes' => 3500,
        'maintenance' => 1500,
        'utilities' => 800,
        'other' => 500
    ]
];

// Mock units data
$units = [
    ['id' => 1, 'unit_number' => '101', 'type' => '1BR', 'sqft' => 650, 'rent' => 1200, 'status' => 'occupied', 'tenant' => 'John Smith'],
    ['id' => 2, 'unit_number' => '102', 'type' => '1BR', 'sqft' => 650, 'rent' => 1200, 'status' => 'occupied', 'tenant' => 'Sarah Johnson'],
    ['id' => 3, 'unit_number' => '103', 'type' => '2BR', 'sqft' => 850, 'rent' => 1600, 'status' => 'vacant', 'tenant' => null],
    ['id' => 4, 'unit_number' => '201', 'type' => '2BR', 'sqft' => 850, 'rent' => 1600, 'status' => 'occupied', 'tenant' => 'Mike Wilson'],
];

// Mock maintenance requests
$maintenanceRequests = [
    ['id' => 1, 'title' => 'Leaky Faucet', 'status' => 'pending', 'priority' => 'medium', 'created_at' => '2024-01-08'],
    ['id' => 2, 'title' => 'AC Not Working', 'status' => 'in_progress', 'priority' => 'high', 'created_at' => '2024-01-10'],
];

ob_start();
?>

<!-- Property Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="relative h-64 bg-gradient-to-r from-primary-600 to-primary-700">
        <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['name']); ?>" class="w-full h-full object-cover opacity-50">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2"><?php echo htmlspecialchars($property['name']); ?></h1>
                    <p class="text-white/90 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <?php echo htmlspecialchars($property['address']); ?>
                    </p>
                </div>
                <div class="flex space-x-2">
                    <?php echo UIComponents::button('Edit Property', 'primary', 'medium', '/admin/properties/' . $property['id'] . '/edit', 'edit'); ?>
                    <?php echo UIComponents::button('Add Unit', 'success', 'medium', '/admin/units/create?property_id=' . $property['id'], 'plus'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Property Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Monthly Revenue', '$' . number_format($property['monthly_revenue']), 'dollar-sign', 'green', 'up', '12% from last month'); ?>
    <?php echo UIComponents::statCard('Occupancy Rate', round(($property['occupied_units'] / $property['unit_count']) * 100, 1) . '%', 'users', 'blue', 'up', '2% increase'); ?>
    <?php echo UIComponents::statCard('Total Units', $property['unit_count'], 'building', 'purple', '', $property['occupied_units'] . ' occupied'); ?>
    <?php echo UIComponents::statCard('Property Value', '$' . number_format($property['current_value']), 'chart-line', 'orange', 'up', '12% ROI'); ?>
</div>

<!-- Tabs Navigation -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button class="tab-button py-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-400" data-tab="overview">
                Overview
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="units">
                Units
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="financials">
                Financials
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
                <!-- Property Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Property Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Property Type</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($property['type']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Year Built</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $property['year_built']; ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Size</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo number_format($property['size_sqft']); ?> sq ft</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Lot Size</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $property['lot_size_acres']; ?> acres</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Parking Spaces</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $property['parking_spaces']; ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd><?php echo UIComponents::badge(ucfirst($property['status']), $property['status'] === 'occupied' ? 'success' : 'warning'); ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Amenities -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Amenities</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <?php foreach ($property['amenities'] as $amenity): ?>
                            <div class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <?php echo htmlspecialchars($amenity); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h3>
                <p class="text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($property['description']); ?></p>
            </div>
        </div>

        <!-- Units Tab -->
        <div id="units" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Units Overview</h3>
                <?php echo UIComponents::button('Add Unit', 'primary', 'small', '/admin/units/create?property_id=' . $property['id'], 'plus'); ?>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($unit['unit_number']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($unit['type']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo $unit['sqft']; ?> sq ft</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">$<?php echo number_format($unit['rent']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo UIComponents::badge(ucfirst($unit['status']), $unit['status'] === 'occupied' ? 'success' : 'warning'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo $unit['tenant'] ? htmlspecialchars($unit['tenant']) : '-'; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="/admin/units/<?php echo $unit['id']; ?>/edit" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">Edit</a>
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
                <!-- Monthly Income -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Income</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Rental Income</span>
                            <span class="text-sm font-bold text-green-600 dark:text-green-400">$<?php echo number_format($property['monthly_revenue']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Other Income</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">$500</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border-t-2 border-green-200 dark:border-green-800">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Total Income</span>
                            <span class="text-sm font-bold text-green-600 dark:text-green-400">$<?php echo number_format($property['monthly_revenue'] + 500); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Monthly Expenses -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Expenses</h3>
                    <div class="space-y-3">
                        <?php foreach ($property['expenses'] as $category => $amount): ?>
                            <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo ucfirst($category); ?></span>
                                <span class="text-sm font-bold text-red-600 dark:text-red-400">$<?php echo number_format($amount); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border-t-2 border-red-200 dark:border-red-800">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Total Expenses</span>
                            <span class="text-sm font-bold text-red-600 dark:text-red-400">$<?php echo number_format(array_sum($property['expenses'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="mt-6 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Net Monthly Profit</h3>
                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        $<?php echo number_format(($property['monthly_revenue'] + 500) - array_sum($property['expenses'])); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Maintenance Tab -->
        <div id="maintenance" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Maintenance Requests</h3>
                <?php echo UIComponents::button('Create Request', 'primary', 'small', '/admin/maintenance/create?property_id=' . $property['id'], 'plus'); ?>
            </div>
            
            <div class="space-y-4">
                <?php foreach ($maintenanceRequests as $request): ?>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['title']); ?></h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Created on <?php echo $request['created_at']; ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php echo UIComponents::badge(ucfirst($request['status']), $request['status'] === 'pending' ? 'warning' : 'info'); ?>
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Property Documents</h3>
                <?php echo UIComponents::button('Upload Document', 'primary', 'small', '/admin/documents/create?property_id=' . $property['id'], 'upload'); ?>
            </div>
            
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <i class="fas fa-folder-open text-4xl mb-4"></i>
                <p>No documents uploaded yet</p>
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
include '../simple_layout.php';
?>
