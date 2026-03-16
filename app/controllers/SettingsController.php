<?php

namespace App\Controllers;

class SettingsController extends BaseController {
    
    private function ensureSettingsTable(): void {
        try {
            $pdo = $this->db->getConnection();
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS admin_settings (
                    id            INT AUTO_INCREMENT PRIMARY KEY,
                    admin_id      INT NOT NULL,
                    setting_key   VARCHAR(100) NOT NULL,
                    setting_value TEXT NULL,
                    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                  ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_admin_setting (admin_id, setting_key)
                )
            ");
        } catch (\Throwable $e) {
            error_log('ensureSettingsTable error: ' . $e->getMessage());
        }
    }

    private function getSetting(int $adminId, string $key, string $default = ''): string {
        try {
            $pdo  = $this->db->getConnection();
            $stmt = $pdo->prepare("
                SELECT setting_value FROM admin_settings
                WHERE admin_id = ? AND setting_key = ?
                LIMIT 1
            ");
            $stmt->execute([$adminId, $key]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ? (string)$row['setting_value'] : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    private function saveSetting(int $adminId, string $key, string $value): void {
        try {
            $pdo = $this->db->getConnection();
            $pdo->prepare("
                INSERT INTO admin_settings (admin_id, setting_key, setting_value)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    setting_value = VALUES(setting_value),
                    updated_at    = CURRENT_TIMESTAMP
            ")->execute([$adminId, $key, $value]);
        } catch (\Throwable $e) {
            error_log('saveSetting error: ' . $e->getMessage());
        }
    }

    public function index() {
        $admin = $this->requireAuth();
        $this->ensureSettingsTable();

        // Load all settings for this admin from DB
        $currency = $this->getSetting($admin['id'], 'currency', 'NGN');
        $currencySymbol = $this->getSetting($admin['id'], 'currency_symbol', '₦');

        \ViewManager::set('title', 'Settings');
        \ViewManager::set('user', $admin);
        \ViewManager::set('settings', [
            'currency'        => $currency,
            'currency_symbol' => $currencySymbol,
        ]);

        // Render settings view
        $this->view('admin/settings', [
            'admin'    => $admin,
            'currency' => $currency,
            'currency_symbol' => $currencySymbol,
        ]);
    }
    
    public function update() {
        $admin = $this->requireAuth();
        $this->ensureSettingsTable();
        $data  = $this->getPostData();

        // Save currency setting
        if (!empty($data['currency'])) {
            $currencyMap = [
                'NGN' => '₦',
                'USD' => '$',
                'GBP' => '£',
                'EUR' => '€',
                'GHS' => '₵',
                'KES' => 'KSh',
                'ZAR' => 'R',
            ];
            $code   = strtoupper($data['currency']);
            $symbol = $currencyMap[$code] ?? '₦';

            $this->saveSetting($admin['id'], 'currency', $code);
            $this->saveSetting($admin['id'], 'currency_symbol', $symbol);
        }

        // Save other settings as key-value pairs
        $allowedKeys = ['timezone', 'date_format', 'language', 'notifications_email'];
        foreach ($allowedKeys as $key) {
            if (isset($data[$key])) {
                $this->saveSetting($admin['id'], $key, $data[$key]);
            }
        }

        if ($this->isApiRequest()) {
            $this->json([
                'success' => true,
                'message' => 'Settings saved successfully',
                'currency_symbol' => $this->getSetting($admin['id'], 'currency_symbol', '₦'),
            ]);
        } else {
            $_SESSION['success'] = 'Settings saved successfully!';
            $this->redirect('/admin/settings');
        }
    }
}
