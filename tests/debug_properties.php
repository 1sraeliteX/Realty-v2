<?php
// Debug script to check property data
require_once __DIR__ . '/config/database.php';

use Config\Database;

echo "<h2>Property Debug Information</h2>";

try {
    $pdo = Database::getInstance()->getConnection();
    echo "<p>✅ Database connection successful</p>";
    
    // Check if properties table exists and has data
    $stmt = $pdo->query("SHOW TABLES LIKE 'properties'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p>✅ Properties table exists</p>";
        
        // Count total properties
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM properties");
        $totalProperties = $stmt->fetchColumn();
        echo "<p>Total properties in database: {$totalProperties}</p>";
        
        // Check properties with admin_id
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM properties WHERE admin_id IS NOT NULL");
        $propertiesWithAdmin = $stmt->fetchColumn();
        echo "<p>Properties with admin_id: {$propertiesWithAdmin}</p>";
        
        // Show recent properties
        $stmt = $pdo->query("SELECT id, name, admin_id, status, created_at, deleted_at FROM properties ORDER BY created_at DESC LIMIT 5");
        $recentProperties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Recent Properties:</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Admin ID</th><th>Status</th><th>Created</th><th>Deleted</th></tr>";
        
        foreach ($recentProperties as $property) {
            $deletedAt = $property['deleted_at'] ? 'Yes' : 'No';
            echo "<tr>";
            echo "<td>{$property['id']}</td>";
            echo "<td>" . htmlspecialchars($property['name']) . "</td>";
            echo "<td>{$property['admin_id']}</td>";
            echo "<td>{$property['status']}</td>";
            echo "<td>{$property['created_at']}</td>";
            echo "<td>{$deletedAt}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check current admin session
        session_start();
        if (isset($_SESSION['admin'])) {
            $adminId = $_SESSION['admin']['id'];
            echo "<p>Current admin ID: {$adminId}</p>";
            
            // Check properties for current admin
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM properties WHERE admin_id = ? AND deleted_at IS NULL");
            $stmt->execute([$adminId]);
            $adminProperties = $stmt->fetchColumn();
            echo "<p>Properties for current admin (not deleted): {$adminProperties}</p>";
            
            // Show admin's properties
            $stmt = $pdo->prepare("SELECT id, name, status, created_at FROM properties WHERE admin_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 5");
            $stmt->execute([$adminId]);
            $adminPropertiesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Current Admin's Properties:</h3>";
            if (empty($adminPropertiesList)) {
                echo "<p>No properties found for current admin</p>";
            } else {
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
                echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Created</th></tr>";
                foreach ($adminPropertiesList as $property) {
                    echo "<tr>";
                    echo "<td>{$property['id']}</td>";
                    echo "<td>" . htmlspecialchars($property['name']) . "</td>";
                    echo "<td>{$property['status']}</td>";
                    echo "<td>{$property['created_at']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "<p>❌ No admin session found</p>";
        }
        
    } else {
        echo "<p>❌ Properties table does not exist</p>";
    }
    
    // Check for any database errors
    $stmt = $pdo->query("SHOW ERRORS");
    $errors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($errors)) {
        echo "<h3>Database Errors:</h3>";
        echo "<pre>";
        print_r($errors);
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='/admin/properties'>Back to Properties</a></p>";
?>
