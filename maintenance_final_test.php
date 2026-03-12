<?php
// Final verification test for maintenance page
echo "=== MAINTENANCE PAGE FINAL VERIFICATION ===\n\n";

// Mock authentication session
$_SESSION['admin'] = [
    'id' => 1,
    'name' => 'Test Admin',
    'email' => 'admin@test.com'
];

// Test the maintenance controller and view
try {
    // Initialize framework
    require_once __DIR__ . '/config/init_framework.php';
    
    // Simulate controller data
    \ViewManager::set('user', [
        'name' => 'Admin User',
        'email' => 'admin@cornerstone.com',
        'avatar' => null
    ]);
    \ViewManager::set('notifications', []);
    \ViewManager::set('title', 'Maintenance Management');
    
    $maintenanceRequests = [
        [
            'id' => 1,
            'title' => 'Leaky Faucet in Apartment 101',
            'property' => 'Sunset Apartments',
            'unit' => 'A-101',
            'tenant' => 'John Smith',
            'category' => 'Plumbing',
            'priority' => 'medium',
            'status' => 'pending',
            'date' => '2024-01-15',
            'assigned_to' => 'John Handyman',
            'description' => 'Kitchen sink faucet is leaking and needs to be repaired'
        ],
        [
            'id' => 2,
            'title' => 'HVAC System Maintenance',
            'property' => 'Ocean View Condos',
            'unit' => 'B-201',
            'tenant' => 'Sarah Johnson',
            'category' => 'HVAC',
            'priority' => 'high',
            'status' => 'in_progress',
            'date' => '2024-01-14',
            'assigned_to' => 'HVAC Pro Services',
            'description' => 'Annual HVAC system inspection and maintenance'
        ],
        [
            'id' => 3,
            'title' => 'Broken Window Lock',
            'property' => 'Mountain Heights',
            'unit' => 'C-301',
            'tenant' => 'Michael Brown',
            'category' => 'Structural',
            'priority' => 'low',
            'status' => 'completed',
            'date' => '2024-01-13',
            'assigned_to' => 'John Handyman',
            'description' => 'Bedroom window lock was broken and has been replaced'
        ]
    ];
    
    $maintenanceStats = [
        'total' => count($maintenanceRequests),
        'pending' => count(array_filter($maintenanceRequests, fn($r) => $r['status'] === 'pending')),
        'in_progress' => count(array_filter($maintenanceRequests, fn($r) => $r['status'] === 'in_progress')),
        'completed' => count(array_filter($maintenanceRequests, fn($r) => $r['status'] === 'completed'))
    ];
    
    \ViewManager::set('maintenanceStats', $maintenanceStats);
    \ViewManager::set('maintenanceRequests', $maintenanceRequests);
    
    ob_start();
    include __DIR__ . '/views/admin/maintenance/list.php';
    $output = ob_get_clean();
    
    echo "✅ Maintenance page loads successfully\n";
    echo "📏 Page length: " . number_format(strlen($output)) . " characters\n";
    
    // Check for styling consistency with dashboard
    $checks = [
        'breadcrumb navigation' => strpos($output, 'Dashboard') !== false,
        'proper layout container' => strpos($output, 'bg-gray-50 dark:bg-gray-900') !== false,
        'stats cards' => strpos($output, 'Total Requests') !== false,
        'table styling' => strpos($output, 'bg-white dark:bg-gray-800') !== false,
        'new request button' => strpos($output, 'New Request') !== false,
        'maintenance table' => strpos($output, 'Maintenance Requests') !== false,
        'dark mode support' => strpos($output, 'dark:') !== false,
        'responsive design' => strpos($output, 'md:grid-cols') !== false,
        'dashboard layout' => strpos($output, 'dashboard_layout.php') !== false,
        'javascript functionality' => strpos($output, 'deleteMaintenance') !== false
    ];
    
    echo "\n📋 Styling Consistency Checks:\n";
    foreach ($checks as $check => $passed) {
        echo "   " . ($passed ? '✅' : '❌') . " " . ucfirst($check) . "\n";
    }
    
    // Check for anti-scattering compliance
    echo "\n🔒 Anti-Scattering Compliance:\n";
    $antiScatteringChecks = [
        'uses init_framework.php' => strpos($output, 'init_framework.php') !== false,
        'no direct require_once' => strpos($output, 'require_once') === false || strpos($output, 'init_framework.php') !== false,
        'uses ViewManager properly' => strpos($output, '\\ViewManager::') !== false,
        'uses dashboard layout' => strpos($output, 'dashboard_layout.php') !== false
    ];
    
    foreach ($antiScatteringChecks as $check => $passed) {
        echo "   " . ($passed ? '✅' : '❌') . " " . ucfirst($check) . "\n";
    }
    
    // Check for errors
    echo "\n🚨 Error Checking:\n";
    if (strpos($output, 'Fatal error') !== false || strpos($output, 'Warning') !== false || strpos($output, 'Notice') !== false) {
        echo "   ❌ PHP errors detected in output\n";
    } else {
        echo "   ✅ No PHP errors detected\n";
    }
    
    // Check for duplicate content
    echo "\n🔄 Duplicate Content Check:\n";
    $tableCount = substr_count($output, '<table');
    $formCount = substr_count($output, '<form');
    echo "   📊 Tables found: $tableCount\n";
    echo "   📊 Forms found: $formCount\n";
    
    if ($tableCount <= 2 && $formCount <= 2) {
        echo "   ✅ No excessive duplicate content detected\n";
    } else {
        echo "   ⚠️  Possible duplicate content - review needed\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
?>
