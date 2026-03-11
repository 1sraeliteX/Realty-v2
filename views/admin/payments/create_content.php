<?php
// Framework initialization is handled by the controller
// This view is anti-scattering compliant - no direct requires or data access

// Get data from ViewManager (anti-scattering compliant)
$tenants = ViewManager::get('tenants') ?? [];
$properties = ViewManager::get('properties') ?? [];
$admin = ViewManager::get('admin') ?? [];
?>

<!-- Breadcrumb Navigation -->
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
                    <a href="/admin/finances" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 md:ml-2">
                        Finances
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

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Record Payment</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Add a new payment record to the system</p>
        </div>
        <a href="/admin/finances" class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Finances
        </a>
    </div>
</div>

    <!-- Form -->
    <form method="POST" action="/admin/payments" enctype="multipart/form-data" class="space-y-6">
        <!-- Payment Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tenant *</label>
                    <select name="tenant_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Tenant</option>
                        <?php foreach ($tenants as $tenant): ?>
                        <option value="<?= $tenant['id'] ?>"><?= $tenant['name'] ?> - <?= $tenant['property'] ?> <?= $tenant['unit'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Property / Unit *</label>
                    <select name="property_unit" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Property/Unit</option>
                        <?php foreach ($properties as $property): ?>
                        <option value="<?= $property['id'] ?>"><?= $property['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Amount *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500 dark:text-gray-400">₦</span>
                        <input type="number" name="amount" step="0.01" required class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Type *</label>
                    <select name="payment_type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Type</option>
                        <option value="rent">Rent</option>
                        <option value="deposit">Deposit</option>
                        <option value="fine">Fine</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Date *</label>
                    <input type="date" name="payment_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes / Description</label>
                <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Optional notes about this payment..."></textarea>
            </div>
        </div>

        <!-- Receipt Upload Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Receipt</h2>
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
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-folder-open mr-2"></i>
                                Choose Files
                            </button>
                        </div>
                    </div>
                </div>

                <!-- File Preview Area -->
                <div id="filePreviewArea" class="hidden space-y-3">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected Files:</h3>
                    <div id="fileList" class="space-y-2"></div>
                </div>

                <!-- Receipt Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Receipt Description (Optional)</label>
                    <textarea name="receipt_description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Add notes about the receipt..."></textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <a href="/admin/finances" class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fas fa-save mr-2"></i>
                Record Payment
            </button>
        </div>
    </form>

<!-- File Upload JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('receiptFile');
    const uploadArea = document.getElementById('receiptUploadArea');
    const filePreviewArea = document.getElementById('filePreviewArea');
    const fileList = document.getElementById('fileList');
    const form = document.querySelector('form');
    
    let uploadedFiles = [];

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // Handle drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        handleFiles(e.dataTransfer.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                showNotification('File "' + file.name + '" is too large. Maximum size is 10MB.', 'error');
                return;
            }

            // Validate file type
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                showNotification('File "' + file.name + '" is not a supported format.', 'error');
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

    // Handle form submission with files
    form.addEventListener('submit', function(e) {
        if (uploadedFiles.length > 0) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            // Add uploaded files to form data
            uploadedFiles.forEach((file, index) => {
                formData.append(`receipt_files[${index}]`, file);
            });
            
            // Submit via AJAX to handle file uploads
            submitFormWithFiles(formData);
        }
    });

    function submitFormWithFiles(formData) {
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
        
        fetch('/admin/payments', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Payment recorded successfully with receipt!', 'success');
                setTimeout(() => {
                    window.location.href = '/admin/finances';
                }, 1500);
            } else {
                showNotification(data.message || 'Error recording payment', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error uploading files. Please try again.', 'error');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
        
        const bgColor = type === 'success' ? 'bg-green-500' : 
                       type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        
        notification.classList.add(bgColor, 'text-white');
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-3"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
});
</script>
