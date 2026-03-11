<?php

/**
 * ArrayHelper - Safe array access utility
 * Prevents "Undefined array key" errors across the entire application
 */
class ArrayHelper {
    
    /**
     * Safely get array value with default fallback
     */
    public static function get($array, $key, $default = null) {
        if (!is_array($array)) {
            return $default;
        }
        return $array[$key] ?? $default;
    }
    
    /**
     * Get nested array value safely
     */
    public static function getNested($array, $keys, $default = null) {
        if (!is_array($array)) {
            return $default;
        }
        
        $current = $array;
        foreach ((array)$keys as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return $default;
            }
            $current = $current[$key];
        }
        
        return $current;
    }
    
    /**
     * Get required value or throw exception
     */
    public static function require($array, $key, $message = null) {
        if (!is_array($array) || !array_key_exists($key, $array)) {
            throw new InvalidArgumentException($message ?? "Required array key '{$key}' is missing");
        }
        return $array[$key];
    }
    
    /**
     * Check if array has all required keys
     */
    public static function hasRequired($array, $requiredKeys) {
        if (!is_array($array)) {
            return false;
        }
        
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Get array with default values for missing keys
     */
    public static function withDefaults($array, $defaults) {
        if (!is_array($array)) {
            return $defaults;
        }
        
        return array_merge($defaults, $array);
    }
    
    /**
     * Format number from array safely
     */
    public static function formatNumber($array, $key, $decimals = 0, $default = '0') {
        $value = self::get($array, $key, 0);
        return number_format($value, $decimals);
    }
    
    /**
     * Get and escape HTML safely
     */
    public static function escape($array, $key, $default = '') {
        $value = self::get($array, $key, $default);
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Get superglobal value safely
     */
    public static function post($key, $default = null) {
        return self::get($_POST, $key, $default);
    }
    
    public static function getParam($key, $default = null) {
        return self::get($_GET, $key, $default);
    }
    
    public static function session($key, $default = null) {
        return self::get($_SESSION, $key, $default);
    }
    
    public static function request($key, $default = null) {
        return self::get($_REQUEST, $key, $default);
    }
}

// Global helper functions for quick access
function arr_get($array, $key, $default = null) {
    return ArrayHelper::get($array, $key, $default);
}

function arr_escape($array, $key, $default = '') {
    return ArrayHelper::escape($array, $key, $default);
}

function arr_format($array, $key, $decimals = 0, $default = '0') {
    return ArrayHelper::formatNumber($array, $key, $decimals, $default);
}
