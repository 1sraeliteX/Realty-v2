<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Room Details';
$pageTitle = 'Room Details';
$pageDescription = 'View comprehensive room information and manage room details';

// Mock room data
$room = [
    'id' => 1,
    'name' => 'Master Bedroom',
    'unit_id' => 1,
    'unit_number' => '101',
    'property_name' => 'Sunset Apartments',
    'type' => 'bedroom',
    'size_sqft' => 180,
    'beds' => 1,
    'baths' => 1,
    'status' => 'occupied',
    'occupant' => 'John Smith',
    'rent_portion' => 600,
    'description' => 'Spacious master bedroom with en-suite bathroom and walk-in closet. Features large windows with natural light and balcony access.',
    'amenities' => ['Walk-in Closet', 'En-suite Bathroom', 'Balcony Access', 'Hardwood Floors', 'Ceiling Fan', 'Large Windows'],
    'created_at' => '2023-01-10',
    'last_updated' => '2024-01-08',
    'images' => [
        'https://picsum.photos/seed/room1/800/600.jpg',
        'https://picsum.photos/seed/room2/800/600.jpg'
    ]
];

// Mock maintenance history
$maintenanceHistory = [
    ['id' => 1, 'title' => 'Ceiling Fan Repair', 'date' => '2023-12-01', 'cost' => 75, 'status' => 'completed'],
    ['id' => 2, 'title' => 'Window Cleaning', 'date' => '2023-10-15', 'cost' => 50, 'status' => 'completed'],
];

ob_start();
?>

<!-- Room Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($room['name']); ?></h1>
                <p class="text-indigo-100"><?php echo htmlspecialchars($room['unit_number']); ?> • <?php echo htmlspecialchars($room['property_name']); ?></p>
                <p class="text-indigo-100"><?php echo ucfirst($room['type']); ?> • <?php echo $room['size_sqft']; ?> sq ft</p>
            </div>
            <div class="flex space-x-2">
                <?php echo UIComponents::button('Edit Room', 'primary', 'medium', '/admin/rooms/' . $room['id'] . '/edit', 'edit'); ?>
                <?php echo UIComponents::button('Schedule Maintenance', 'info', 'medium', '/admin/maintenance/create?room_id=' . $room['id'], 'tools'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Room Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Type', ucfirst($room['type']), 'door-open', 'blue', '', 'Room category'); ?>
    <?php echo UIComponents::statCard('Status', ucfirst($room['status']), 'home', 'green', '', $room['occupant'] ? 'Occupied' : 'Available'); ?>
    <?php echo UIComponents::statCard('Size', $room['size_sqft'] . ' sq ft', 'expand', 'purple', '', $room['beds'] . 'BR/' . $room['baths'] . 'BA'); ?>
    <?php echo UIComponents::statCard('Rent Portion', '$' . number_format($room['rent_portion']), 'dollar-sign', 'orange', '', 'Monthly contribution'); ?>
</div>

<!-- Tabs Navigation -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button class="tab-button py-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-400" data-tab="overview">
                Overview
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="amenities">
                Amenities
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="maintenance">
                Maintenance
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="gallery">
                Gallery
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="p-6">
        <!-- Overview Tab -->
        <div id="overview" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Room Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Room Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Room Name</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($room['name']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo ucfirst($room['type']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Size</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $room['size_sqft']; ?> sq ft</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Beds/Baths</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $room['beds']; ?>/<?php echo $room['baths']; ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd><?php echo UIComponents::badge(ucfirst($room['status']), $room['status'] === 'occupied' ? 'success' : 'warning'); ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Assignment Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assignment Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Property</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($room['property_name']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($room['unit_number']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Occupant</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo $room['occupant'] ? htmlspecialchars($room['occupant']) : 'Unoccupied'; ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rent Portion</dt>
                            <dd class="text-sm font-bold text-gray-900 dark:text-white">$<?php echo number_format($room['rent_portion']); ?></dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Description -->
            <?php if (!empty($room['description'])): ?>
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($room['description']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="mt-6 flex space-x-4">
                <?php if ($room['status'] === 'vacant'): ?>
                    <?php echo UIComponents::button('Assign Occupant', 'success', 'medium', '#', 'user-plus'); ?>
                <?php endif; ?>
                <?php echo UIComponents::button('Update Room', 'primary', 'medium', '/admin/rooms/' . $room['id'] . '/edit', 'edit'); ?>
                <?php echo UIComponents::button('Schedule Maintenance', 'info', 'medium', '/admin/maintenance/create?room_id=' . $room['id'], 'tools'); ?>
                <?php echo UIComponents::button('Generate Report', 'secondary', 'medium', '#', 'file-alt'); ?>
            </div>
        </div>

        <!-- Amenities Tab -->
        <div id="amenities" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Room Amenities</h3>
                <?php echo UIComponents::button('Edit Amenities', 'primary', 'small', '/admin/rooms/' . $room['id'] . '/edit', 'edit'); ?>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($room['amenities'] as $amenity): ?>
                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($amenity); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Maintenance Tab -->
        <div id="maintenance" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Maintenance History</h3>
                <?php echo UIComponents::button('Schedule Maintenance', 'primary', 'small', '/admin/maintenance/create?room_id=' . $room['id'], 'plus'); ?>
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

        <!-- Gallery Tab -->
        <div id="gallery" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Room Gallery</h3>
                <?php echo UIComponents::button('Upload Photos', 'primary', 'small', '#', 'camera'); ?>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($room['images'] as $index => $image): ?>
                    <div class="relative group">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="Room photo <?php echo $index + 1; ?>" class="w-full h-64 object-cover rounded-lg">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex space-x-2">
                                <button class="p-2 bg-white rounded-full text-gray-800 hover:bg-gray-100">
                                    <i class="fas fa-expand"></i>
                                </button>
                                <button class="p-2 bg-white rounded-full text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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

// Assign occupant
function assignOccupant() {
    showToast('Opening occupant assignment dialog...', 'info');
}

// Generate report
function generateReport() {
    showToast('Generating room report...', 'info');
    setTimeout(() => {
        showToast('Room report generated successfully!', 'success');
    }, 2000);
}

// Upload photos
function uploadPhotos() {
    showToast('Opening photo uploader...', 'info');
}
</script>

<?php
$content = ob_get_clean();
include '../simple_layout.php';
?>
