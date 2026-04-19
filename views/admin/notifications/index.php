<?php
$notifications_list = ViewManager::get('notifications_list', []);
$unread = array_filter($notifications_list, fn($n) => !$n['is_read']);
?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <?php echo count($notifications_list); ?> total &mdash; <?php echo count($unread); ?> unread
            </p>
        </div>
        <?php if (count($unread) > 0): ?>
        <button onclick="markAllRead()" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fas fa-check-double mr-2"></i> Mark all as read
        </button>
        <?php endif; ?>
    </div>

    <!-- Notifications List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow divide-y divide-gray-200 dark:divide-gray-700">
        <?php if (empty($notifications_list)): ?>
        <div class="p-12 text-center">
            <i class="fas fa-bell-slash text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400">No notifications yet.</p>
        </div>
        <?php else: ?>
            <?php foreach ($notifications_list as $n): ?>
            <div id="notif-<?php echo (int)$n['id']; ?>"
                 class="flex items-start gap-4 p-4 <?php echo $n['is_read'] ? 'opacity-60' : 'bg-blue-50 dark:bg-blue-900/10'; ?> hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <!-- Icon -->
                <div class="flex-shrink-0 mt-1">
                    <?php
                    $iconMap = ['success' => 'check-circle text-green-500', 'warning' => 'exclamation-triangle text-yellow-500', 'error' => 'times-circle text-red-500'];
                    $icon = $iconMap[$n['type']] ?? 'info-circle text-blue-500';
                    ?>
                    <i class="fas fa-<?php echo $icon; ?> text-xl"></i>
                </div>
                <!-- Body -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        <?php echo htmlspecialchars($n['title']); ?>
                        <?php if (!$n['is_read']): ?>
                        <span class="ml-2 inline-block w-2 h-2 rounded-full bg-blue-500"></span>
                        <?php endif; ?>
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5"><?php echo htmlspecialchars($n['message']); ?></p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?php echo htmlspecialchars($n['time_ago']); ?></p>
                </div>
                <!-- Actions -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <?php if (!$n['is_read']): ?>
                    <button onclick="markRead(<?php echo (int)$n['id']; ?>)"
                            class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 whitespace-nowrap">
                        Mark read
                    </button>
                    <?php endif; ?>
                    <?php if (!empty($n['link'])): ?>
                    <a href="<?php echo htmlspecialchars($n['link']); ?>"
                       class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function markRead(id) {
    fetch('/api/notifications/mark-read', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
        body: JSON.stringify({id})
    }).then(r => r.json()).then(data => {
        if (data.success) {
            const el = document.getElementById('notif-' + id);
            if (el) {
                el.classList.remove('bg-blue-50', 'dark:bg-blue-900/10');
                el.classList.add('opacity-60');
                const dot = el.querySelector('.rounded-full.bg-blue-500');
                if (dot) dot.remove();
                const btn = el.querySelector('button[onclick^="markRead"]');
                if (btn) btn.remove();
            }
        }
    });
}

function markAllRead() {
    fetch('/api/notifications/mark-all-read', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}
    }).then(r => r.json()).then(data => {
        if (data.success) location.reload();
    });
}
</script>
