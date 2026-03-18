<?php

/**
 * Currency Helper - Dynamic Currency Symbol Management
 * Provides centralized currency symbol retrieval for anti-scattering compliance
 */

class CurrencyHelper {
    
    /**
     * Get currency symbol for current admin
     * @param string $default Default symbol if none found
     * @return string Currency symbol
     */
    public static function getSymbol($default = '₦') {
        try {
            // Check if session has admin_id and DB is available
            if (isset($_SESSION['admin_id']) && isset($GLOBALS['db'])) {
                $pdo = $GLOBALS['db']->getConnection();
                $stmt = $pdo->prepare("
                    SELECT setting_value FROM admin_settings
                    WHERE admin_id = ? AND setting_key = 'currency_symbol'
                    LIMIT 1
                ");
                $stmt->execute([$_SESSION['admin_id']]);
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $row ? $row['setting_value'] : $default;
            }
        } catch (\Throwable $e) {
            error_log('CurrencyHelper::getSymbol error: ' . $e->getMessage());
        }
        return $default;
    }
    
    /**
     * Get currency code for current admin
     * @param string $default Default code if none found
     * @return string Currency code
     */
    public static function getCode($default = 'NGN') {
        try {
            if (isset($_SESSION['admin_id']) && isset($GLOBALS['db'])) {
                $pdo = $GLOBALS['db']->getConnection();
                $stmt = $pdo->prepare("
                    SELECT setting_value FROM admin_settings
                    WHERE admin_id = ? AND setting_key = 'currency'
                    LIMIT 1
                ");
                $stmt->execute([$_SESSION['admin_id']]);
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $row ? $row['setting_value'] : $default;
            }
        } catch (\Throwable $e) {
            error_log('CurrencyHelper::getCode error: ' . $e->getMessage());
        }
        return $default;
    }
    
    /**
     * Format amount with currency symbol
     * @param float $amount Amount to format
     * @param bool $formatNumber Whether to format with thousands separator
     * @return string Formatted amount with currency symbol
     */
    public static function formatAmount($amount, $formatNumber = true) {
        $symbol = self::getSymbol();
        if ($formatNumber) {
            return $symbol . number_format($amount, 2);
        }
        return $symbol . $amount;
    }
    
    /**
     * Get all available currencies
     * @return array Available currencies with symbols
     */
    public static function getAvailableCurrencies() {
        return [
            'NGN' => '₦',
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            'GHS' => '₵',
            'KES' => 'KSh',
            'ZAR' => 'R',
        ];
    }
}

?>
