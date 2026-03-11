<?php
// Include UI Components
require_once __DIR__ . '/../../../components/UIComponents.php';

$title = 'Tenants & Occupants';
$pageTitle = 'Tenants & Occupants';
$pageDescription = 'Manage tenants and room assignments in one place';

// Mock data for demonstration
$tenants = [];
$occupants = [];

// Stats calculations
$totalOccupants = count($occupants);
$activeOccupants = count(array_filter($occupants, fn($o) => $o['status'] === 'active'));
$availableRooms = 12; // Mock data
$totalProperties = 8; // Mock data

ob_start();
?>

<!-- Breadcrumbs -->
<div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
    <a href="/admin/dashboard" class="hover:text-gray-700 dark:hover:text-gray-300">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs"></i>
    <span class="text-gray-900 dark:text-white">Tenants Occupants</span>
</div>

<!-- Header with Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tenants & Occupants</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage tenants and room assignments in one place</p>
    </div>
    <div class="mt-4 sm:mt-0 flex space-x-3">
        <button onclick="refreshData()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-sync-alt mr-2"></i>
            Refresh
        </button>
        <button onclick="addNewOccupant()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            <i class="fas fa-plus mr-2"></i>
            Add New
        </button>
    </div>
</div>

<!-- Tabs -->
<div class="border-b border-gray-200 dark:border-gray-700 mb-8">
    <nav class="-mb-px flex space-x-8">
        <button onclick="switchTab('tenants')" id="tenantsTab" class="py-2 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-400">
            Tenants (<?php echo count($tenants); ?>)
        </button>
        <button onclick="switchTab('occupants')" id="occupantsTab" class="py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
            Occupants (<?php echo count($occupants); ?>)
        </button>
    </nav>
</div>

<!-- Tenants Content (Hidden by default) -->
<div id="tenantsContent" class="hidden">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <?php 
        $activeTenants = count(array_filter($tenants, fn($t) => $t['lease_status'] === 'active'));
        $expiringLeases = count(array_filter($tenants, fn($t) => $t['lease_status'] === 'expiring'));
        $overduePayments = count(array_filter($tenants, fn($t) => $t['payment_status'] === 'overdue'));
        $totalRevenue = array_sum(array_column($tenants, 'rent_amount'));
        
        echo UIComponents::statsCard('Total Tenants', count($tenants), 'users', null, 'blue');
        echo UIComponents::statsCard('Active', $activeTenants, 'home', null, 'green');
        echo UIComponents::statsCard('Expiring Leases', $expiringLeases, 'calendar-alt', null, 'yellow');
        echo UIComponents::statsCard('Properties', count(array_unique(array_column($tenants, 'property_id'))), 'building', null, 'purple');
        ?>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <?php echo UIComponents::searchBar('Search tenants...', '', 'searchTenants(this.value)'); ?>
            </div>
            
            <!-- Property Filter -->
            <?php 
            echo UIComponents::select(
                'property_filter',
                'Property',
                [
                    '' => 'All Properties',
                    'Sunset Apartments' => 'Sunset Apartments',
                    'Downtown Plaza' => 'Downtown Plaza',
                    'Riverside Complex' => 'Riverside Complex'
                ],
                '',
                false,
                'col-span-1'
            ); ?>
            
            <!-- Status Filter -->
            <?php 
            echo UIComponents::select(
                'status_filter',
                'Status',
                [
                    '' => 'All Status',
                    'active' => 'Active',
                    'expiring' => 'Expiring Soon',
                    'terminated' => 'Terminated'
                ],
                '',
                false,
                'col-span-1'
            ); ?>
        </div>
    </div>

    <!-- Tenants Table -->
    <?php if (empty($tenants)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
            <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-home text-3xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No tenants found</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">No tenants assigned yet. Add your first tenant to get started.</p>
            <button onclick="addNewTenant()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <i class="fas fa-user-plus mr-2"></i>
                Add First Tenant
            </button>
        </div>
    <?php else: ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($tenants as $tenant): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php echo UIComponents::avatar($tenant['first_name'] . ' ' . $tenant['last_name'], null, 'medium'); ?>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                ID: #<?php echo str_pad($tenant['id'], 4, '0', STR_PAD_LEFT); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <i class="fas fa-envelope mr-1 text-gray-400"></i>
                                        <?php echo htmlspecialchars($tenant['email']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-phone mr-1 text-gray-400"></i>
                                        <?php echo htmlspecialchars($tenant['phone']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($tenant['property_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Unit <?php echo htmlspecialchars($tenant['unit_number']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        $<?php echo number_format($tenant['rent_amount'], 0); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $statusColor = $tenant['lease_status'] === 'active' ? 'success' : 
                                                 ($tenant['lease_status'] === 'expiring' ? 'warning' : 'danger');
                                    echo UIComponents::badge(ucfirst($tenant['lease_status']), $statusColor, 'small'); 
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="/admin/tenants/<?php echo $tenant['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/tenants/<?php echo $tenant['id']; ?>/edit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Occupants Content (Active by default) -->
<div id="occupantsContent">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <?php 
        echo UIComponents::statsCard('Total', $totalOccupants, 'users', null, 'blue');
        echo UIComponents::statsCard('Active', $activeOccupants, 'home', null, 'green');
        echo UIComponents::statsCard('Available Rooms', $availableRooms, 'building', null, 'yellow');
        echo UIComponents::statsCard('Properties', $totalProperties, 'building', null, 'purple');
        ?>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <?php echo UIComponents::searchBar('Search occupants, phone, room, or property...', '', 'searchOccupants(this.value)'); ?>
            </div>
            
            <!-- Property Filter -->
            <?php 
            echo UIComponents::select(
                'occupant_property_filter',
                'Property',
                [
                    '' => 'All Properties',
                    'Sunset Apartments' => 'Sunset Apartments',
                    'Downtown Plaza' => 'Downtown Plaza',
                    'Riverside Complex' => 'Riverside Complex'
                ],
                '',
                false,
                'col-span-1'
            ); ?>
            
            <!-- Status Filter -->
            <?php 
            echo UIComponents::select(
                'occupant_status_filter',
                'Status',
                [
                    '' => 'All Status',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'pending' => 'Pending'
                ],
                '',
                false,
                'col-span-1'
            ); ?>
        </div>
    </div>

    <!-- Occupants Content -->
    <?php if (empty($occupants)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
            <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-home text-3xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No occupants found</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">No occupants assigned yet. Add your first occupant to get started.</p>
            <button onclick="addNewOccupant()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <i class="fas fa-user-plus mr-2"></i>
                Add First Occupant
            </button>
        </div>
    <?php else: ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Occupant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Room</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($occupants as $occupant): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php echo UIComponents::avatar($occupant['first_name'] . ' ' . $occupant['last_name'], null, 'medium'); ?>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($occupant['first_name'] . ' ' . $occupant['last_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                ID: #<?php echo str_pad($occupant['id'], 4, '0', STR_PAD_LEFT); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <i class="fas fa-phone mr-1 text-gray-400"></i>
                                        <?php echo htmlspecialchars($occupant['phone']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-envelope mr-1 text-gray-400"></i>
                                        <?php echo htmlspecialchars($occupant['email']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($occupant['room_number']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($occupant['property_name']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $statusColor = $occupant['status'] === 'active' ? 'success' : 
                                                 ($occupant['status'] === 'pending' ? 'warning' : 'danger');
                                    echo UIComponents::badge(ucfirst($occupant['status']), $statusColor, 'small'); 
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="/admin/occupants/<?php echo $occupant['id']; ?>" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/occupants/<?php echo $occupant['id']; ?>/edit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Tab switching
function switchTab(tab) {
    const tenantsTab = document.getElementById('tenantsTab');
    const occupantsTab = document.getElementById('occupantsTab');
    const tenantsContent = document.getElementById('tenantsContent');
    const occupantsContent = document.getElementById('occupantsContent');
    
    if (tab === 'tenants') {
        // Show tenants tab
        tenantsTab.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
        tenantsTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
        
        occupantsTab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
        occupantsTab.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
        
        tenantsContent.classList.remove('hidden');
        occupantsContent.classList.add('hidden');
    } else {
        // Show occupants tab
        occupantsTab.classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
        occupantsTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
        
        tenantsTab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
        tenantsTab.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
        
        occupantsContent.classList.remove('hidden');
        tenantsContent.classList.add('hidden');
    }
}

// Search functionality
function searchTenants(query) {
    const rows = document.querySelectorAll('#tenantsContent tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function searchOccupants(query) {
    const rows = document.querySelectorAll('#occupantsContent tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Action handlers
function refreshData() {
    showToast('Refreshing data...', 'info');
    setTimeout(() => {
        showToast('Data refreshed successfully', 'success');
    }, 1500);
}

function addNewOccupant() {
    window.location.href = '/admin/occupants/create';
}

function addNewTenant() {
    window.location.href = '/admin/tenants/create';
}

// Filter handlers
document.getElementById('property_filter')?.addEventListener('change', function() {
    filterTenants();
});

document.getElementById('status_filter')?.addEventListener('change', function() {
    filterTenants();
});

document.getElementById('occupant_property_filter')?.addEventListener('change', function() {
    filterOccupants();
});

document.getElementById('occupant_status_filter')?.addEventListener('change', function() {
    filterOccupants();
});

function filterTenants() {
    const propertyFilter = document.getElementById('property_filter').value;
    const statusFilter = document.getElementById('status_filter').value;
    
    const rows = document.querySelectorAll('#tenantsContent tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchesProperty = !propertyFilter || text.includes(propertyFilter.toLowerCase());
        const matchesStatus = !statusFilter || text.includes(statusFilter.toLowerCase());
        
        if (matchesProperty && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterOccupants() {
    const propertyFilter = document.getElementById('occupant_property_filter').value;
    const statusFilter = document.getElementById('occupant_status_filter').value;
    
    const rows = document.querySelectorAll('#occupantsContent tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchesProperty = !propertyFilter || text.includes(propertyFilter.toLowerCase());
        const matchesStatus = !statusFilter || text.includes(statusFilter.toLowerCase());
        
        if (matchesProperty && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Initialize with occupants tab active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('occupants');
});
</script>

<?php
$content = ob_get_clean();
?>
