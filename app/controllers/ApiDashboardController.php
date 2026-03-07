<?php

namespace App\Controllers;

use App\Middleware\JwtMiddleware;

class ApiDashboardController extends BaseController {
    private $jwtMiddleware;

    public function __construct() {
        parent::__construct();
        $this->jwtMiddleware = new JwtMiddleware();
    }

    public function stats() {
        $admin = $this->jwtMiddleware->getCurrentUser();
        $stats = $this->getDashboardStats($admin['id']);
        
        $this->json($stats);
    }

    public function revenue() {
        $admin = $this->jwtMiddleware->getCurrentUser();
        $months = $_GET['months'] ?? 12;
        $revenueData = $this->getRevenueData($admin['id'], $months);
        
        $this->json($revenueData);
    }

    public function recentActivities() {
        $admin = $this->jwtMiddleware->getCurrentUser();
        $limit = $_GET['limit'] ?? 10;
        $recentActivities = $this->getRecentActivities($admin['id'], $limit);
        
        $this->json($recentActivities);
    }

    public function recentProperties() {
        $admin = $this->jwtMiddleware->getCurrentUser();
        $limit = $_GET['limit'] ?? 5;
        $recentProperties = $this->getRecentProperties($admin['id'], $limit);
        
        $this->json($recentProperties);
    }

    private function getDashboardStats($adminId) {
        $stats = [];
        
        // Total properties
        $sql = "SELECT COUNT(*) as total FROM properties WHERE admin_id = ? AND deleted_at IS NULL";
        $result = $this->db->fetch($sql, [$adminId]);
        $stats['total_properties'] = (int) $result['total'];
        
        // Total units
        $sql = "SELECT COUNT(*) as total FROM units u 
                JOIN properties p ON u.property_id = p.id 
                WHERE p.admin_id = ? AND u.deleted_at IS NULL AND p.deleted_at IS NULL";
        $result = $this->db->fetch($sql, [$adminId]);
        $stats['total_units'] = (int) $result['total'];
        
        // Active tenants
        $sql = "SELECT COUNT(*) as total FROM tenants t 
                JOIN properties p ON t.property_id = p.id 
                WHERE p.admin_id = ? AND t.deleted_at IS NULL AND p.deleted_at IS NULL 
                AND t.rent_expiry_date >= CURDATE()";
        $result = $this->db->fetch($sql, [$adminId]);
        $stats['active_tenants'] = (int) $result['total'];
        
        // Occupied units
        $sql = "SELECT COUNT(*) as total FROM units u 
                JOIN properties p ON u.property_id = p.id 
                WHERE p.admin_id = ? AND u.status = 'occupied' 
                AND u.deleted_at IS NULL AND p.deleted_at IS NULL";
        $result = $this->db->fetch($sql, [$adminId]);
        $stats['occupied_units'] = (int) $result['total'];
        
        // Monthly revenue
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM payments py 
                JOIN properties p ON py.property_id = p.id 
                WHERE p.admin_id = ? AND py.status = 'paid' 
                AND MONTH(py.payment_date) = MONTH(CURRENT_DATE) 
                AND YEAR(py.payment_date) = YEAR(CURRENT_DATE)";
        $result = $this->db->fetch($sql, [$adminId]);
        $stats['monthly_revenue'] = (float) $result['total'];
        
        // Pending payments
        $sql = "SELECT COUNT(*) as total FROM payments py 
                JOIN properties p ON py.property_id = p.id 
                WHERE p.admin_id = ? AND py.status = 'pending' 
                AND py.due_date <= CURDATE() AND py.deleted_at IS NULL";
        $result = $this->db->fetch($sql, [$adminId]);
        $stats['pending_payments'] = (int) $result['total'];
        
        // Occupancy rate
        if ($stats['total_units'] > 0) {
            $stats['occupancy_rate'] = round(($stats['occupied_units'] / $stats['total_units']) * 100, 1);
        } else {
            $stats['occupancy_rate'] = 0;
        }
        
        return $stats;
    }

    private function getRecentActivities($adminId, $limit = 10) {
        $sql = "SELECT a.*, p.name as property_name 
                FROM activities a 
                LEFT JOIN properties p ON a.entity_type = 'property' AND a.entity_id = p.id 
                WHERE a.admin_id = ? 
                ORDER BY a.created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$adminId, $limit]);
    }

    private function getRecentProperties($adminId, $limit = 5) {
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units
                FROM properties p 
                WHERE p.admin_id = ? AND p.deleted_at IS NULL 
                ORDER BY p.created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$adminId, $limit]);
    }

    private function getRevenueData($adminId, $months = 12) {
        $sql = "SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, 
                       SUM(amount) as revenue 
                FROM payments py 
                JOIN properties p ON py.property_id = p.id 
                WHERE p.admin_id = ? AND py.status = 'paid' 
                AND py.payment_date >= DATE_SUB(CURRENT_DATE, INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(payment_date, '%Y-%m') 
                ORDER BY month ASC";
        
        $results = $this->db->fetchAll($sql, [$adminId, $months]);
        
        // Fill in missing months with zero revenue
        $revenueData = [];
        $currentDate = new \DateTime();
        $currentDate->modify('-' . ($months - 1) . ' months');
        
        for ($i = 0; $i < $months; $i++) {
            $monthKey = $currentDate->format('Y-m');
            $revenueData[] = [
                'month' => $monthKey,
                'revenue' => 0
            ];
            $currentDate->modify('+1 month');
        }
        
        foreach ($results as $result) {
            foreach ($revenueData as &$data) {
                if ($data['month'] === $result['month']) {
                    $data['revenue'] = (float) $result['revenue'];
                    break;
                }
            }
        }
        
        return $revenueData;
    }
}
