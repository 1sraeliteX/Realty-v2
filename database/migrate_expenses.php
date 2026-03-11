<?php
// Database migration script to create the missing expenses table

require_once __DIR__ . '/../config/database.php';

use Config\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    
    echo "Creating expenses table...\n";
    
    // Read the expenses table creation SQL
    $sql = "
    CREATE TABLE IF NOT EXISTS `expenses` (
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
    ";
    
    $pdo->exec($sql);
    
    echo "✅ Expenses table created successfully!\n";
    
    // Insert some sample expense data for testing
    echo "Inserting sample expense data...\n";
    
    $sampleExpenses = [
        [
            'admin_id' => 3,
            'description' => 'Plumbing Repair - Unit 101',
            'amount' => 250.00,
            'category' => 'maintenance',
            'expense_date' => '2024-01-15',
            'payment_method' => 'bank_transfer',
            'vendor' => 'Quick Fix Plumbing',
            'notes' => 'Fixed leaking faucet in bathroom'
        ],
        [
            'admin_id' => 3,
            'description' => 'Building Insurance - Monthly',
            'amount' => 450.00,
            'category' => 'insurance',
            'expense_date' => '2024-01-01',
            'payment_method' => 'bank_transfer',
            'vendor' => 'SafeGuard Insurance',
            'notes' => 'Monthly property insurance premium'
        ],
        [
            'admin_id' => 3,
            'description' => 'Cleaning Supplies',
            'amount' => 85.50,
            'category' => 'supplies',
            'expense_date' => '2024-01-10',
            'payment_method' => 'cash',
            'vendor' => 'Office Depot',
            'notes' => 'Cleaning supplies for common areas'
        ]
    ];
    
    $insertSql = "
    INSERT INTO `expenses` (admin_id, description, amount, category, expense_date, payment_method, vendor, notes) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = $pdo->prepare($insertSql);
    
    foreach ($sampleExpenses as $expense) {
        $stmt->execute([
            $expense['admin_id'],
            $expense['description'],
            $expense['amount'],
            $expense['category'],
            $expense['expense_date'],
            $expense['payment_method'],
            $expense['vendor'],
            $expense['notes']
        ]);
    }
    
    echo "✅ Sample expense data inserted successfully!\n";
    echo "🎉 Database migration completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error creating expenses table: " . $e->getMessage() . "\n";
    exit(1);
}
?>
