<?php
// Create payments table script
require_once __DIR__ . '/config/database.php';

echo "<h1>Create Payments Table</h1>";

try {
    $pdo = \Config\Database::getInstance()->getConnection();
    
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/create_payments_table.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "<h2>Executing SQL Statements...</h2>";
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "<p style='color: green;'>✅ Executed: " . substr($statement, 0, 50) . "...</p>";
            } catch (Exception $e) {
                echo "<p style='color: orange;'>⚠️ Skipped (may already exist): " . substr($statement, 0, 50) . "...</p>";
            }
        }
    }
    
    echo "<h2 style='color: green;'>✅ Payments table created successfully!</h2>";
    
    // Verify table creation
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM payments");
    $count = $stmt->fetch()['count'];
    echo "<h3>Sample payments inserted: $count</h3>";
    
    // Show sample data
    $stmt = $pdo->query("SELECT * FROM payments LIMIT 5");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h4>Sample Payment Records:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Tenant ID</th><th>Amount</th><th>Status</th><th>Payment Date</th><th>Type</th></tr>";
    foreach ($payments as $payment) {
        echo "<tr>";
        echo "<td>{$payment['id']}</td>";
        echo "<td>{$payment['tenant_id']}</td>";
        echo "<td>\${$payment['amount']}</td>";
        echo "<td>{$payment['payment_status']}</td>";
        echo "<td>{$payment['payment_date']}</td>";
        echo "<td>{$payment['payment_type']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h2>Next Steps:</h2>";
    echo "<p>1. <a href='/admin/login'>Login to Admin Dashboard</a></p>";
    echo "<p>2. The dashboard should now display payment statistics correctly</p>";
    echo "<p>3. <a href='/check_database.php'>Verify all tables exist</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error creating payments table</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
