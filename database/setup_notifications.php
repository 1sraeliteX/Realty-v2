<?php
require_once __DIR__ . '/config/bootstrap.php';

echo "<h2>Setting up Notifications System</h2>";

try {
    // Read and execute the SQL
    $sql = file_get_contents(__DIR__ . '/database/create_notifications_table.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $pdo = $db->getConnection();
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            echo "<p>Executing: " . htmlspecialchars(substr($statement, 0, 50)) . "...</p>";
            $pdo->exec($statement);
        }
    }
    
    echo '<p class="text-green-600"><i class="fas fa-check-circle"></i> Notifications table created successfully!</p>';
    
    // Test the notification system
    echo '<h3>Testing Notification System</h3>';
    
    // Test creating a notification
    $success = NotificationController::create(
        1, // admin_id
        'System Test',
        'Notifications system is working correctly!',
        'success',
        'system_test'
    );
    
    if ($success) {
        echo '<p class="text-green-600"><i class="fas fa-check-circle"></i> Test notification created successfully!</p>';
    } else {
        echo '<p class="text-red-600"><i class="fas fa-times-circle"></i> Failed to create test notification</p>';
    }
    
} catch (Exception $e) {
    echo '<p class="text-red-600"><i class="fas fa-times-circle"></i> Error: ' . $e->getMessage() . '</p>';
}

echo '<p><a href="debugchecker.php" class="bg-blue-600 text-white px-4 py-2 rounded">View Debug Report</a></p>';
?>
