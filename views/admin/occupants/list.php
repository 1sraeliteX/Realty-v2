<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Occupants Management';
$pageTitle = 'Occupants';
$pageDescription = 'Manage building occupants and residents';

// Mock occupants data
$occupants = [
    [
        'id' => 1,
        'first_name' => 'John',
        'last_name' => 'Smith',
        'email' => 'john.smith@email.com',
        'phone' => '(555) 123-4567',
        'type' => 'tenant',
        'property_id' => 1,
        'property_name' => 'Sunset Apartments',
        'unit_id' => 1,
        'unit_number' => '101',
        'move_in_date' => '2023-01-15',
        'status' => 'active',
        'emergency_contact' => 'Jane Smith',
        'emergency_phone' => '(555) 987-6543',
        'vehicle_info' => 'Tesla Model 3 - ABC123',
        'parking_space' => 'P-101',
        'created_at' => '2023-01-10'
    ],
    [
        'id' => 2,
        'first_name' => 'Sarah',
        'last_name' => 'Johnson',
        'email' => 'sarah.johnson@email.com',
        'phone' => '(555) 234-5678',
        'type' => 'tenant',
        'property_id' => 1,
        'property_name' => 'Sunset Apartments',
        'unit_id' => 2,
        'unit_number' => '102',
        'move_in_date' => '2023-02-01',
        'status' => 'active',
        'emergency_contact' => 'Mike Johnson',
        'emergency_phone' => '(555) 876-5432',
        'vehicle_info' => 'Honda Civic - XYZ789',
        'parking_space' => 'P-102',
        'created_at' => '2023-01-25'
    ],
    [
        'id' => 3,
        'first_name' => 'David',
        'last_name' => 'Wilson',
        'email' => 'david.wilson@email.com',
        'phone' => '(555) 345-6789',
        'type' => 'family_member',
        'property_id' => 1,
        'property_name' => 'Sunset Apartments',
        'unit_id' => 1,
        'unit_number' => '101',
        'move_in_date' => '2023-01-15',
        'status' => 'active',
        'emergency_contact' => 'John Smith',
        'emergency_phone' => '(555) 123-4567',
        'vehicle_info' => 'None',
        'parking_space' => 'None',
        'created_at' => '2023-01-15'
    ],
    [
        'id' => 4,
        'first_name' => 'Emily',
        'last_name' => 'Brown',
        'email' => 'emily.brown@email.com',
        'phone' => '(555) 456-7890',
        'type' => 'guest',
        'property_id' => 1,
        'property_name' => 'Sunset Apartments',
        'unit_id' => 3,
        'unit_number' => '103',
        'move_in_date' => '2024-01-05',
        'status' => 'temporary',
        'emergency_contact' => 'Sarah Johnson',
        'emergency_phone' => '(555) 234-5678',
        'vehicle_info' => 'Toyota Camry - DEF456',
        'parking_space' => 'Guest',
        'created_at' => '2024-01-05'
    ]
];

ob_start();
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php 
    $totalOccupants = count($occupants);
    $activeOccupants = count(array_filter($occupants, fn($o) => $o['status'] === 'active'));
    $tenants = count(array_filter($occupants, fn($o) => $o['type'] === 'tenant'));
    $temporary = count(array_filter($occupants, fn($o) => $o['status'] === 'temporary'));
    ?>
    
    <?php echo UIComponents::statCard('Total Occupants', $totalOccupants, 'users', 'blue', '', 'All residents'); ?>
    <?php echo UIComponents::statCard('Active Occupants', $activeOccupants, 'user-check', 'green', '', round(($activeOccupants / $totalOccupants) * 100, 1) . '% occupancy'); ?>
    <?php echo UIComponents::statCard('Tenants', $tenants, 'home', 'purple', '', 'Primary leaseholders'); ?>
    <?php echo UIComponents::statCard('Temporary', $temporary, 'clock', 'orange', '', 'Guests & visitors'); ?>
</div>

<!-- Actions Bar -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center space-x-3 mb-3 sm:mb-0">
            <?php echo UIComponents::button('Add Occupant', 'primary', 'medium', '/admin/occupants/create', 'user-plus'); ?>
            <?php echo UIComponents::button('Import Occupants', 'secondary', 'medium', '#', 'upload'); ?>
            <?php echo UIComponents::button('Export List', 'info', 'medium', '#', 'download'); ?>
        </div>
        
        <div class="flex items-center space-x-3">
            <div class="relative">
                <input 
                    type="text" 
                    id="searchOccupants"
                    placeholder="Search occupants..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                    onkeyup="searchOccupants(this.value)"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            
            <select id="typeFilter" onchange="filterOccupants()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">All Types</option>
                <option value="tenant">Tenants</option>
                <option value="family_member">Family Members</option>
                <option value="guest">Guests</option>
            </select>
            
            <select id="statusFilter" onchange="filterOccupants()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="temporary">Temporary</option>
            </select>
        </div>
    </div>
</div>

<!-- Occupants Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Occupant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Move In</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="occupantsTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($occupants as $occupant): ?>
                    <tr data-type="<?php echo $occupant['type']; ?>" data-status="<?php echo $occupant['status']; ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php echo UIComponents::avatar($occupant['first_name'] . ' ' . $occupant['last_name'], null, 'small'); ?>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($occupant['first_name'] . ' ' . $occupant['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <?php echo htmlspecialchars($occupant['parking_space']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['email']); ?></div>
                            <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($occupant['phone']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo UIComponents::badge(ucfirst(str_replace('_', ' ', $occupant['type'])), 
                                $occupant['type'] === 'tenant' ? 'success' : 
                                ($occupant['type'] === 'family_member' ? 'info' : 'warning')); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($occupant['property_name']); ?></div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Unit <?php echo htmlspecialchars($occupant['unit_number']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <?php echo date('M j, Y', strtotime($occupant['move_in_date'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo UIComponents::badge(ucfirst($occupant['status']), 
                                $occupant['status'] === 'active' ? 'success' : 
                                ($occupant['status'] === 'temporary' ? 'warning' : 'secondary')); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="/admin/occupants/<?php echo $occupant['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/admin/occupants/<?php echo $occupant['id']; ?>/edit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteOccupant(<?php echo $occupant['id']; ?>)" class="text-red-600 hover:text-red-900 dark:text-red-400" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
                <button onclick="goToPage('prev')" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Previous
                </button>
                <button onclick="goToPage('next')" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">1</span> to <span class="font-medium"><?php echo count($occupants); ?></span> of 
                        <span class="font-medium"><?php echo count($occupants); ?></span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button onclick="goToPage('prev')" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-primary-50 dark:bg-primary-900 text-sm font-medium text-primary-600 dark:text-primary-400">
                            1
                        </button>
                        <button onclick="goToPage('next')" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Modal -->
<?php echo UIComponents::modal('quickActionsModal', 'Quick Actions', '
    <div class="space-y-3">
        <button onclick="sendBulkNotification()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-bell mr-3 text-blue-600"></i>
            Send Bulk Notification
        </button>
        <button onclick="exportOccupants()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-download mr-3 text-green-600"></i>
            Export Occupants List
        </button>
        <button onclick="importOccupants()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-upload mr-3 text-purple-600"></i>
            Import Occupants
        </button>
        <button onclick="generateBadges()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-id-card mr-3 text-orange-600"></i>
            Generate ID Badges
        </button>
    </div>
', 'medium'); ?>

<script>
// Search functionality
function searchOccupants(query) {
    const rows = document.querySelectorAll('#occupantsTableBody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    });
}

// Filter functionality
function filterOccupants() {
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('#occupantsTableBody tr');
    rows.forEach(row => {
        const type = row.dataset.type;
        const status = row.dataset.status;
        
        const matchesType = !typeFilter || type === typeFilter;
        const matchesStatus = !statusFilter || status === statusFilter;
        
        row.style.display = matchesType && matchesStatus ? '' : 'none';
    });
}

// Delete occupant
function deleteOccupant(id) {
    if (confirm('Are you sure you want to delete this occupant? This action cannot be undone.')) {
        showToast('Deleting occupant...', 'info');
        setTimeout(() => {
            showToast('Occupant deleted successfully!', 'success');
            // In a real app, this would remove the row and make an API call
            location.reload();
        }, 2000);
    }
}

// Quick actions
function sendBulkNotification() {
    closeModal('quickActionsModal');
    showToast('Opening bulk notification composer...', 'info');
}

function exportOccupants() {
    closeModal('quickActionsModal');
    showToast('Exporting occupants list...', 'info');
    setTimeout(() => {
        showToast('Occupants exported successfully!', 'success');
    }, 2000);
}

function importOccupants() {
    closeModal('quickActionsModal');
    showToast('Opening import wizard...', 'info');
}

function generateBadges() {
    closeModal('quickActionsModal');
    showToast('Generating ID badges...', 'info');
    setTimeout(() => {
        showToast('ID badges generated successfully!', 'success');
    }, 2000);
}

// Pagination
function goToPage(direction) {
    showToast(`Loading ${direction} page...`, 'info');
}

// Add quick actions button to header
document.addEventListener('DOMContentLoaded', function() {
    const headerActions = document.querySelector('.bg-white.dark\\:bg-gray-800.rounded-lg.shadow-lg.p-4.mb-6 .flex.flex-col.sm\\:flex-row.sm\\:items-center.sm\\:justify-between .flex.items-center.space-x-3.mb-3.sm\\:mb-0');
    if (headerActions) {
        const quickActionsBtn = document.createElement('button');
        quickActionsBtn.className = 'inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700';
        quickActionsBtn.innerHTML = '<i class="fas fa-bolt mr-2"></i>Quick Actions';
        quickActionsBtn.onclick = () => document.getElementById('quickActionsModal').classList.remove('hidden');
        headerActions.appendChild(quickActionsBtn);
    }
});
</script>

<?php
$content = ob_get_clean();
include '../simple_layout.php';
?>
