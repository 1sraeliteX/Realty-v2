<?php

namespace App\Helpers;

/**
 * Notification Helper
 * 
 * Provides static methods for creating notifications in the Cornerstone Realty application.
 * This helper makes it easy to create notifications from anywhere in the application.
 */
class NotificationHelper {
    
    /**
     * Create a new notification
     * 
     * @param int $adminId - Admin user ID
     * @param string $type - Notification type (info, success, warning, error)
     * @param string $title - Notification title
     * @param string $message - Notification message
     * @param string|null $link - Optional link for notification action
     * @return bool - Success status
     */
    public static function create($adminId, $type, $title, $message, $link = null) {
        try {
            $notificationModel = new \App\Models\NotificationModel();
            
            $data = [
                'admin_id' => $adminId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link
            ];
            
            $notificationModel->create($data);
            return true;
            
        } catch (\Exception $e) {
            error_log('NotificationHelper::create error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create tenant-related notification
     * 
     * @param int $adminId - Admin user ID
     * @param string $tenantName - Tenant name
     * @param string $action - Action performed (registered, updated, etc.)
     * @param int|null $tenantId - Optional tenant ID for link
     * @return bool - Success status
     */
    public static function createTenantNotification($adminId, $tenantName, $action, $tenantId = null) {
        $title = "Tenant {$action}";
        $message = "Tenant {$tenantName} has been {$action}";
        $link = $tenantId ? "/admin/tenants/{$tenantId}" : null;
        
        return self::create($adminId, 'info', $title, $message, $link);
    }
    
    /**
     * Create payment-related notification
     * 
     * @param int $adminId - Admin user ID
     * @param string $amount - Payment amount
     * @param string $tenantName - Tenant name
     * @param string $status - Payment status
     * @param int|null $paymentId - Optional payment ID for link
     * @return bool - Success status
     */
    public static function createPaymentNotification($adminId, $amount, $tenantName, $status, $paymentId = null) {
        $title = "Payment {$status}";
        $message = "Payment of {$amount} {$status} from {$tenantName}";
        $link = $paymentId ? "/admin/payments/{$paymentId}" : null;
        
        $type = $status === 'paid' ? 'success' : ($status === 'overdue' ? 'warning' : 'info');
        
        return self::create($adminId, $type, $title, $message, $link);
    }
    
    /**
     * Create maintenance-related notification
     * 
     * @param int $adminId - Admin user ID
     * @param string $maintenanceTitle - Maintenance request title
     * @param string $action - Action performed (created, updated, completed)
     * @param int|null $maintenanceId - Optional maintenance ID for link
     * @return bool - Success status
     */
    public static function createMaintenanceNotification($adminId, $maintenanceTitle, $action, $maintenanceId = null) {
        $title = "Maintenance {$action}";
        $message = "Maintenance request '{$maintenanceTitle}' has been {$action}";
        $link = $maintenanceId ? "/admin/maintenance/{$maintenanceId}" : null;
        
        $type = $action === 'completed' ? 'success' : ($action === 'created' ? 'info' : 'warning');
        
        return self::create($adminId, $type, $title, $message, $link);
    }
    
    /**
     * Create property-related notification
     * 
     * @param int $adminId - Admin user ID
     * @param string $propertyName - Property name
     * @param string $action - Action performed (created, updated, deleted)
     * @param int|null $propertyId - Optional property ID for link
     * @return bool - Success status
     */
    public static function createPropertyNotification($adminId, $propertyName, $action, $propertyId = null) {
        $title = "Property {$action}";
        $message = "Property '{$propertyName}' has been {$action}";
        $link = $propertyId ? "/admin/properties/{$propertyId}" : null;
        
        $type = $action === 'deleted' ? 'warning' : 'info';
        
        return self::create($adminId, $type, $title, $message, $link);
    }
    
    /**
     * Create system notification
     * 
     * @param int $adminId - Admin user ID
     * @param string $title - Notification title
     * @param string $message - Notification message
     * @param string $type - Notification type (default: info)
     * @return bool - Success status
     */
    public static function createSystemNotification($adminId, $title, $message, $type = 'info') {
        return self::create($adminId, $type, $title, $message, null);
    }
}
