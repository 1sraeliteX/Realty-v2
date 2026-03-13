<?php

namespace App\Controllers;

class SuperAdminController extends BaseController {
    public function index() {
        // Require super admin authentication
        $admin = $this->requireSuperAdmin();
        
        // Get platform statistics
        $stats = $this->getPlatformStats();
        
        // Get recent admins
        $recentAdmins = $this->getRecentAdmins(5);
        
        // Get recent platform activities
        $recentActivities = $this->getPlatformActivities(10);
        
        // Get platform revenue data for charts
        $revenueData = $this->getPlatformRevenueData();
        
        // Get top performing properties
        $topProperties = $this->getTopProperties(5);
        
        // Calculate platform trends
        $trends = $this->calculatePlatformTrends($stats);
        
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set data through ViewManager (anti-scattering compliant)
        ViewManager::set('stats', $stats);
        ViewManager::set('recentAdmins', $recentAdmins);
        ViewManager::set('recentActivities', $recentActivities);
        ViewManager::set('revenueData', $revenueData);
        ViewManager::set('topProperties', $topProperties);
        ViewManager::set('platform_trends', $trends);
        ViewManager::set('user', $admin);
        ViewManager::set('title', 'Super Admin Dashboard');
        
        // Render with enhanced dashboard layout
        $content = $this->renderView('superadmin.dashboard_enhanced', [
            'stats' => $stats,
            'recentAdmins' => $recentAdmins,
            'recentActivities' => $recentActivities,
            'revenueData' => $revenueData,
            'topProperties' => $topProperties,
            'platform_trends' => $trends
        ]);
        
        $this->view('superadmin.dashboard_layout', [
            'admin' => $admin,
            'title' => 'Super Admin Dashboard',
            'content' => $content
        ]);
    }

    public function properties() {
        $admin = $this->requireSuperAdmin();
        
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $adminFilter = $_GET['admin_id'] ?? '';
        $status = $_GET['status'] ?? '';
        
        // Build query for all properties (superadmin can see all)
        $where = ["p.deleted_at IS NULL"];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(p.name LIKE ? OR p.address LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($adminFilter)) {
            $where[] = "p.admin_id = ?";
            $params[] = $adminFilter;
        }
        
        if (!empty($status)) {
            $where[] = "p.status = ?";
            $params[] = $status;
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT p.*, a.email as admin_email,
                       (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as unit_count
                FROM properties p 
                LEFT JOIN admins a ON p.admin_id = a.id
                WHERE {$whereClause}
                ORDER BY p.created_at DESC";
        
        $result = $this->paginate($sql, $page, 20, $params);
        
        // Get all admins for filter dropdown
        $admins = $this->db->query("SELECT id, email FROM admins WHERE role = 'admin' ORDER BY email")->fetchAll(PDO::FETCH_ASSOC);
        
        $this->view('superadmin.properties', [
            'admin' => $admin,
            'properties' => $result['data'],
            'pagination' => $result['pagination'],
            'admins' => $admins,
            'search' => $search,
            'adminFilter' => $adminFilter,
            'status' => $status
        ]);
    }

    public function admins() {
        $admin = $this->requireSuperAdmin();
        
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        
        $where = ["role = 'admin'"];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(email LIKE ? OR id LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT a.*, 
                       (SELECT COUNT(*) FROM properties p WHERE p.admin_id = a.id AND p.deleted_at IS NULL) as property_count,
                       (SELECT COUNT(*) FROM units u JOIN properties p ON u.property_id = p.id WHERE p.admin_id = a.id AND u.deleted_at IS NULL) as unit_count,
                       created_at as created_date
                FROM admins a 
                WHERE {$whereClause}
                ORDER BY a.created_at DESC";
        
        $result = $this->paginate($sql, $page, 20, $params);
        
        $this->view('superadmin.admins', [
            'admin' => $admin,
            'admins' => $result['data'],
            'pagination' => $result['pagination'],
            'search' => $search
        ]);
    }

    public function createAdmin() {
        $admin = $this->requireSuperAdmin();
        
        $this->view('superadmin.create_admin', [
            'admin' => $admin
        ]);
    }

    public function storeAdmin() {
        $admin = $this->requireSuperAdmin();
        $data = $this->getPostData();
        
        // Validate required fields
        $required = ['email', 'password', 'confirm_password'];
        $errors = $this->validateRequired($data, $required);
        
        if ($data['password'] !== $data['confirm_password']) {
            $errors['password'] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            $this->redirect('/superadmin/admins/create');
        }
        
        // Check if email already exists
        $existing = $this->db->fetch("SELECT id FROM admins WHERE email = ?", [$data['email']]);
        if ($existing) {
            $_SESSION['error'] = 'Email already exists';
            $_SESSION['old'] = $data;
            $this->redirect('/superadmin/admins/create');
        }
        
        // Create admin
        $adminData = [
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'admin',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $adminId = $this->db->insert('admins', $adminData);
        
        // Log activity
        $this->logActivity($admin['id'], 'create', "Created admin: {$data['email']}", 'admin', $adminId);
        
        $_SESSION['success'] = 'Admin created successfully!';
        $this->redirect('/superadmin/admins');
    }

    public function toggleAdminStatus($id) {
        $admin = $this->requireSuperAdmin();
        
        $targetAdmin = $this->db->fetch("SELECT id, email, status FROM admins WHERE id = ? AND role = 'admin'", [$id]);
        if (!$targetAdmin) {
            $_SESSION['error'] = 'Admin not found';
            $this->redirect('/superadmin/admins');
        }
        
        $newStatus = $targetAdmin['status'] === 'active' ? 'inactive' : 'active';
        $this->db->update('admins', ['status' => $newStatus], 'id = ?', [$id]);
        
        // Log activity
        $this->logActivity($admin['id'], 'update', "Changed admin status: {$targetAdmin['email']} to $newStatus", 'admin', $id);
        
        $_SESSION['success'] = "Admin status changed to $newStatus";
        $this->redirect('/superadmin/admins');
    }

    public function platformStats() {
        $admin = $this->requireSuperAdmin();
        
        $stats = $this->getPlatformStats();
        
        if ($this->isApiRequest()) {
            $this->json($stats);
        } else {
            $this->view('superadmin.stats', [
                'admin' => $admin,
                'stats' => $stats
            ]);
        }
    }

    private function getPlatformStats() {
        $stats = [];
        
        // Admin stats
        $stats['total_admins'] = $this->db->fetch("SELECT COUNT(*) as count FROM admins WHERE role = 'admin'")['count'];
        $stats['active_admins'] = $this->db->fetch("SELECT COUNT(*) as count FROM admins WHERE role = 'admin'")['count']; // All admins are considered active since no status column exists
        
        // Property stats
        $stats['total_properties'] = $this->db->fetch("SELECT COUNT(*) as count FROM properties WHERE deleted_at IS NULL")['count'];
        $stats['active_properties'] = $this->db->fetch("SELECT COUNT(*) as count FROM properties WHERE deleted_at IS NULL AND status = 'active'")['count'];
        
        // Unit stats
        $stats['total_units'] = $this->db->fetch("SELECT COUNT(*) as count FROM units WHERE deleted_at IS NULL")['count'];
        $stats['occupied_units'] = $this->db->fetch("SELECT COUNT(*) as count FROM units WHERE deleted_at IS NULL AND status = 'occupied'")['count'];
        
        // Calculate occupancy rate
        $stats['occupancy_rate'] = $stats['total_units'] > 0 ? round(($stats['occupied_units'] / $stats['total_units']) * 100, 1) : 0;
        
        // Tenant stats
        $stats['total_tenants'] = $this->db->fetch("SELECT COUNT(*) as count FROM tenants WHERE deleted_at IS NULL")['count'];
        $stats['active_tenants'] = $this->db->fetch("SELECT COUNT(*) as count FROM tenants WHERE deleted_at IS NULL AND status = 'active'")['count'];
        
        // Payment stats
        $stats['total_payments'] = $this->db->fetch("SELECT COUNT(*) as count FROM payments")['count'];
        $stats['total_revenue'] = $this->db->fetch("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'paid'")['total'] ?? 0;
        
        // Additional stats for dashboard
        $stats['pending_maintenance'] = $this->db->fetch("SELECT COUNT(*) as count FROM maintenance_requests WHERE status = 'pending'")['count'] ?? 0;
        $stats['new_applications'] = 0; // Set to 0 since applications table doesn't exist
        
        // Recent activity
        $stats['recent_properties'] = $this->db->fetch("SELECT COUNT(*) as count FROM properties WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND deleted_at IS NULL")['count'];
        $stats['recent_payments'] = $this->db->fetch("SELECT COUNT(*) as count FROM payments WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")['count'];
        
        return $stats;
    }

    private function getRecentAdmins($limit = 5) {
        return $this->db->query("SELECT id, email, name, created_at FROM admins WHERE role = 'admin' ORDER BY created_at DESC LIMIT $limit")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getPlatformActivities($limit = 10) {
        return $this->db->query("
            SELECT al.*, a.email as admin_email, a.name as admin_name
            FROM activity_logs al 
            LEFT JOIN admins a ON al.admin_id = a.id 
            ORDER BY al.created_at DESC 
            LIMIT $limit
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getPlatformRevenueData() {
        $revenueData = [];
        
        // Get last 12 months of revenue data
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthName = date('M Y', strtotime("-$i months"));
            
            $revenue = $this->db->fetch(
                "SELECT SUM(amount) as total FROM payments WHERE 
                 status = 'paid' AND 
                 DATE_FORMAT(created_at, '%Y-%m') = ?",
                [$month]
            )['total'] ?? 0;
            
            $revenueData[] = [
                'month' => $monthName,
                'amount' => (float) $revenue
            ];
        }
        
        return $revenueData;
    }

    private function getTopProperties($limit = 5) {
        return $this->db->query("
            SELECT p.*, a.name as admin_name, a.email as admin_email,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.deleted_at IS NULL) as total_units,
                   (SELECT COUNT(*) FROM units u WHERE u.property_id = p.id AND u.status = 'occupied' AND u.deleted_at IS NULL) as occupied_units,
                   (SELECT COALESCE(SUM(amount), 0) FROM payments pay 
                    JOIN units u ON pay.unit_id = u.id 
                    WHERE u.property_id = p.id AND pay.status = 'paid') as revenue
            FROM properties p 
            LEFT JOIN admins a ON p.admin_id = a.id 
            WHERE p.deleted_at IS NULL 
            ORDER BY revenue DESC 
            LIMIT $limit
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calculatePlatformTrends($currentStats) {
        $trends = [];
        
        // Get previous month data for comparison
        $lastMonth = date('Y-m', strtotime('-1 month'));
        $currentMonth = date('Y-m');
        
        // Properties trend
        $lastMonthProperties = $this->db->fetch(
            "SELECT COUNT(*) as count FROM properties WHERE deleted_at IS NULL AND DATE_FORMAT(created_at, '%Y-%m') = ?",
            [$lastMonth]
        )['count'] ?? 0;
        $currentMonthProperties = $this->db->fetch(
            "SELECT COUNT(*) as count FROM properties WHERE deleted_at IS NULL AND DATE_FORMAT(created_at, '%Y-%m') = ?",
            [$currentMonth]
        )['count'] ?? 0;
        $trends['property_trend'] = $lastMonthProperties > 0 ? round((($currentMonthProperties - $lastMonthProperties) / $lastMonthProperties) * 100, 1) : 0;
        
        // Units trend
        $lastMonthUnits = $this->db->fetch(
            "SELECT COUNT(*) as count FROM units WHERE deleted_at IS NULL AND DATE_FORMAT(created_at, '%Y-%m') = ?",
            [$lastMonth]
        )['count'] ?? 0;
        $currentMonthUnits = $this->db->fetch(
            "SELECT COUNT(*) as count FROM units WHERE deleted_at IS NULL AND DATE_FORMAT(created_at, '%Y-%m') = ?",
            [$currentMonth]
        )['count'] ?? 0;
        $trends['units_trend'] = $lastMonthUnits > 0 ? round((($currentMonthUnits - $lastMonthUnits) / $lastMonthUnits) * 100, 1) : 0;
        
        // Tenants trend
        $lastMonthTenants = $this->db->fetch(
            "SELECT COUNT(*) as count FROM tenants WHERE deleted_at IS NULL AND DATE_FORMAT(created_at, '%Y-%m') = ?",
            [$lastMonth]
        )['count'] ?? 0;
        $currentMonthTenants = $this->db->fetch(
            "SELECT COUNT(*) as count FROM tenants WHERE deleted_at IS NULL AND DATE_FORMAT(created_at, '%Y-%m') = ?",
            [$currentMonth]
        )['count'] ?? 0;
        $trends['tenants_trend'] = $lastMonthTenants > 0 ? round((($currentMonthTenants - $lastMonthTenants) / $lastMonthTenants) * 100, 1) : 0;
        
        // Admins trend
        $lastMonthAdmins = $this->db->fetch(
            "SELECT COUNT(*) as count FROM admins WHERE role = 'admin' AND DATE_FORMAT(created_at, '%Y-%m') = ?",
            [$lastMonth]
        )['count'] ?? 0;
        $currentMonthAdmins = $this->db->fetch(
            "SELECT COUNT(*) as count FROM admins WHERE role = 'admin' AND DATE_FORMAT(created_at, '%Y-%m') = ?",
            [$currentMonth]
        )['count'] ?? 0;
        $trends['admin_trend'] = $lastMonthAdmins > 0 ? round((($currentMonthAdmins - $lastMonthAdmins) / $lastMonthAdmins) * 100, 1) : 0;
        
        // Revenue trend
        $lastMonthRevenue = $this->db->fetch(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'paid' AND DATE_FORMAT(created_at, '%Y-%m') = ?",
            [$lastMonth]
        )['total'] ?? 0;
        $currentMonthRevenue = $this->db->fetch(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'paid' AND DATE_FORMAT(created_at, '%Y-%m') = ?",
            [$currentMonth]
        )['total'] ?? 0;
        $trends['revenue_trend'] = $lastMonthRevenue > 0 ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;
        
        return $trends;
    }

    public function exportData() {
        // Require super admin authentication
        $admin = $this->requireSuperAdmin();
        
        $format = $_GET['format'] ?? 'json';
        $data = $this->getPlatformExportData();
        
        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="platform_data.csv"');
            $this->generateCSV($data);
            exit;
        }
        
        $this->json($data);
    }

    private function getPlatformExportData() {
        return [
            'admins' => $this->db->query("SELECT id, email, role, status, created_at FROM admins")->fetchAll(PDO::FETCH_ASSOC),
            'properties' => $this->db->query("SELECT id, admin_id, name, status, created_at FROM properties WHERE deleted_at IS NULL")->fetchAll(PDO::FETCH_ASSOC),
            'units' => $this->db->query("SELECT id, property_id, status, rent_price FROM units WHERE deleted_at IS NULL")->fetchAll(PDO::FETCH_ASSOC),
            'payments' => $this->db->query("SELECT id, tenant_id, amount, status, created_at FROM payments WHERE deleted_at IS NULL")->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    private function generateCSV($data) {
        $output = fopen('php://output', 'w');
        
        foreach ($data as $table => $rows) {
            fputcsv($output, ["=== $table ==="]);
            if (!empty($rows)) {
                fputcsv($output, array_keys($rows[0]));
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
            }
            fputcsv($output, []);
        }
        
        fclose($output);
    }
}
