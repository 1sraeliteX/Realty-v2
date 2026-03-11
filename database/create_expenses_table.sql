-- Missing expenses table for the FinanceController
-- Add this to your database schema

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` enum('maintenance','utilities','insurance','taxes','repairs','supplies','marketing','other') DEFAULT 'other',
  `expense_date` date NOT NULL,
  `payment_method` enum('cash','bank_transfer','check','online','mobile','credit_card') DEFAULT 'bank_transfer',
  `vendor` varchar(255) DEFAULT NULL,
  `receipt_reference` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_admin_id_index` (`admin_id`),
  KEY `expenses_category_index` (`category`),
  KEY `expenses_expense_date_index` (`expense_date`),
  KEY `expenses_deleted_at_index` (`deleted_at`),
  CONSTRAINT `expenses_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
