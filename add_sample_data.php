<?php
/**
 * Add sample data for dashboard testing
 */

require_once __DIR__ . '/config/database.php';

use Config\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    
    echo "Adding sample data for dashboard testing...\n";
    
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
    
    // Create sample tenants if none exist
    $tenantResult = $pdo->query("SELECT t.id FROM tenants t JOIN units u ON t.unit_id = u.id WHERE u.property_id = $propertyId LIMIT 1")->fetch();
    if (!$tenantResult) {
        $units = $pdo->query("SELECT id FROM units WHERE property_id = $propertyId AND status = 'occupied'")->fetchAll();
        foreach ($units as $unit) {
            $stmt = $pdo->prepare("INSERT INTO tenants (admin_id, unit_id, name, email, phone, rent_start_date, rent_expiry_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $adminId, 
                $unit['id'], 
                'John Doe', 
                'john.doe@email.com', 
                '08012345678', 
                date('Y-m-d', strtotime('-6 months')),
                date('Y-m-d', strtotime('+6 months')),
                'active'
            ]);
        }
        echo "✓ Created sample tenants\n";
    }
    
    // Create sample maintenance requests
    $maintenanceCount = $pdo->query("SELECT COUNT(*) as count FROM maintenance_requests WHERE admin_id = $adminId")->fetchColumn();
    if ($maintenanceCount == 0) {
        $units = $pdo->query("SELECT id, unit_number FROM units WHERE property_id = $propertyId")->fetchAll();
        foreach ($units as $index => $unit) {
            $priorities = ['low', 'medium', 'high', 'urgent'];
            $statuses = ['pending', 'in_progress', 'completed'];
            $titles = [
                'Air Conditioning Repair',
                'Plumbing Issue', 
                'Electrical Problem',
                'General Maintenance'
            ];
            
            $stmt = $pdo->prepare("INSERT INTO maintenance_requests (admin_id, property_id, unit_id, title, description, priority, status, due_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $adminId,
                $propertyId,
                $unit['id'],
                $titles[$index % count($titles)],
                'Sample maintenance request for testing dashboard',
                $priorities[$index % count($priorities)],
                $statuses[$index % 2], // Alternate between pending and in_progress
                date('Y-m-d', strtotime('+' . ($index + 1) . ' days'))
            ]);
        }
        echo "✓ Created sample maintenance requests\n";
    }
    
    // Create sample tenant applications
    $applicationCount = $pdo->query("SELECT COUNT(*) as count FROM tenant_applications WHERE admin_id = $adminId")->fetchColumn();
    if ($applicationCount == 0) {
        $units = $pdo->query("SELECT id, unit_number FROM units WHERE property_id = $propertyId AND status = 'available'")->fetchAll();
        foreach ($units as $index => $unit) {
            $statuses = ['pending', 'under_review', 'approved', 'rejected'];
            $stmt = $pdo->prepare("INSERT INTO tenant_applications (admin_id, property_id, unit_id, first_name, last_name, email, phone, status, proposed_move_in_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $adminId,
                $propertyId,
                $unit['id'],
                'Applicant',
                chr(65 + $index), // A, B, C...
                'applicant' . ($index + 1) . '@email.com',
                '0809876543' . $index,
                $statuses[$index % count($statuses)],
                date('Y-m-d', strtotime('+' . ($index + 1) . ' weeks'))
            ]);
        }
        echo "✓ Created sample tenant applications\n";
    }
    
    // Create sample payments
    $paymentCount = $pdo->query("SELECT COUNT(*) as count FROM payments WHERE admin_id = $adminId")->fetchColumn();
    if ($paymentCount == 0) {
        $tenants = $pdo->query("SELECT t.id, p.id as property_id FROM tenants t JOIN units u ON t.unit_id = u.id JOIN properties p ON u.property_id = p.id WHERE p.admin_id = $adminId")->fetchAll();
        foreach ($tenants as $index => $tenant) {
            $stmt = $pdo->prepare("INSERT INTO payments (admin_id, tenant_id, property_id, amount, payment_type, payment_method, due_date, payment_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $adminId,
                $tenant['id'],
                $tenant['property_id'],
                250000.00,
                'rent',
                'bank_transfer',
                date('Y-m-d', strtotime('-' . ($index * 30) . ' days')),
                date('Y-m-d', strtotime('-' . ($index * 30) . ' days')),
                'paid'
            ]);
        }
        echo "✓ Created sample payments\n";
    }
    
    // Create sample activities
    $activityCount = $pdo->query("SELECT COUNT(*) as count FROM activities WHERE admin_id = $adminId")->fetchColumn();
    if ($activityCount == 0) {
        $activities = [
            ['create', 'property', $propertyId, 'Created new property'],
            ['create', 'unit', 1, 'Added new unit'],
            ['create', 'tenant', 1, 'Added new tenant'],
            ['create', 'maintenance', 1, 'Submitted maintenance request'],
            ['create', 'application', 1, 'Received tenant application'],
            ['payment', 'payment', 1, 'Recorded rent payment'],
            ['update', 'property', $propertyId, 'Updated property details'],
            ['login', 'user', $adminId, 'User logged in']
        ];
        
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
