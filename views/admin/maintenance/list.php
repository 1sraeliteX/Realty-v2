<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Get data from ViewManager (anti-scattering compliant)
$maintenanceStats = \ViewManager::get('maintenanceStats') ?? [
    'total' => 0,
    'pending' => 0,
    'in_progress' => 0,
    'completed' => 0
];
$maintenanceRequests = \ViewManager::get('maintenanceRequests') ?? [];
$user = \ViewManager::get('user');

ob_start();
?>

<!-- Breadcrumb -->
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/admin/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">
                        Maintenance
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Maintenance Overview Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <i class="fas fa-wrench text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Requests</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo $maintenanceStats['total']; ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo $maintenanceStats['pending']; ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                <i class="fas fa-spinner text-orange-600 dark:text-orange-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Progress</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo $maintenanceStats['in_progress']; ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo $maintenanceStats['completed']; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Requests Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Maintenance Requests</h3>
            <a href="/admin/maintenance/create" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>New Request
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Property</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tenant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($maintenanceRequests)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-wrench text-4xl mb-4 text-gray-300 dark:text-gray-600"></i>
                        <p class="text-lg font-medium">No maintenance requests found</p>
                        <p class="text-sm mt-1">Create your first maintenance request to get started</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($maintenanceRequests as $request): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            #<?php echo $request['id']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <?php echo $request['property']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <?php echo $request['tenant']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <?php echo $request['category']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $priorityColors = [
                                'low' => 'gray',
                                'medium' => 'yellow', 
                                'high' => 'orange',
                                'urgent' => 'red'
                            ];
                            $color = $priorityColors[$request['priority']] ?? 'gray';
                            echo UIComponents::badge(ucfirst($request['priority']), $color);
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $statusColors = [
                                'pending' => 'yellow',
                                'in_progress' => 'blue',
                                'completed' => 'green',
                                'cancelled' => 'gray'
                            ];
                            $color = $statusColors[$request['status']] ?? 'gray';
                            echo UIComponents::badge(ucfirst(str_replace('_', ' ', $request['status'])), $color);
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <?php echo date('M j, Y', strtotime($request['date'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="/admin/maintenance/<?php echo $request['id']; ?>/edit" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteMaintenance(<?php echo $request['id']; ?>)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
            </div>
            <div class="mt-4 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delete Maintenance Request</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete this maintenance request? This action cannot be undone.
                    </p>
                </div>
                <div class="flex justify-center space-x-4 mt-4">
                    <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                        Cancel
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteMaintenance(id) {
    document.getElementById('deleteForm').action = '/admin/maintenance/' + id + '/delete';
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>

<?php
$content = ob_get_clean();

// Set content for layout (anti-scattering compliant)
\ViewManager::set('content', $content);

// Include the dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
