<?php
// This view is now rendered by ViewManager in the controller
// Load UIComponents for toast notifications and other UI elements
if (!class_exists('ComponentRegistry')) {
    require_once __DIR__ . '/../../../config/bootstrap.php';
}
ComponentRegistry::load('ui-components');
?>

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

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
                        Communications
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <i class="fas fa-envelope text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Sent</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo number_format(ViewManager::get('stats')['total'] ?? 0); ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Delivered</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo number_format(ViewManager::get('stats')['sent_count'] ?? 0); ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                <i class="fas fa-eye text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Read</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo number_format(ViewManager::get('stats')['draft_count'] ?? 0); ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                <i class="fas fa-clock text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo number_format(ViewManager::get('stats')['email_count'] ?? 0); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Communications List -->
<div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Recent Communications</h2>
                <p class="text-gray-600 dark:text-gray-400">Manage and track all communications</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="showNewCommunicationModal()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i> New Message
                </button>
                <button onclick="exportCommunications()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    <i class="fas fa-download mr-2"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" id="searchCommunications" placeholder="Search communications..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div>
                <select id="filterType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Types</option>
                    <option value="email">Email</option>
                    <option value="sms">SMS</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="broadcast">Broadcast</option>
                </select>
            </div>
            <div>
                <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="sent">Sent</option>
                    <option value="delivered">Delivered</option>
                    <option value="read">Read</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Communications Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-cream-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Recipient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-cream-50 dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php 
                $communications = ViewManager::get('communications') ?? [];
                // Debug: Uncomment to see what data we have
                // error_log('Communications count: ' . count($communications));
                // if (!empty($communications)) {
                //     error_log('First communication: ' . print_r($communications[0], true));
                // }
                ?>
                <?php if (empty($communications)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        No communications found. Create your first communication to see it here.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($communications as $communication): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($communication['subject'] ?? 'No Subject'); ?></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($communication['property_name'] ?? 'No Property'); ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($communication['tenant_name'] ?? 'Unknown'); ?></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($communication['tenant_email'] ?? 'No email'); ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $type = $communication['type'] ?? 'unknown';
                        $typeClass = $type === 'email' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                    ($type === 'sms' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                    ($type === 'whatsapp' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                    'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'));
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $typeClass; ?>">
                            <?php 
                            if ($type === 'whatsapp') {
                                echo '<i class="fab fa-whatsapp mr-1"></i>';
                            }
                            echo ucfirst($type); 
                            ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $status = $communication['status'] ?? 'unknown';
                        $statusClass = $status === 'sent' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : 
                                       ($status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($status === 'read' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                       ($status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                       'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200')));
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusClass; ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $priority = $communication['priority'] ?? 'normal';
                        $priorityClass = $priority === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                         ($priority === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                         'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300');
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $priorityClass; ?>">
                            <?php echo ucfirst($priority); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                        <?php echo date('M j, Y', strtotime($communication['created_at'])); ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                        <div class="flex space-x-2">
                            <button onclick="viewCommunication(<?php echo $communication['id']; ?>)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="resendCommunication(<?php echo $communication['id']; ?>)" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                                <i class="fas fa-redo"></i>
                            </button>
                            <button onclick="deleteCommunication(<?php echo $communication['id']; ?>)" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing <span class="font-medium"><?php echo min(1, ViewManager::get('pagination')['total'] ?? 0); ?></span> to 
                <span class="font-medium"><?php echo min(ViewManager::get('pagination')['per_page'] ?? 10, ViewManager::get('pagination')['total'] ?? 0); ?></span> of 
                <span class="font-medium"><?php echo ViewManager::get('pagination')['total'] ?? 0; ?></span> results
            </div>
            <div class="flex space-x-2">
                <?php $currentPage = ViewManager::get('pagination')['current_page'] ?? 1; ?>
                <?php $lastPage = ViewManager::get('pagination')['last_page'] ?? 1; ?>
                
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?php echo $currentPage - 1; ?>" class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php else: ?>
                    <button class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                <?php endif; ?>
                
                <?php for ($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <button class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-primary-600 text-white"><?php echo $i; ?></button>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>" class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($currentPage < $lastPage): ?>
                    <a href="?page=<?php echo $currentPage + 1; ?>" class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <button class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- New Communication Modal -->
<div id="newCommunicationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-lg bg-cream-50 dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Communication</h3>
            <button onclick="hideNewCommunicationModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Message Type Selection -->
        <div id="typeSelection" class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Choose Message Type *</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button type="button" onclick="selectMessageType('email')" class="message-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-primary-500 dark:hover:border-primary-400 transition-colors">
                    <i class="fas fa-envelope text-2xl text-blue-600 dark:text-blue-400 mb-2"></i>
                    <div class="font-medium text-gray-900 dark:text-white">Email</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Send via email</div>
                </button>
                <button type="button" onclick="selectMessageType('sms')" class="message-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-primary-500 dark:hover:border-primary-400 transition-colors">
                    <i class="fas fa-sms text-2xl text-green-600 dark:text-green-400 mb-2"></i>
                    <div class="font-medium text-gray-900 dark:text-white">SMS</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Send via SMS</div>
                </button>
                <button type="button" onclick="selectMessageType('whatsapp')" class="message-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-primary-500 dark:hover:border-primary-400 transition-colors">
                    <i class="fab fa-whatsapp text-2xl text-green-600 dark:text-green-400 mb-2"></i>
                    <div class="font-medium text-gray-900 dark:text-white">WhatsApp</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Send via WhatsApp</div>
                </button>
            </div>
        </div>

        <!-- Message Form (Hidden by default) -->
        <form id="newCommunicationForm" class="hidden">
            <input type="hidden" name="type" id="messageType">
            
            <!-- Template Selection -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-file-alt mr-1"></i> Use Template (Optional)
                </label>
                <select id="templateSelect" onchange="loadTemplate()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Select a template...</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority *</label>
                    <select name="priority" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Priority</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div id="whatsappTemplateSection" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">WhatsApp Template</label>
                    <select id="whatsappTemplateSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Use text message</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recipients *</label>
                <?php 
                // Debug: Check if tenants data is available
                $tenants = ViewManager::get('tenants') ?? [];
                // Uncomment the line below for debugging
                // error_log('Tenants count in view: ' . count($tenants));
                ?>
                <select id="recipientSelect" name="recipients" required multiple class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <?php foreach ($tenants as $tenant): ?>
                        <option value="<?php echo $tenant['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($tenant['name']); ?>"
                                data-email="<?php echo htmlspecialchars($tenant['email']); ?>"
                                data-phone="<?php echo htmlspecialchars($tenant['phone'] ?? ''); ?>"
                                data-property="<?php echo htmlspecialchars($tenant['property_name'] ?? 'Not Assigned'); ?>">
                            <?php echo htmlspecialchars($tenant['name']); ?> - 
                            <?php echo htmlspecialchars($tenant['email']); ?>
                            <?php if (!empty($tenant['phone'])): ?>
                                (<?php echo htmlspecialchars($tenant['phone']); ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hold Ctrl/Cmd to select multiple recipients</p>
                <div id="recipientWarning" class="hidden mt-2 text-sm text-yellow-600 dark:text-yellow-400">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <span id="recipientWarningText"></span>
                </div>
            </div>
            
            <!-- Tenant Information Section -->
            <div id="tenantInfoSection" class="mb-4 hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-users mr-1"></i> Selected Tenant Information
                </label>
                <div id="selectedTenantsInfo" class="space-y-2">
                    <!-- Tenant info will be auto-populated here -->
                </div>
            </div>
            
            <div id="subjectSection" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject *</label>
                <input type="text" name="subject" id="subjectInput" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message *</label>
                <textarea name="message" id="messageInput" required rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"></textarea>
                <div id="messagePreview" class="hidden mt-3 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preview:</div>
                    <div id="previewContent" class="text-sm text-gray-900 dark:text-white"></div>
                </div>
            </div>

            <!-- Template Variables -->
            <div id="templateVariables" class="hidden mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Template Variables</label>
                <div id="variablesContainer" class="space-y-2"></div>
            </div>
            
            <div class="flex justify-between">
                <button type="button" onclick="backToTypeSelection()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </button>
                <div class="flex space-x-3">
                    <button type="button" onclick="hideNewCommunicationModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <i class="fas fa-paper-plane mr-2"></i> Send Message
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load templates on page load
    loadTemplates();
    
    // Search functionality
    document.getElementById('searchCommunications').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Filter functionality
    document.getElementById('filterType').addEventListener('change', filterCommunications);
    document.getElementById('filterStatus').addEventListener('change', filterCommunications);
});

// Message type selection
function selectMessageType(type) {
    document.getElementById('messageType').value = type;
    
    // Update UI
    document.querySelectorAll('.message-type-btn').forEach(btn => {
        btn.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900');
        btn.classList.add('border-gray-300', 'dark:border-gray-600');
    });
    
    event.target.closest('.message-type-btn').classList.remove('border-gray-300', 'dark:border-gray-600');
    event.target.closest('.message-type-btn').classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900');
    
    // Show form and hide type selection
    document.getElementById('typeSelection').classList.add('hidden');
    document.getElementById('newCommunicationForm').classList.remove('hidden');
    
    // Configure form based on type
    configureFormForType(type);
    
    // Load compatible templates
    loadTemplates();
    
    // Check recipients
    checkRecipientCompatibility(type);
}

function configureFormForType(type) {
    const subjectSection = document.getElementById('subjectSection');
    const whatsappTemplateSection = document.getElementById('whatsappTemplateSection');
    
    if (type === 'whatsapp') {
        subjectSection.classList.add('hidden');
        whatsappTemplateSection.classList.remove('hidden');
        loadWhatsAppTemplates();
    } else {
        subjectSection.classList.remove('hidden');
        whatsappTemplateSection.classList.add('hidden');
    }
}

function backToTypeSelection() {
    document.getElementById('typeSelection').classList.remove('hidden');
    document.getElementById('newCommunicationForm').classList.add('hidden');
    
    // Reset form
    document.getElementById('newCommunicationForm').reset();
    document.querySelectorAll('.message-type-btn').forEach(btn => {
        btn.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900');
        btn.classList.add('border-gray-300', 'dark:border-gray-600');
    });
}

function loadTemplates() {
    const messageType = document.getElementById('messageType').value;
    const url = messageType ? `/admin/communications/templates?type=${messageType}` : '/admin/communications/templates';
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('templateSelect');
            select.innerHTML = '<option value="">Select a template...</option>';
            
            data.templates.forEach(template => {
                const option = document.createElement('option');
                option.value = template.id;
                option.textContent = template.name;
                option.dataset.type = template.type;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading templates:', error));
}

function loadWhatsAppTemplates() {
    // Load WhatsApp API templates if configured
    fetch('/admin/communications/whatsapp-templates')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('whatsappTemplateSelect');
            select.innerHTML = '<option value="">Use text message</option>';
            
            if (data.success && data.templates) {
                data.templates.forEach(template => {
                    const option = document.createElement('option');
                    option.value = template.name;
                    option.textContent = template.name;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading WhatsApp templates:', error));
}

function loadTemplate() {
    const templateId = document.getElementById('templateSelect').value;
    const messageType = document.getElementById('messageType').value;
    
    if (!templateId) {
        clearTemplateForm();
        return;
    }
    
    fetch(`/admin/communications/template/${templateId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const template = data.template;
                
                // Fill form with template data
                document.getElementById('subjectInput').value = template.subject || '';
                document.getElementById('messageInput').value = template.message;
                
                // Show template variables
                showTemplateVariables(template.variables || []);
                
                // Update preview
                updateMessagePreview();
            }
        })
        .catch(error => console.error('Error loading template:', error));
}

function showTemplateVariables(variables) {
    const container = document.getElementById('variablesContainer');
    const section = document.getElementById('templateVariables');
    
    if (variables.length === 0) {
        section.classList.add('hidden');
        return;
    }
    
    container.innerHTML = '';
    variables.forEach(variable => {
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-2';
        
        if (variable === 'tenant_name') {
            // Create tenant dropdown instead of text input
            div.innerHTML = `
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">${variable}:</label>
                <select id="var_${variable}" onchange="onTenantSelected(this)" 
                        class="flex-1 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                    <option value="">Select a tenant...</option>
                </select>
                <input type="hidden" id="var_tenant_phone" placeholder="Tenant phone">
                <input type="hidden" id="var_property_name" placeholder="Property name">
                <input type="hidden" id="var_due_date" placeholder="Due date">
            `;
            
            // Load tenants for the dropdown
            loadTenantsForDropdown();
        } else if (variable === 'property_name' || variable === 'due_date') {
            // These will be auto-filled, so make them readonly
            div.innerHTML = `
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">${variable}:</label>
                <input type="text" id="var_${variable}" placeholder="Auto-filled from tenant selection" readonly
                       class="flex-1 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white text-sm">
            `;
        } else {
            // Regular text input for other variables
            div.innerHTML = `
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">${variable}:</label>
                <input type="text" id="var_${variable}" placeholder="Enter ${variable}" 
                       class="flex-1 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                       oninput="updateMessagePreview()">
            `;
        }
        
        container.appendChild(div);
    });
    
    // Add property selection dropdown container (hidden by default)
    const propertyDiv = document.createElement('div');
    propertyDiv.id = 'propertySelectionContainer';
    propertyDiv.className = 'hidden items-center space-x-2 mt-2';
    propertyDiv.innerHTML = `
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Select Property:</label>
        <select id="propertySelect" onchange="onPropertySelected(this)" 
                class="flex-1 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
            <option value="">Select property...</option>
        </select>
    `;
    container.appendChild(propertyDiv);
    
    section.classList.remove('hidden');
    
    // Auto-fill variables from selected tenants
    autoFillTemplateVariables(variables);
}

function autoFillTemplateVariables(variables) {
    const select = document.getElementById('recipientSelect');
    const selectedOptions = Array.from(select.selectedOptions);
    
    if (selectedOptions.length === 0) return;
    
    // Use the first selected tenant for auto-filling
    const tenant = selectedOptions[0];
    const tenantData = {
        tenant_name: tenant.dataset.name,
        tenant_email: tenant.dataset.email,
        tenant_phone: tenant.dataset.phone,
        property_name: tenant.dataset.property,
        property_manager: 'Property Manager', // Default value
        contact_number: tenant.dataset.phone
    };
    
    variables.forEach(variable => {
        const input = document.getElementById(`var_${variable}`);
        if (input && !input.value && tenantData[variable]) {
            input.value = tenantData[variable];
        }
    });
    
    // Update preview after auto-filling
    updateMessagePreview();
}

// Load tenants for the dropdown in template variables
function loadTenantsForDropdown() {
    fetch('/admin/communications/tenants-for-template')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('var_tenant_name');
                if (select) {
                    // Clear existing options except the first one
                    while (select.children.length > 1) {
                        select.removeChild(select.lastChild);
                    }
                    
                    // Add tenant options
                    data.tenants.forEach(tenant => {
                        const option = document.createElement('option');
                        option.value = tenant.id;
                        option.textContent = `${tenant.name} - ${tenant.email}`;
                        option.dataset.tenant = JSON.stringify(tenant);
                        select.appendChild(option);
                    });
                }
            }
        })
        .catch(error => console.error('Error loading tenants:', error));
}

// Handle tenant selection in template variables
function onTenantSelected(select) {
    const selectedOption = select.options[select.selectedIndex];
    const tenantData = selectedOption ? JSON.parse(selectedOption.dataset.tenant || '{}') : null;
    
    if (tenantData) {
        // Fill tenant phone (hidden field)
        const phoneField = document.getElementById('var_tenant_phone');
        if (phoneField) {
            phoneField.value = tenantData.phone || '';
        }
        
        // Handle property selection
        if (tenantData.properties && tenantData.properties.length > 1) {
            // Show property selection dropdown if tenant has multiple properties
            showPropertySelection(tenantData.properties);
        } else if (tenantData.properties && tenantData.properties.length === 1) {
            // Auto-fill single property
            fillPropertyData(tenantData.properties[0], tenantData);
        } else {
            // No properties, clear property fields
            clearPropertyFields();
        }
    } else {
        clearAllFields();
    }
    
    updateMessagePreview();
}

// Show property selection dropdown for tenants with multiple properties
function showPropertySelection(properties) {
    const container = document.getElementById('propertySelectionContainer');
    const select = document.getElementById('propertySelect');
    
    if (container && select) {
        // Clear existing options
        while (select.children.length > 1) {
            select.removeChild(select.lastChild);
        }
        
        // Add property options
        properties.forEach(property => {
            const option = document.createElement('option');
            option.value = property.id;
            option.textContent = `${property.name}${property.unit_number ? ' - Unit ' + property.unit_number : ''}`;
            option.dataset.property = JSON.stringify(property);
            select.appendChild(option);
        });
        
        // Show the dropdown
        container.classList.remove('hidden');
        container.classList.add('flex');
    }
}

// Handle property selection
function onPropertySelected(select) {
    const selectedOption = select.options[select.selectedIndex];
    const propertyData = selectedOption ? JSON.parse(selectedOption.dataset.property || '{}') : null;
    
    if (propertyData) {
        fillPropertyData(propertyData);
    }
    
    updateMessagePreview();
}

// Fill property data
function fillPropertyData(propertyData, tenantData = null) {
    const propertyField = document.getElementById('var_property_name');
    const dueDateField = document.getElementById('var_due_date');
    
    if (propertyField) {
        propertyField.value = propertyData.name || '';
    }
    
    if (dueDateField) {
        // Use tenant's next payment due date or format lease end date
        let dueDate = tenantData?.next_payment_due;
        if (!dueDate && tenantData?.lease_end_date) {
            dueDate = tenantData.lease_end_date;
        }
        
        if (dueDate) {
            dueDateField.value = formatDateNigeria(new Date(dueDate));
        } else {
            dueDateField.value = '';
        }
    }
    
    // Hide property selection dropdown after selection
    const container = document.getElementById('propertySelectionContainer');
    if (container) {
        container.classList.add('hidden');
        container.classList.remove('flex');
    }
}

// Clear property fields
function clearPropertyFields() {
    const propertyField = document.getElementById('var_property_name');
    const dueDateField = document.getElementById('var_due_date');
    
    if (propertyField) propertyField.value = '';
    if (dueDateField) dueDateField.value = '';
    
    // Hide property selection dropdown
    const container = document.getElementById('propertySelectionContainer');
    if (container) {
        container.classList.add('hidden');
        container.classList.remove('flex');
    }
}

// Clear all fields
function clearAllFields() {
    clearPropertyFields();
    
    const phoneField = document.getElementById('var_tenant_phone');
    if (phoneField) phoneField.value = '';
}

// Format date in Nigerian style (DD/MM/YYYY)
function formatDateNigeria(date) {
    if (!date) return '';
    
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    
    return `${day}/${month}/${year}`;
}

// Format phone number for WhatsApp (international format)
function formatPhoneForWhatsApp(phone) {
    if (!phone) return '';
    
    // Remove all non-digit characters
    let cleanPhone = phone.replace(/\D/g, '');
    
    // If it starts with 0 (Nigerian local format), replace with 234
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '234' + cleanPhone.substring(1);
    }
    
    // If it doesn't start with country code, assume Nigerian
    if (!cleanPhone.startsWith('234') && cleanPhone.length === 10) {
        cleanPhone = '234' + cleanPhone;
    }
    
    return cleanPhone;
}

// Get the rendered message with template variables substituted
function getRenderedMessage() {
    const message = document.getElementById('messageInput').value;
    const variables = document.querySelectorAll('#variablesContainer input, #variablesContainer select');
    
    let renderedMessage = message;
    variables.forEach(element => {
        const variable = element.id.replace('var_', '');
        let value = element.value || '';
        
        // For dropdown, get the selected text
        if (element.tagName === 'SELECT') {
            const selectedOption = element.options[element.selectedIndex];
            if (selectedOption && selectedOption.dataset.tenant) {
                const tenantData = JSON.parse(selectedOption.dataset.tenant);
                value = tenantData.name || '';
            }
        }
        
        // Replace template variables
        renderedMessage = renderedMessage.replace(new RegExp(`{{${variable}}}`, 'g'), value);
    });
    
    return renderedMessage;
}

// Get tenant phone number from template variables
function getTenantPhoneNumber() {
    // Try to get phone from hidden field first (template variables)
    const phoneField = document.getElementById('var_tenant_phone');
    if (phoneField && phoneField.value) {
        return phoneField.value;
    }
    
    // Fallback to selected recipient
    const recipientSelect = document.getElementById('recipientSelect');
    if (recipientSelect && recipientSelect.selectedOptions.length > 0) {
        const selectedOption = recipientSelect.selectedOptions[0];
        return selectedOption.dataset.phone || '';
    }
    
    return '';
}

// Open WhatsApp DM with phone number and message
function openWhatsAppDM(phoneNumber, message) {
    const formattedPhone = formatPhoneForWhatsApp(phoneNumber);
    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/${formattedPhone}?text=${encodedMessage}`;
    
    // Open in new tab
    window.open(whatsappUrl, '_blank');
}

function clearTemplateForm() {
    document.getElementById('subjectInput').value = '';
    document.getElementById('messageInput').value = '';
    document.getElementById('templateVariables').classList.add('hidden');
    document.getElementById('messagePreview').classList.add('hidden');
}

function updateMessagePreview() {
    const message = document.getElementById('messageInput').value;
    const variables = document.querySelectorAll('#variablesContainer input');
    
    let preview = message;
    variables.forEach(input => {
        const variable = input.id.replace('var_', '');
        const value = input.value || `[${variable}]`;
        preview = preview.replace(new RegExp(`{{${variable}}}`, 'g'), value);
    });
    
    const previewDiv = document.getElementById('messagePreview');
    const previewContent = document.getElementById('previewContent');
    
    if (variables.length > 0) {
        previewDiv.classList.remove('hidden');
        previewContent.textContent = preview;
    } else {
        previewDiv.classList.add('hidden');
    }
}

function checkRecipientCompatibility(type) {
    const select = document.getElementById('recipientSelect');
    const warning = document.getElementById('recipientWarning');
    const warningText = document.getElementById('recipientWarningText');
    
    // Update tenant information display
    updateTenantInfo();
    
    if (type === 'whatsapp' || type === 'sms') {
        let missingPhone = [];
        
        Array.from(select.options).forEach(option => {
            if (option.selected && !option.dataset.phone) {
                missingPhone.push(option.dataset.name);
            }
        });
        
        if (missingPhone.length > 0) {
            warning.classList.remove('hidden');
            warningText.textContent = `${missingPhone.join(', ')} ${missingPhone.length === 1 ? 'does' : 'do'} not have phone numbers`;
        } else {
            warning.classList.add('hidden');
        }
    } else {
        warning.classList.add('hidden');
    }
}

function updateTenantInfo() {
    const select = document.getElementById('recipientSelect');
    const infoSection = document.getElementById('tenantInfoSection');
    const infoContainer = document.getElementById('selectedTenantsInfo');
    
    const selectedOptions = Array.from(select.selectedOptions);
    
    if (selectedOptions.length === 0) {
        infoSection.classList.add('hidden');
        return;
    }
    
    infoSection.classList.remove('hidden');
    
    let infoHtml = '';
    selectedOptions.forEach(option => {
        const name = option.dataset.name;
        const email = option.dataset.email;
        const phone = option.dataset.phone;
        const property = option.dataset.property;
        
        infoHtml += `
            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900 dark:text-white mb-1">
                            <i class="fas fa-user mr-2 text-gray-400"></i>${name}
                        </div>
                        <div class="space-y-1 text-sm">
                            ${email ? `<div class="text-gray-600 dark:text-gray-300"><i class="fas fa-envelope mr-2 text-gray-400"></i>${email}</div>` : ''}
                            ${phone ? `<div class="text-gray-600 dark:text-gray-300"><i class="fas fa-phone mr-2 text-gray-400"></i>${phone}</div>` : ''}
                            <div class="text-gray-600 dark:text-gray-300"><i class="fas fa-home mr-2 text-gray-400"></i>${property}</div>
                        </div>
                    </div>
                    <div class="ml-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            <i class="fas fa-check mr-1"></i>Selected
                        </span>
                    </div>
                </div>
            </div>
        `;
    });
    
    infoContainer.innerHTML = infoHtml;
}

// Recipient change handler
document.addEventListener('DOMContentLoaded', function() {
    const recipientSelect = document.getElementById('recipientSelect');
    if (recipientSelect) {
        recipientSelect.addEventListener('change', function() {
            // Always update tenant info when selection changes
            updateTenantInfo();
            
            const messageType = document.getElementById('messageType').value;
            if (messageType) {
                checkRecipientCompatibility(messageType);
            }
        });
        
        // Check for pre-selected tenant from dashboard
        if (window.selectedCommunicationsTenant) {
            prepopulateCommunicationsWithTenant(window.selectedCommunicationsTenant);
        }
    }
});

function prepopulateCommunicationsWithTenant(tenant) {
    const recipientSelect = document.getElementById('recipientSelect');
    
    if (!recipientSelect || !tenant) return;
    
    // Find and select the tenant in the dropdown
    const options = Array.from(recipientSelect.options);
    const tenantOption = options.find(option => parseInt(option.value) === parseInt(tenant.id));
    
    if (tenantOption) {
        tenantOption.selected = true;
        
        // Update tenant info display
        updateTenantInfo();
        
        // Show success message
        showToast(`Pre-populated with tenant: ${tenant.name}`, 'success');
        
        // Clear the global variable
        window.selectedCommunicationsTenant = null;
    }
}

function filterCommunications() {
    const typeFilter = document.getElementById('filterType').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const type = row.querySelector('td:nth-child(3) span').textContent.toLowerCase();
        const status = row.querySelector('td:nth-child(4) span').textContent.toLowerCase();
        
        const typeMatch = !typeFilter || type === typeFilter;
        const statusMatch = !statusFilter || status === statusFilter;
        
        row.style.display = typeMatch && statusMatch ? '' : 'none';
    });
}

function showNewCommunicationModal() {
    document.getElementById('newCommunicationModal').classList.remove('hidden');
}

function hideNewCommunicationModal() {
    document.getElementById('newCommunicationModal').classList.add('hidden');
    backToTypeSelection();
}

function viewCommunication(id) {
    showToast('Viewing communication #' + id, 'info');
}

function resendCommunication(id) {
    if (confirm('Are you sure you want to resend this message?')) {
        showToast('Message resent successfully!', 'success');
    }
}

function deleteCommunication(id) {
    if (confirm('Are you sure you want to delete this communication?')) {
        showToast('Communication deleted successfully!', 'success');
    }
}

function exportCommunications() {
    showToast('Exporting communications...', 'info');
}

// Enhanced form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newCommunicationForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';
            submitBtn.disabled = true;
            
            // Get form data
            const formData = new FormData(e.target);
            const data = {
                type: formData.get('type'),
                priority: formData.get('priority'),
                recipients: formData.getAll('recipients'),
                subject: formData.get('subject'),
                message: formData.get('message'),
                send_immediately: '1'
            };
            
            // Add WhatsApp template if selected
            const whatsappTemplate = document.getElementById('whatsappTemplateSelect').value;
            if (data.type === 'whatsapp' && whatsappTemplate) {
                data.whatsapp_template = whatsappTemplate;
            }
            
            // Send to backend
            fetch('/admin/communications/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                hideNewCommunicationModal();
                if (data.success) {
                    showToast('Message sent successfully!', 'success');
                    
                    // Auto-open WhatsApp if it's a WhatsApp message
                    if (data.type === 'whatsapp') {
                        // Get the rendered message with template variables substituted
                        const message = getRenderedMessage();
                        const phoneNumber = getTenantPhoneNumber();
                        
                        if (phoneNumber && message) {
                            openWhatsAppDM(phoneNumber, message);
                        }
                    }
                    
                    // Reload page to show new communication
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast(data.message || 'Failed to send message', 'error');
                }
            })
            .catch(error => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                hideNewCommunicationModal();
                showToast('Error sending message', 'error');
            });
        });
    }
});
</script>

