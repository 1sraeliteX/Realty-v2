<?php
/**
 * Create missing database tables for the admin dashboard
 */

require_once __DIR__ . '/config/database.php';

use Config\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    
    echo "Creating missing database tables...\n";
    
    // Create maintenance_requests table
    $maintenanceRequestsSQL = "
    CREATE TABLE IF NOT EXISTS `maintenance_requests` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `admin_id` int(11) NOT NULL,
      `property_id` int(11) NOT NULL,
      `unit_id` int(11) DEFAULT NULL,
      `tenant_id` int(11) DEFAULT NULL,
      `title` varchar(255) NOT NULL,
      `description` text DEFAULT NULL,
      `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
      `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
      `due_date` date DEFAULT NULL,
      `completed_date` date DEFAULT NULL,
      `assigned_to` varchar(255) DEFAULT NULL,
      `cost_estimate` decimal(10,2) DEFAULT NULL,
      `actual_cost` decimal(10,2) DEFAULT NULL,
      `images` json DEFAULT NULL,
      `notes` text DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `deleted_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `maintenance_requests_admin_id_index` (`admin_id`),
      KEY `maintenance_requests_property_id_index` (`property_id`),
      KEY `maintenance_requests_unit_id_index` (`unit_id`),
      KEY `maintenance_requests_tenant_id_index` (`tenant_id`),
      KEY `maintenance_requests_status_index` (`status`),
      KEY `maintenance_requests_priority_index` (`priority`),
      KEY `maintenance_requests_due_date_index` (`due_date`),
      KEY `maintenance_requests_deleted_at_index` (`deleted_at`),
      CONSTRAINT `maintenance_requests_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
      CONSTRAINT `maintenance_requests_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
      CONSTRAINT `maintenance_requests_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE,
      CONSTRAINT `maintenance_requests_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($maintenanceRequestsSQL);
    echo "✓ maintenance_requests table created\n";
    
    // Create tenant_applications table
    $tenantApplicationsSQL = "
    CREATE TABLE IF NOT EXISTS `tenant_applications` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `admin_id` int(11) NOT NULL,
      `property_id` int(11) NOT NULL,
      `unit_id` int(11) DEFAULT NULL,
      `first_name` varchar(100) NOT NULL,
      `last_name` varchar(100) NOT NULL,
      `email` varchar(255) NOT NULL,
      `phone` varchar(50) NOT NULL,
      `date_of_birth` date DEFAULT NULL,
      `nationality` varchar(100) DEFAULT NULL,
      `occupation` varchar(100) DEFAULT NULL,
      `employer` varchar(255) DEFAULT NULL,
      `monthly_income` decimal(10,2) DEFAULT NULL,
      `id_type` enum('nin','passport','drivers_license','voters_card','international_passport') DEFAULT 'nin',
      `id_number` varchar(100) DEFAULT NULL,
      `id_document` varchar(255) DEFAULT NULL,
      `emergency_contact_name` varchar(255) DEFAULT NULL,
      `emergency_contact_phone` varchar(50) DEFAULT NULL,
      `emergency_contact_relationship` varchar(100) DEFAULT NULL,
      `previous_landlord` varchar(255) DEFAULT NULL,
      `previous_landlord_phone` varchar(50) DEFAULT NULL,
      `reason_for_leaving` text DEFAULT NULL,
      `proposed_move_in_date` date DEFAULT NULL,
      `lease_duration_months` int(3) DEFAULT 12,
      `status` enum('pending','under_review','approved','rejected','withdrawn') DEFAULT 'pending',
      `review_notes` text DEFAULT NULL,
      `reviewed_by` int(11) DEFAULT NULL,
      `reviewed_at` timestamp NULL DEFAULT NULL,
      `application_fee` decimal(10,2) DEFAULT 0.00,
      `application_fee_paid` tinyint(1) DEFAULT 0,
      `documents` json DEFAULT NULL,
      `notes` text DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `deleted_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `tenant_applications_admin_id_index` (`admin_id`),
      KEY `tenant_applications_property_id_index` (`property_id`),
      KEY `tenant_applications_unit_id_index` (`unit_id`),
      KEY `tenant_applications_status_index` (`status`),
      KEY `tenant_applications_created_at_index` (`created_at`),
      KEY `tenant_applications_deleted_at_index` (`deleted_at`),
      CONSTRAINT `tenant_applications_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
      CONSTRAINT `tenant_applications_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
      CONSTRAINT `tenant_applications_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($tenantApplicationsSQL);
    echo "✓ tenant_applications table created\n";
    
    // Check if activities table exists and has the right structure
    $checkActivitiesSQL = "SHOW TABLES LIKE 'activities'";
    $result = $pdo->query($checkActivitiesSQL)->fetch();
    
    if ($result) {
        echo "✓ activities table already exists\n";
    } else {
        echo "! activities table not found - will use existing schema\n";
    }
    
    echo "\nDatabase tables created successfully!\n";
    
    // Insert some sample data for testing
    echo "\nInserting sample data for testing...\n";
    
    // Get first admin user
    $adminResult = $pdo->query("SELECT id FROM admins LIMIT 1")->fetch();
    if ($adminResult) {
        $adminId = $adminResult['id'];
        
        // Insert sample maintenance requests
        $sampleMaintenance = [
            $adminId, 1, 1, 1, 'HVAC Repair', 'Air conditioning unit not cooling properly', 'high', 'pending', 
            date('Y-m-d', strtotime('+2 days')), NULL, NULL, 15000.00, NULL, NULL, 'Tenant reported high temperature'
        ];
        
        $pdo->exec("INSERT INTO maintenance_requests (admin_id, property_id, unit_id, tenant_id, title, description, priority, status, due_date, completed_date, assigned_to, cost_estimate, actual_cost, images, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt = $pdo->prepare("INSERT INTO maintenance_requests (admin_id, property_id, unit_id, tenant_id, title, description, priority, status, due_date, completed_date, assigned_to, cost_estimate, actual_cost, images, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($sampleMaintenance);
        echo "✓ Sample maintenance request added\n";
        
        // Insert sample tenant application
        $sampleApplication = [
            $adminId, 1, 1, 'John', 'Doe', 'john.doe@email.com', '08012345678', '1990-01-01',
            'Nigerian', 'Software Engineer', 'Tech Corp', 500000.00, 'nin', '12345678901', NULL,
            'Jane Doe', '08098765432', 'Spouse', NULL, NULL, NULL,
            date('Y-m-d', strtotime('+1 month')), 12, 'pending', NULL, NULL, NULL, 5000.00, 1, NULL, NULL
        ];
        
        $stmt = $pdo->prepare("INSERT INTO tenant_applications (admin_id, property_id, unit_id, first_name, last_name, email, phone, date_of_birth, nationality, occupation, employer, monthly_income, id_type, id_number, id_document, emergency_contact_name, emergency_contact_phone, emergency_contact_relationship, previous_landlord, previous_landlord_phone, reason_for_leaving, proposed_move_in_date, lease_duration_months, status, review_notes, reviewed_by, reviewed_at, application_fee, application_fee_paid, documents, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($sampleApplication);
        echo "✓ Sample tenant application added\n";
    }
    
    echo "\nSetup complete! The dashboard should now display live data.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database configuration and try again.\n";
}
