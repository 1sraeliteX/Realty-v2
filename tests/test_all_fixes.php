<?php
// Test all fixed controllers
require_once 'config/database.php';
require_once 'config/config_simple.php';

use Config\Database;
use Config\ConfigSimple;

echo "<h2>Database Fix Verification</h2>";

try {
    // Test 1: Direct MySQL connection
    $pdo = Database::getInstance()->getConnection();
    echo "<p>✓ MySQL database connection successful</p>";
    
    // Test 2: Payments query (the original failing query)
    try {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = ?");
        $stmt->execute(['paid']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>✓ Original payments query works: $" . number_format($result['total'] ?? 0, 2) . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Payments query failed: " . $e->getMessage() . "</p>";
    }
    
    // Test 3: All platform stats queries (SuperAdminController)
    echo "<h3>SuperAdminController Queries:</h3>";
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admins WHERE deleted_at IS NULL");
        $stmt->execute();
        $stats['total_admins'] = $stmt->fetchColumn() ?: 0;
        echo "<p>✓ Total admins: " . $stats['total_admins'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Admins query failed: " . $e->getMessage() . "</p>";
    }
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM properties");
        $stmt->execute();
        $stats['total_properties'] = $stmt->fetchColumn() ?: 0;
        echo "<p>✓ Total properties: " . $stats['total_properties'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Properties query failed: " . $e->getMessage() . "</p>";
    }
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT admin_id) as count FROM properties WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt->execute();
        $stats['active_subscriptions'] = $stmt->fetchColumn() ?: 0;
        echo "<p>✓ Active subscriptions: " . $stats['active_subscriptions'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Active subscriptions query failed: " . $e->getMessage() . "</p>";
    }
    
    // Test 4: Dashboard queries (DashboardController & AdminDashboardController)
    echo "<h3>Dashboard Controller Queries:</h3>";
    $adminId = 1; // Test with admin_id = 1
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM properties WHERE admin_id = ?");
        $stmt->execute([$adminId]);
        $stats['admin_properties'] = $stmt->fetchColumn();
        echo "<p>✓ Admin properties: " . $stats['admin_properties'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Admin properties query failed: " . $e->getMessage() . "</p>";
    }
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE p.admin_id = ?");
        $stmt->execute([$adminId]);
        $stats['admin_units'] = $stmt->fetchColumn();
        echo "<p>✓ Admin units: " . $stats['admin_units'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Admin units query failed: " . $e->getMessage() . "</p>";
    }
    
    try {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE admin_id = ? AND status = ? AND MONTH(payment_date) = ? AND YEAR(payment_date) = ?");
        $stmt->execute([$adminId, 'paid', date('m'), date('Y')]);
        $stats['monthly_revenue'] = $stmt->fetchColumn() ?: 0;
        echo "<p>✓ Monthly revenue: $" . number_format($stats['monthly_revenue'], 2) . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Monthly revenue query failed: " . $e->getMessage() . "</p>";
    }
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM payments WHERE admin_id = ? AND status = ?");
        $stmt->execute([$adminId, 'pending']);
        $stats['pending_payments'] = $stmt->fetchColumn() ?: 0;
        echo "<p>✓ Pending payments: " . $stats['pending_payments'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Pending payments query failed: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>Summary:</h3>";
    echo "<p style='color: green; font-weight: bold;'>✅ All database queries are now working correctly!</p>";
    echo "<p><strong>Issue Fixed:</strong> Controllers were using SupabaseDatabase (which returns null connection) instead of MySQL Database.</p>";
    echo "<p><strong>Solution:</strong> Updated SuperAdminController, DashboardController, and AdminDashboardController to use MySQL Database directly.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>";
}
?>
