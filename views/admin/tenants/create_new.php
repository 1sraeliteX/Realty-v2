<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Add New Tenant');
ViewManager::set('user', [
    'name' => 'Admin User',
    'email' => 'admin@cornerstone.com',
    'avatar' => null
]);
ViewManager::set('notifications', []);

// Mock data for form (would come from DataProvider in production)
$properties = DataProvider::get('properties', [
    ['id' => 1, 'name' => 'Sunset Apartments'],
    ['id' => 2, 'name' => 'Ocean View Condos'],
    ['id' => 3, 'name' => 'Mountain Heights']
]);

$units = DataProvider::get('units', [
    ['id' => 1, 'property_id' => 1, 'number' => 'A-101'],
    ['id' => 2, 'property_id' => 1, 'number' => 'A-102'],
    ['id' => 3, 'property_id' => 2, 'number' => 'B-201'],
    ['id' => 4, 'property_id' => 2, 'number' => 'B-202'],
    ['id' => 5, 'property_id' => 3, 'number' => 'C-301']
]);

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
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="/admin/tenants" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 md:ml-2">
                        Tenants
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">
                        Add New Tenant
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Progress Indicator -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center">
            <div id="step1-indicator" class="w-10 h-10 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300">1</div>
            <div class="ml-3">
                <h3 id="step1-title" class="text-sm font-medium text-gray-900 dark:text-white transition-all duration-300">Basic Info</h3>
                <p id="step1-desc" class="text-xs text-gray-500 dark:text-gray-400 transition-all duration-300">Personal details</p>
            </div>
        </div>
        <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 mx-4">
            <div id="progress-bar" class="h-full bg-primary-600 transition-all duration-500" style="width: 0%"></div>
        </div>
        <div class="flex items-center">
            <div id="step2-indicator" class="w-10 h-10 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300">2</div>
            <div class="ml-3">
                <h3 id="step2-title" class="text-sm font-medium text-gray-500 dark:text-gray-400 transition-all duration-300">Identity</h3>
                <p id="step2-desc" class="text-xs text-gray-400 dark:text-gray-500 transition-all duration-300">ID & documents</p>
            </div>
        </div>
        <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 mx-4">
            <div id="progress-bar-2" class="h-full bg-gray-300 dark:bg-gray-600 transition-all duration-500"></div>
        </div>
        <div class="flex items-center">
            <div id="step3-indicator" class="w-10 h-10 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300">3</div>
            <div class="ml-3">
                <h3 id="step3-title" class="text-sm font-medium text-gray-500 dark:text-gray-400 transition-all duration-300">Emergency</h3>
                <p id="step3-desc" class="text-xs text-gray-400 dark:text-gray-500 transition-all duration-300">Contact person</p>
            </div>
        </div>
        <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 mx-4">
            <div id="progress-bar-3" class="h-full bg-gray-300 dark:bg-gray-600 transition-all duration-500"></div>
        </div>
        <div class="flex items-center">
            <div id="step4-indicator" class="w-10 h-10 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300">4</div>
            <div class="ml-3">
                <h3 id="step4-title" class="text-sm font-medium text-gray-500 dark:text-gray-400 transition-all duration-300">Lease</h3>
                <p id="step4-desc" class="text-xs text-gray-400 dark:text-gray-500 transition-all duration-300">Rent details</p>
            </div>
        </div>
        <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 mx-4">
            <div id="progress-bar-4" class="h-full bg-gray-300 dark:bg-gray-600 transition-all duration-500"></div>
        </div>
        <div class="flex items-center">
            <div id="step5-indicator" class="w-10 h-10 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300">5</div>
            <div class="ml-3">
                <h3 id="step5-title" class="text-sm font-medium text-gray-500 dark:text-gray-400 transition-all duration-300">Review</h3>
                <p id="step5-desc" class="text-xs text-gray-400 dark:text-gray-500 transition-all duration-300">Confirm & submit</p>
            </div>
        </div>
    </div>
</div>

<!-- Error Banner (hidden by default) -->
<div id="error-banner" class="hidden mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-red-400"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Please complete required fields</h3>
            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                <p id="error-message">Fill in all required fields before proceeding.</p>
            </div>
        </div>
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button onclick="hideErrorBanner()" class="inline-flex bg-red-50 dark:bg-red-900/20 rounded-md p-1.5 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form Container -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
    <form id="tenantForm" onsubmit="submitTenantForm(event)">
        <!-- Step 1: Basic Information -->
        <div id="step1" class="step-content p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Basic Information</h2>
                <p class="text-gray-600 dark:text-gray-400">Enter the tenant's personal and contact details</p>
            </div>

            <!-- Tenant Type Toggle -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Tenant Type *</label>
                <div class="flex space-x-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="tenant_type" value="individual" checked class="sr-only peer">
                        <div class="px-4 py-2 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg peer-checked:border-primary-600 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 transition-all duration-200">
                            <i class="fas fa-user mr-2 text-primary-600"></i>
                            <span class="font-medium text-gray-900 dark:text-white">Individual</span>
                        </div>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="tenant_type" value="business" class="sr-only peer">
                        <div class="px-4 py-2 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg peer-checked:border-primary-600 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 transition-all duration-200">
                            <i class="fas fa-building mr-2 text-primary-600"></i>
                            <span class="font-medium text-gray-900 dark:text-white">Business</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div id="first-name-field">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                    <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
                
                <div id="last-name-field">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                    <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div id="company-name-field" class="hidden md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name *</label>
                    <input type="text" name="company_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                    <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alternate Phone</label>
                    <input type="tel" name="alternate_phone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="button" onclick="nextStep(2)" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                    Next Step <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 2: Identity -->
        <div id="step2" class="step-content hidden p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Identity Verification</h2>
                <p class="text-gray-600 dark:text-gray-400">Upload identification documents and verify identity</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Type *</label>
                    <select name="id_type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="">Select ID Type</option>
                        <option value="nin">National ID Number</option>
                        <option value="passport">Passport</option>
                        <option value="driver_license">Driver's License</option>
                        <option value="voter_card">Voter's Card</option>
                        <option value="residence_permit">Residence Permit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Number *</label>
                    <input type="text" name="id_number" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nationality</label>
                    <input type="text" name="nationality" placeholder="e.g., Nigerian, American" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Occupation</label>
                    <input type="text" name="occupation" placeholder="e.g., Software Engineer, Teacher" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
            </div>

            <!-- File Upload -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Document Upload</label>
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-primary-500 transition-colors" id="upload-area">
                    <input type="file" id="id-document" name="id_document" class="hidden" accept="image/*,.pdf" onchange="handleFileSelect(event)">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">Click to upload or drag and drop</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">PNG, JPG, PDF up to 10MB</p>
                    <button type="button" onclick="document.getElementById('id-document').click()" class="mt-3 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Choose File
                    </button>
                </div>
                <div id="file-preview" class="mt-3 hidden">
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <div class="flex items-center">
                            <i class="fas fa-file text-primary-600 mr-3"></i>
                            <span id="file-name" class="text-sm text-gray-700 dark:text-gray-300"></span>
                        </div>
                        <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                <textarea name="identity_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-none" placeholder="Additional notes about identity verification..."></textarea>
            </div>
            
            <div class="mt-8 flex justify-between">
                <button type="button" onclick="previousStep(1)" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <button type="button" onclick="nextStep(3)" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                    Next Step <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 3: Emergency Contact -->
        <div id="step3" class="step-content hidden p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Emergency Contact</h2>
                <p class="text-gray-600 dark:text-gray-400">Provide contact information for emergency situations</p>
            </div>

            <!-- Info Callout -->
            <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Emergency Contact Purpose</h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <p>This person will be contacted in case of emergencies or if we cannot reach the primary tenant.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Name *</label>
                    <input type="text" name="emergency_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                    <input type="tel" name="emergency_phone" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Relationship *</label>
                    <select name="emergency_relationship" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="">Select Relationship</option>
                        <option value="spouse">Spouse</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Sibling</option>
                        <option value="child">Child</option>
                        <option value="relative">Relative</option>
                        <option value="friend">Friend</option>
                        <option value="colleague">Colleague</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" name="emergency_email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Notes</label>
                <textarea name="emergency_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-none" placeholder="Any additional information about the emergency contact..."></textarea>
            </div>
            
            <div class="mt-8 flex justify-between">
                <button type="button" onclick="previousStep(2)" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <button type="button" onclick="nextStep(4)" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                    Next Step <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 4: Lease Details -->
        <div id="step4" class="step-content hidden p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Lease Details</h2>
                <p class="text-gray-600 dark:text-gray-400">Configure rental agreement and payment information</p>
            </div>

            <!-- Attach Lease Toggle -->
            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="attach_lease" id="attach-lease-toggle" class="sr-only peer" onchange="toggleLeaseFields()">
                    <div class="relative">
                        <div class="block bg-gray-200 dark:bg-gray-700 w-14 h-8 rounded-full peer-checked:bg-primary-600 transition-colors"></div>
                        <div class="absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform peer-checked:translate-x-6"></div>
                    </div>
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Attach lease now</span>
                </label>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enable to set up lease details immediately</p>
            </div>

            <div id="lease-fields" class="space-y-6 opacity-50 pointer-events-none transition-opacity duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property *</label>
                        <select name="property_id" required onchange="updateUnits()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select Property</option>
                            <?php foreach ($properties as $property): ?>
                                <option value="<?php echo $property['id']; ?>"><?php echo htmlspecialchars($property['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit *</label>
                        <select name="unit_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select Unit</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date *</label>
                        <input type="date" name="lease_start" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date *</label>
                        <input type="date" name="lease_end" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rent Amount *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                            <input type="number" name="rent_amount" required min="0" step="0.01" class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Frequency *</label>
                        <select name="payment_frequency" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select Frequency</option>
                            <option value="weekly">Weekly</option>
                            <option value="bi-weekly">Bi-weekly</option>
                            <option value="monthly" selected>Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="annually">Annually</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Security Deposit</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                            <input type="number" name="security_deposit" min="0" step="0.01" class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method *</label>
                        <select name="payment_method" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select Method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-between">
                <button type="button" onclick="previousStep(3)" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <button type="button" onclick="nextStep(5)" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                    Next Step <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Step 5: Review -->
        <div id="step5" class="step-content hidden p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Review & Submit</h2>
                <p class="text-gray-600 dark:text-gray-400">Review all information before creating the tenant</p>
            </div>

            <!-- Summary Card -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-6">
                <div class="flex items-start space-x-4">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div id="avatar-initial" class="w-16 h-16 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 rounded-full flex items-center justify-center text-xl font-semibold">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    
                    <!-- Basic Info -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 id="review-name" class="text-lg font-semibold text-gray-900 dark:text-white">Tenant Name</h3>
                            <span id="review-status" class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-medium rounded-full">Active</span>
                        </div>
                        <div class="space-y-1">
                            <p id="review-email" class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-envelope mr-2"></i>email@example.com
                            </p>
                            <p id="review-phone" class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-phone mr-2"></i>+1 234 567 8900
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Summaries -->
            <div class="space-y-4">
                <!-- Identity Section -->
                <div id="identity-summary" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-id-card mr-2 text-primary-600"></i>Identity Information
                    </h4>
                    <div id="identity-details" class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <!-- Identity details will be populated here -->
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div id="emergency-summary" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-phone-alt mr-2 text-primary-600"></i>Emergency Contact
                    </h4>
                    <div id="emergency-details" class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <!-- Emergency contact details will be populated here -->
                    </div>
                </div>

                <!-- Lease Section -->
                <div id="lease-summary" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hidden">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-file-contract mr-2 text-primary-600"></i>Lease Details
                    </h4>
                    <div id="lease-details" class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <!-- Lease details will be populated here -->
                    </div>
                </div>
            </div>

            <!-- Terms Checkbox -->
            <div class="mt-6">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" name="terms_accepted" required class="mt-1 mr-3">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        I confirm that all information provided is accurate and I have the authority to create this tenant account. I understand that this information will be used for tenant management and communication purposes.
                    </span>
                </label>
            </div>
            
            <div class="mt-8 flex justify-between">
                <button type="button" onclick="previousStep(4)" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-check mr-2"></i> Create Tenant
                </button>
            </div>
        </div>

        <!-- Success Screen (hidden by default) -->
        <div id="success-screen" class="hidden p-6 text-center">
            <div class="mb-6">
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Tenant Created Successfully!</h2>
                <p class="text-gray-600 dark:text-gray-400">The new tenant has been added to your system.</p>
            </div>

            <div class="flex justify-center space-x-4">
                <button type="button" onclick="addAnother()" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i> Add Another
                </button>
                <button type="button" onclick="viewTenants()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-users mr-2"></i> View Tenants
                </button>
            </div>
        </div>
    </form>
</div>

<style>
/* Custom scrollbar for form area */
.step-content {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}

.step-content::-webkit-scrollbar {
    width: 6px;
}

.step-content::-webkit-scrollbar-track {
    background: transparent;
}

.step-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.dark .step-content::-webkit-scrollbar-thumb {
    background: #475569;
}

.step-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .step-content::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
</style>

<script>
// Form state management
let currentStep = 1;
let formData = {};

// Mock units data
const unitsData = <?php echo json_encode($units); ?>;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateProgressIndicator(1);
    setupTenantTypeToggle();
    setupDragAndDrop();
});

// Tenant type toggle
function setupTenantTypeToggle() {
    const tenantTypeRadios = document.querySelectorAll('input[name="tenant_type"]');
    tenantTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const isBusiness = this.value === 'business';
            const firstNameField = document.getElementById('first-name-field');
            const lastNameField = document.getElementById('last-name-field');
            const companyField = document.getElementById('company-name-field');
            
            if (isBusiness) {
                firstNameField.classList.add('hidden');
                lastNameField.classList.add('hidden');
                companyField.classList.remove('hidden');
                document.querySelector('input[name="company_name"]').required = true;
                document.querySelector('input[name="first_name"]').required = false;
                document.querySelector('input[name="last_name"]').required = false;
            } else {
                firstNameField.classList.remove('hidden');
                lastNameField.classList.remove('hidden');
                companyField.classList.add('hidden');
                document.querySelector('input[name="company_name"]').required = false;
                document.querySelector('input[name="first_name"]').required = true;
                document.querySelector('input[name="last_name"]').required = true;
            }
        });
    });
}

// Navigation functions
function nextStep(step) {
    if (!validateStep(currentStep)) {
        return;
    }
    
    // Save current step data
    saveStepData(currentStep);
    
    // Hide current step
    document.getElementById(`step${currentStep}`).classList.add('hidden');
    
    // Show next step
    document.getElementById(`step${step}`).classList.remove('hidden');
    
    // Update progress
    updateProgressIndicator(step);
    
    // If moving to review step, populate summary
    if (step === 5) {
        populateReviewSummary();
    }
    
    currentStep = step;
}

function previousStep(step) {
    // Hide current step
    document.getElementById(`step${currentStep}`).classList.add('hidden');
    
    // Show previous step
    document.getElementById(`step${step}`).classList.remove('hidden');
    
    // Update progress
    updateProgressIndicator(step);
    
    currentStep = step;
}

// Progress indicator
function updateProgressIndicator(step) {
    // Update progress bars
    for (let i = 1; i < 5; i++) {
        const progressBar = document.getElementById(`progress-bar${i === 1 ? '' : '-' + i}`);
        if (progressBar) {
            if (i < step) {
                progressBar.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                progressBar.classList.add('bg-primary-600');
                progressBar.style.width = '100%';
            } else if (i === step) {
                progressBar.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                progressBar.classList.add('bg-primary-600');
                progressBar.style.width = '50%';
            } else {
                progressBar.classList.add('bg-gray-300', 'dark:bg-gray-600');
                progressBar.classList.remove('bg-primary-600');
                progressBar.style.width = '0%';
            }
        }
    }
    
    // Update step indicators
    for (let i = 1; i <= 5; i++) {
        const indicator = document.getElementById(`step${i}-indicator`);
        const title = document.getElementById(`step${i}-title`);
        const desc = document.getElementById(`step${i}-desc`);
        
        if (i < step) {
            // Completed
            indicator.className = 'w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300';
            indicator.innerHTML = '<i class="fas fa-check"></i>';
            title.className = 'text-sm font-medium text-gray-900 dark:text-white transition-all duration-300';
            desc.className = 'text-xs text-gray-500 dark:text-gray-400 transition-all duration-300';
        } else if (i === step) {
            // Current
            indicator.className = 'w-10 h-10 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300';
            indicator.innerHTML = i;
            title.className = 'text-sm font-medium text-gray-900 dark:text-white transition-all duration-300';
            desc.className = 'text-xs text-gray-500 dark:text-gray-400 transition-all duration-300';
        } else {
            // Future
            indicator.className = 'w-10 h-10 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300';
            indicator.innerHTML = i;
            title.className = 'text-sm font-medium text-gray-500 dark:text-gray-400 transition-all duration-300';
            desc.className = 'text-xs text-gray-400 dark:text-gray-500 transition-all duration-300';
        }
    }
}

// Validation
function validateStep(step) {
    const stepElement = document.getElementById(`step${step}`);
    const requiredFields = stepElement.querySelectorAll('[required]');
    
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            field.focus();
            showError(`Please fill in ${field.previousElementSibling.textContent.replace('*', '').trim()}`);
            return false;
        }
    }
    
    // Special validation for step 4
    if (step === 4 && document.getElementById('attach-lease-toggle').checked) {
        const leaseFields = document.querySelectorAll('#lease-fields [required]');
        for (let field of leaseFields) {
            if (!field.value.trim()) {
                field.focus();
                showError(`Please fill in ${field.previousElementSibling.textContent.replace('*', '').trim()}`);
                return false;
            }
        }
    }
    
    return true;
}

// Error handling
function showError(message) {
    document.getElementById('error-message').textContent = message;
    document.getElementById('error-banner').classList.remove('hidden');
    setTimeout(() => {
        hideErrorBanner();
    }, 5000);
}

function hideErrorBanner() {
    document.getElementById('error-banner').classList.add('hidden');
}

// Data management
function saveStepData(step) {
    const stepElement = document.getElementById(`step${step}`);
    const inputs = stepElement.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (input.type === 'checkbox') {
            formData[input.name] = input.checked;
        } else if (input.type === 'radio') {
            if (input.checked) {
                formData[input.name] = input.value;
            }
        } else {
            formData[input.name] = input.value;
        }
    });
}

// Lease fields toggle
function toggleLeaseFields() {
    const isChecked = document.getElementById('attach-lease-toggle').checked;
    const leaseFields = document.getElementById('lease-fields');
    
    if (isChecked) {
        leaseFields.classList.remove('opacity-50', 'pointer-events-none');
        // Make lease fields required
        leaseFields.querySelectorAll('[name]').forEach(field => {
            if (field.name !== 'security_deposit') {
                field.required = true;
            }
        });
    } else {
        leaseFields.classList.add('opacity-50', 'pointer-events-none');
        // Remove required from lease fields
        leaseFields.querySelectorAll('[name]').forEach(field => {
            field.required = false;
        });
    }
}

// Unit update
function updateUnits() {
    const propertyId = document.querySelector('select[name="property_id"]').value;
    const unitSelect = document.querySelector('select[name="unit_id"]');
    
    // Clear current options
    unitSelect.innerHTML = '<option value="">Select Unit</option>';
    
    if (propertyId) {
        // Filter units by property
        const filteredUnits = unitsData.filter(unit => unit.property_id == propertyId);
        
        filteredUnits.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit.id;
            option.textContent = unit.number;
            unitSelect.appendChild(option);
        });
    }
}

// File handling
function setupDragAndDrop() {
    const uploadArea = document.getElementById('upload-area');
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
    });
    
    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect({ target: { files } });
        }
    });
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        // Check file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            showError('File size must be less than 10MB');
            return;
        }
        
        // Check file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            showError('Only images and PDF files are allowed');
            return;
        }
        
        // Show file preview
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-preview').classList.remove('hidden');
    }
}

function removeFile() {
    document.getElementById('id-document').value = '';
    document.getElementById('file-preview').classList.add('hidden');
}

// Review summary
function populateReviewSummary() {
    // Basic info
    const tenantType = formData.tenant_type || 'individual';
    let name = '';
    if (tenantType === 'business') {
        name = formData.company_name || 'Company Name';
    } else {
        name = `${formData.first_name || 'First'} ${formData.last_name || 'Last'}`;
    }
    
    document.getElementById('review-name').textContent = name;
    document.getElementById('review-email').innerHTML = `<i class="fas fa-envelope mr-2"></i>${formData.email || 'N/A'}`;
    document.getElementById('review-phone').innerHTML = `<i class="fas fa-phone mr-2"></i>${formData.phone || 'N/A'}`;
    
    // Status badge
    const statusBadge = document.getElementById('review-status');
    const status = formData.status || 'active';
    const statusColors = {
        active: 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
        inactive: 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200',
        pending: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200'
    };
    statusBadge.className = `px-2 py-1 ${statusColors[status]} text-xs font-medium rounded-full`;
    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    
    // Avatar initial
    const avatarInitial = document.getElementById('avatar-initial');
    if (tenantType === 'business') {
        avatarInitial.innerHTML = '<i class="fas fa-building"></i>';
    } else {
        const initials = `${(formData.first_name || '')[0]}${(formData.last_name || '')[0]}`.toUpperCase();
        avatarInitial.textContent = initials || 'U';
    }
    
    // Identity section
    const identitySummary = document.getElementById('identity-summary');
    const identityDetails = document.getElementById('identity-details');
    if (formData.id_type || formData.id_number) {
        identitySummary.classList.remove('hidden');
        identityDetails.innerHTML = `
            ${formData.id_type ? `<p><strong>ID Type:</strong> ${formData.id_type.replace('_', ' ').toUpperCase()}</p>` : ''}
            ${formData.id_number ? `<p><strong>ID Number:</strong> ${formData.id_number}</p>` : ''}
            ${formData.date_of_birth ? `<p><strong>Date of Birth:</strong> ${formData.date_of_birth}</p>` : ''}
            ${formData.nationality ? `<p><strong>Nationality:</strong> ${formData.nationality}</p>` : ''}
            ${formData.occupation ? `<p><strong>Occupation:</strong> ${formData.occupation}</p>` : ''}
        `;
    } else {
        identitySummary.classList.add('hidden');
    }
    
    // Emergency contact section
    const emergencySummary = document.getElementById('emergency-summary');
    const emergencyDetails = document.getElementById('emergency-details');
    if (formData.emergency_name) {
        emergencySummary.classList.remove('hidden');
        emergencyDetails.innerHTML = `
            <p><strong>Name:</strong> ${formData.emergency_name}</p>
            <p><strong>Phone:</strong> ${formData.emergency_phone || 'N/A'}</p>
            ${formData.emergency_relationship ? `<p><strong>Relationship:</strong> ${formData.emergency_relationship}</p>` : ''}
            ${formData.emergency_email ? `<p><strong>Email:</strong> ${formData.emergency_email}</p>` : ''}
        `;
    } else {
        emergencySummary.classList.add('hidden');
    }
    
    // Lease section
    const leaseSummary = document.getElementById('lease-summary');
    const leaseDetails = document.getElementById('lease-details');
    if (formData.attach_lease) {
        leaseSummary.classList.remove('hidden');
        leaseDetails.innerHTML = `
            <p><strong>Property:</strong> ${document.querySelector(`select[name="property_id"] option[value="${formData.property_id}"]`)?.textContent || 'N/A'}</p>
            <p><strong>Unit:</strong> ${document.querySelector(`select[name="unit_id"] option[value="${formData.unit_id}"]`)?.textContent || 'N/A'}</p>
            <p><strong>Lease Period:</strong> ${formData.lease_start || 'N/A'} to ${formData.lease_end || 'N/A'}</p>
            <p><strong>Rent:</strong> $${formData.rent_amount || '0'}/${formData.payment_frequency || 'month'}</p>
            ${formData.security_deposit ? `<p><strong>Security Deposit:</strong> $${formData.security_deposit}</p>` : ''}
            <p><strong>Payment Method:</strong> ${formData.payment_method?.replace('_', ' ') || 'N/A'}</p>
        `;
    } else {
        leaseSummary.classList.add('hidden');
    }
}

// Form submission
function submitTenantForm(event) {
    event.preventDefault();
    
    if (!validateStep(5)) {
        return;
    }
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Hide form and show success screen
        document.getElementById('step5').classList.add('hidden');
        document.getElementById('success-screen').classList.remove('hidden');
        
        // Show success toast
        if (typeof showToast === 'function') {
            showToast('Tenant created successfully!', 'success');
        }
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
}

// Success screen actions
function addAnother() {
    // Reset form
    document.getElementById('tenantForm').reset();
    formData = {};
    currentStep = 1;
    
    // Hide success screen, show first step
    document.getElementById('success-screen').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    
    // Reset progress
    updateProgressIndicator(1);
    
    // Reset tenant type fields
    document.getElementById('first-name-field').classList.remove('hidden');
    document.getElementById('last-name-field').classList.remove('hidden');
    document.getElementById('company-name-field').classList.add('hidden');
    document.querySelector('input[name="first_name"]').required = true;
    document.querySelector('input[name="last_name"]').required = true;
    document.querySelector('input[name="company_name"]').required = false;
    
    // Reset lease fields
    document.getElementById('attach-lease-toggle').checked = false;
    toggleLeaseFields();
    
    // Remove file if any
    removeFile();
}

function viewTenants() {
    window.location.href = '/admin/tenants';
}
</script>

<?php
$content = ob_get_clean();
echo ViewManager::render('admin.dashboard_layout', ['content' => $content]);
?>
