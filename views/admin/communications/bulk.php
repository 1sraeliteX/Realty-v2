<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
$recipients = ViewManager::get('recipients', []);
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900
                    dark:text-white">Bulk Message</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Send a message to multiple tenants at once
        </p>
    </div>
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="POST" action="/admin/communications"
              class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700
                               dark:text-gray-300 mb-2">
                    Subject
                </label>
                <input type="text" name="subject"
                       class="w-full px-3 py-2 border border-gray-300
                              dark:border-gray-600 rounded-lg bg-cream-50
                              dark:bg-gray-700 text-gray-900
                              dark:text-white focus:outline-none
                              focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700
                               dark:text-gray-300 mb-2">
                    Message
                </label>
                <textarea name="message" rows="6"
                          class="w-full px-3 py-2 border border-gray-300
                                 dark:border-gray-600 rounded-lg bg-cream-50
                                 dark:bg-gray-700 text-gray-900
                                 dark:text-white focus:outline-none
                                 focus:ring-2 focus:ring-primary-500
                                 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <a href="/admin/communications"
                   class="px-4 py-2 border border-gray-300
                          dark:border-gray-600 rounded-lg text-sm
                          text-gray-700 dark:text-gray-300
                          hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 text-white
                               rounded-lg hover:bg-primary-700 text-sm">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Send Bulk Message
                </button>
            </div>
        </form>
    </div>
</div>
