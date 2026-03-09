<?php
// Include UI Components
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'Tenant Details';
$pageTitle = 'Tenant Information';

$content = ob_start();
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
                <a href="/admin/tenants/<?php echo $_GET['id'] ?? '1'; ?>/edit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
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
                        <p class="text-gray-900 dark:text-white">John Doe</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="text-gray-900 dark:text-white">john.doe@example.com</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                        <p class="text-gray-900 dark:text-white">(555) 123-4567</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Active
                        </span>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Address</h2>
                <div class="space-y-2">
                    <p class="text-gray-900 dark:text-white">123 Main Street, Apt 4B</p>
                    <p class="text-gray-900 dark:text-white">New York, NY 10001</p>
                </div>
            </div>

            <!-- Lease Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lease Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Property</label>
                        <p class="text-gray-900 dark:text-white">Sunset Apartments - Unit 101</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Rent</label>
                        <p class="text-gray-900 dark:text-white">$1,500.00</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease Start</label>
                        <p class="text-gray-900 dark:text-white">January 1, 2024</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Lease End</label>
                        <p class="text-gray-900 dark:text-white">December 31, 2024</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="/admin/payments/create?tenant_id=1" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        Record Payment
                    </a>
                    <a href="/admin/maintenance/create?tenant_id=1" class="block w-full text-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-tools mr-2"></i>
                        Create Maintenance Request
                    </a>
                    <a href="/admin/communications/create?tenant_id=1" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Message
                    </a>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact</h3>
                <div class="space-y-2">
                    <p class="text-gray-900 dark:text-white">Jane Doe</p>
                    <p class="text-gray-900 dark:text-white">(555) 987-6543</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include the admin dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
