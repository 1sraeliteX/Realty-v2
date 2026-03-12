<?php
/**
 * Add minimal sample data for dashboard testing
 */

require_once __DIR__ . '/config/database.php';

use Config\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    
    echo "Adding minimal sample data for dashboard testing...\n";
    
    // Get first admin user
    $adminResult = $pdo->query("SELECT id FROM admins LIMIT 1")->fetch();
    if (!$adminResult) {
        echo "No admin user found. Please create an admin first.\n";
        exit;
    }
    $adminId = $adminResult['id'];
    
    // Create sample property if none exists
    $propertyResult = $pdo->query("SELECT id FROM properties WHERE admin_id = $adminId LIMIT 1")->fetch();
    if (!$propertyResult) {
        $stmt = $pdo->prepare("INSERT INTO properties (admin_id, name, address, type, status, rent_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$adminId, 'Sunset Apartments', '123 Lagos Street, Ikoyi, Lagos', 'apartment', 'active', 500000.00]);
        $propertyId = $pdo->lastInsertId();
        echo "✓ Created sample property\n";
    } else {
        $propertyId = $propertyResult['id'];
    }
    
    // Create sample units if none exist
    $unitResult = $pdo->query("SELECT id FROM units WHERE property_id = $propertyId LIMIT 1")->fetch();
    if (!$unitResult) {
        $stmt = $pdo->prepare("INSERT INTO units (property_id, unit_number, type, rent_price, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$propertyId, '101', '1br', 250000.00, 'occupied']);
        $stmt->execute([$propertyId, '102', '1br', 250000.00, 'available']);
        $stmt->execute([$propertyId, '201', '2br', 350000.00, 'occupied']);
        echo "✓ Created sample units\n";
    }
    
    // Create sample maintenance requests
    $maintenanceCount = $pdo->query("SELECT COUNT(*) as count FROM maintenance_requests WHERE admin_id = $adminId")->fetchColumn();
    if ($maintenanceCount == 0) {
        $units = $pdo->query("SELECT id, unit_number FROM units WHERE property_id = $propertyId")->fetchAll();
        foreach ($units as $index => $unit) {
            $priorities = array('low', 'medium', 'high', 'urgent');
            $statuses = array('pending', 'in_progress', 'completed');
            $titles = array(
                'Air Conditioning Repair',
                'Plumbing Issue', 
                'Electrical Problem',
                'General Maintenance'
            );
            
            $stmt = $pdo->prepare("INSERT INTO maintenance_requests (admin_id, property_id, unit_id, title, description, priority, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $adminId,
                $propertyId,
                $unit['id'],
                $titles[$index % count($titles)],
                'Sample maintenance request for testing dashboard',
                $priorities[$index % count($priorities)],
                $statuses[$index % 2] // Alternate between pending and in_progress
            ]);
        }
        echo "✓ Created sample maintenance requests\n";
    }
    
    // Create sample tenant applications
    $applicationCount = $pdo->query("SELECT COUNT(*) as count FROM tenant_applications WHERE admin_id = $adminId")->fetchColumn();
    if ($applicationCount == 0) {
        $units = $pdo->query("SELECT id, unit_number FROM units WHERE property_id = $propertyId AND status = 'available'")->fetchAll();
        foreach ($units as $index => $unit) {
            $statuses = array('pending', 'under_review', 'approved', 'rejected');
            $stmt = $pdo->prepare("INSERT INTO tenant_applications (admin_id, property_id, unit_id, first_name, last_name, email, phone, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $adminId,
                $propertyId,
                $unit['id'],
                'Applicant',
                chr(65 + $index), // A, B, C...
                'applicant' . ($index + 1) . '@email.com',
                '0809876543' . $index,
                $statuses[$index % count($statuses)]
            ]);
        }
        echo "✓ Created sample tenant applications\n";
    }
    
    // Create sample activities
    $activityCount = $pdo->query("SELECT COUNT(*) as count FROM activities WHERE admin_id = $adminId")->fetchColumn();
    if ($activityCount == 0) {
        $activities = array(
            array('create', 'property', $propertyId, 'Created new property'),
            array('create', 'unit', 1, 'Added new unit'),
            array('create', 'maintenance', 1, 'Submitted maintenance request'),
            array('create', 'application', 1, 'Received tenant application'),
            array('login', 'user', $adminId, 'User logged in')
        );
        
        foreach ($activities as $index => $activity) {
            $stmt = $pdo->prepare("INSERT INTO activities (admin_id, action, entity_type, entity_id, description, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $adminId,
                $activity[0],
                $activity[1], 
                $activity[2],
                $activity[3],
                date('Y-m-d H:i:s', strtotime('-' . ($index * 2) . ' hours'))
            ]);
        }
        echo "✓ Created sample activities\n";
    }
    
    echo "\n✅ Sample data created successfully!\n";
    echo "The dashboard should now display live data.\n";
    echo "Visit http://127.0.0.1:8080/admin/dashboard to see the changes.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
