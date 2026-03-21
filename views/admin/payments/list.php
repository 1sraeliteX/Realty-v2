<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
ComponentRegistry::load('ui-components');
$payments   = ViewManager::get('payments', []);
$pagination = ViewManager::get('pagination', []);
$stats      = ViewManager::get('stats', []);
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payments</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Track and manage all rent payments
            </p>
        </div>
        <a href="/admin/payments/create"
           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i>Record Payment
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <?php
        $statCards = [
            ['label'=>'Total Received','key'=>'total_received','icon'=>'fa-check-circle','color'=>'green','prefix'=>'₦'],
            ['label'=>'Pending','key'=>'total_pending','icon'=>'fa-clock','color'=>'yellow','prefix'=>'₦'],
            ['label'=>'Overdue','key'=>'total_overdue','icon'=>'fa-exclamation-circle','color'=>'red','prefix'=>'₦'],
            ['label'=>'This Month','key'=>'this_month','icon'=>'fa-calendar','color'=>'blue','prefix'=>'₦'],
        ];
        foreach ($statCards as $sc): ?>
        <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow p-5">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-<?php echo $sc['color']; ?>-100 dark:bg-<?php echo $sc['color']; ?>-900 rounded-lg">
                    <i class="fas <?php echo $sc['icon']; ?> text-<?php echo $sc['color']; ?>-600 dark:text-<?php echo $sc['color']; ?>-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <?php echo $sc['label']; ?>
                    </p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        <?php echo $sc['prefix']; echo number_format($stats[$sc['key']] ?? 0, 0); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Payments Table -->
    <div class="bg-cream-50 dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Filter bar -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                <input type="text" id="paymentSearch" placeholder="Search payments..."
                       oninput="filterPayments(this.value)"
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <select id="statusFilter" onchange="filterPayments()"
                    class="w-36 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-cream-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">All Status</option>
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <?php
                        $ths = ['Ref #','Tenant','Property','Amount','Type','Due Date','Status','Actions'];
                        foreach ($ths as $th): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <?php echo $th; ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="bg-cream-50 dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="paymentsTableBody">
                    <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-receipt text-3xl mb-3 block opacity-30"></i>
                            No payments recorded yet.
                            <a href="/admin/payments/create" class="text-primary-600 hover:underline ml-1">
                                Record first payment
                            </a>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $pay):
                            $sc = [
                                'paid'      =>'bg-green-100 text-green-800',
                                'pending'   =>'bg-yellow-100 text-yellow-800',
                                'overdue'   =>'bg-red-100 text-red-800',
                                'cancelled' =>'bg-gray-100 text-gray-800',
                            ];
                            $cls = $sc[$pay['status'] ?? ''] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 payment-row">
                            <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-white">
                                <?php echo htmlspecialchars($pay['receipt_reference'] ?? '—'); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                <?php echo htmlspecialchars($pay['tenant_name'] ?? '—'); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-[150px] truncate">
                                <?php echo htmlspecialchars($pay['property_name'] ?? '—'); ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                ₦<?php echo number_format($pay['amount'] ?? 0, 0); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <?php echo ucfirst($pay['payment_type'] ?? '—'); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <?php echo htmlspecialchars($pay['due_date'] ?? '—'); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $cls; ?>">
                                    <?php echo ucfirst($pay['status'] ?? ''); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-2">
                                    <a href="/admin/payments/<?php echo $pay['id']; ?>" class="text-primary-600 hover:underline text-xs">
                                        View
                                    </a>
                                    <a href="/admin/payments/<?php echo $pay['id']; ?>/edit" class="text-blue-600 hover:underline text-xs">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterPayments(query) {
    const rows   = document.querySelectorAll('.payment-row');
    const status = document.getElementById('statusFilter').value;
    const search = query !== undefined ? query : document.getElementById('paymentSearch').value;
    rows.forEach(row => {
        const text     = row.textContent.toLowerCase();
        const matchQ   = !search || text.includes(search.toLowerCase());
        const matchS   = !status || text.includes(status.toLowerCase());
        row.style.display = (matchQ && matchS) ? '' : 'none';
    });
}
</script>
