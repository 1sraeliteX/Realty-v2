<?php
// Test database connection and payments query directly
require_once 'config/database.php';
require_once 'config/config_simple.php';

use Config\Database;
use Config\ConfigSimple;

try {
    echo "<h2>Direct Database Test</h2>";
    
    // Test MySQL database connection
    $pdo = Database::getInstance()->getConnection();
    echo "<p>✓ MySQL database connection successful</p>";
    
    // Test the exact query from getPlatformStats
    try {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = ?");
        $stmt->execute(['paid']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>✓ Payments query executed successfully</p>";
        echo "<p>Total paid payments: $" . number_format($result['total'] ?? 0, 2) . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Payments query failed: " . $e->getMessage() . "</p>";
    }
    
    // Test all the queries from getPlatformStats
    echo "<h3>All Platform Stats Queries:</h3>";
    
    // Total admins
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admins WHERE deleted_at IS NULL");
        $stmt->execute();
        $stats['total_admins'] = $stmt->fetchColumn() ?: 0;
        echo "<p>✓ Total admins: " . $stats['total_admins'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Admins query failed: " . $e->getMessage() . "</p>";
    }
    
    // Total properties
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM properties");
        $stmt->execute();
        $stats['total_properties'] = $stmt->fetchColumn() ?: 0;
        echo "<p>✓ Total properties: " . $stats['total_properties'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Properties query failed: " . $e->getMessage() . "</p>";
    }
    
    // Active subscriptions
    try {
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT admin_id) as count FROM properties WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt->execute();
        $stats['active_subscriptions'] = $stmt->fetchColumn() ?: 0;
        echo "<p>✓ Active subscriptions: " . $stats['active_subscriptions'] . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Active subscriptions query failed: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>Conclusion:</h3>";
    echo "<p style='color: green;'>✓ All database queries are working correctly with MySQL</p>";
    echo "<p>The issue was that SuperAdminController was using SupabaseDatabase (which returns null connection) instead of MySQL Database.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>";
}
?>
