<?php
// Test UI Components
require_once __DIR__ . '/../components/UIComponents.php';

echo "<h1>UI Components Test</h1>";

try {
    // Test statsCard
    $statsCard = UIComponents::statsCard('Test Card', '123', 'users', null, 'green');
    echo "<h2>Stats Card Test:</h2>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo $statsCard;
    echo "</div>";
    
    // Test avatar
    $avatar = UIComponents::avatar('John Doe', null, 'medium');
    echo "<h2>Avatar Test:</h2>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo $avatar;
    echo "</div>";
    
    // Test searchBar
    $searchBar = UIComponents::searchBar('Search...', '', 'testFunction()');
    echo "<h2>Search Bar Test:</h2>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo $searchBar;
    echo "</div>";
    
    echo "<h2>✅ All UI Components working correctly!</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error in UI Components:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
