<?php

/**
 * Attachment Component - Document upload, preview, and management
 * Follows anti-scattering guide principles
 */
class AttachmentComponent {
    
    /**
     * Render attachment upload area with drag and drop
     */
    public static function renderUploadArea($config = []) {
        $defaults = [
            'id' => 'attachment-upload',
            'name' => 'attachments[]',
            'multiple' => true,
            'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png,.gif',
            'max_size' => 10, // MB
            'max_files' => 5,
            'preview' => true,
            'class' => ''
        ];
        
        $config = array_merge($defaults, $config);
        
        ob_start();
        ?>
        <div class="attachment-upload-container <?php echo $config['class']; ?>" data-config='<?php echo json_encode($config); ?>'>
            <!-- Upload Area -->
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-primary-500 dark:hover:border-primary-400 transition-colors" 
                 id="<?php echo $config['id']; ?>-dropzone">
                <div class="space-y-4">
                    <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">
                            Drop files here or <span class="text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">browse</span>
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <?php echo implode(', ', array_map('strtoupper', explode(',', $config['accept']))); ?> 
                            • Max <?php echo $config['max_size']; ?>MB each
                            <?php if ($config['max_files'] > 1): ?>• Max <?php echo $config['max_files']; ?> files<?php endif; ?>
                        </p>
                    </div>
                    <input type="file" 
                           id="<?php echo $config['id']; ?>" 
                           name="<?php echo $config['name']; ?>" 
                           class="hidden" 
                           <?php echo $config['multiple'] ? 'multiple' : ''; ?>
                           accept="<?php echo $config['accept']; ?>"
                           data-max-size="<?php echo $config['max_size'] * 1024 * 1024; ?>"
                           data-max-files="<?php echo $config['max_files']; ?>">
                </div>
            </div>
            
            <!-- Preview Area -->
            <?php if ($config['preview']): ?>
            <div id="<?php echo $config['id']; ?>-preview" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 hidden">
                <!-- Previews will be inserted here -->
            </div>
            <?php endif; ?>
            
            <!-- Progress Bar -->
            <div id="<?php echo $config['id']; ?>-progress" class="mt-4 hidden">
                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 text-center">Uploading...</p>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            AttachmentComponent.init('<?php echo $config['id']; ?>');
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render existing attachments with preview and delete functionality
     */
    public static function renderAttachmentsList($attachments, $config = []) {
        $defaults = [
            'show_preview' => true,
            'show_download' => true,
            'show_delete' => true,
            'grid_view' => true,
            'class' => ''
        ];
        
        $config = array_merge($defaults, $config);
        
        if (empty($attachments)) {
            return '<div class="text-center py-8 text-gray-500 dark:text-gray-400">No attachments found</div>';
        }
        
        ob_start();
        ?>
        <div class="attachments-list <?php echo $config['grid_view'] ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4' : 'space-y-3'; ?> <?php echo $config['class']; ?>">
            <?php foreach ($attachments as $attachment): ?>
                <?php echo self::renderAttachmentItem($attachment, $config); ?>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render single attachment item
     */
    private static function renderAttachmentItem($attachment, $config) {
        $fileType = self::getFileType($attachment['file_name'] ?? $attachment['name'] ?? '');
        $fileIcon = self::getFileIcon($fileType);
        $fileSize = self::formatFileSize($attachment['file_size'] ?? 0);
        
        ob_start();
        ?>
        <div class="attachment-item bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
            <!-- Preview Area -->
            <?php if ($config['show_preview']): ?>
            <div class="attachment-preview relative h-32 bg-gray-50 dark:bg-gray-900 flex items-center justify-center cursor-pointer" 
                 onclick="AttachmentComponent.previewAttachment(<?php echo json_encode($attachment); ?>)">
                <?php if ($fileType === 'image'): ?>
                    <img src="<?php echo $attachment['file_path'] ?? $attachment['url'] ?? '#'; ?>" 
                         alt="<?php echo htmlspecialchars($attachment['file_name'] ?? $attachment['name'] ?? ''); ?>"
                         class="max-w-full max-h-full object-contain"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="hidden items-center justify-center w-full h-full">
                        <i class="<?php echo $fileIcon; ?> text-4xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                <?php else: ?>
                    <i class="<?php echo $fileIcon; ?> text-4xl text-gray-400 dark:text-gray-500"></i>
                <?php endif; ?>
                
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all flex items-center justify-center">
                    <i class="fas fa-search-plus text-white opacity-0 hover:opacity-100 transition-opacity"></i>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- File Info -->
            <div class="p-4">
                <div class="space-y-2">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate" 
                                title="<?php echo htmlspecialchars($attachment['file_name'] ?? $attachment['name'] ?? ''); ?>">
                                <?php echo htmlspecialchars($attachment['file_name'] ?? $attachment['name'] ?? ''); ?>
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <?php echo $fileSize; ?> • <?php echo date('M j, Y', strtotime($attachment['created_at'] ?? 'now')); ?>
                            </p>
                        </div>
                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            <?php echo strtoupper($fileType); ?>
                        </span>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 pt-2">
                        <?php if ($config['show_download']): ?>
                        <button onclick="AttachmentComponent.downloadAttachment(<?php echo json_encode($attachment); ?>)" 
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded transition-colors">
                            <i class="fas fa-download mr-1"></i>
                            Download
                        </button>
                        <?php endif; ?>
                        
                        <?php if ($config['show_delete']): ?>
                        <button onclick="AttachmentComponent.deleteAttachment(<?php echo json_encode($attachment); ?>, this)" 
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors">
                            <i class="fas fa-trash mr-1"></i>
                            Delete
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get file type from filename
     */
    private static function getFileType($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
            return 'image';
        } elseif (in_array($ext, ['pdf'])) {
            return 'pdf';
        } elseif (in_array($ext, ['doc', 'docx'])) {
            return 'word';
        } elseif (in_array($ext, ['xls', 'xlsx'])) {
            return 'excel';
        } elseif (in_array($ext, ['ppt', 'pptx'])) {
            return 'powerpoint';
        } elseif (in_array($ext, ['txt', 'rtf'])) {
            return 'text';
        } elseif (in_array($ext, ['zip', 'rar', '7z'])) {
            return 'archive';
        } else {
            return 'unknown';
        }
    }
    
    /**
     * Get file icon based on type
     */
    private static function getFileIcon($type) {
        $icons = [
            'image' => 'fas fa-file-image text-blue-500',
            'pdf' => 'fas fa-file-pdf text-red-500',
            'word' => 'fas fa-file-word text-blue-600',
            'excel' => 'fas fa-file-excel text-green-600',
            'powerpoint' => 'fas fa-file-powerpoint text-orange-500',
            'text' => 'fas fa-file-alt text-gray-500',
            'archive' => 'fas fa-file-archive text-purple-500',
            'unknown' => 'fas fa-file text-gray-400'
        ];
        
        return $icons[$type] ?? $icons['unknown'];
    }
    
    /**
     * Format file size
     */
    private static function formatFileSize($bytes) {
        if ($bytes == 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
    
    /**
     * Render attachment preview modal
     */
    public static function renderPreviewModal() {
        ob_start();
        ?>
        <!-- Attachment Preview Modal -->
        <div id="attachment-preview-modal" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-black bg-opacity-75" onclick="AttachmentComponent.closePreview()"></div>
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl max-h-[90vh] w-full overflow-hidden">
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="preview-title">Document Preview</h3>
                        <button onclick="AttachmentComponent.closePreview()" 
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-4 overflow-auto" style="max-height: calc(90vh - 120px);">
                        <div id="preview-content" class="flex items-center justify-center">
                            <!-- Preview content will be loaded here -->
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button onclick="AttachmentComponent.downloadCurrentAttachment()" 
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            <i class="fas fa-download mr-2"></i>
                            Download
                        </button>
                        <button onclick="AttachmentComponent.closePreview()" 
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

/**
 * AttachmentComponent JavaScript functionality
 */
class AttachmentComponentJS {
    public static function renderJS() {
        ob_start();
        ?>
        <script>
        class AttachmentComponent {
            static currentAttachment = null;
            
            static init(id) {
                const container = document.querySelector(`[data-config][id="${id}"]`);
                if (!container) return;
                
                const config = JSON.parse(container.dataset.config);
                const dropzone = document.getElementById(`${id}-dropzone`);
                const fileInput = document.getElementById(id);
                
                // Click to upload
                dropzone.addEventListener('click', (e) => {
                    if (e.target.tagName !== 'INPUT') {
                        fileInput.click();
                    }
                });
                
                // File selection
                fileInput.addEventListener('change', (e) => {
                    this.handleFiles(e.target.files, config);
                });
                
                // Drag and drop
                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                });
                
                dropzone.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                });
                
                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                    this.handleFiles(e.dataTransfer.files, config);
                });
            }
            
            static handleFiles(files, config) {
                const maxSize = config.max_size * 1024 * 1024;
                const maxFiles = config.max_files;
                const validTypes = config.accept.split(',').map(type => type.trim());
                
                // Validate files
                const validFiles = Array.from(files).filter(file => {
                    // Check file size
                    if (file.size > maxSize) {
                        this.showToast(`File "${file.name}" is too large. Max size is ${config.max_size}MB.`, 'error');
                        return false;
                    }
                    
                    // Check file type
                    const fileExt = '.' + file.name.split('.').pop().toLowerCase();
                    if (!validTypes.includes(fileExt)) {
                        this.showToast(`File "${file.name}" is not a valid type.`, 'error');
                        return false;
                    }
                    
                    return true;
                });
                
                // Check file count
                if (validFiles.length > maxFiles) {
                    this.showToast(`Maximum ${maxFiles} files allowed.`, 'error');
                    return;
                }
                
                // Show previews
                if (config.preview) {
                    this.showPreviews(validFiles, config);
                }
            }
            
            static showPreviews(files, config) {
                const previewContainer = document.getElementById(`${config.id}-preview`);
                if (!previewContainer) return;
                
                previewContainer.classList.remove('hidden');
                previewContainer.innerHTML = '';
                
                files.forEach((file, index) => {
                    const preview = this.createPreview(file, index, config);
                    previewContainer.appendChild(preview);
                });
            }
            
            static createPreview(file, index, config) {
                const div = document.createElement('div');
                div.className = 'relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4';
                
                const fileType = this.getFileType(file.name);
                const isImage = fileType === 'image';
                
                div.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            ${isImage ? 
                                `<img src="${URL.createObjectURL(file)}" class="w-full h-full object-cover rounded-lg">` :
                                `<i class="${this.getFileIcon(fileType)} text-lg text-gray-400 dark:text-gray-500"></i>`
                            }
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${file.name}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${this.formatFileSize(file.size)}</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" 
                                class="flex-shrink-0 text-red-500 hover:text-red-700 dark:hover:text-red-400">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                
                return div;
            }
            
            static previewAttachment(attachment) {
                this.currentAttachment = attachment;
                const modal = document.getElementById('attachment-preview-modal');
                const title = document.getElementById('preview-title');
                const content = document.getElementById('preview-content');
                
                title.textContent = attachment.file_name || attachment.name || 'Document Preview';
                
                const fileType = this.getFileType(attachment.file_name || attachment.name || '');
                
                if (fileType === 'image') {
                    content.innerHTML = `
                        <img src="${attachment.file_path || attachment.url || '#'}" 
                             alt="${attachment.file_name || attachment.name || ''}"
                             class="max-w-full max-h-full object-contain"
                             onerror="this.innerHTML='<div class=\\'text-center\\'><i class=\\'fas fa-file-image text-6xl text-gray-400\\'></i><p class=\\'mt-4 text-gray-500\\'>Image not available</p></div>'">
                    `;
                } else {
                    content.innerHTML = `
                        <div class="text-center">
                            <i class="${this.getFileIcon(fileType)} text-6xl text-gray-400 dark:text-gray-500 mb-4"></i>
                            <p class="text-gray-600 dark:text-gray-400">Preview not available for this file type</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Download the file to view its contents</p>
                        </div>
                    `;
                }
                
                modal.classList.remove('hidden');
            }
            
            static closePreview() {
                document.getElementById('attachment-preview-modal').classList.add('hidden');
                this.currentAttachment = null;
            }
            
            static downloadAttachment(attachment) {
                const link = document.createElement('a');
                link.href = attachment.file_path || attachment.url || '#';
                link.download = attachment.file_name || attachment.name || 'document';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                this.showToast('Download started', 'success');
            }
            
            static downloadCurrentAttachment() {
                if (this.currentAttachment) {
                    this.downloadAttachment(this.currentAttachment);
                }
            }
            
            static deleteAttachment(attachment, button) {
                if (confirm('Are you sure you want to delete this attachment?')) {
                    // Simulate deletion
                    const item = button.closest('.attachment-item');
                    item.style.opacity = '0.5';
                    item.style.pointerEvents = 'none';
                    
                    setTimeout(() => {
                        item.remove();
                        this.showToast('Attachment deleted successfully', 'success');
                    }, 500);
                }
            }
            
            static getFileType(filename) {
                const ext = filename.split('.').pop().toLowerCase();
                const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                const pdfTypes = ['pdf'];
                const wordTypes = ['doc', 'docx'];
                const excelTypes = ['xls', 'xlsx'];
                const pptTypes = ['ppt', 'pptx'];
                
                if (imageTypes.includes(ext)) return 'image';
                if (pdfTypes.includes(ext)) return 'pdf';
                if (wordTypes.includes(ext)) return 'word';
                if (excelTypes.includes(ext)) return 'excel';
                if (pptTypes.includes(ext)) return 'powerpoint';
                
                return 'unknown';
            }
            
            static getFileIcon(type) {
                const icons = {
                    'image': 'fas fa-file-image text-blue-500',
                    'pdf': 'fas fa-file-pdf text-red-500',
                    'word': 'fas fa-file-word text-blue-600',
                    'excel': 'fas fa-file-excel text-green-600',
                    'powerpoint': 'fas fa-file-powerpoint text-orange-500',
                    'unknown': 'fas fa-file text-gray-400'
                };
                
                return icons[type] || icons.unknown;
            }
            
            static formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
            
            static showToast(message, type = 'info') {
                // Simple toast implementation
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                    type === 'success' ? 'bg-green-500 text-white' :
                    type === 'error' ? 'bg-red-500 text-white' :
                    'bg-blue-500 text-white'
                }`;
                toast.textContent = message;
                
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        }
        </script>
        <?php
        return ob_get_clean();
    }
}
?>
