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
                    <a href="/admin/tenants-occupants" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 md:ml-2">
                        Tenants & Occupants
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

<!-- Form Container -->
<div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
    <form id="tenantForm"
          onsubmit="submitTenantForm(event)"
          enctype="multipart/form-data">
        
        <div class="p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Tenant Information</h2>
                <p class="text-gray-600 dark:text-gray-400">Enter the tenant's personal and contact details</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                    <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                    <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                    <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Type</label>
                    <select name="id_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="">Select ID Type</option>
                        <option value="nin">National ID Number</option>
                        <option value="passport">Passport</option>
                        <option value="driver_license">Driver's License</option>
                        <option value="voter_card">Voter's Card</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Number</label>
                    <input type="text" name="id_number" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700
                                   dark:text-gray-300 mb-2">
                        ID Document Upload
                        <span class="text-xs text-gray-400 ml-1">(optional)</span>
                    </label>

                    <div class="flex gap-2 mb-3">
                        <button type="button" id="tenantTabFile"
                            onclick="switchTenantUploadTab('file')"
                            class="tenant-upload-tab active-tab px-4 py-2 text-sm
                                   rounded-lg border border-primary-600 bg-primary-600
                                   text-white transition-colors">
                            <i class="fas fa-folder-open mr-2"></i>Browse Files
                        </button>
                        <button type="button" id="tenantTabCamera"
                            onclick="switchTenantUploadTab('camera')"
                            class="tenant-upload-tab px-4 py-2 text-sm rounded-lg
                                   border border-gray-300 dark:border-gray-600
                                   text-gray-700 dark:text-gray-300
                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                   transition-colors">
                            <i class="fas fa-camera mr-2"></i>Use Camera
                        </button>
                    </div>

                    <div id="tenantPanelFile">
                        <div id="tenantDropZone"
                             class="border-2 border-dashed border-gray-300
                                    dark:border-gray-600 rounded-lg p-6 text-center
                                    cursor-pointer hover:border-primary-500
                                    dark:hover:border-primary-400 transition-colors
                                    bg-gray-50 dark:bg-gray-700/50"
                             onclick="document.getElementById('tenantIdFileInput').click()"
                             ondragover="tenantHandleDragOver(event)"
                             ondragleave="tenantHandleDragLeave(event)"
                             ondrop="tenantHandleFileDrop(event)">
                            <i class="fas fa-cloud-upload-alt text-gray-400
                                      text-3xl mb-2"></i>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Click to browse or drag & drop
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                JPG, PNG, PDF — max 5MB each, up to 2 files
                            </p>
                        </div>
                        <input type="file"
                               id="tenantIdFileInput"
                               name="id_documents[]"
                               accept="image/*,.pdf"
                               multiple
                               class="hidden"
                               onchange="tenantHandleFileSelect(this.files)">
                    </div>

                    <div id="tenantPanelCamera" class="hidden">
                        <div class="rounded-lg overflow-hidden bg-black relative"
                             style="max-height:300px">
                            <video id="tenantCameraStream" autoplay playsinline
                                   muted class="w-full object-cover hidden"
                                   style="max-height:300px"></video>
                            <div id="tenantCameraPlaceholder"
                                 class="flex flex-col items-center justify-center
                                        bg-gray-800 text-gray-400 py-12">
                                <i class="fas fa-camera text-4xl mb-3"></i>
                                <p class="text-sm">Camera not started</p>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" onclick="tenantStartCamera()"
                                    id="tenantStartCameraBtn"
                                    class="flex-1 px-4 py-2 bg-primary-600 text-white
                                           rounded-lg hover:bg-primary-700 text-sm">
                                <i class="fas fa-play mr-2"></i>Start Camera
                            </button>
                            <button type="button" onclick="tenantCapturePhoto()"
                                    id="tenantCaptureBtn"
                                    class="flex-1 px-4 py-2 bg-green-600 text-white
                                           rounded-lg hover:bg-green-700 text-sm hidden">
                                <i class="fas fa-camera mr-2"></i>Capture
                            </button>
                            <button type="button" onclick="tenantStopCamera()"
                                    id="tenantStopCameraBtn"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg
                                           hover:bg-red-700 text-sm hidden">
                                <i class="fas fa-stop mr-2"></i>Stop
                            </button>
                        </div>
                        <canvas id="tenantCaptureCanvas" class="hidden"></canvas>
                    </div>

                    <div id="tenantIdPreviewArea"
                         class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3 hidden">
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Upload front and back of ID or passport.
                        Accepted: JPG, PNG, PDF. Max 5MB per file.
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <input type="text" name="address" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors" placeholder="Street address">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Phone</label>
                    <input type="tel" name="emergency_contact_phone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
            </div>

            <!-- Lease Information -->
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Lease Information</h3>
                    <p class="text-gray-600 dark:text-gray-400">Set up the lease details for this tenant</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property *</label>
                        <select name="property_id" required onchange="updateTenantUnits()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select Property</option>
                            <?php foreach ($properties as $property): ?>
                                <option value="<?php echo $property['id']; ?>"><?php echo htmlspecialchars($property['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit *</label>
                        <select name="unit_id" required onchange="updateRentAmount()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select Unit</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lease Start Date *</label>
                        <input type="date" name="lease_start_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lease End Date</label>
                        <input type="date" name="lease_end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monthly Rent *</label>
                        <input type="number" name="rent_amount" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Security Deposit</label>
                        <input type="number" name="security_deposit" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Frequency</label>
                        <select name="payment_frequency" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="bi-weekly">Bi-Weekly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="annually">Annually</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                            <option value="notice_given">Notice Given</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Additional Information</h3>
                    <p class="text-gray-600 dark:text-gray-400">Any additional notes or information</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-none" placeholder="Additional notes about the tenant..."></textarea>
                </div>
            </div>

            <!-- Next of Kin -->
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white
                               mb-2 flex items-center">
                        <i class="fas fa-user-friends mr-2 text-primary-600"></i>
                        Next of Kin
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Emergency contact information for the tenant
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                       dark:text-gray-300 mb-2">Full Name</label>
                        <input type="text" name="next_of_kin"
                               placeholder="Next of kin full name"
                               class="w-full px-3 py-2 border border-gray-300
                                      dark:border-gray-600 rounded-lg bg-cream-50
                                      dark:bg-gray-700 text-gray-900 dark:text-white
                                      placeholder-gray-500 dark:placeholder-gray-400
                                      focus:outline-none focus:ring-2
                                      focus:ring-primary-500 focus:border-transparent
                                      transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                       dark:text-gray-300 mb-2">Phone Number</label>
                        <input type="tel" name="next_of_kin_phone"
                               placeholder="e.g. 08012345678"
                               class="w-full px-3 py-2 border border-gray-300
                                      dark:border-gray-600 rounded-lg bg-cream-50
                                      dark:bg-gray-700 text-gray-900 dark:text-white
                                      placeholder-gray-500 dark:placeholder-gray-400
                                      focus:outline-none focus:ring-2
                                      focus:ring-primary-500 focus:border-transparent
                                      transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700
                                       dark:text-gray-300 mb-2">Address</label>
                        <textarea name="next_of_kin_address" rows="2"
                                  placeholder="Next of kin residential address"
                                  class="w-full px-3 py-2 border border-gray-300
                                         dark:border-gray-600 rounded-lg bg-cream-50
                                         dark:bg-gray-700 text-gray-900 dark:text-white
                                         placeholder-gray-500 dark:placeholder-gray-400
                                         focus:outline-none focus:ring-2
                                         focus:ring-primary-500 focus:border-transparent
                                         transition-colors resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex flex-col sm:flex-row sm:justify-between gap-3 sm:gap-0">

                <!-- Cancel — full width on mobile, auto on desktop -->
                <a href="/admin/tenants-occupants"
                   class="flex items-center justify-center sm:justify-start
                          px-6 py-3 sm:py-2 bg-gray-300 dark:bg-gray-600
                          text-gray-700 dark:text-gray-300 rounded-lg
                          hover:bg-gray-400 dark:hover:bg-gray-500
                          focus:outline-none focus:ring-2 focus:ring-gray-500
                          focus:ring-offset-2 transition-all duration-200
                          text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> Cancel
                </a>

                <!-- Right side buttons — stack on mobile, row on desktop -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center
                                   px-6 py-3 sm:py-2 bg-primary-600 text-white
                                   rounded-lg hover:bg-primary-700
                                   focus:outline-none focus:ring-2
                                   focus:ring-primary-500 focus:ring-offset-2
                                   transition-all duration-200 text-sm font-medium">
                        <i class="fas fa-check mr-2"></i> Create Tenant
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as default for lease start date
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="lease_start_date"]').value = today;
    
    // Set lease end date to 1 year from today
    const oneYearLater = new Date();
    oneYearLater.setFullYear(oneYearLater.getFullYear() + 1);
    document.querySelector('input[name="lease_end_date"]').value = oneYearLater.toISOString().split('T')[0];
});

function updateTenantUnits() {
    const propertyId = document.querySelector('select[name="property_id"]').value;
    const unitSelect = document.querySelector('select[name="unit_id"]');
    unitSelect.innerHTML = '<option value="">Loading units...</option>';

    if (!propertyId) {
        unitSelect.innerHTML = '<option value="">Select Unit</option>';
        return;
    }

    fetch('/admin/units?property_id=' + propertyId + '&_ajax=1', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        const units = Array.isArray(data)
            ? data
            : (data.units || data.data || []);
        unitSelect.innerHTML = '<option value="">Select Unit</option>';
        if (!units.length) {
            unitSelect.innerHTML = '<option value="">No available units</option>';
            return;
        }
        units.forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.id;
            const rent = u.rent_price
                ? ' — ₦' + Number(u.rent_price).toLocaleString()
                : '';
            opt.textContent = (u.unit_number || u.number)
                              + ' (' + (u.type || '') + ')' + rent;
            opt.dataset.rent = u.rent_price || '';
            unitSelect.appendChild(opt);
        });
    })
    .catch(() => {
        // Fallback to PHP-embedded units
        const all = <?php echo json_encode($units ?? []); ?>;
        const filtered = all.filter(u => u.property_id == propertyId);
        unitSelect.innerHTML = '<option value="">Select Unit</option>';
        filtered.forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.id;
            opt.textContent = (u.unit_number || u.number)
                              + ' (' + (u.type || '') + ')';
            opt.dataset.rent = u.rent_price || '';
            unitSelect.appendChild(opt);
        });
    });
}

function updateRentAmount() {
    const unitSelect = document.querySelector('select[name="unit_id"]');
    const rentInput = document.querySelector('input[name="rent_amount"]');
    const selectedOption = unitSelect.options[unitSelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.rent) {
        rentInput.value = selectedOption.dataset.rent;
    }
}

function submitTenantForm(event) {
    event.preventDefault();
    
    // Basic validation
    const form = event.target;
    const formData = new FormData(form);
    
    // Check required fields
    const requiredFields = ['first_name', 'last_name', 'email', 'phone', 'property_id', 'unit_id', 'lease_start_date', 'rent_amount', 'status'];
    for (const field of requiredFields) {
        if (!formData.get(field)) {
            showToast('Please fill in all required fields.', 'error');
            return;
        }
    }
    
    // Email validation
    const email = formData.get('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showToast('Please enter a valid email address.', 'error');
        return;
    }
    
    // Date validation
    const startDate = new Date(formData.get('lease_start_date'));
    const endDate = formData.get('lease_end_date') ? new Date(formData.get('lease_end_date')) : null;
    
    if (endDate && endDate <= startDate) {
        showToast('Lease end date must be after lease start date.', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Show success message
        showToast('Tenant created successfully!', 'success');
        
        // Redirect after a short delay
        setTimeout(() => {
            window.location.href = '/admin/tenants-occupants';
        }, 1500);
    }, 1500);
}

// ── Tenant upload tab switching ───────────────────────────────────
function switchTenantUploadTab(tab) {
    document.getElementById('tenantPanelFile')
        .classList.toggle('hidden', tab !== 'file');
    document.getElementById('tenantPanelCamera')
        .classList.toggle('hidden', tab !== 'camera');
    document.querySelectorAll('.tenant-upload-tab').forEach(btn => {
        btn.classList.remove('bg-primary-600','text-white',
                             'border-primary-600');
        btn.classList.add('border-gray-300','text-gray-700');
    });
    const active = document.getElementById(
        tab === 'file' ? 'tenantTabFile' : 'tenantTabCamera');
    active.classList.add('bg-primary-600','text-white',
                         'border-primary-600');
    active.classList.remove('border-gray-300','text-gray-700');
    if (tab !== 'camera') tenantStopCamera();
}

function tenantHandleDragOver(e) {
    e.preventDefault();
    document.getElementById('tenantDropZone').classList.add(
        'border-primary-500','bg-primary-50','dark:bg-primary-900/20');
}
function tenantHandleDragLeave(e) {
    document.getElementById('tenantDropZone').classList.remove(
        'border-primary-500','bg-primary-50','dark:bg-primary-900/20');
}
function tenantHandleFileDrop(e) {
    e.preventDefault();
    tenantHandleDragLeave(e);
    tenantHandleFileSelect(e.dataTransfer.files);
}
function tenantHandleFileSelect(files) {
    const preview = document.getElementById('tenantIdPreviewArea');
    preview.innerHTML = '';
    const valid = Array.from(files).slice(0,2).filter(f => {
        if (f.size > 5*1024*1024) {
            showToast(f.name + ' exceeds 5MB', 'error');
            return false;
        }
        return true;
    });
    if (!valid.length) return;
    preview.classList.remove('hidden');
    valid.forEach((file, i) => {
        const div = document.createElement('div');
        div.className = 'relative group rounded-lg overflow-hidden ' +
                        'border border-gray-200 dark:border-gray-600';
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                div.innerHTML = `<img src="${e.target.result}"
                    class="w-full h-24 object-cover">
                    <button type="button"
                        onclick="this.closest('div').remove()"
                        class="absolute top-1 right-1 bg-red-500 text-white
                               rounded-full w-5 h-5 text-xs flex items-center
                               justify-center opacity-0 group-hover:opacity-100">
                        <i class="fas fa-times"></i></button>`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        } else {
            div.innerHTML = `<div class="w-full h-24 flex flex-col
                items-center justify-center text-gray-400">
                <i class="fas fa-file-pdf text-2xl text-red-400 mb-1"></i>
                <span class="text-xs truncate px-2">${file.name}</span>
                </div>
                <button type="button"
                    onclick="this.closest('div').remove()"
                    class="absolute top-1 right-1 bg-red-500 text-white
                           rounded-full w-5 h-5 text-xs flex items-center
                           justify-center opacity-0 group-hover:opacity-100">
                    <i class="fas fa-times"></i></button>`;
            preview.appendChild(div);
        }
    });
}

// ── Tenant camera ─────────────────────────────────────────────────
let tenantCameraStream = null;
async function tenantStartCamera() {
    try {
        tenantCameraStream = await navigator.mediaDevices.getUserMedia(
            { video: { facingMode: 'environment' }, audio: false });
        const video = document.getElementById('tenantCameraStream');
        video.srcObject = tenantCameraStream;
        document.getElementById('tenantCameraPlaceholder')
            .classList.add('hidden');
        video.classList.remove('hidden');
        document.getElementById('tenantStartCameraBtn')
            .classList.add('hidden');
        document.getElementById('tenantCaptureBtn')
            .classList.remove('hidden');
        document.getElementById('tenantStopCameraBtn')
            .classList.remove('hidden');
    } catch(err) {
        showToast('Camera access denied: ' + err.message, 'error');
    }
}
function tenantCapturePhoto() {
    const video  = document.getElementById('tenantCameraStream');
    const canvas = document.getElementById('tenantCaptureCanvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
    let hidden = document.getElementById('tenantCameraData');
    if (!hidden) {
        hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.id   = 'tenantCameraData';
        hidden.name = 'camera_capture_data';
        document.getElementById('tenantForm').appendChild(hidden);
    }
    hidden.value = dataUrl;
    const preview = document.getElementById('tenantIdPreviewArea');
    preview.classList.remove('hidden');
    const div = document.createElement('div');
    div.className = 'relative group rounded-lg overflow-hidden ' +
                    'border border-gray-200 dark:border-gray-600';
    div.innerHTML = `<img src="${dataUrl}"
        class="w-full h-24 object-cover">
        <button type="button"
            onclick="this.closest('div').remove();
                     document.getElementById('tenantCameraData').value='';"
            class="absolute top-1 right-1 bg-red-500 text-white
                   rounded-full w-5 h-5 text-xs flex items-center
                   justify-center opacity-0 group-hover:opacity-100">
            <i class="fas fa-times"></i></button>
        <span class="absolute bottom-0 left-0 right-0 text-center
                     text-xs bg-black/50 text-white py-0.5">
            Camera capture</span>`;
    preview.appendChild(div);
    showToast('Photo captured!', 'success');
    tenantStopCamera();
    switchTenantUploadTab('file');
}
function tenantStopCamera() {
    if (tenantCameraStream) {
        tenantCameraStream.getTracks().forEach(t => t.stop());
        tenantCameraStream = null;
    }
    const video = document.getElementById('tenantCameraStream');
    if (video) { video.srcObject = null; video.classList.add('hidden'); }
    const ph = document.getElementById('tenantCameraPlaceholder');
    if (ph) ph.classList.remove('hidden');
    ['tenantStartCameraBtn'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    });
    ['tenantCaptureBtn','tenantStopCameraBtn'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });
}
window.addEventListener('beforeunload', tenantStopCamera);
</script>


<?php
// Capture content
$content = ob_get_clean();

// Set content for layout
ViewManager::set('content', $content);

// Render using the dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
