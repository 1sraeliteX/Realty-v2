<?php
// Get data from ViewManager (anti-scattering compliant)
$requests = ViewManager::get('requests', []);
$pagination = ViewManager::get('pagination', []);
$stats = ViewManager::get('stats', []);
$properties = ViewManager::get('properties', []);
$filters = ViewManager::get('filters', []);
?>
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        $stats = ViewManager::get('stats', [
            'total_requests' => 0,
            'urgent_count' => 0,
            'pending_count' => 0,
            'in_progress_count' => 0
        ]);
        ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-tools text-2xl text-orange-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Requests</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['total_requests'] ?? 0); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Urgent</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['urgent_count'] ?? 0); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-2xl text-yellow-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['pending_count'] ?? 0); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-cog text-2xl text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">In Progress</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['in_progress_count'] ?? 0); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Header and Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Maintenance Requests</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track and manage maintenance requests</p>
            </div>
            <a href="/admin/maintenance/create"
               class="inline-flex items-center px-4 py-2 bg-primary-600
                      text-white rounded-lg hover:bg-primary-700
                      text-sm font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>New Request
            </a>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" 
                       placeholder="Search requests..." 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <option value="">All Priorities</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
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
        </div>
    </div>

    <!-- Maintenance Requests Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <?php if (empty($requests)): ?>
            <div class="text-center py-12">
                <i class="fas fa-tools text-4xl text-gray-300 dark:text-gray-600 mb-3 block"></i>
                <p class="text-gray-500 dark:text-gray-400">
                    No maintenance requests yet.
                </p>
                <a href="/admin/maintenance/create"
                   class="mt-3 inline-block text-primary-600 hover:underline text-sm">
                      Create first request
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <?php echo count($requests); ?> request(s) found.
                    </p>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($requests as $request): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($request['title'] ?? '—'); ?>
                                </div>
                                <?php if (!empty($request['description'])): ?>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                        <?php echo htmlspecialchars(substr($request['description'], 0, 60)) . '...'; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $priority = $request['priority'] ?? 'low';
                                $priorityClass = match($priority) {
                                    'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'high'   => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                    'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    default  => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                };
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $priorityClass; ?>">
                                    <?php echo ucfirst($priority); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $status = $request['status'] ?? 'pending';
                                $statusClass = match($status) {
                                    'completed'   => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'cancelled'   => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    default       => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                };
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusClass; ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $status)); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                <?php echo htmlspecialchars($request['property_name'] ?? '—'); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                <?php echo htmlspecialchars($request['tenant_name'] ?? '—'); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                <?php echo isset($request['created_at']) 
                                    ? date('M d, Y', strtotime($request['created_at'])) 
                                    : '—'; ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-3">
                                    <a href="/admin/maintenance/<?php echo $request['id']; ?>"
                                       class="text-primary-600 hover:text-primary-800 dark:text-primary-400 transition-colors" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin/maintenance/<?php echo $request['id']; ?>/edit"
                                       class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 transition-colors" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteMaintenance(<?php echo $request['id']; ?>)"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 transition-colors" 
                                            title="Delete">
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
            <?php if (!empty($pagination) && $pagination['last_page'] > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing 
                        <span class="font-medium"><?php echo (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?></span>
                        to 
                        <span class="font-medium"><?php echo min($pagination['current_page'] * $pagination['per_page'], $pagination['total']); ?></span>
                        of 
                        <span class="font-medium"><?php echo $pagination['total']; ?></span>
                        results
                    </div>
                    <div class="flex gap-2">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <a href="?page=<?php echo $pagination['current_page'] - 1; ?>" 
                               class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['last_page'], $pagination['current_page'] + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?>" 
                               class="px-3 py-1 text-sm border <?php echo $i == $pagination['current_page'] ? 'bg-primary-600 text-white border-primary-600' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'; ?> rounded transition-colors">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                            <a href="?page=<?php echo $pagination['current_page'] + 1; ?>" 
                               class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Delete maintenance request
function deleteMaintenance(id) {
    if (confirm('Are you sure you want to delete this maintenance request?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/maintenance/' + id + '/delete';
        document.body.appendChild(form);
        form.submit();
    }
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filters = document.querySelectorAll('select, input[type="text"]');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            const url = new URL(window.location);
            if (this.name && this.value) {
                url.searchParams.set(this.name, this.value);
            } else {
                url.searchParams.delete(this.name);
            }
            window.location.href = url.toString();
        });
    });
});
</script>
