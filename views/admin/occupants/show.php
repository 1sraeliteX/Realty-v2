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


<!-- Occupant Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-teal-600 to-teal-700 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <?php echo UIComponents::avatar($occupant['first_name'] . ' ' . $occupant['last_name'], $occupant['profile_photo'], 'large'); ?>
                <div>
                    <h1 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($occupant['first_name'] . ' ' . $occupant['last_name']); ?></h1>
                    <p class="text-teal-100"><?php echo htmlspecialchars($occupant['email']); ?></p>
                    <p class="text-teal-100"><?php echo htmlspecialchars($occupant['phone']); ?></p>
                </div>
            </div>
            <div class="flex space-x-2">
                <?php echo UIComponents::button('Edit Occupant', 'primary', 'medium', '/admin/occupants/' . $occupant['id'] . '/edit', 'edit'); ?>
                <?php echo UIComponents::button('Send Message', 'info', 'medium', '#', 'envelope'); ?>
                <?php echo UIComponents::button('Generate Badge', 'success', 'medium', '#', 'id-card'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Occupant Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Type', ucfirst(str_replace('_', ' ', $occupant['type'])), 'user', 'blue', '', 'Primary resident'); ?>
    <?php echo UIComponents::statCard('Status', ucfirst($occupant['status']), 'user-check', 'green', '', 'Active since ' . date('M j, Y', strtotime($occupant['move_in_date']))); ?>
    <?php echo UIComponents::statCard('Property', htmlspecialchars($occupant['property_name']), 'building', 'purple', '', 'Unit ' . htmlspecialchars($occupant['unit_number'])); ?>
    <?php echo UIComponents::statCard('Parking', htmlspecialchars($occupant['parking_space']), 'car', 'orange', '', $occupant['vehicle_info'] ? 'Vehicle registered' : 'No vehicle'); ?>
</div>

<!-- Tabs Navigation -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button class="tab-button py-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-400" data-tab="overview">
                Overview
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="family">
                Family Members
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="access">
                Access Cards
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="vehicle">
                Vehicle Info
            </button>
            <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300" data-tab="history">
                History
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
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['first_name'] . ' ' . $occupant['last_name']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['email']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['phone']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($occupant['date_of_birth'])); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Occupant Type</dt>
                            <dd><?php echo UIComponents::badge(ucfirst(str_replace('_', ' ', $occupant['type'])), 'success'); ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Property Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Property Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Property</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['property_name']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['unit_number']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Move-in Date</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($occupant['move_in_date'])); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Parking Space</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['parking_space']); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Storage Unit</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['storage_unit']); ?></dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact</h3>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Name</p>
                            <p class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['emergency_contact']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</p>
                            <p class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['emergency_phone']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Relationship</p>
                            <p class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['emergency_relationship']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <?php if (!empty($occupant['notes'])): ?>
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($occupant['notes']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Family Members Tab -->
        <div id="family" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Family Members & Roommates</h3>
                <?php echo UIComponents::button('Add Family Member', 'primary', 'small', '/admin/occupants/create?unit_id=' . $occupant['unit_id'], 'user-plus'); ?>
            </div>
            
            <div class="space-y-4">
                <?php foreach ($familyMembers as $member): ?>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <?php echo UIComponents::avatar($member['name'], null, 'medium'); ?>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($member['name']); ?></h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($member['relationship']); ?> • Age <?php echo $member['age']; ?></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Move-in: <?php echo date('M j, Y', strtotime($member['move_in_date'])); ?></p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="/admin/occupants/<?php echo $member['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">View</a>
                                <a href="/admin/occupants/<?php echo $member['id']; ?>/edit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Edit</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Access Cards Tab -->
        <div id="access" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Access Cards</h3>
                <?php echo UIComponents::button('Issue New Card', 'primary', 'small', '#', 'credit-card'); ?>
            </div>
            
            <div class="space-y-4">
                <?php foreach ($accessCards as $card): ?>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Card #<?php echo htmlspecialchars($card['card_number']); ?></h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Issued: <?php echo date('M j, Y', strtotime($card['issued_date'])); ?></p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <?php foreach ($card['access_areas'] as $area): ?>
                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-xs rounded-full">
                                            <?php echo htmlspecialchars($area); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php echo UIComponents::badge(ucfirst($card['status']), $card['status'] === 'active' ? 'success' : 'warning'); ?>
                                <button class="text-red-600 hover:text-red-800 dark:text-red-400">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Vehicle Info Tab -->
        <div id="vehicle" class="tab-content hidden">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vehicle Information</h3>
            
            <?php if ($occupant['vehicle_info']): ?>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Vehicle Details</p>
                            <p class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['vehicle_info']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Parking Space</p>
                            <p class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['parking_space']); ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex space-x-4">
                        <?php echo UIComponents::button('Update Vehicle', 'primary', 'small', '/admin/occupants/' . $occupant['id'] . '/edit', 'edit'); ?>
                        <?php echo UIComponents::button('Remove Vehicle', 'danger', 'small', '#', 'trash'); ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-car text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Vehicle Registered</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">This occupant has not registered any vehicle</p>
                    <?php echo UIComponents::button('Register Vehicle', 'primary', 'medium', '/admin/occupants/' . $occupant['id'] . '/edit', 'plus'); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- History Tab -->
        <div id="history" class="tab-content hidden">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Occupant History</h3>
            
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-home text-green-600 dark:text-green-400 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Moved into Unit <?php echo htmlspecialchars($occupant['unit_number']); ?></p>
                            <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y', strtotime($occupant['move_in_date'])); ?></span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Initial move-in as primary tenant</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-id-card text-blue-600 dark:text-blue-400 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Access cards issued</p>
                            <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y', strtotime($occupant['move_in_date'])); ?></span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">2 access cards issued for building and parking access</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-car text-purple-600 dark:text-purple-400 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Vehicle registered</p>
                            <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y', strtotime($occupant['move_in_date'])); ?></span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tesla Model 3 registered with parking space P-101</p>
                    </div>
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

// Send message
function sendMessage() {
    showToast('Opening message composer...', 'info');
}

// Generate badge
function generateBadge() {
    showToast('Generating ID badge...', 'info');
    setTimeout(() => {
        showToast('ID badge generated successfully!', 'success');
    }, 2000);
}

// Issue new card
function issueNewCard() {
    showToast('Opening card issuance form...', 'info');
}

// Remove vehicle
function removeVehicle() {
    if (confirm('Remove vehicle registration?')) {
        showToast('Removing vehicle...', 'info');
        setTimeout(() => {
            showToast('Vehicle removed successfully!', 'success');
            location.reload();
        }, 2000);
    }
}
</script>


<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
