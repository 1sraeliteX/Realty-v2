<?php

namespace App\Models;

// Manually require database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

/**
 * Notification Model
 * 
 * Handles notification data operations for the Cornerstone Realty application.
 * Uses MySQL database with PDO for secure database operations.
 */
class NotificationModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get unread notification count for admin
     * 
     * @param int $adminId - Admin user ID
     * @return int - Number of unread notifications
     */
    public function getUnreadCount($adminId) {
        try {
            $query = "SELECT COUNT(*) as count FROM notifications WHERE admin_id = ? AND is_read = FALSE";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$adminId]);
            $result = $stmt->fetch();
            return (int) $result['count'];
            
        } catch (\Exception $e) {
            error_log("NotificationModel::getUnreadCount error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get recent notifications for admin
     * 
     * @param int $adminId - Admin user ID
     * @param int $limit - Maximum number of notifications to return
     * @return array - List of recent notifications
     */
    public function getRecent($adminId, $limit = 10) {
        try {
            $query = "
                SELECT id, type, title, message, is_read, link, created_at
                FROM notifications 
                WHERE admin_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$adminId, $limit]);
            return $stmt->fetchAll();
            
        } catch (\Exception $e) {
            error_log("NotificationModel::getRecent error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Mark notification as read
     * 
     * @param int $notificationId - Notification ID
     * @param int $adminId - Admin user ID for authorization
     * @return bool - Success status
     */
    public function markAsRead($notificationId, $adminId) {
        try {
            $query = "
                UPDATE notifications 
                SET is_read = TRUE, updated_at = NOW() 
                WHERE id = ? AND admin_id = ?
            ";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$notificationId, $adminId]);
            
        } catch (\Exception $e) {
            error_log("NotificationModel::markAsRead error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark all notifications as read for admin
     * 
     * @param int $adminId - Admin user ID
     * @return bool - Success status
     */
    public function markAllAsRead($adminId) {
        try {
            $query = "
                UPDATE notifications 
                SET is_read = TRUE, updated_at = NOW() 
                WHERE admin_id = ? AND is_read = FALSE
            ";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$adminId]);
            
        } catch (\Exception $e) {
            error_log("NotificationModel::markAllAsRead error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new notification
     * 
     * @param array $data - Notification data
     * @return int - New notification ID
     */
    public function create($data) {
        try {
            $query = "
                INSERT INTO notifications (
                    admin_id, type, title, message, link, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['admin_id'],
                $data['type'] ?? 'info',
                $data['title'],
                $data['message'],
                $data['link'] ?? null
            ]);
            
            return $this->db->lastInsertId();
            
        } catch (\Exception $e) {
            error_log("NotificationModel::create error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete notification
     * 
     * @param int $notificationId - Notification ID
     * @param int $adminId - Admin user ID for authorization
     * @return bool - Success status
     */
    public function delete($notificationId, $adminId) {
        try {
            $query = "DELETE FROM notifications WHERE id = ? AND admin_id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$notificationId, $adminId]);
            
        } catch (\Exception $e) {
            error_log("NotificationModel::delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get notification by ID
     * 
     * @param int $notificationId - Notification ID
     * @param int $adminId - Admin user ID for authorization
     * @return array|null - Notification data or null if not found
     */
    public function getById($notificationId, $adminId) {
        try {
            $query = "
                SELECT * FROM notifications 
                WHERE id = ? AND admin_id = ? 
                LIMIT 1
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$notificationId, $adminId]);
            $notification = $stmt->fetch();
            
            return $notification ?: null;
            
        } catch (\Exception $e) {
            error_log("NotificationModel::getById error: " . $e->getMessage());
            return null;
        }
    }
}
