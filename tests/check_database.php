<?php
// Database table check script
require_once __DIR__ . '/config/database.php';

echo "<h1>Database Tables Check</h1>";

try {
    $pdo = \Config\Database::getInstance()->getConnection();
    
    echo "<h2>✅ Database Connection Successful</h2>";
    
    // Get all tables
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Existing Tables:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li style='color: green;'>✅ $table</li>";
    }
    echo "</ul>";
    
    // Check specifically for payments table
    if (in_array('payments', $tables)) {
        echo "<h3 style='color: green;'>✅ Payments table exists</h3>";
        
        // Show payments table structure
        $stmt = $pdo->query('DESCRIBE payments');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h4>Payments Table Structure:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show sample data
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM payments');
        $count = $stmt->fetch()['count'];
        echo "<h4>Payments Table Records: $count</h4>";
        
    } else {
        echo "<h3 style='color: red;'>❌ Payments table MISSING</h3>";
        echo "<p>This is causing the dashboard to fail when trying to load payment statistics.</p>";
    }
    
    // Check for other essential tables
    $essentialTables = ['properties', 'tenants', 'units', 'admins'];
    echo "<h3>Essential Tables Status:</h3>";
    foreach ($essentialTables as $table) {
        if (in_array($table, $tables)) {
            echo "<p style='color: green;'>✅ $table exists</p>";
        } else {
            echo "<p style='color: red;'>❌ $table missing</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Database Connection Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/'>Back to Home</a> | <a href='/admin/login'>Admin Login</a></p>";
?>
