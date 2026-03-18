<?php

namespace App\Controllers;

class NotificationController extends BaseController {
    private $notificationModel;
    
    public function __construct() {
        parent::__construct();
        $this->notificationModel = new \App\Models\NotificationModel();
    }
    
    /**
     * Get unread notifications count for current admin
     */
    public function getUnreadCount() {
        $admin = $this->requireAuth();
        
        try {
            $count = $this->notificationModel->getUnreadCount($admin['id']);
            
            $this->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Error fetching notifications: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get recent notifications for dropdown
     */
    public function getRecent() {
        $admin = $this->requireAuth();
        
        try {
            $notifications = $this->notificationModel->getRecent($admin['id'], 10);
            
            // Format timestamps and add link field for compatibility
            foreach ($notifications as &$notification) {
                $notification['time_ago'] = $this->timeAgo($notification['created_at']);
                $notification['created_at'] = date('Y-m-d H:i:s', strtotime($notification['created_at']));
                $notification['activity_type'] = $notification['type'];
                $notification['related_id'] = null;
                $notification['related_type'] = null;
            }
            
            $this->json([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (\Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Error fetching notifications: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead() {
        $admin = $this->requireAuth();
        $data = $this->getPostData();
        $notificationId = $data['id'] ?? null;
        
        if (!$notificationId) {
            $this->json([
                'success' => false,
                'message' => 'Notification ID required'
            ], 400);
            return;
        }
        
        try {
            $success = $this->notificationModel->markAsRead($notificationId, $admin['id']);
            
            if ($success) {
                $this->json([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => 'Notification not found or already read'
                ], 404);
            }
        } catch (\Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Error marking notification as read: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead() {
        $admin = $this->requireAuth();
        
        try {
            $success = $this->notificationModel->markAllAsRead($admin['id']);
            
            // Get updated count
            $newCount = $this->notificationModel->getUnreadCount($admin['id']);
            
            $this->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'count' => $newCount
            ]);
        } catch (\Throwable $e) {
            $this->json([
                'success' => false,
                'message' => 'Error marking all notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create a new notification (called by other controllers)
     */
    public static function create($adminId, $title, $message, $type = 'info', $activityType = '', $relatedId = null, $relatedType = null) {
        try {
            global $db;
            if (!$db) {
                error_log('NotificationController::create - Database not available');
                return false;
            }
            
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO notifications (admin_id, title, message, type, activity_type, related_id, related_type)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$adminId, $title, $message, $type, $activityType, $relatedId, $relatedType]);
        } catch (\Throwable $e) {
            error_log('NotificationController::create error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Format time ago string
     */
    private function timeAgo($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' minutes ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' hours ago';
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . ' days ago';
        } else {
            return date('M j, Y', $time);
        }
    }
}
