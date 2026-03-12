<?php

/**
 * Activity Logger - Anti-Scattering Compliant
 * Logs user activities for dashboard feed
 */

class ActivityLogger {
    
    /**
     * Log an activity to the database
     * 
     * @param int $adminId Admin user ID
     * @param string $action Action performed (create, update, delete, etc.)
     * @param string $entityType Type of entity (property, tenant, maintenance, etc.)
     * @param int $entityId ID of the entity
     * @param string $description Human-readable description
     * @param array $metadata Additional metadata (JSON encoded)
     * @return bool Success status
     */
    public static function log($adminId, $action, $entityType, $entityId, $description, $metadata = []) {
        try {
            $pdo = \Config\Database::getInstance()->getConnection();
            
            $stmt = $pdo->prepare("
                INSERT INTO activities (admin_id, action, entity_type, entity_id, description, metadata, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $metadataJson = !empty($metadata) ? json_encode($metadata) : null;
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            
            return $stmt->execute([
                $adminId,
                $action,
                $entityType,
                $entityId,
                $description,
                $metadataJson,
                $ipAddress,
                $userAgent
            ]);
            
        } catch (Exception $e) {
            error_log("Failed to log activity: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log property-related activity
     */
    public static function logProperty($adminId, $action, $propertyId, $propertyName) {
        $description = ucfirst($action) . " property '{$propertyName}'";
        return self::log($adminId, $action, 'property', $propertyId, $description);
    }
    
    /**
     * Log tenant-related activity
     */
    public static function logTenant($adminId, $action, $tenantId, $tenantName) {
        $description = ucfirst($action) . " tenant '{$tenantName}'";
        return self::log($adminId, $action, 'tenant', $tenantId, $description);
    }
    
    /**
     * Log maintenance-related activity
     */
    public static function logMaintenance($adminId, $action, $maintenanceId, $maintenanceTitle) {
        $description = ucfirst($action) . " maintenance request '{$maintenanceTitle}'";
        return self::log($adminId, $action, 'maintenance', $maintenanceId, $description);
    }
    
    /**
     * Log payment-related activity
     */
    public static function logPayment($adminId, $action, $paymentId, $amount, $property_name = null) {
        $description = ucfirst($action) . " payment of N" . number_format($amount, 2);
        if ($property_name) {
            $description .= " for '{$property_name}'";
        }
        return self::log($adminId, $action, 'payment', $paymentId, $description);
    }
    
    /**
     * Log application-related activity
     */
    public static function logApplication($adminId, $action, $applicationId, $applicantName) {
        $description = ucfirst($action) . " application from '{$applicantName}'";
        return self::log($adminId, $action, 'application', $applicationId, $description);
    }
    
    /**
     * Log unit-related activity
     */
    public static function logUnit($adminId, $action, $unitId, $unitNumber, $property_name = null) {
        $description = ucfirst($action) . " unit '{$unitNumber}'";
        if ($property_name) {
            $description .= " in '{$property_name}'";
        }
        return self::log($adminId, $action, 'unit', $unitId, $description);
    }
    
    /**
     * Log login activity
     */
    public static function logLogin($adminId, $adminName) {
        $description = "User '{$adminName}' logged in";
        return self::log($adminId, 'login', 'user', $adminId, $description);
    }
    
    /**
     * Log logout activity
     */
    public static function logLogout($adminId, $adminName) {
        $description = "User '{$adminName}' logged out";
        return self::log($adminId, 'logout', 'user', $adminId, $description);
    }
}
