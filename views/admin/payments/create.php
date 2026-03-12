<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/bootstrap.php';

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Record Payment');
ViewManager::set('user', [
    'name' => 'Admin User',
    'email' => 'admin@cornerstone.com',
    'avatar' => null
]);
ViewManager::set('notifications', []);

// Mock data for form (would come from DataProvider in production)
$tenants = DataProvider::get('tenants', [
    ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'property_name' => 'Sunset Apartments', 'unit_number' => 'A-101'],
    ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Smith', 'property_name' => 'Ocean View Condos', 'unit_number' => 'B-201'],
    ['id' => 3, 'first_name' => 'Mike', 'last_name' => 'Johnson', 'property_name' => 'Mountain Heights', 'unit_number' => 'C-301']
]);

$properties = DataProvider::get('properties', [
    ['id' => 1, 'name' => 'Sunset Apartments'],
    ['id' => 2, 'name' => 'Ocean View Condos'],
    ['id' => 3, 'name' => 'Mountain Heights']
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
                    <a href="/admin/payments" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 md:ml-2">
                        Payments
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">
                        Record Payment
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Form Container -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
    <form id="paymentForm" onsubmit="submitPaymentForm(event)" enctype="multipart/form-data">
        <div class="p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Payment Information</h2>
                <p class="text-gray-600 dark:text-gray-400">Enter the payment details and upload receipt</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tenant *</label>
                    <select name="tenant_id" required onchange="updateTenantInfo()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="">Select Tenant</option>
                        <?php foreach ($tenants as $tenant): ?>
                            <option value="<?php echo $tenant['id']; ?>" data-property="<?php echo htmlspecialchars($tenant['property_name']); ?>" data-unit="<?php echo htmlspecialchars($tenant['unit_number']); ?>">
                                <?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?> - <?php echo htmlspecialchars($tenant['property_name']); ?> <?php echo htmlspecialchars($tenant['unit_number']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property / Unit *</label>
                    <select name="property_unit" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="">Select Property/Unit</option>
                        <?php foreach ($properties as $property): ?>
                            <option value="<?php echo $property['id']; ?>"><?php echo htmlspecialchars($property['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Amount *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500 dark:text-gray-400">$</span>
                        <input type="number" name="amount" step="0.01" required class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors" placeholder="0.00">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Type *</label>
                    <select name="payment_type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="">Select Type</option>
                        <option value="rent">Rent</option>
                        <option value="deposit">Security Deposit</option>
                        <option value="maintenance">Maintenance Fee</option>
                        <option value="late_fee">Late Fee</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="overdue">Overdue</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Date *</label>
                    <input type="date" name="payment_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="online">Online Payment</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes / Description</label>
                <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-none" placeholder="Optional notes about this payment..."></textarea>
            </div>
        </div>

        <!-- Receipt Upload Section -->
        <div class="border-t border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Payment Receipt</h3>
                    <p class="text-gray-600 dark:text-gray-400">Upload payment receipt (optional)</p>
                </div>

                <div class="space-y-4">
                    <!-- File Upload Area -->
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-gray-400 dark:hover:border-gray-500 transition-colors" id="receiptUploadArea">
                        <div class="space-y-4">
                            <div class="flex justify-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <div>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">Upload Payment Receipt</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Drag and drop your receipt here, or click to browse
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                    Supported formats: PDF, JPG, PNG, DOC, DOCX (Max 10MB)
                                </p>
                            </div>
                            <div>
                                <input type="file" 
                                       id="receiptFile" 
                                       name="receipt_file" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" 
                                       class="hidden" 
                                       multiple>
                                <button type="button" 
                                        onclick="document.getElementById('receiptFile').click()" 
                                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                    <i class="fas fa-folder-open mr-2"></i>
                                    Choose Files
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- File Preview Area -->
                    <div id="filePreviewArea" class="hidden space-y-3">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected Files:</h4>
                        <div id="fileList" class="space-y-2"></div>
                    </div>

                    <!-- Receipt Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Receipt Description (Optional)</label>
                        <textarea name="receipt_description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-none" placeholder="Add notes about the receipt..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-between">
            <a href="/admin/payments" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Cancel
            </a>
            <div class="space-x-3">
                <button type="button" onclick="saveAsDraft()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    Save as Draft
                </button>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-check mr-2"></i> Record Payment
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as default for payment date
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="payment_date"]').value = today;
    
    initializeFileUpload();
});

function initializeFileUpload() {
    const fileInput = document.getElementById('receiptFile');
    const uploadArea = document.getElementById('receiptUploadArea');
    const filePreviewArea = document.getElementById('filePreviewArea');
    const fileList = document.getElementById('fileList');
    
    let uploadedFiles = [];

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // Handle drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        handleFiles(e.dataTransfer.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                showToast('File "' + file.name + '" is too large. Maximum size is 10MB.', 'error');
                return;
            }

            // Validate file type
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                showToast('File "' + file.name + '" is not a supported format.', 'error');
                return;
            }

            uploadedFiles.push(file);
            displayFilePreview(file);
        });

        if (uploadedFiles.length > 0) {
            filePreviewArea.classList.remove('hidden');
        }
    }

    function displayFilePreview(file) {
        const fileItem = document.createElement('div');
        fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg';
        
        const fileIcon = getFileIcon(file.type);
        const fileSize = formatFileSize(file.size);
        
        fileItem.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas ${fileIcon} text-lg text-gray-600 dark:text-gray-400"></i>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">${file.name}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${fileSize}</p>
                </div>
            </div>
            <button type="button" onclick="removeFile('${file.name}')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        fileList.appendChild(fileItem);
    }

    function getFileIcon(fileType) {
        if (fileType === 'application/pdf') return 'fa-file-pdf';
        if (fileType.includes('image/')) return 'fa-file-image';
        if (fileType.includes('word')) return 'fa-file-word';
        return 'fa-file';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    window.removeFile = function(fileName) {
        uploadedFiles = uploadedFiles.filter(file => file.name !== fileName);
        renderFileList();
        
        if (uploadedFiles.length === 0) {
            filePreviewArea.classList.add('hidden');
        }
    };

    function renderFileList() {
        fileList.innerHTML = '';
        uploadedFiles.forEach(file => {
            displayFilePreview(file);
        });
    }
}

function updateTenantInfo() {
    const tenantSelect = document.querySelector('select[name="tenant_id"]');
    const selectedOption = tenantSelect.options[tenantSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const propertyName = selectedOption.dataset.property;
        const unitNumber = selectedOption.dataset.unit;
        console.log('Selected tenant:', selectedOption.text, 'Property:', propertyName, 'Unit:', unitNumber);
    }
}

function submitPaymentForm(event) {
    event.preventDefault();
    
    // Basic validation
    const form = event.target;
    const formData = new FormData(form);
    
    // Check required fields
    const requiredFields = ['tenant_id', 'property_unit', 'amount', 'payment_type', 'status', 'payment_date'];
    for (const field of requiredFields) {
        if (!formData.get(field)) {
            showToast('Please fill in all required fields.', 'error');
            return;
        }
    }
    
    // Amount validation
    const amount = parseFloat(formData.get('amount'));
    if (amount <= 0) {
        showToast('Payment amount must be greater than 0.', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Recording...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Show success message
        showToast('Payment recorded successfully!', 'success');
        
        // Redirect after a short delay
        setTimeout(() => {
            window.location.href = '/admin/payments';
        }, 1500);
    }, 1500);
}

function saveAsDraft() {
    showToast('Draft saved successfully!', 'info');
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
