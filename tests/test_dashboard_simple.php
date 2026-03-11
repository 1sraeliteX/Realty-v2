<?php
// Start session
session_start();

// Mock admin user for testing
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Test Admin';
$_SESSION['admin_role'] = 'admin';

// Test the database query directly
require_once 'config/database.php';

try {
    $pdo = Config\Database::getInstance()->getConnection();
    
    echo "Testing database queries for dashboard stats...\n\n";
    
    // Test 1: Total properties
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM properties WHERE admin_id = ?");
    $stmt->execute([1]);
    $total_properties = $stmt->fetchColumn();
    echo "✅ Total properties: $total_properties\n";
    
    // Test 2: Total units
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                           JOIN properties p ON u.property_id = p.id 
                           WHERE p.admin_id = ?");
    $stmt->execute([1]);
    $total_units = $stmt->fetchColumn();
    echo "✅ Total units: $total_units\n";
    
    // Test 3: Monthly revenue (the problematic query)
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE admin_id = ? AND status = ? AND MONTH(payment_date) = ? AND YEAR(payment_date) = ?");
    $stmt->execute([1, 'paid', date('m'), date('Y')]);
    $monthly_revenue = $stmt->fetchColumn() ?: 0;
    echo "✅ Monthly revenue: $monthly_revenue\n";
    
    // Test 4: Pending payments
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM payments WHERE admin_id = ? AND status = ?");
    $stmt->execute([1, 'pending']);
    $pending_payments = $stmt->fetchColumn() ?: 0;
    echo "✅ Pending payments: $pending_payments\n";
    
    echo "\n🎉 All dashboard queries working correctly!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
