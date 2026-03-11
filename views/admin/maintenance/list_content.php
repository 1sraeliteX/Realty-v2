<!-- Header with Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Maintenance</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Track and manage maintenance requests</p>
    </div>
    <div class="mt-4 sm:mt-0 flex space-x-3">
        <button onclick="exportMaintenance()" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-download mr-2"></i>
            Export
        </button>
        <a href="/admin/maintenance/create" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            <i class="fas fa-plus mr-2"></i>
            Create Request
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                <i class="fas fa-tools text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Requests</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $maintenanceStats['total'] ?? 24 ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $maintenanceStats['pending'] ?? 8 ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                <i class="fas fa-spinner text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Progress</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $maintenanceStats['in_progress'] ?? 5 ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $maintenanceStats['completed'] ?? 11 ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Search -->
        <div class="md:col-span-2">
            <?php echo UIComponents::searchBar('Search requests...', '', 'searchMaintenance(this.value)'); ?>
        </div>
        
        <!-- Status Filter -->
        <?php 
        echo UIComponents::select(
            'status_filter',
            'Status',
            [
                '' => 'All Status',
                'pending' => 'Pending',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled'
            ],
            '',
            false,
            'col-span-1'
        ); ?>
        
        <!-- Priority Filter -->
        <?php 
        echo UIComponents::select(
            'priority_filter',
            'Priority',
            [
                '' => 'All Priority',
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
                'urgent' => 'Urgent'
            ],
            '',
            false,
            'col-span-1'
        ); ?>
    </div>
</div>

    <!-- Maintenance Requests Table -->
<?php if (empty($maintenanceRequests)): ?>
<!-- Empty State -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
    <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
        <i class="fas fa-wrench text-gray-400 dark:text-gray-500 text-2xl"></i>
    </div>
    <h3 class="text-xl font-semibold text-white mb-2">No maintenance requests</h3>
    <p class="text-gray-400 mb-6">Create your first maintenance request</p>
    <a href="/admin/maintenance/create" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200">
        <i class="fas fa-plus mr-2"></i>
        New Request
    </a>
</div>
<?php else: ?>
<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tenant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Property</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Issue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($maintenanceRequests as $request): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#<?= str_pad($request['id'], 3, '0', STR_PAD_LEFT) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($request['tenant_name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($request['property_name']) ?> - <?= htmlspecialchars($request['unit_number']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($request['issue']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $priorityColor = $request['priority'] === 'high' ? 'danger' : 
                                       ($request['priority'] === 'medium' ? 'warning' : 'success');
                        echo UIComponents::badge(ucfirst($request['priority']), $priorityColor, 'small'); 
                        ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $statusColor = $request['status'] === 'pending' ? 'warning' : 
                                      ($request['status'] === 'in_progress' ? 'info' : 'success');
                        echo UIComponents::badge(ucfirst(str_replace('_', ' ', $request['status'])), $statusColor, 'small'); 
                        ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="/admin/maintenance/<?= $request['id'] ?>" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/maintenance/<?= $request['id'] ?>/edit" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteMaintenance(<?= $request['id'] ?>)" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

                                                                                    
