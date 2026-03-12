<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Set page title
ViewManager::set('title', 'Profile');

// Get user data from ViewManager (anti-scattering compliant)
$user = ViewManager::get('user');
if (!$user) {
    $user = [
        'name' => 'Admin User',
        'email' => 'admin@cornerstonerealty.com',
        'phone_number' => '(555) 123-4567',
        'company' => 'Cornerstone Realty',
        'role_position' => 'Property Manager',
        'address' => '123 Main St, Anytown, USA',
        'bio' => 'Property management professional with over 10 years of experience in residential and commercial real estate.'
    ];
    ViewManager::set('user', $user);
}

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
                        Profile
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Picture Section -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Picture</h2>
                <div class="text-center">
                    <div class="relative inline-block">
                        <div class="w-32 h-32 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto">
                            <span class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-center space-x-3 mt-4">
                        <button onclick="takePhoto()" class="w-10 h-10 bg-primary-600 hover:bg-primary-700 text-white rounded-full flex items-center justify-center transition-colors duration-200 shadow-lg">
                            <i class="fas fa-camera text-sm"></i>
                        </button>
                        <button onclick="uploadPhoto()" class="w-10 h-10 bg-gray-600 hover:bg-gray-700 text-white rounded-full flex items-center justify-center transition-colors duration-200 shadow-lg">
                            <i class="fas fa-upload text-sm"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">
                        Click camera icon to take a photo or upload icon to choose from device
                    </p>
                </div>
            </div>
        </div>

        <!-- Personal Information Section -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                <form id="profileForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['name']); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                            <input type="tel" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company</label>
                            <input type="text" name="company" value="<?php echo htmlspecialchars($user['company'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role/Position</label>
                            <input type="text" name="role_position" value="<?php echo htmlspecialchars($user['role_position'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bio</label>
                        <textarea name="bio" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Account Statistics Section -->
    <div class="mt-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo ViewManager::get('propertiesManaged'); ?></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Properties Managed</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-friends text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo ViewManager::get('activeTenants'); ?></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Active Tenants</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo ViewManager::get('monthlyRevenue'); ?></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Monthly Revenue</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo ViewManager::get('pendingPayments'); ?></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pending Payments</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex justify-end space-x-3">
        <button onclick="handleLogout()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 flex items-center">
            <i class="fas fa-right-from-bracket mr-2"></i>
            Logout
        </button>
        <button onclick="saveProfile()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors duration-200 flex items-center">
            <i class="fas fa-save mr-2"></i>
            Save Changes
        </button>
    </div>
</div>

<script>
function handleLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '/admin/logout';
    }
}

function saveProfile() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);
    
    // Show loading state
    showToast('Saving profile...', 'info');
    
    // Simulate save (in real app, this would be an API call)
    setTimeout(() => {
        showToast('Profile saved successfully!', 'success');
    }, 1000);
}

function takePhoto() {
    showToast('Camera feature coming soon!', 'info');
}

function uploadPhoto() {
    showToast('File upload feature coming soon!', 'info');
}
</script>

<?php
// Capture the content and set it for the layout
$content = ob_get_clean();
ViewManager::set('content', $content);

// Include the dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
