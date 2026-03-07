<?php
$title = 'Admin Management';
$pageTitle = 'Admin Management';
$content = ob_start();
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage platform administrators and their permissions</p>
    </div>
    <button onclick="openCreateAdminModal()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-plus mr-2"></i>Add New Admin
    </button>
</div>

<!-- Admins Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">All Administrators</h3>
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <input type="text" placeholder="Search admins..." class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <select class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Admin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Business</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($admins)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-user-shield text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">No administrators found</p>
                            <button onclick="openCreateAdminModal()" class="mt-3 text-primary-600 hover:text-primary-500 dark:text-primary-400">
                                Add your first administrator →
                            </button>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($admins as $adminItem): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center">
                                            <span class="text-white font-medium"><?php echo strtoupper(substr($adminItem['name'], 0, 1)); ?></span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($adminItem['name']); ?></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">ID: <?php echo substr($adminItem['id'], 0, 8); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($adminItem['email']); ?></div>
                                <?php if ($adminItem['phone']): ?>
                                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($adminItem['phone']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($adminItem['business_name']): ?>
                                    <div class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($adminItem['business_name']); ?></div>
                                <?php else: ?>
                                    <span class="text-sm text-gray-400 dark:text-gray-500">Not specified</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $adminItem['role'] === 'super_admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'; ?>">
                                    <?php echo ucfirst($adminItem['role']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?php echo date('M j, Y', strtotime($adminItem['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="editAdmin('<?php echo $adminItem['id']; ?>')" class="text-primary-600 hover:text-primary-500 dark:text-primary-400 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($adminItem['id'] !== $admin['id']): ?>
                                    <button onclick="deleteAdmin('<?php echo $adminItem['id']; ?>', '<?php echo htmlspecialchars($adminItem['name']); ?>')" class="text-red-600 hover:text-red-500 dark:text-red-400">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400 dark:text-gray-600">
                                        <i class="fas fa-shield-alt"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Admin Modal -->
<div id="adminModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modalTitle">Add New Admin</h3>
            <button onclick="closeAdminModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="adminForm" onsubmit="saveAdmin(event)">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                <input type="text" id="adminName" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                <input type="email" id="adminEmail" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password *</label>
                <input type="password" id="adminPassword" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                <input type="tel" id="adminPhone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Business Name</label>
                <input type="text" id="adminBusiness" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role *</label>
                <select id="adminRole" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAdminModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Save Admin
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'superadmin_layout.php';
?>

<script>
let currentEditingAdmin = null;

function openCreateAdminModal() {
    currentEditingAdmin = null;
    document.getElementById('modalTitle').textContent = 'Add New Admin';
    document.getElementById('adminForm').reset();
    document.getElementById('adminModal').classList.remove('hidden');
}

function editAdmin(adminId) {
    currentEditingAdmin = adminId;
    document.getElementById('modalTitle').textContent = 'Edit Admin';
    // Load admin data into form
    // This would typically make an API call to get admin data
    document.getElementById('adminModal').classList.remove('hidden');
}

function closeAdminModal() {
    document.getElementById('adminModal').classList.add('hidden');
    document.getElementById('adminForm').reset();
    currentEditingAdmin = null;
}

function saveAdmin(event) {
    event.preventDefault();
    
    const formData = {
        name: document.getElementById('adminName').value,
        email: document.getElementById('adminEmail').value,
        password: document.getElementById('adminPassword').value,
        phone: document.getElementById('adminPhone').value,
        business_name: document.getElementById('adminBusiness').value,
        role: document.getElementById('adminRole').value
    };
    
    // Here you would make an API call to save the admin
    console.log('Saving admin:', formData);
    
    showToast('Admin saved successfully', 'success');
    closeAdminModal();
    // Reload the page to show the updated list
    setTimeout(() => window.location.reload(), 1000);
}

function deleteAdmin(adminId, adminName) {
    if (confirm(`Are you sure you want to delete admin "${adminName}"? This action cannot be undone.`)) {
        // Here you would make an API call to delete the admin
        console.log('Deleting admin:', adminId);
        showToast('Admin deleted successfully', 'success');
        setTimeout(() => window.location.reload(), 1000);
    }
}
</script>
