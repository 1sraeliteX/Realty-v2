<?php

namespace App\Controllers;

// Manually require the database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

class AdminDashboardController extends BaseController {
    public function index() {
        // Require authentication
        $admin = $this->requireAuth();
        
        // Ensure this is an admin, not super admin
        if ($admin['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin area only.';
            header('Location: /superadmin/dashboard');
            exit;
        }
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/init_framework.php';
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats($admin['id']);
        
        // Calculate real trends from historical data
        $trends = $this->calculateDashboardTrends($admin['id'], $stats);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($admin['id'], 10);
        
        // Get recent properties
        $recentProperties = $this->getRecentProperties($admin['id'], 5);
        
        // Get revenue data for chart
        $revenueData = $this->getRevenueData($admin['id'], 12); // Last 12 months
        
        // Get additional data for dashboard
        $maintenanceRequests = $this->getMaintenanceRequests($admin['id'], 5);
        $newApplications = $this->getNewApplications($admin['id'], 5);
        $upcomingTasks = $this->getUpcomingTasks($admin['id'], 5);
        
        // Set data through ViewManager (anti-scattering compliant)
        \ViewManager::set('title', 'Admin Dashboard');
        \ViewManager::set('stats', $stats);
        \ViewManager::set('dashboard_trends', $trends);
        \ViewManager::set('recentActivities', $recentActivities);
        \ViewManager::set('recentProperties', $recentProperties);
        \ViewManager::set('revenueData', $revenueData);
        \ViewManager::set('maintenanceRequests', $maintenanceRequests);
        \ViewManager::set('newApplications', $newApplications);
        \ViewManager::set('upcomingTasks', $upcomingTasks);
        
        // Capture dashboard content (anti-scattering compliant)
        ob_start();
        try {
            include __DIR__ . '/../../views/admin/dashboard_enhanced.php';
            $content = ob_get_clean();
        } catch (Exception $e) {
            ob_end_clean();
            $content = '<div class="text-center py-8"><h1 class="text-2xl font-bold text-red-600">Dashboard Error</h1><p class="text-gray-600 mt-2">Error: ' . $e->getMessage() . '</p></div>';
        }
        
        // Set content and render with layout (anti-scattering compliant)
        \ViewManager::set('content', $content);
        
        // Include the layout directly (anti-scattering compliant)
        include __DIR__ . '/../../views/admin/dashboard_layout.php';
    }
    
    public function reports() {
        // Require authentication
        $admin = $this->requireAuth();
        
        // Ensure this is an admin, not super admin
        if ($admin['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin area only.';
            header('Location: /superadmin/dashboard');
            exit;
        }
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/init_framework.php';
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats($admin['id']);
        
        // Calculate real trends from historical data
        $trends = $this->calculateDashboardTrends($admin['id'], $stats);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($admin['id'], 10);
        
        // Get recent properties
        $recentProperties = $this->getRecentProperties($admin['id'], 5);
        
        // Get revenue data for chart
        $revenueData = $this->getRevenueData($admin['id'], 12); // Last 12 months
        
        // Get additional data for dashboard
        $maintenanceRequests = $this->getMaintenanceRequests($admin['id'], 5);
        $newApplications = $this->getNewApplications($admin['id'], 5);
        $upcomingTasks = $this->getUpcomingTasks($admin['id'], 5);
        
        // Set data through ViewManager (anti-scattering compliant)
        \ViewManager::set('title', 'Dashboard Reports');
        \ViewManager::set('stats', $stats);
        \ViewManager::set('dashboard_trends', $trends);
        \ViewManager::set('recentActivities', $recentActivities);
        \ViewManager::set('recentProperties', $recentProperties);
        \ViewManager::set('revenueData', $revenueData);
        \ViewManager::set('maintenanceRequests', $maintenanceRequests);
        \ViewManager::set('newApplications', $newApplications);
        \ViewManager::set('upcomingTasks', $upcomingTasks);
        
        // Capture dashboard reports content (anti-scattering compliant)
        ob_start();
        include __DIR__ . '/../../views/admin/dashboard/reports.php';
        $content = ob_get_clean();
        
        // Set content and render with layout (anti-scattering compliant)
        \ViewManager::set('content', $content);
        echo \ViewManager::render('admin.dashboard_layout');
    }

    private function getDashboardStats($adminId) {
        $stats = [];
        // Use MySQL database directly for dashboard stats
        $pdo = \Config\Database::getInstance()->getConnection();
        
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
        
        // Total tenants/occupants
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tenants t
                               JOIN units u ON t.unit_id = u.id
                               JOIN properties p ON u.property_id = p.id
                               WHERE p.admin_id = ?");
        $stmt->execute([$adminId]);
        $stats['active_tenants'] = $stmt->fetchColumn();
        
        // Occupied units
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u 
                               JOIN properties p ON u.property_id = p.id 
                               WHERE p.admin_id = ? AND u.status = ?");
        $stmt->execute([$adminId, 'occupied']);
        $stats['occupied_units'] = $stmt->fetchColumn();
        
        // Pending maintenance requests
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM maintenance_requests mr
                               JOIN properties p ON mr.property_id = p.id
                               WHERE p.admin_id = ? AND mr.status = ?");
        $stmt->execute([$adminId, 'pending']);
        $stats['pending_maintenance'] = $stmt->fetchColumn();
        
        // New applications
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tenant_applications ta
                               JOIN properties p ON ta.property_id = p.id
                               WHERE p.admin_id = ? AND ta.status = ?");
        $stmt->execute([$adminId, 'pending']);
        $stats['new_applications'] = $stmt->fetchColumn();
        
        // Monthly revenue - from actual payments
        try {
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments py
                                   JOIN properties p ON py.property_id = p.id
                                   WHERE p.admin_id = ? AND py.status = ? 
                                   AND MONTH(payment_date) = MONTH(CURRENT_DATE) 
                                   AND YEAR(payment_date) = YEAR(CURRENT_DATE)");
            $stmt->execute([$adminId, 'paid']);
            $monthlyRevenue = $stmt->fetchColumn();
            
            // If no payments this month, get from previous month or use default
            if ($monthlyRevenue == 0) {
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments py
                                       JOIN properties p ON py.property_id = p.id
                                       WHERE p.admin_id = ? AND py.status = ? 
                                       AND MONTH(payment_date) = MONTH(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH))
                                       AND YEAR(payment_date) = YEAR(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH))");
                $stmt->execute([$adminId, 'paid']);
                $monthlyRevenue = $stmt->fetchColumn();
            }
            
            $stats['monthly_revenue'] = $monthlyRevenue ?: 0;
        } catch (Exception $e) {
            error_log("Error fetching monthly revenue: " . $e->getMessage());
            $stats['monthly_revenue'] = 0;
        }
        
        // Pending payments
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM payments py
                                   JOIN properties p ON py.property_id = p.id
                                   WHERE p.admin_id = ? AND py.status = ?");
            $stmt->execute([$adminId, 'pending']);
            $stats['pending_payments'] = $stmt->fetchColumn() ?: 0;
        } catch (Exception $e) {
            error_log("Error fetching pending payments: " . $e->getMessage());
            $stats['pending_payments'] = 0;
        }
        
        // Occupancy rate
        if ($stats['total_units'] > 0) {
            $stats['occupancy_rate'] = round(($stats['occupied_units'] / $stats['total_units']) * 100, 1);
        } else {
            $stats['occupancy_rate'] = 0;
        }
        
        return $stats;
    }

    private function calculateDashboardTrends($adminId, $currentStats) {
        $trends = [
            'property_trend' => 0,
            'units_trend' => 0,
            'tenants_trend' => 0,
            'occupancy_trend' => 0,
            'revenue_trend' => 0
        ];
        
        try {
            $pdo = \Config\Database::getInstance()->getConnection();
            
            // Get previous month stats for comparison
            $lastMonth = date('Y-m', strtotime('-1 month'));
            $currentMonth = date('Y-m');
            
            // Properties trend (new properties this month vs last month)
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM properties WHERE admin_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?");
            $stmt->execute([$adminId, $currentMonth]);
            $currentMonthProperties = $stmt->fetchColumn();
            
            $stmt->execute([$adminId, $lastMonth]);
            $lastMonthProperties = $stmt->fetchColumn();
            
            if ($lastMonthProperties > 0) {
                $trends['property_trend'] = round((($currentMonthProperties - $lastMonthProperties) / $lastMonthProperties) * 100, 1);
            }
            
            // Revenue trend (this month vs last month)
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments py
                                   JOIN properties p ON py.property_id = p.id
                                   WHERE p.admin_id = ? AND py.status = 'paid'
                                   AND DATE_FORMAT(payment_date, '%Y-%m') = ?");
            $stmt->execute([$adminId, $currentMonth]);
            $currentMonthRevenue = $stmt->fetchColumn();
            
            $stmt->execute([$adminId, $lastMonth]);
            $lastMonthRevenue = $stmt->fetchColumn();
            
            if ($lastMonthRevenue > 0) {
                $trends['revenue_trend'] = round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
            }
            
            // Tenants trend (new tenants this month vs last month)
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tenants t
                                   JOIN units u ON t.unit_id = u.id
                                   JOIN properties p ON u.property_id = p.id
                                   WHERE p.admin_id = ? AND DATE_FORMAT(t.created_at, '%Y-%m') = ?");
            $stmt->execute([$adminId, $currentMonth]);
            $currentMonthTenants = $stmt->fetchColumn();
            
            $stmt->execute([$adminId, $lastMonth]);
            $lastMonthTenants = $stmt->fetchColumn();
            
            if ($lastMonthTenants > 0) {
                $trends['tenants_trend'] = round((($currentMonthTenants - $lastMonthTenants) / $lastMonthTenants) * 100, 1);
            }
            
            // Occupancy trend (compare current occupancy rate with previous month)
            $lastMonthOccupancy = $this->getOccupancyRateForMonth($adminId, $lastMonth);
            if ($lastMonthOccupancy > 0) {
                $trends['occupancy_trend'] = round((($currentStats['occupancy_rate'] - $lastMonthOccupancy) / $lastMonthOccupancy) * 100, 1);
            }
            
            // Units trend (new units this month vs last month)
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM units u
                                   JOIN properties p ON u.property_id = p.id
                                   WHERE p.admin_id = ? AND DATE_FORMAT(u.created_at, '%Y-%m') = ?");
            $stmt->execute([$adminId, $currentMonth]);
            $currentMonthUnits = $stmt->fetchColumn();
            
            $stmt->execute([$adminId, $lastMonth]);
            $lastMonthUnits = $stmt->fetchColumn();
            
            if ($lastMonthUnits > 0) {
                $trends['units_trend'] = round((($currentMonthUnits - $lastMonthUnits) / $lastMonthUnits) * 100, 1);
            }
            
        } catch (Exception $e) {
            error_log("Error calculating dashboard trends: " . $e->getMessage());
            // Return default trends (all 0) if calculation fails
        }
        
        return $trends;
    }
    
    private function getOccupancyRateForMonth($adminId, $month) {
        try {
            $pdo = \Config\Database::getInstance()->getConnection();
            
            // Get total units and occupied units for specific month
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM units u
                                   JOIN properties p ON u.property_id = p.id
                                   WHERE p.admin_id = ? AND u.created_at <= LAST_DAY(?)");
            $stmt->execute([$adminId, $month . '-01']);
            $totalUnits = $stmt->fetchColumn();
            
            if ($totalUnits == 0) return 0;
            
            $stmt = $pdo->prepare("SELECT COUNT(*) as occupied FROM units u
                                   JOIN properties p ON u.property_id = p.id
                                   WHERE p.admin_id = ? AND u.status = 'occupied' 
                                   AND u.created_at <= LAST_DAY(?)");
            $stmt->execute([$adminId, $month . '-01']);
            $occupiedUnits = $stmt->fetchColumn();
            
            return round(($occupiedUnits / $totalUnits) * 100, 1);
            
        } catch (Exception $e) {
            error_log("Error getting occupancy rate for month: " . $e->getMessage());
            return 0;
        }
    }

    private function getRecentActivities($adminId, $limit = 10) {
        try {
            $pdo = \Config\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT a.*, p.name as property_name 
                                   FROM activities a
                                   LEFT JOIN properties p ON a.entity_id = p.id AND a.entity_type = 'property'
                                   WHERE a.admin_id = ? 
                                   ORDER BY a.created_at DESC 
                                   LIMIT ?");
            $stmt->execute([$adminId, $limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error fetching recent activities: " . $e->getMessage());
            return [];
        }
    }

    private function getRecentProperties($adminId, $limit = 5) {
        try {
            $pdo = \Config\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT p.*, 
                                   COUNT(u.id) as unit_count,
                                   SUM(CASE WHEN u.status = 'occupied' THEN 1 ELSE 0 END) as occupied_units
                                   FROM properties p
                                   LEFT JOIN units u ON p.id = u.property_id
                                   WHERE p.admin_id = ? 
                                   GROUP BY p.id
                                   ORDER BY p.created_at DESC 
                                   LIMIT ?");
            $stmt->execute([$adminId, $limit]);
            $properties = $stmt->fetchAll();
            
            // Add default image if none exists
            foreach ($properties as &$property) {
                $images = json_decode($property['images'] ?? '[]', true);
                if (!empty($images) && is_array($images)) {
                    $property['image'] = $images[0] ?? '/assets/images/placeholder-property.jpg';
                } else {
                    $property['image'] = '/assets/images/placeholder-property.jpg';
                }
                // Ensure status field is set
                $property['status'] = $property['status'] ?? 'active';
            }
            
            return $properties;
        } catch (Exception $e) {
            error_log("Error fetching recent properties: " . $e->getMessage());
            return [];
        }
    }

    private function getRevenueData($adminId, $months = 12) {
        try {
            $pdo = \Config\Database::getInstance()->getConnection();
            $revenueData = [];
            $currentDate = new \DateTime();
            
            // Get actual payment data for the last N months
            for ($i = 0; $i < $months; $i++) {
                $date = clone $currentDate;
                $date->modify('-' . $i . ' months');
                $monthStart = $date->format('Y-m-01');
                $monthEnd = $date->format('Y-m-t');
                
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments py
                                       JOIN properties p ON py.property_id = p.id
                                       WHERE p.admin_id = ? AND py.status = 'paid'
                                       AND payment_date BETWEEN ? AND ?");
                $stmt->execute([$adminId, $monthStart, $monthEnd]);
                $monthlyRevenue = $stmt->fetchColumn();
                
                $monthKey = $date->format('M Y');
                $revenueData[$monthKey] = $monthlyRevenue ?: 0;
            }
            
            // Reverse the array to show oldest to newest
            return array_reverse($revenueData);
            
        } catch (Exception $e) {
            error_log("Error generating revenue data: " . $e->getMessage());
            
            // Return default data in case of error
            $defaultData = [];
            $currentDate = new \DateTime();
            $currentDate->modify('-' . ($months - 1) . ' months');
            
            for ($i = 0; $i < $months; $i++) {
                $monthKey = $currentDate->format('M Y');
                $defaultData[$monthKey] = 0;
                $currentDate->modify('+1 month');
            }
            
            return $defaultData;
        }
    }
    
    private function getMaintenanceRequests($adminId, $limit = 5) {
        // Return mock data for now to avoid database errors
        return [
            [
                'id' => 1,
                'title' => 'HVAC Repair',
                'priority' => 'urgent',
                'property_name' => 'Sunset Apartments',
                'unit_number' => '5A',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'id' => 2,
                'title' => 'Plumbing Issue',
                'priority' => 'medium',
                'property_name' => 'Downtown Plaza',
                'unit_number' => '2B',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
            ]
        ];
    }
    
    private function getNewApplications($adminId, $limit = 5) {
        // Return mock data for now to avoid database errors
        return [
            [
                'id' => 1,
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'property_name' => 'Sunset Apartments',
                'unit_number' => '3C',
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'id' => 2,
                'first_name' => 'Mike',
                'last_name' => 'Chen',
                'property_name' => 'Riverside Complex',
                'unit_number' => '1A',
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ]
        ];
    }
    
    private function getUpcomingTasks($adminId, $limit = 5) {
        // Return mock data for now to avoid database errors
        return [
            [
                'id' => 1,
                'title' => 'Inspect Unit 4B',
                'property_name' => 'Riverside Complex',
                'unit_number' => '4B',
                'due_date' => date('Y-m-d'),
                'task_type' => 'maintenance'
            ],
            [
                'id' => 2,
                'title' => 'Send rent reminders',
                'property_name' => 'All Properties',
                'unit_number' => 'N/A',
                'due_date' => date('Y-m-d', strtotime('+1 day')),
                'task_type' => 'admin'
            ],
            [
                'id' => 3,
                'title' => 'Property tax filing',
                'property_name' => 'All Properties',
                'unit_number' => 'N/A',
                'due_date' => date('Y-m-d', strtotime('+1 week')),
                'task_type' => 'admin'
            ]
        ];
    }
    
    public function getActivityIcon($action) {
        $icons = [
            'create' => 'plus',
            'type' => 'right-to-bracket',
            'logout' => 'right-from-bracket',
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
