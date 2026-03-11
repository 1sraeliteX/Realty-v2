<?php
// Debug script for finances page
require_once __DIR__ . '/config/init_framework.php';

// Initialize data provider
DataProvider::init();

// Check data availability
$financeStats = DataProvider::get('finance_stats');
$payments = DataProvider::get('payments');
$notifications = DataProvider::get('notifications');

echo "=== FINANCES PAGE DIAGNOSTIC ===\n\n";

echo "Finance Stats Data:\n";
print_r($financeStats);

echo "\nPayments Data (" . count($payments) . " records):\n";
foreach ($payments as $payment) {
    echo "- ID: {$payment['id']}, Tenant: {$payment['tenant']}, Amount: ₦{$payment['amount']}, Status: {$payment['status']}\n";
}

echo "\nNotifications Data (" . count($notifications) . " records):\n";
foreach ($notifications as $notif) {
    echo "- {$notif['type']}: {$notif['message']} ({$notif['time']})\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
?>
