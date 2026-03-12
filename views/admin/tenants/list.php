<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$tenants = ViewManager::get('tenants') ?? DataProvider::get('tenants');
$stats = ViewManager::get('stats') ?? DataProvider::get('tenant_stats');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Tenants Management');
ViewManager::set('pageTitle', 'Tenants');
ViewManager::set('pageDescription', 'Manage tenant information and lease agreements');

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
        <a href="/admin/tenants/create" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            <i class="fas fa-user-plus mr-2"></i>
            Add Tenant
        </a>
    </div>
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
            <button id="gridView" class="p-2 text-primary-600 border border-primary-600 rounded">
                <i class="fas fa-th"></i>
            </button>
            <button id="listView" class="p-2 text-gray-400 border border-gray-300 rounded">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>
</div>

<!-- Tenants Grid -->
<div id="tenantsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <?php foreach ($tenants as $tenant): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-200">
            <!-- Tenant Header -->
            <div class="relative h-32 bg-gradient-to-r from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700">
                <div class="absolute top-2 right-2">
                    <?php 
                    $leaseColor = $tenant['lease_status'] === 'active' ? 'success' : 
                                 ($tenant['lease_status'] === 'expiring' ? 'warning' : 'danger');
                    echo UIComponents::badge(ucfirst($tenant['lease_status']), $leaseColor, 'small'); 
                    ?>
                </div>
                <div class="absolute top-2 left-2">
                    <?php echo UIComponents::badge('Tenant', 'gray', 'small'); ?>
                </div>
                <div class="absolute bottom-4 left-4 right-4">
                    <div class="flex items-center">
                        <?php echo UIComponents::avatar($tenant['first_name'] . ' ' . $tenant['last_name'], null, 'large'); ?>
                        <div class="ml-3 min-w-0 flex-1">
                            <h3 class="text-lg font-semibold text-white truncate"><?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?></h3>
                            <p class="text-sm text-gray-200 truncate">ID: #<?php echo str_pad($tenant['id'], 4, '0', STR_PAD_LEFT); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tenant Details -->
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 truncate">
                            <i class="fas fa-envelope mr-1 flex-shrink-0"></i>
                            <?php echo htmlspecialchars($tenant['email']); ?>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 truncate">
                            <i class="fas fa-phone mr-1 flex-shrink-0"></i>
                            <?php echo htmlspecialchars($tenant['phone']); ?>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                            <i class="fas fa-map-marker-alt mr-1 flex-shrink-0"></i>
                            <?php echo htmlspecialchars($tenant['property_name']); ?>, Unit <?php echo htmlspecialchars($tenant['unit_number']); ?>
                        </p>
                    </div>
                    <div class="flex space-x-1">
                        <a href="/admin/tenants/<?php echo $tenant['id']; ?>" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/admin/tenants/<?php echo $tenant['id']; ?>/edit" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">$<?php echo number_format($tenant['rent_amount'], 0); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Monthly Rent</p>
                    </div>
                    <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                        <?php 
                        $paymentColor = $tenant['payment_status'] === 'current' ? 'success' : 
                                      ($tenant['payment_status'] === 'overdue' ? 'danger' : 'info');
                        echo '<p class="text-lg font-semibold text-' . $paymentColor . '-600 dark:text-' . $paymentColor . '-400">' . ucfirst($tenant['payment_status']) . '</p>';
                        ?>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Payment Status</p>
                    </div>
                </div>
                
                <!-- Lease Info -->
                <div class="flex items-center justify-between mb-4">
                    <div class="min-w-0 flex-1 mr-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Lease Period</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            <?php echo date('M j, Y', strtotime($tenant['lease_start'])); ?> - <?php echo date('M j, Y', strtotime($tenant['lease_end'])); ?>
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Time Remaining</p>
                        <p class="text-sm font-medium text-primary-600 dark:text-primary-400">
                            <?php 
                            $daysRemaining = (strtotime($tenant['lease_end']) - strtotime(date('Y-m-d'))) / 86400;
                            if ($daysRemaining > 0) {
                                echo max(0, floor($daysRemaining)) . ' days';
                            } else {
                                echo 'Expired';
                            }
                            ?>
                        </p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <a href="/admin/tenants/<?php echo $tenant['id']; ?>" class="flex-1 text-center px-3 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">
                        View Details
                    </a>
                    <button onclick="sendMessage(<?php echo $tenant['id']; ?>)" class="flex-1 text-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Message
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
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
            <i class="fas fa-file-lines mr-3 text-green-600"></i>
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
    // Filter tenants based on search query
    const cards = document.querySelectorAll('#tenantsGrid > div');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// View toggle
document.getElementById('gridView').addEventListener('click', function() {
    this.classList.add('text-primary-600', 'border-primary-600');
    this.classList.remove('text-gray-400', 'border-gray-300');
    document.getElementById('listView').classList.add('text-gray-400', 'border-gray-300');
    document.getElementById('listView').classList.remove('text-primary-600', 'border-primary-600');
});

document.getElementById('listView').addEventListener('click', function() {
    this.classList.add('text-primary-600', 'border-primary-600');
    this.classList.remove('text-gray-400', 'border-gray-300');
    document.getElementById('gridView').classList.add('text-gray-400', 'border-gray-300');
    document.getElementById('gridView').classList.remove('text-primary-600', 'border-primary-600');
    showToast('List view coming soon!', 'info');
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
    
    const cards = document.querySelectorAll('#tenantsGrid > div');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const matchesProperty = !propertyFilter || text.includes(propertyFilter.toLowerCase());
        const matchesStatus = !leaseStatusFilter || text.includes(leaseStatusFilter.toLowerCase());
        
        if (matchesProperty && matchesStatus) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
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
include __DIR__ . '/../simple_layout.php';
?>
