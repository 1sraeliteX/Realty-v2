<?php
// Framework components are auto-loaded by ViewManager (anti-scattering compliant)

// Get data from ViewManager (anti-scattering compliant)
$stats = ViewManager::get('stats', [
    'total_units' => 48,
    'occupied_units' => 36,
    'vacant_units' => 12,
    'maintenance_units' => 3
]);

$units = ViewManager::get('units', [
    ['id' => 1, 'unit_number' => '101', 'property_name' => 'Sunset Apartments', 'type' => '1br', 'bedrooms' => 1, 'bathrooms' => 1, 'rent_price' => 1200, 'status' => 'occupied', 'tenant_name' => 'John Doe'],
    ['id' => 2, 'unit_number' => '102', 'property_name' => 'Sunset Apartments', 'type' => '2br', 'bedrooms' => 2, 'bathrooms' => 2, 'rent_price' => 1500, 'status' => 'available', 'tenant_name' => null],
    ['id' => 3, 'unit_number' => '201', 'property_name' => 'Downtown Plaza', 'type' => 'studio', 'bedrooms' => 0, 'bathrooms' => 1, 'rent_price' => 900, 'status' => 'maintenance', 'tenant_name' => null]
]);

$properties = ViewManager::get('properties', [
    ['id' => 1, 'name' => 'Sunset Apartments'],
    ['id' => 2, 'name' => 'Downtown Plaza'],
    ['id' => 3, 'name' => 'Riverside Complex']
]);
?>

<!-- Units Management Content -->
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Units Management</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage all property units across your portfolio</p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-download mr-2"></i>
                Export
            </button>
            <a href="/admin/units/create" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <i class="fas fa-plus mr-2"></i>
                Add Unit
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                    <i class="fas fa-door-open text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Units</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $stats['total_units'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupied</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $stats['occupied_units'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $stats['vacant_units'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                    <i class="fas fa-tools text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Maintenance</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $stats['maintenance_units'] ?? 0; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" placeholder="Search units..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">All Properties</option>
                    <?php foreach ($properties as $property): ?>
                        <option value="<?php echo $property['id']; ?>"><?php echo htmlspecialchars($property['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="occupied">Occupied</option>
                    <option value="available">Available</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    <option value="studio">Studio</option>
                    <option value="1br">1 Bedroom</option>
                    <option value="2br">2 Bedroom</option>
                    <option value="3br">3 Bedroom</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Units Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tenant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($units as $unit): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($unit['unit_number']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($unit['property_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo ucfirst($unit['type']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">$<?php echo number_format($unit['rent_price'], 0); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusColors = [
                                    'occupied' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'available' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'maintenance' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                                ];
                                $colorClass = $statusColors[$unit['status']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $colorClass; ?>">
                                    <?php echo ucfirst($unit['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo $unit['tenant_name'] ?: '-'; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="/admin/units/<?php echo $unit['id']; ?>/edit" class="text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300 mr-3">Edit</a>
                                <a href="#" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
