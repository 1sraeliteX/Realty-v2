<?php
// Initialize framework (anti-scattering compliant)
require_once __DIR__ . '/../../../config/init_framework.php';

// Load components through registry (anti-scattering compliant)
ComponentRegistry::load('ui-components');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('title', 'Admin Page');
ViewManager::set('user', [
    'name' => 'Admin User',
    'email' => 'admin@cornerstone.com',
    'avatar' => null
]);
ViewManager::set('notifications', []);

ob_start();
?>


<!-- Communication Header -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($communication['subject']); ?></h1>
                <p class="text-blue-100"><?php echo htmlspecialchars($communication['recipient_name']); ?> • Unit <?php echo htmlspecialchars($communication['unit_number']); ?></p>
                <p class="text-blue-100"><?php echo htmlspecialchars($communication['property_name']); ?></p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-blue-100 text-sm">Type</p>
                    <p class="text-lg font-bold text-white"><?php echo ucfirst($communication['type']); ?></p>
                </div>
                <?php echo UIComponents::badge(ucfirst($communication['status']), 
                    $communication['status'] === 'sent' ? 'success' : 
                    ($communication['status'] === 'read' ? 'info' : 'warning'), 'large'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Communication Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php echo UIComponents::statCard('Sent Date', date('M j, Y H:i', strtotime($communication['sent_date'])), 'clock', 'blue', '', ucfirst($communication['type'])); ?>
    <?php echo UIComponents::statCard('Status', ucfirst($communication['status']), 'envelope', 'green', '', $communication['read_date'] ? 'Read at ' . date('H:i', strtotime($communication['read_date'])) : 'Not read yet'); ?>
    <?php echo UIComponents::statCard('Priority', ucfirst($communication['priority']), 'flag', 'orange', '', ucfirst($communication['category'])); ?>
    <?php echo UIComponents::statCard('Recipient', htmlspecialchars($communication['recipient_name']), 'user', 'purple', '', 'View profile'); ?>
</div>

<!-- Communication Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Message Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Message Content</h3>
            
            <div class="space-y-4">
                <!-- Message Header -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">From: <?php echo htmlspecialchars($communication['sender_name']); ?></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($communication['sender_email']); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sent: <?php echo date('M j, Y H:i', strtotime($communication['sent_date'])); ?></p>
                            <?php if ($communication['read_date']): ?>
                                <p class="text-sm text-green-600 dark:text-green-400">Read: <?php echo date('M j, Y H:i', strtotime($communication['read_date'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Message Body -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Subject: <?php echo htmlspecialchars($communication['subject']); ?></h4>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?php echo htmlspecialchars($communication['message']); ?></p>
                    </div>
                </div>

                <!-- Attachments -->
                <?php if (!empty($communication['attachments'])): ?>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Attachments</h4>
                        <div class="space-y-2">
                            <?php foreach ($communication['attachments'] as $attachment): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($attachment['name']); ?></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo $attachment['size']; ?></p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex space-x-4">
                <?php echo UIComponents::button('Reply', 'primary', 'medium', '#', 'reply'); ?>
                <?php echo UIComponents::button('Forward', 'info', 'medium', '#', 'share'); ?>
                <?php echo UIComponents::button('Print', 'secondary', 'medium', '#', 'print'); ?>
                <?php echo UIComponents::button('Archive', 'warning', 'medium', '#', 'archive'); ?>
            </div>
        </div>

        <!-- Conversation Thread -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Conversation Thread</h3>
            
            <div class="space-y-4">
                <?php foreach ($conversation as $message): ?>
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 <?php echo $message['direction'] === 'outgoing' ? 'bg-blue-100 dark:bg-blue-900' : ($message['direction'] === 'incoming' ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-gray-700'); ?> rounded-full flex items-center justify-center">
                                <i class="fas fa-<?php echo $message['direction'] === 'outgoing' ? 'paper-plane' : ($message['direction'] === 'incoming' ? 'reply' : 'cog'); ?> text-<?php echo $message['direction'] === 'outgoing' ? 'blue' : ($message['direction'] === 'incoming' ? 'green' : 'gray'); ?>-600 dark:text-<?php echo $message['direction'] === 'outgoing' ? 'blue' : ($message['direction'] === 'incoming' ? 'green' : 'gray'); ?>-400 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($message['sender']); ?>
                                    <?php if ($message['direction'] === 'system'): ?>
                                        <span class="ml-2 text-xs text-gray-500">(System)</span>
                                    <?php endif; ?>
                                </p>
                                <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M j, Y H:i', strtotime($message['date'])); ?></span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1"><?php echo htmlspecialchars($message['message']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Recipient Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recipient Information</h3>
            <div class="text-center mb-4">
                <?php echo UIComponents::avatar($communication['recipient_name'], null, 'large'); ?>
                <h4 class="mt-3 text-lg font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($communication['recipient_name']); ?></h4>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($communication['recipient_email']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Property</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($communication['property_name']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Unit</span>
                    <span class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($communication['unit_number']); ?></span>
                </div>
            </div>
            <div class="mt-4">
                <?php echo UIComponents::button('View Profile', 'primary', 'small', '/admin/tenants/' . $communication['recipient_id'], 'user'); ?>
            </div>
        </div>

        <!-- Communication Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Communication Details</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                    <div class="mt-1"><?php echo UIComponents::badge(ucfirst($communication['type']), 'info'); ?></div>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                    <div class="mt-1"><?php echo UIComponents::badge(ucfirst($communication['category']), 'warning'); ?></div>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Priority</p>
                    <div class="mt-1"><?php echo UIComponents::badge(ucfirst($communication['priority']), 
                        $communication['priority'] === 'high' ? 'danger' : 
                        ($communication['priority'] === 'normal' ? 'info' : 'success')); ?></div>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <div class="mt-1"><?php echo UIComponents::badge(ucfirst($communication['status']), 
                        $communication['status'] === 'sent' ? 'success' : 
                        ($communication['status'] === 'read' ? 'info' : 'warning')); ?></div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <?php echo UIComponents::button('Send Follow-up', 'primary', 'full', '#', 'paper-plane'); ?>
                <?php echo UIComponents::button('Schedule Call', 'info', 'full', '#', 'phone'); ?>
                <?php echo UIComponents::button('Create Task', 'success', 'full', '#', 'tasks'); ?>
                <?php echo UIComponents::button('Add to Template', 'warning', 'full', '#', 'bookmark'); ?>
            </div>
        </div>
    </div>
</div>

<script>
// Reply to communication
function replyToCommunication() {
    showToast('Opening reply composer...', 'info');
}

// Forward communication
function forwardCommunication() {
    showToast('Opening forward dialog...', 'info');
}

// Print communication
function printCommunication() {
    window.print();
}

// Archive communication
function archiveCommunication() {
    if (confirm('Are you sure you want to archive this communication?')) {
        showToast('Archiving communication...', 'info');
        setTimeout(() => {
            showToast('Communication archived successfully!', 'success');
        }, 2000);
    }
}

// Send follow-up
function sendFollowUp() {
    showToast('Opening follow-up composer...', 'info');
}

// Schedule call
function scheduleCall() {
    showToast('Opening call scheduler...', 'info');
}

// Create task
function createTask() {
    showToast('Creating task from communication...', 'info');
}

// Add to template
function addToTemplate() {
    showToast('Adding to templates...', 'info');
}
</script>


<?php
$content = ob_get_clean();
include '../dashboard_layout.php';
?>
