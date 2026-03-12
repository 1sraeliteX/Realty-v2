<?php
// Simple database test
require_once __DIR__ . '/config/database.php';

use Config\Database;

echo "<h2>Database Connection Test</h2>";

try {
    $pdo = Database::getInstance()->getConnection();
    echo "<p>✅ Database connection successful</p>";
    
    // Test basic query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "<p>✅ Basic query test: " . $result['test'] . "</p>";
    
    // Check properties table structure
    $stmt = $pdo->query("DESCRIBE properties");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Properties Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
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
    
    // Test inserting a sample property
    $testData = [
        'admin_id' => 1,
        'name' => 'Test Property ' . date('Y-m-d H:i:s'),
        'address' => '123 Test Street',
        'type' => 'residential',
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $columns = implode(', ', array_keys($testData));
    $placeholders = implode(', ', array_fill(0, count($testData), '?'));
    $values = array_values($testData);
    
    $sql = "INSERT INTO properties ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($values);
    
    if ($result) {
        $insertId = $pdo->lastInsertId();
        echo "<p>✅ Test property inserted with ID: $insertId</p>";
        
        // Try to select it back
        $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
        $stmt->execute([$insertId]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($property) {
            echo "<p>✅ Test property retrieved successfully</p>";
            echo "<pre>" . htmlspecialchars(json_encode($property, JSON_PRETTY_PRINT)) . "</pre>";
            
            // Clean up - delete test property
            $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
            $stmt->execute([$insertId]);
            echo "<p>✅ Test property cleaned up</p>";
        } else {
            echo "<p>❌ Failed to retrieve test property</p>";
        }
    } else {
        echo "<p>❌ Failed to insert test property</p>";
        echo "<p>Error: " . $stmt->errorInfo()[2] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='/admin/properties'>Back to Properties</a></p>";
?>
