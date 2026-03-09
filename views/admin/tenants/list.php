<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Tenants Management';
$pageTitle = 'Tenants';
$pageDescription = 'Manage tenant information and lease agreements';

// Mock tenants data
$tenants = [
    [
        'id' => 1,
        'first_name' => 'John',
        'last_name' => 'Smith',
        'email' => 'john.smith@email.com',
        'phone' => '(555) 123-4567',
        'property_name' => 'Sunset Apartments',
        'unit_number' => '1A',
        'lease_status' => 'active',
        'payment_status' => 'current',
        'rent_amount' => 1200,
        'lease_start' => '2023-01-01',
        'lease_end' => '2024-01-01',
        'move_in_date' => '2023-01-01',
        'emergency_contact' => 'Jane Smith - (555) 987-6543',
        'created_at' => '2022-12-15'
    ],
    [
        'id' => 2,
        'first_name' => 'Sarah',
        'last_name' => 'Johnson',
        'email' => 'sarah.johnson@email.com',
        'phone' => '(555) 234-5678',
        'property_name' => 'Sunset Apartments',
        'unit_number' => '1B',
        'lease_status' => 'active',
        'payment_status' => 'current',
        'rent_amount' => 1200,
        'lease_start' => '2023-02-01',
        'lease_end' => '2024-02-01',
        'move_in_date' => '2023-02-01',
        'emergency_contact' => 'Mike Johnson - (555) 876-5432',
        'created_at' => '2023-01-20'
    ],
    [
        'id' => 3,
        'first_name' => 'Mike',
        'last_name' => 'Chen',
        'email' => 'mike.chen@email.com',
        'phone' => '(555) 345-6789',
        'property_name' => 'Sunset Apartments',
        'unit_number' => '2A',
        'lease_status' => 'active',
        'payment_status' => 'overdue',
        'rent_amount' => 1600,
        'lease_start' => '2023-03-01',
        'lease_end' => '2024-03-01',
        'move_in_date' => '2023-03-01',
        'emergency_contact' => 'Lisa Chen - (555) 765-4321',
        'created_at' => '2023-02-10'
    ],
    [
        'id' => 4,
        'first_name' => 'Emily',
        'last_name' => 'Davis',
        'email' => 'emily.davis@email.com',
        'phone' => '(555) 456-7890',
        'property_name' => 'Sunset Apartments',
        'unit_number' => '3A',
        'lease_status' => 'active',
        'payment_status' => 'current',
        'rent_amount' => 900,
        'lease_start' => '2023-04-01',
        'lease_end' => '2024-04-01',
        'move_in_date' => '2023-04-01',
        'emergency_contact' => 'Robert Davis - (555) 654-3210',
        'created_at' => '2023-03-15'
    ],
    [
        'id' => 5,
        'first_name' => 'Robert',
        'last_name' => 'Wilson',
        'email' => 'robert.wilson@email.com',
        'phone' => '(555) 567-8901',
        'property_name' => 'Sunset Apartments',
        'unit_number' => '3B',
        'lease_status' => 'expiring',
        'payment_status' => 'current',
        'rent_amount' => 900,
        'lease_start' => '2023-05-01',
        'lease_end' => '2024-02-01',
        'move_in_date' => '2023-05-01',
        'emergency_contact' => 'Mary Wilson - (555) 543-2109',
        'created_at' => '2023-04-20'
    ],
    [
        'id' => 6,
        'first_name' => 'Lisa',
        'last_name' => 'Anderson',
        'email' => 'lisa.anderson@email.com',
        'phone' => '(555) 678-9012',
        'property_name' => 'Downtown Plaza',
        'unit_number' => '101',
        'lease_status' => 'terminated',
        'payment_status' => 'paid',
        'rent_amount' => 2000,
        'lease_start' => '2023-01-01',
        'lease_end' => '2023-12-31',
        'move_in_date' => '2023-01-01',
        'emergency_contact' => 'Tom Anderson - (555) 432-1098',
        'created_at' => '2022-12-01'
    ]
];

ob_start();
?>

<!-- Header with Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tenants</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage tenant information and lease agreements</p>
    </div>
    <div class="mt-4 sm:mt-0 flex space-x-3">
        <button onclick="exportTenants()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-download mr-2"></i>
            Export
        </button>
        <a href="/admin/tenants" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            <i class="fas fa-user-plus mr-2"></i>
            Add Tenant
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <?php 
    $activeTenants = count(array_filter($tenants, fn($t) => $t['lease_status'] === 'active'));
    $expiringLeases = count(array_filter($tenants, fn($t) => $t['lease_status'] === 'expiring'));
    $overduePayments = count(array_filter($tenants, fn($t) => $t['payment_status'] === 'overdue'));
    $totalRevenue = array_sum(array_column($tenants, 'rent_amount'));
    
    echo UIComponents::statsCard('Active Tenants', $activeTenants, 'users', null, 'green');
    echo UIComponents::statsCard('Expiring Leases', $expiringLeases, 'calendar-alt', null, 'yellow');
    echo UIComponents::statsCard('Overdue Payments', $overduePayments, 'exclamation-triangle', null, 'red');
    echo UIComponents::statsCard('Monthly Revenue', '$' . number_format($totalRevenue, 0), 'dollar-sign', null, 'blue');
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
        
        <!-- Lease Status Filter -->
        <?php 
        echo UIComponents::select(
            'lease_status_filter',
            'Lease Status',
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
    
    <!-- Additional Filters -->
    <div class="mt-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <button class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">
                <i class="fas fa-filter mr-1"></i>
                Advanced Filters
            </button>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Showing <?php echo count($tenants); ?> tenants
            </span>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">View:</span>
            <button id="gridView" class="p-2 text-gray-400 border border-gray-300 rounded">
                <i class="fas fa-th"></i>
            </button>
            <button id="listView" class="p-2 text-primary-600 border border-primary-600 rounded">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>
</div>

<!-- Tenants Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Tenant
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Contact
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Property
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Rent
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Lease Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Payment Status
                    </th>
                    <th class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
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
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                per month
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $leaseColor = $tenant['lease_status'] === 'active' ? 'success' : 
                                         ($tenant['lease_status'] === 'expiring' ? 'warning' : 'danger');
                            echo UIComponents::badge(ucfirst($tenant['lease_status']), $leaseColor, 'small'); 
                            ?>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <?php if ($tenant['lease_status'] === 'expiring'): ?>
                                    Expires: <?php echo date('M j, Y', strtotime($tenant['lease_end'])); ?>
                                <?php elseif ($tenant['lease_status'] === 'active'): ?>
                                    Until <?php echo date('M j, Y', strtotime($tenant['lease_end'])); ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $paymentColor = $tenant['payment_status'] === 'current' ? 'success' : 
                                          ($tenant['payment_status'] === 'overdue' ? 'danger' : 'info');
                            echo UIComponents::badge(ucfirst($tenant['payment_status']), $paymentColor, 'small'); 
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
                                <button onclick="sendMessage(<?php echo $tenant['id']; ?>)" class="text-green-600 hover:text-green-900 dark:text-green-400">
                                    <i class="fas fa-envelope"></i>
                                </button>
                                <button onclick="viewPayments(<?php echo $tenant['id']; ?>)" class="text-purple-600 hover:text-purple-900 dark:text-purple-400">
                                    <i class="fas fa-credit-card"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="flex items-center justify-between">
    <div class="text-sm text-gray-700 dark:text-gray-300">
        Showing <span class="font-medium">1</span> to <span class="font-medium"><?php echo count($tenants); ?></span> of <span class="font-medium"><?php echo count($tenants); ?></span> results
    </div>
    <?php echo UIComponents::pagination(1, 1, 'goToPage'); ?>
</div>

<!-- Quick Actions Modal -->
<?php 
echo UIComponents::modal(
    'quickActionsModal',
    'Quick Actions',
    '<div class="space-y-3">
        <button onclick="sendBulkMessage()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-envelope mr-3 text-blue-600"></i>
            Send Bulk Message
        </button>
        <button onclick="generateReports()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-file-alt mr-3 text-green-600"></i>
            Generate Reports
        </button>
        <button onclick="sendReminders()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-bell mr-3 text-yellow-600"></i>
            Send Payment Reminders
        </button>
        <button onclick="importTenants()" class="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
            <i class="fas fa-upload mr-3 text-purple-600"></i>
            Import Tenants
        </button>
    </div>',
    '<button onclick="closeModal(\'quickActionsModal\')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Close</button>',
    'medium'
); ?>

<script>
let currentTenantId = null;

// Search functionality
function searchTenants(query) {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// View toggle
document.getElementById('gridView').addEventListener('click', function() {
    this.classList.add('text-primary-600', 'border-primary-600');
    this.classList.remove('text-gray-400', 'border-gray-300');
    document.getElementById('listView').classList.add('text-gray-400', 'border-gray-300');
    document.getElementById('listView').classList.remove('text-primary-600', 'border-primary-600');
    showToast('Grid view coming soon!', 'info');
});

document.getElementById('listView').addEventListener('click', function() {
    this.classList.add('text-primary-600', 'border-primary-600');
    this.classList.remove('text-gray-400', 'border-gray-300');
    document.getElementById('gridView').classList.add('text-gray-400', 'border-gray-300');
    document.getElementById('gridView').classList.remove('text-primary-600', 'border-primary-600');
});

// Tenant actions
function sendMessage(tenantId) {
    currentTenantId = tenantId;
    showToast('Opening message composer...', 'info');
    // In a real app, this would open a message modal
}

function viewPayments(tenantId) {
    window.location.href = `/admin/payments?tenant_id=${tenantId}`;
}

// Filter handlers
document.getElementById('property_filter').addEventListener('change', function() {
    filterTenants();
});

document.getElementById('lease_status_filter').addEventListener('change', function() {
    filterTenants();
});

function filterTenants() {
    const propertyFilter = document.getElementById('property_filter').value;
    const leaseStatusFilter = document.getElementById('lease_status_filter').value;
    
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchesProperty = !propertyFilter || text.includes(propertyFilter.toLowerCase());
        const matchesStatus = !leaseStatusFilter || text.includes(leaseStatusFilter.toLowerCase());
        
        if (matchesProperty && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Quick actions
function exportTenants() {
    showToast('Exporting tenant data...', 'info');
    setTimeout(() => {
        showToast('Tenants exported successfully', 'success');
    }, 2000);
}

function sendBulkMessage() {
    closeModal('quickActionsModal');
    showToast('Opening bulk message composer...', 'info');
}

function generateReports() {
    closeModal('quickActionsModal');
    showToast('Generating tenant reports...', 'info');
    setTimeout(() => {
        showToast('Reports generated successfully', 'success');
    }, 2000);
}

function sendReminders() {
    closeModal('quickActionsModal');
    showToast('Sending payment reminders...', 'info');
    setTimeout(() => {
        showToast('Reminders sent successfully', 'success');
    }, 2000);
}

function importTenants() {
    closeModal('quickActionsModal');
    showToast('Opening import wizard...', 'info');
}

function goToPage(page) {
    showToast(`Loading page ${page}...`, 'info');
}

// Quick actions button (add to header)
document.addEventListener('DOMContentLoaded', function() {
    const headerActions = document.querySelector('.flex.space-x-3');
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
include '../dashboard_layout.php';
?>
