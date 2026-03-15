<?php
// Enhanced test script to verify property creation and display
require_once __DIR__ . '/config/bootstrap.php';

echo "<!DOCTYPE html><html><head><title>Property Creation Test</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;}.success{background:#d4edda;color:#155724;padding:10px;border:1px solid #c3e6cb;border-radius:5px;margin:10px 0;}.error{background:#f8d7da;color:#721c24;padding:10px;border:1px solid #f5c6cb;border-radius:5px;margin:10px 0;}.info{background:#d1ecf1;color:#0c5460;padding:10px;border:1px solid #bee5eb;border-radius:5px;margin:10px 0;}pre{background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;}</style></head><body>";

echo "<h1>🧪 Enhanced Property Creation Test</h1>";

// Get admin session
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo "<div class='error'>❌ Please <a href='/admin/login'>login first</a></div>";
    echo "</body></html>";
    exit;
}

$adminId = $_SESSION['admin_id'];
echo "<div class='success'>✅ Testing with Admin ID: $adminId</div>";

// Test database connection
try {
    $db = \Config\Database::getInstance();
    echo "<div class='success'>✅ Database connection OK</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Database connection failed: " . $e->getMessage() . "</div>";
    echo "</body></html>";
    exit;
}

// Test 1: Check properties table structure
echo "<h2>1. Properties Table Structure</h2>";
try {
    $result = $db->query("DESCRIBE properties");
    if ($result && $result->num_rows > 0) {
        echo "<div class='success'>✅ Properties table exists</div>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'><tr><th>Field</th><th>Type</th><th>Null</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='error'>❌ Properties table not found</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error checking table: " . $e->getMessage() . "</div>";
}

// Test 2: Simulate form submission
echo "<h2>2. Simulating Form Submission</h2>";
$testData = [
    'name' => 'Test Property ' . date('Y-m-d H:i:s'),
    'address' => '123 Test Street, Test City',
    'type' => 'apartment',
    'status' => 'active',
    'year_built' => '2020',
    'water_availability' => 'yes',
    'description' => 'This is a test property',
    'bedrooms' => '2',
    'bathrooms' => '2',
    'kitchens' => '1',
    'parking' => 'yes',
    'category' => 'residential'
];

echo "<h4>Test Form Data:</h4>";
echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>";

// Test 3: Direct database insertion
echo "<h2>3. Direct Database Insertion Test</h2>";
try {
    $propertyData = array_merge($testData, [
        'admin_id' => $adminId,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    $propertyId = $db->insert('properties', $propertyData);
    
    if ($propertyId) {
        echo "<div class='success'>✅ Property created successfully (ID: $propertyId)</div>";
        
        // Verify the property was saved
        $stmt = $db->prepare("SELECT * FROM properties WHERE id = ?");
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $savedProperty = $result->fetch_assoc();
            echo "<div class='success'>✅ Property verified in database</div>";
            echo "<h4>Saved Property:</h4>";
            echo "<pre>" . json_encode($savedProperty, JSON_PRETTY_PRINT) . "</pre>";
            
            // Clean up
            $deleteStmt = $db->prepare("DELETE FROM properties WHERE id = ?");
            $deleteStmt->bind_param("i", $propertyId);
            $deleteStmt->execute();
            echo "<div class='info'>ℹ️ Test property cleaned up</div>";
        } else {
            echo "<div class='error'>❌ Property not found after insertion</div>";
        }
    } else {
        echo "<div class='error'>❌ Failed to insert property</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Insertion failed: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 4: Check PropertyController
echo "<h2>4. PropertyController Test</h2>";
if (file_exists(__DIR__ . '/app/controllers/PropertyController.php')) {
    echo "<div class='success'>✅ PropertyController exists</div>";
    
    try {
        $controller = new \App\Controllers\PropertyController();
        echo "<div class='success'>✅ PropertyController instantiated</div>";
        
        // Test the store method by mocking POST data
        $_POST = $testData;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        echo "<div class='info'>ℹ️ Testing store method...</div>";
        
        // This will redirect or output JSON, so we capture it
        ob_start();
        try {
            $controller->store();
        } catch (Exception $e) {
            echo "<div class='error'>❌ Store method error: " . $e->getMessage() . "</div>";
        }
        $output = ob_get_clean();
        
        if (!empty($output)) {
            echo "<h4>Controller Output:</h4>";
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Controller error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ PropertyController not found</div>";
}

echo "<h2>📋 Test Summary</h2>";
echo "<p><a href='/admin/properties/create'>📝 Test Manual Form Submission</a></p>";
echo "<p><a href='/admin/properties'>🏠 View Properties List</a></p>";
echo "<p><a href='/debugchecker.php'>🔍 Run Full Debug Checker</a></p>";
echo "<p><em>Test completed: " . date('Y-m-d H:i:s') . "</em></p>";
echo "</body></html>";
?>
