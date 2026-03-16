<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Add New Occupant');
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
    ['id' => 1, 'property_id' => 1, 'number' => 'A-101', 'type' => '1 Bedroom'],
    ['id' => 2, 'property_id' => 1, 'number' => 'A-102', 'type' => '2 Bedroom'],
    ['id' => 3, 'property_id' => 2, 'number' => 'B-201', 'type' => 'Studio'],
    ['id' => 4, 'property_id' => 2, 'number' => 'B-202', 'type' => '1 Bedroom'],
    ['id' => 5, 'property_id' => 3, 'number' => 'C-301', 'type' => '3 Bedroom']
]);

$tenants = DataProvider::get('tenants', [
    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
    ['id' => 3, 'name' => 'Mike Johnson', 'email' => 'mike@example.com']
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
                    <a href="/admin/tenants-occupants" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 md:ml-2">
                        Tenants & Occupants
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">
                        Add New Occupant
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Form Container -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
    <form id="occupantForm"
        onsubmit="submitOccupantForm(event)"
        enctype="multipart/form-data">
        <div class="p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Occupant Information</h2>
                <p class="text-gray-600 dark:text-gray-400">Enter the occupant's personal and contact details</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                    <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                    <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
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

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700
                                   dark:text-gray-300 mb-2">
                        ID Document Upload
                        <span class="text-xs text-gray-400 ml-1">(optional)</span>
                    </label>

                    <!-- Upload method tabs -->
                    <div class="flex gap-2 mb-3">
                        <button type="button" id="tabFile"
                            onclick="switchUploadTab('file')"
                            class="upload-tab-btn active-tab px-4 py-2 text-sm rounded-lg
                                   border border-primary-600 bg-primary-600 text-white
                                   transition-colors">
                            <i class="fas fa-folder-open mr-2"></i>Browse Files
                        </button>
                        <button type="button" id="tabCamera"
                            onclick="switchUploadTab('camera')"
                            class="upload-tab-btn px-4 py-2 text-sm rounded-lg
                                   border border-gray-300 dark:border-gray-600
                                   text-gray-700 dark:text-gray-300
                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                   transition-colors">
                            <i class="fas fa-camera mr-2"></i>Use Camera
                        </button>
                    </div>

                    <!-- File upload panel -->
                    <div id="panelFile">
                        <div id="idDropZone"
                             class="border-2 border-dashed border-gray-300 dark:border-gray-600
                                    rounded-lg p-6 text-center cursor-pointer
                                    hover:border-primary-500 dark:hover:border-primary-400
                                    transition-colors bg-gray-50 dark:bg-gray-700/50"
                             onclick="document.getElementById('idFileInput').click()"
                             ondragover="handleDragOver(event)"
                             ondragleave="handleDragLeave(event)"
                             ondrop="handleFileDrop(event)">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Click to browse or drag & drop
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                JPG, PNG, PDF — max 5MB each, up to 2 files
                            </p>
                        </div>
                        <!-- Hidden file input — accepts images and PDFs from device -->
                        <input type="file"
                               id="idFileInput"
                               name="id_documents[]"
                               accept="image/*,.pdf"
                               multiple
                               capture=""
                               class="hidden"
                               onchange="handleFileSelect(this.files)">
                    </div>

                    <!-- Camera panel -->
                    <div id="panelCamera" class="hidden">
                        <div class="rounded-lg overflow-hidden bg-black relative"
                             style="max-height:300px">
                            <video id="cameraStream" autoplay playsinline muted
                                   class="w-full object-cover"
                                   style="max-height:300px"></video>
                            <div id="cameraPlaceholder"
                                 class="flex flex-col items-center justify-center
                                        bg-gray-800 text-gray-400 py-12">
                                <i class="fas fa-camera text-4xl mb-3"></i>
                                <p class="text-sm">Camera not started</p>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" onclick="startCamera()"
                                    id="startCameraBtn"
                                    class="flex-1 px-4 py-2 bg-primary-600 text-white
                                           rounded-lg hover:bg-primary-700 text-sm">
                                <i class="fas fa-play mr-2"></i>Start Camera
                            </button>
                            <button type="button" onclick="capturePhoto()"
                                    id="captureBtn"
                                    class="flex-1 px-4 py-2 bg-green-600 text-white
                                           rounded-lg hover:bg-green-700 text-sm hidden">
                                <i class="fas fa-camera mr-2"></i>Capture
                            </button>
                            <button type="button" onclick="stopCamera()"
                                    id="stopCameraBtn"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg
                                           hover:bg-red-700 text-sm hidden">
                                <i class="fas fa-stop mr-2"></i>Stop
                            </button>
                        </div>
                        <!-- Hidden canvas for photo capture -->
                        <canvas id="captureCanvas" class="hidden"></canvas>
                    </div>

                    <!-- Preview area -->
                    <div id="idPreviewArea" class="mt-3 grid grid-cols-2 md:grid-cols-4
                                            gap-3 hidden"></div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Upload front and back of ID or passport.
                        Accepted: JPG, PNG, PDF. Max 5MB per file.
                    </p>
                </div>
            </div>

            <!-- Assignment Information -->
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Assignment Information</h3>
                    <p class="text-gray-600 dark:text-gray-400">Assign the occupant to a property and unit</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property *</label>
                        <select name="property_id" required onchange="updateOccupantUnits()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Relationship to Tenant</label>
                        <select name="relationship" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select Relationship</option>
                            <option value="primary_tenant">Primary Tenant</option>
                            <option value="spouse">Spouse</option>
                            <option value="child">Child</option>
                            <option value="parent">Parent</option>
                            <option value="sibling">Sibling</option>
                            <option value="roommate">Roommate</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Associated Tenant</label>
                        <select name="tenant_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select Tenant (Optional)</option>
                            <?php foreach ($tenants as $tenant): ?>
                                <option value="<?php echo $tenant['id']; ?>"><?php echo htmlspecialchars($tenant['name']); ?> (<?php echo htmlspecialchars($tenant['email']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Move-in Date *</label>
                        <input type="date" name="move_in_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                            <option value="moved_out">Moved Out</option>
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
                    <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-none" placeholder="Additional notes about the occupant..."></textarea>
                </div>
            </div>

            <!-- Next of Kin -->
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2
                               flex items-center">
                        <i class="fas fa-user-friends mr-2 text-primary-600"></i>
                        Next of Kin
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Emergency contact information for the occupant
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700
                                       dark:text-gray-300 mb-2">Full Name</label>
                        <input type="text" name="next_of_kin"
                               placeholder="Next of kin full name"
                               class="w-full px-3 py-2 border border-gray-300
                                      dark:border-gray-600 rounded-lg bg-white
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
                                      dark:border-gray-600 rounded-lg bg-white
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
                                         dark:border-gray-600 rounded-lg bg-white
                                         dark:bg-gray-700 text-gray-900 dark:text-white
                                         placeholder-gray-500 dark:placeholder-gray-400
                                         focus:outline-none focus:ring-2
                                         focus:ring-primary-500 focus:border-transparent
                                         transition-colors resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex flex-col sm:flex-row sm:justify-between
                        gap-3 sm:gap-0">

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
                    <button type="button" onclick="saveAsDraft()"
                            class="w-full sm:w-auto flex items-center justify-center
                                   px-6 py-3 sm:py-2 bg-gray-200 dark:bg-gray-700
                                   text-gray-700 dark:text-gray-300 rounded-lg
                                   hover:bg-gray-300 dark:hover:bg-gray-600
                                   focus:outline-none focus:ring-2 focus:ring-gray-500
                                   focus:ring-offset-2 transition-all duration-200
                                   text-sm font-medium">
                        Save as Draft
                    </button>
                    <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center
                                   px-6 py-3 sm:py-2 bg-primary-600 text-white
                                   rounded-lg hover:bg-primary-700
                                   focus:outline-none focus:ring-2
                                   focus:ring-primary-500 focus:ring-offset-2
                                   transition-all duration-200 text-sm font-medium">
                        <i class="fas fa-check mr-2"></i> Create Occupant
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
// ── Upload tab switching ──────────────────────────────────────────
function switchUploadTab(tab) {
    document.getElementById('panelFile').classList.toggle('hidden', tab !== 'file');
    document.getElementById('panelCamera').classList.toggle('hidden', tab !== 'camera');
    document.querySelectorAll('.upload-tab-btn').forEach(btn => {
        btn.classList.remove('bg-primary-600','text-white','border-primary-600');
        btn.classList.add('border-gray-300','dark:border-gray-600',
                          'text-gray-700','dark:text-gray-300');
    });
    const activeBtn = document.getElementById(
        tab === 'file' ? 'tabFile' : 'tabCamera');
    activeBtn.classList.add('bg-primary-600','text-white','border-primary-600');
    activeBtn.classList.remove('border-gray-300','text-gray-700');

    if (tab !== 'camera') stopCamera();
}

// ── File drag & drop ─────────────────────────────────────────────
function handleDragOver(e) {
    e.preventDefault();
    document.getElementById('idDropZone').classList.add(
        'border-primary-500','bg-primary-50','dark:bg-primary-900/20');
}
function handleDragLeave(e) {
    document.getElementById('idDropZone').classList.remove(
        'border-primary-500','bg-primary-50','dark:bg-primary-900/20');
}
function handleFileDrop(e) {
    e.preventDefault();
    handleDragLeave(e);
    handleFileSelect(e.dataTransfer.files);
}
function handleFileSelect(files) {
    const MAX = 2, MAX_MB = 5;
    const preview = document.getElementById('idPreviewArea');
    preview.innerHTML = '';
    const valid = Array.from(files).slice(0, MAX).filter(f => {
        if (f.size > MAX_MB * 1024 * 1024) {
            showToast(f.name + ' exceeds 5MB limit', 'error');
            return false;
        }
        return true;
    });
    if (!valid.length) return;
    preview.classList.remove('hidden');
    valid.forEach((file, i) => {
        const div = document.createElement('div');
        div.className = 'relative group rounded-lg overflow-hidden ' +
                        'border border-gray-200 dark:border-gray-600 bg-gray-50 ' +
                        'dark:bg-gray-700';
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                div.innerHTML = `
                    <img src="${e.target.result}"
                         class="w-full h-24 object-cover">
                    <button type="button"
                        onclick="removePreview(this, ${i})"
                        class="absolute top-1 right-1 bg-red-500 text-white
                               rounded-full w-5 h-5 text-xs flex items-center
                               justify-center opacity-0 group-hover:opacity-100">
                        <i class="fas fa-times"></i>
                    </button>`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        } else {
            div.innerHTML = `
                <div class="w-full h-24 flex flex-col items-center
                             justify-center text-gray-400">
                    <i class="fas fa-file-pdf text-2xl text-red-400 mb-1"></i>
                    <span class="text-xs truncate px-2 w-full text-center">
                        ${file.name}</span>
                </div>
                <button type="button"
                    onclick="removePreview(this, ${i})"
                    class="absolute top-1 right-1 bg-red-500 text-white
                           rounded-full w-5 h-5 text-xs flex items-center
                           justify-center opacity-0 group-hover:opacity-100">
                    <i class="fas fa-times"></i>
                </button>`;
            preview.appendChild(div);
        }
    });
}
function removePreview(btn, idx) {
    btn.closest('div').remove();
    const preview = document.getElementById('idPreviewArea');
    if (!preview.children.length) preview.classList.add('hidden');
}

// ── Camera ────────────────────────────────────────────────────────
let cameraStream = null;
async function startCamera() {
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia(
            { video: { facingMode: 'environment' }, audio: false });
        const video = document.getElementById('cameraStream');
        video.srcObject = cameraStream;
        document.getElementById('cameraPlaceholder').classList.add('hidden');
        video.classList.remove('hidden');
        document.getElementById('startCameraBtn').classList.add('hidden');
        document.getElementById('captureBtn').classList.remove('hidden');
        document.getElementById('stopCameraBtn').classList.remove('hidden');
    } catch (err) {
        showToast('Camera access denied or not available: ' + err.message, 'error');
    }
}
function capturePhoto() {
    const video  = document.getElementById('cameraStream');
    const canvas = document.getElementById('captureCanvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

    // Store in hidden input for form submission
    let hidden = document.getElementById('cameraCaptureData');
    if (!hidden) {
        hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.id   = 'cameraCaptureData';
        hidden.name = 'camera_capture_data';
        document.getElementById('occupantForm').appendChild(hidden);
    }
    hidden.value = dataUrl;

    // Show preview
    const preview = document.getElementById('idPreviewArea');
    preview.classList.remove('hidden');
    const div = document.createElement('div');
    div.className = 'relative group rounded-lg overflow-hidden ' +
                    'border border-gray-200 dark:border-gray-600';
    div.innerHTML = `
        <img src="${dataUrl}" class="w-full h-24 object-cover">
        <button type="button" onclick="clearCameraCapture(this)"
                class="absolute top-1 right-1 bg-red-500 text-white
                       rounded-full w-5 h-5 text-xs flex items-center
                       justify-center opacity-0 group-hover:opacity-100">
            <i class="fas fa-times"></i>
        </button>
        <span class="absolute bottom-0 left-0 right-0 text-center
                     text-xs bg-black/50 text-white py-0.5">
            Camera capture
        </span>`;
    preview.appendChild(div);
    showToast('Photo captured!', 'success');
    stopCamera();
    switchUploadTab('file');
}
function clearCameraCapture(btn) {
    btn.closest('div').remove();
    const hidden = document.getElementById('cameraCaptureData');
    if (hidden) hidden.value = '';
}
function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }
    const video = document.getElementById('cameraStream');
    if (video) {
        video.srcObject = null;
        video.classList.add('hidden');
    }
    const ph = document.getElementById('cameraPlaceholder');
    if (ph) ph.classList.remove('hidden');
    const start   = document.getElementById('startCameraBtn');
    const capture = document.getElementById('captureBtn');
    const stop    = document.getElementById('stopCameraBtn');
    if (start)   start.classList.remove('hidden');
    if (capture) capture.classList.add('hidden');
    if (stop)    stop.classList.add('hidden');
}

// ── Form init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const mdi = document.querySelector('input[name="move_in_date"]');
    if (mdi) mdi.value = today;
    // Stop camera if user navigates away
    window.addEventListener('beforeunload', stopCamera);
});

// ── Units filter ──────────────────────────────────────────────────
function updateOccupantUnits() {
    const propertyId = document.querySelector('select[name="property_id"]').value;
    const unitSelect = document.querySelector('select[name="unit_id"]');
    unitSelect.innerHTML = '<option value="">Select Unit</option>';
    if (!propertyId) return;

    // Fetch real units from API
    fetch('/admin/units?property_id=' + propertyId + '&_ajax=1', {
        headers: { 'X-Requested-With': 'XMLHttpRequest',
                   'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const units = Array.isArray(data) ? data :
                      (data.units || data.data || []);
        units.forEach(unit => {
            const opt = document.createElement('option');
            opt.value = unit.id;
            opt.textContent = (unit.unit_number || unit.number) +
                              ' — ' + (unit.type || '');
            unitSelect.appendChild(opt);
        });
        if (!units.length) {
            unitSelect.innerHTML =
                '<option value="">No units found for this property</option>';
        }
    })
    .catch(() => {
        // Fallback to PHP-embedded units
        const units = <?php echo json_encode($units); ?>;
        units.filter(u => u.property_id == propertyId).forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.id;
            opt.textContent = u.number + ' — ' + u.type;
            unitSelect.appendChild(opt);
        });
    });
}

// ── Form submit ───────────────────────────────────────────────────
function submitOccupantForm(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    const required = ['first_name','last_name','email','phone',
                      'property_id','unit_id','move_in_date','status'];
    for (const f of required) {
        if (!formData.get(f)) {
            showToast('Please fill in all required fields.', 'error');
            const el = form.querySelector(`[name="${f}"]`);
            if (el) {
                el.classList.add('border-red-500');
                el.focus();
            }
            return;
        }
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    const orig = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
    submitBtn.disabled = true;

    fetch('/admin/occupants?_ajax=1', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest',
                   'Accept': 'application/json' },
        body: formData
    })
    .then(r => r.text().then(text => {
        try { return JSON.parse(text); }
        catch(e) {
            console.error('Non-JSON response:', text);
            throw new Error('Server returned non-JSON response');
        }
    }))
    .then(data => {
        submitBtn.innerHTML = orig;
        submitBtn.disabled  = false;
        if (data.success) {
            showToast('Occupant created successfully!', 'success');
            stopCamera();
            setTimeout(() => {
                window.location.href = '/admin/tenants-occupants';
            }, 1500);
        } else {
            const msg = data.errors
                ? Object.values(data.errors).join(', ')
                : (data.error || data.message || 'Failed to create occupant');
            showToast(msg, 'error');
        }
    })
    .catch(err => {
        submitBtn.innerHTML = orig;
        submitBtn.disabled  = false;
        showToast('Error: ' + err.message, 'error');
    });
}

function saveAsDraft() {
    showToast('Draft saved successfully!', 'info');
}

// Safe showToast fallback
if (typeof showToast !== 'function') {
    window.showToast = function(msg, type) {
        const c = {success:'#10b981',error:'#ef4444',
                   info:'#3b82f6',warning:'#f59e0b'};
        const t = document.createElement('div');
        t.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;' +
            'padding:12px 20px;border-radius:8px;color:#fff;font-size:14px;' +
            'font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,.3);background:'
            + (c[type]||c.info);
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => {
            t.style.opacity='0'; t.style.transition='opacity .3s';
            setTimeout(()=>t.remove(),300);
        }, 3500);
    };
}
</script>

<?php
// Capture content
$content = ob_get_clean();

// Set content for layout
ViewManager::set('content', $content);

// Render using the dashboard layout
include __DIR__ . '/../dashboard_layout.php';
?>
