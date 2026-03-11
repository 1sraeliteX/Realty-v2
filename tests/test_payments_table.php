<?php
// Test database connection and payments table
require_once 'config/config_simple.php';

use Config\ConfigSimple;

try {
    // Get database configuration
    $config = ConfigSimple::getInstance();
    $dbConfig = $config->get('database');
    
    // Create database connection
    $pdo = new PDO(
        "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['name'] . ";charset=utf8mb4",
        $dbConfig['user'],
        $dbConfig['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<h2>Database Connection Test</h2>";
    echo "<p>✓ Connected to database: " . $dbConfig['name'] . "</p>";
    
    // Test if payments table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'payments'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "<p>✓ Payments table exists</p>";
        
        // Test the exact query from SuperAdminController
        try {
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = ?");
            $stmt->execute(['paid']);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>✓ Query executed successfully</p>";
            echo "<p>Total paid payments: $" . number_format($result['total'] ?? 0, 2) . "</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Query failed: " . $e->getMessage() . "</p>";
        }
        
        // Show table structure
        echo "<h3>Payments Table Structure:</h3>";
        $stmt = $pdo->prepare("DESCRIBE payments");
        $stmt->execute();
        $columns = $stmt->fetchAll();
        
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show sample data
        echo "<h3>Sample Data (first 5 rows):</h3>";
        $stmt = $pdo->prepare("SELECT * FROM payments LIMIT 5");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        
        if (count($rows) > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Amount</th><th>Status</th><th>Payment Type</th><th>Payment Date</th></tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>$" . number_format($row['amount'], 2) . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>" . $row['payment_type'] . "</td>";
                echo "<td>" . $row['payment_date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data in payments table</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Payments table does not exist</p>";
        
        // Show all tables
        echo "<h3>All tables in database:</h3>";
        $stmt = $pdo->prepare("SHOW TABLES");
        $stmt->execute();
        $tables = $stmt->fetchAll();
        
        echo "<ul>";
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            echo "<li>" . $tableName . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>";
}
?>
