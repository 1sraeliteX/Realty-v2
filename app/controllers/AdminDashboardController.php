<?php

namespace App\Controllers;

// Manually require the database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

class AdminDashboardController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Ensure this is an admin, not super admin
        if ($admin['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin area only.';
            header('Location: /superadmin/dashboard');
            exit;
        }
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats($admin['id']);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($admin['id'], 10);
        
        // Get recent properties
        $recentProperties = $this->getRecentProperties($admin['id'], 5);
        
        // Get revenue data for chart
        $revenueData = $this->getRevenueData($admin['id'], 12); // Last 12 months
        
        $this->view('dashboard.index', [
            'admin' => $admin,
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'recentProperties' => $recentProperties,
            'revenueData' => $revenueData
        ]);
    }

    private function getDashboardStats($adminId) {
        $stats = [];
        $pdo = $this->db->getConnection();
        
        // Total properties
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM properties WHERE admin_id = ?");
        $stmt->execute([$adminId]);
        $stats['total_properties'] = $stmt->fetchColumn();
        
        // Total units
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE p.admin_id = ?");
        $stmt->execute([$adminId]);
        $stats['total_units'] = $stmt->fetchColumn();
        
        // Active tenants
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tenants WHERE admin_id = ? AND rent_expiry_date >= ?");
        $stmt->execute([$adminId, date('Y-m-d')]);
        $stats['active_tenants'] = $stmt->fetchColumn();
        
        // Occupied units
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE p.admin_id = ? AND u.status = ?");
        $stmt->execute([$adminId, 'occupied']);
        $stats['occupied_units'] = $stmt->fetchColumn();
        
        // Monthly revenue
        $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM payments WHERE admin_id = ? AND status = ? AND MONTH(payment_date) = ? AND YEAR(payment_date) = ?");
        $stmt->execute([$adminId, 'paid', date('m'), date('Y')]);
        $stats['monthly_revenue'] = $stmt->fetchColumn() ?: 0;
        
        // Pending payments
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM payments WHERE admin_id = ? AND status = ?");
        $stmt->execute([$adminId, 'pending']);
        $stats['pending_payments'] = $stmt->fetchColumn();
        
        // Occupancy rate
        if ($stats['total_units'] > 0) {
            $stats['occupancy_rate'] = round(($stats['occupied_units'] / $stats['total_units']) * 100, 1);
        } else {
            $stats['occupancy_rate'] = 0;
        }
        
        return $stats;
    }

    private function getRecentActivities($adminId, $limit = 10) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM activities WHERE admin_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$adminId, $limit]);
        return $stmt->fetchAll();
    }

    private function getRecentProperties($adminId, $limit = 5) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM properties WHERE admin_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$adminId, $limit]);
        return $stmt->fetchAll();
    }

    private function getRevenueData($adminId, $months = 12) {
        $pdo = $this->db->getConnection();
        
        // Get payments for the last 12 months
        $stmt = $pdo->prepare("
            SELECT SUM(amount) as total, DATE_FORMAT(payment_date, '%Y-%m') as month 
            FROM payments 
            WHERE admin_id = ? AND status = ? AND payment_date >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
            ORDER BY month
        ");
        $stmt->execute([$adminId, 'paid', $months]);
        $results = $stmt->fetchAll();
        
        // Initialize all months with zero
        $revenueData = [];
        $currentDate = new \DateTime();
        $currentDate->modify('-' . ($months - 1) . ' months');
        
        for ($i = 0; $i < $months; $i++) {
            $monthKey = $currentDate->format('Y-m');
            $revenueData[$monthKey] = 0;
            $currentDate->modify('+1 month');
        }
        
        // Fill in actual revenue
        foreach ($results as $row) {
            $revenueData[$row['month']] = (float) $row['total'];
        }
        
        return $revenueData;
    }
    
    public function getActivityIcon($action) {
        $icons = [
            'create' => 'plus',
            'update' => 'edit',
            'delete' => 'trash',
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'view' => 'eye',
            'export' => 'download',
            'upload' => 'upload',
            'payment' => 'credit-card',
            'invoice' => 'file-invoice',
            'tenant' => 'user',
            'property' => 'home',
            'unit' => 'building',
            'maintenance' => 'tools'
        ];
        
        return $icons[$action] ?? 'circle';
    }
}
