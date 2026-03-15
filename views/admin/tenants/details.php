<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$tenant = ViewManager::get('tenant') ?? DataProvider::get('tenant');
$payment_history = ViewManager::get('payment_history') ?? DataProvider::get('payment_history');
$maintenanceRequests = ViewManager::get('maintenance_requests') ?? DataProvider::get('maintenance_requests');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Tenant Details');
ViewManager::set('pageTitle', 'Tenant Information');

ob_start();
?>

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tenant Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View and manage tenant information</p>
            </div>
            <div class="flex space-x-3">
                <a href="/admin/tenants" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Tenants
                </a>
                <a href="/admin/tenants/<?php echo $tenant['id']; ?>/edit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Tenant
                </a>
            </div>
        </div>
    </div>

    <!-- Tenant Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['email']); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['phone']); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <?php 
                        $statusColor = $tenant['lease_status'] === 'active' ? 'success' : 
                                     ($tenant['lease_status'] === 'expiring' ? 'warning' : 'danger');
                        echo UIComponents::badge(ucfirst($tenant['lease_status']), $statusColor, 'small'); 
                        ?>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Address</h2>
                <div class="space-y-2">
                    <?php if (isset($tenant['address'])): ?>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['address']); ?></p>
                        <p class="text-gray-900 dark:text-white">
                            <?php echo htmlspecialchars($tenant['city'] . ', ' . $tenant['state'] . ' ' . $tenant['zip_code']); ?>
                        </p>
                    <?php else: ?>
                        <p class="text-gray-500 dark:text-gray-400">No address on file</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Lease Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lease Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Property</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['property_name']); ?> - Unit <?php echo htmlspecialchars($tenant['unit_number']); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Rent</label>
                        <p class="text-gray-900 dark:text-white">$<?php echo number_format($tenant['rent_amount'], 2); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease Start</label>
                        <p class="text-gray-900 dark:text-white"><?php echo date('F j, Y', strtotime($tenant['lease_start'])); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease End</label>
                        <p class="text-gray-900 dark:text-white"><?php echo date('F j, Y', strtotime($tenant['lease_end'])); ?></p>
                    </div>
                </div>
                
                <!-- Rent Duration Progress Bar -->
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Lease Progress</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <?php 
                            $daysRemaining = (strtotime($tenant['lease_end']) - strtotime(date('Y-m-d'))) / 86400;
                            $totalDays = (strtotime($tenant['lease_end']) - strtotime($tenant['lease_start'])) / 86400;
                            $daysPassed = $totalDays - $daysRemaining;
                            $progressPercent = $totalDays > 0 ? max(0, min(100, ($daysPassed / $totalDays) * 100)) : 0;
                            echo max(0, floor($daysRemaining)) . ' days remaining';
                            ?>
                        </p>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-300 <?php 
                            if ($progressPercent >= 90) echo 'bg-red-500';
                            elseif ($progressPercent >= 75) echo 'bg-yellow-500';
                            elseif ($progressPercent >= 50) echo 'bg-blue-500';
                            else echo 'bg-green-500';
                        ?>" style="width: <?php echo $progressPercent; ?>%"></div>
                    </div>
                    <div class="flex justify-between mt-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y', strtotime($tenant['lease_start'])); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y', strtotime($tenant['lease_end'])); ?></p>
                    </div>
                    <div class="mt-2">
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            <?php 
                            if ($daysRemaining < 0) {
                                echo '<span class="text-red-600 font-medium">Lease Expired</span>';
                            } elseif ($daysRemaining <= 30) {
                                echo '<span class="text-yellow-600 font-medium">Lease Expiring Soon</span>';
                            } elseif ($daysRemaining <= 90) {
                                echo '<span class="text-blue-600 font-medium">Lease Renewal Period</span>';
                            } else {
                                echo '<span class="text-green-600 font-medium">Lease Active</span>';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment History</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($payment_history as $payment): ?>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <?php echo date('M j, Y', strtotime($payment['date'])); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        $<?php echo number_format($payment['amount'], 2); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($payment['method']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <?php 
                                        $color = $payment['status'] === 'paid' ? 'success' : 'warning';
                                        echo UIComponents::badge(ucfirst($payment['status']), $color, 'small'); 
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Maintenance Requests -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Maintenance Requests</h2>
                <div class="space-y-3">
                    <?php foreach ($maintenanceRequests as $request): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($request['type']); ?></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($request['description']); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y', strtotime($request['date'])); ?></p>
                            </div>
                            <?php 
                            $color = $request['status'] === 'completed' ? 'success' : 
                                    ($request['status'] === 'pending' ? 'warning' : 'info');
                            echo UIComponents::badge(ucfirst($request['status']), $color, 'small'); 
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="/admin/payments/create?tenant_id=<?php echo $tenant['id']; ?>" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        Record Payment
                    </a>
                    <a href="/admin/maintenance/create?tenant_id=<?php echo $tenant['id']; ?>" class="block w-full text-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-tools mr-2"></i>
                        Create Maintenance Request
                    </a>
                    <a href="/admin/communications/create?tenant_id=<?php echo $tenant['id']; ?>" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Message
                    </a>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact</h3>
                <div class="space-y-2">
                    <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['emergency_contact_name'] ?? 'Not provided'); ?></p>
                    <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['emergency_contact_phone'] ?? 'Not provided'); ?></p>
                </div>
            </div>

            <!-- Next of Kin -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Next of Kin</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['next_of_kin'] ?? 'Not provided'); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['next_of_kin_phone'] ?? 'Not provided'); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Relationship</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['next_of_kin_relationship'] ?? 'Not specified'); ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['next_of_kin_email'] ?? 'Not provided'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Profile Picture -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Picture</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-20 h-20 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($tenant['profile_picture'])): ?>
                                <img src="/uploads/tenants/<?php echo htmlspecialchars($tenant['profile_picture']); ?>" alt="Profile" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-user text-2xl text-gray-400"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <button onclick="document.getElementById('profilePictureUpload').click()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                                <i class="fas fa-camera mr-2"></i>Change Photo
                            </button>
                            <input type="file" id="profilePictureUpload" class="hidden" accept="image/*" onchange="uploadProfilePicture(this)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ID Documents -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Proof of ID</h3>
                <div class="space-y-4">
                    <?php if (!empty($tenant['id_document'])): ?>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-file-lines text-2xl text-blue-600"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($tenant['id_document']); ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">ID Document</p>
                                    </div>
                                </div>
                                <a href="/uploads/tenants/<?php echo htmlspecialchars($tenant['id_document']); ?>" target="_blank" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">No ID document uploaded</p>
                            <button onclick="document.getElementById('idDocumentUpload').click()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                                <i class="fas fa-upload mr-2"></i>Upload ID
                            </button>
                            <input type="file" id="idDocumentUpload" class="hidden" accept="image/*,.pdf" onchange="uploadIdDocument(this)">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Profile picture upload
function uploadProfilePicture(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showToast('Please select an image file', 'error');
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5242880) {
            showToast('File size must be less than 5MB', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('profile_picture', file);
        formData.append('tenant_id', <?php echo $tenant['id']; ?>);
        
        fetch('/admin/tenants/upload-profile-picture', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Profile picture updated successfully', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Upload failed', 'error');
            }
        })
        .catch(error => {
            showToast('Upload failed. Please try again.', 'error');
        });
    }
}

// ID document upload
function uploadIdDocument(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        if (!file.type.startsWith('image/') && file.type !== 'application/pdf') {
            showToast('Please select an image or PDF file', 'error');
            return;
        }
        
        // Validate file size (10MB)
        if (file.size > 10485760) {
            showToast('File size must be less than 10MB', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('id_document', file);
        formData.append('tenant_id', <?php echo $tenant['id']; ?>);
        
        fetch('/admin/tenants/upload-id-document', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('ID document uploaded successfully', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Upload failed', 'error');
            }
        })
        .catch(error => {
            showToast('Upload failed. Please try again.', 'error');
        });
    }
}

// Toast notification helper
function showToast(message, type = 'info') {
    // Create toast element if it doesn't exist
    if (!document.getElementById('toast-container')) {
        const toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }
    
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' : 
                    type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
    
    toast.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    toast.textContent = message;
    
    document.getElementById('toast-container').appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<?php
$content = ob_get_clean();

// Use ViewManager for rendering (anti-scattering compliant)
ViewManager::set('content', $content);
echo ViewManager::render('admin.dashboard_layout');
?>
