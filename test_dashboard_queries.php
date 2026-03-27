<?php
// Test each dashboard query individually
require_once __DIR__ . '/config/database.php';

try {
    $pdo = \Config\Database::getInstance()->getConnection();
    $adminId = 1; // Test with admin ID 1
    
    echo "Testing dashboard queries with admin_id = $adminId\n";
    echo "=============================================\n\n";
    
    // Test 1: Total properties
    echo "1. Total properties query:\n";
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM properties WHERE admin_id = ?");
        $stmt->execute([$adminId]);
        $count = $stmt->fetchColumn();
        echo "   ✅ SUCCESS: $count properties\n";
    } catch (Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    }
    
    // Test 2: Total units (join through properties)
    echo "\n2. Total units query:\n";
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE p.admin_id = ?");
        $stmt->execute([$adminId]);
        $count = $stmt->fetchColumn();
        echo "   ✅ SUCCESS: $count units\n";
    } catch (Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Total tenants
    echo "\n3. Total tenants query:\n";
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tenants t
                               JOIN units u ON t.unit_id = u.id
                               JOIN properties p ON u.property_id = p.id
                               WHERE p.admin_id = ?");
        $stmt->execute([$adminId]);
        $count = $stmt->fetchColumn();
        echo "   ✅ SUCCESS: $count tenants\n";
    } catch (Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Occupied units
    echo "\n4. Occupied units query:\n";
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE p.admin_id = ? AND u.status = ?");
        $stmt->execute([$adminId, 'occupied']);
        $count = $stmt->fetchColumn();
        echo "   ✅ SUCCESS: $count occupied units\n";
    } catch (Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Maintenance requests
    echo "\n5. Maintenance requests query:\n";
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM maintenance_requests mr
                               JOIN properties p ON mr.property_id = p.id
                               WHERE p.admin_id = ? AND mr.status = ?");
        $stmt->execute([$adminId, 'pending']);
        $count = $stmt->fetchColumn();
        echo "   ✅ SUCCESS: $count pending maintenance requests\n";
    } catch (Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    }
    
    // Test 6: New applications
    echo "\n6. New applications query:\n";
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tenant_applications ta
                               JOIN properties p ON ta.property_id = p.id
                               WHERE p.admin_id = ? AND ta.status = ?");
        $stmt->execute([$adminId, 'pending']);
        $count = $stmt->fetchColumn();
        echo "   ✅ SUCCESS: $count new applications\n";
    } catch (Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    }
    
    // Test 7: Monthly revenue
    echo "\n7. Monthly revenue query:\n";
    try {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments py
                               JOIN properties p ON py.property_id = p.id
                               WHERE p.admin_id = ? AND py.status = ? AND MONTH(py.payment_date) = MONTH(NOW()) AND YEAR(py.payment_date) = YEAR(NOW())");
        $stmt->execute([$adminId, 'paid']);
        $total = $stmt->fetchColumn();
        echo "   ✅ SUCCESS: ₦" . number_format($total, 2) . " monthly revenue\n";
    } catch (Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
}
?>
