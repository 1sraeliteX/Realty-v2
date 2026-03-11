-- Payments table creation for real estate management system
-- This table stores all payment transactions from tenants

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_type` varchar(50) DEFAULT 'rent',
  `reference` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_property_id` (`property_id`),
  KEY `idx_unit_id` (`unit_id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample payment data for testing
INSERT INTO `payments` (`tenant_id`, `property_id`, `unit_id`, `admin_id`, `amount`, `payment_method`, `payment_status`, `payment_type`, `reference`, `description`, `payment_date`, `due_date`, `paid_at`) VALUES
(1, 1, 1, 1, 1500.00, 'bank_transfer', 'paid', 'rent', 'REN-2024-001', 'Monthly rent for Unit 1A', '2024-01-15', '2024-01-01', '2024-01-15 10:30:00'),
(2, 1, 2, 1, 1200.00, 'cash', 'paid', 'rent', 'REN-2024-002', 'Monthly rent for Unit 1B', '2024-01-14', '2024-01-01', '2024-01-14 14:20:00'),
(3, 2, 3, 1, 1800.00, 'bank_transfer', 'paid', 'rent', 'REN-2024-003', 'Monthly rent for Unit 2A', '2024-01-16', '2024-01-01', '2024-01-16 09:15:00'),
(1, 1, 1, 1, 1500.00, 'bank_transfer', 'paid', 'rent', 'REN-2024-004', 'Monthly rent for Unit 1A', '2024-02-15', '2024-02-01', '2024-02-15 11:45:00'),
(2, 1, 2, 1, 1200.00, 'cash', 'pending', 'rent', 'REN-2024-005', 'Monthly rent for Unit 1B', NULL, '2024-02-01', NULL),
(3, 2, 3, 1, 1800.00, 'bank_transfer', 'paid', 'rent', 'REN-2024-006', 'Monthly rent for Unit 2A', '2024-02-16', '2024-02-01', '2024-02-16 08:30:00'),
(4, 3, 4, 1, 2000.00, 'bank_transfer', 'paid', 'rent', 'REN-2024-007', 'Monthly rent for Unit 3A', '2024-01-20', '2024-01-01', '2024-01-20 16:00:00'),
(5, 3, 5, 1, 1700.00, 'cash', 'paid', 'rent', 'REN-2024-008', 'Monthly rent for Unit 3B', '2024-01-18', '2024-01-01', '2024-01-18 13:45:00'),
(1, 1, 1, 1, 1500.00, 'bank_transfer', 'paid', 'rent', 'REN-2024-009', 'Monthly rent for Unit 1A', '2024-03-15', '2024-03-01', '2024-03-15 10:00:00'),
(3, 2, 3, 1, 1800.00, 'bank_transfer', 'pending', 'rent', 'REN-2024-010', 'Monthly rent for Unit 2A', NULL, '2024-03-01', NULL),
(4, 3, 4, 1, 2000.00, 'bank_transfer', 'paid', 'rent', 'REN-2024-011', 'Monthly rent for Unit 3A', '2024-02-20', '2024-02-01', '2024-02-20 15:30:00'),
(5, 3, 5, 1, 1700.00, 'cash', 'paid', 'rent', 'REN-2024-012', 'Monthly rent for Unit 3B', '2024-02-18', '2024-02-01', '2024-02-18 12:15:00');
