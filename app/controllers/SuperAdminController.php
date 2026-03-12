<?php

namespace App\Controllers;

// Manually require database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

class SuperAdminController extends BaseController {
    public function index() {
        // Temporarily bypass authentication for development
        $admin = [
            'id' => 4,
            'name' => 'Super Administrator',
            'email' => 'superadmin@cornerstone.com',
            'role' => 'super_admin'
        ];
        
        // $admin = $this->requireSuperAdmin();
        
        // Get platform statistics
        $stats = $this->getPlatformStats();
        
        // Get recent admins
        $recentAdmins = $this->getRecentAdmins(5);
        
        // Get recent platform activities
        $recentActivities = $this->getPlatformActivities(10);
        
        $this->view('superadmin.dashboard', [
            'admin' => $admin,
            'stats' => $stats,
            'recentAdmins' => $recentAdmins,
            'recentActivities' => $recentActivities
        ]);
    }

    public function admins() {
        // Temporarily bypass authentication for development
        $admin = [
            'id' => 4,
            'name' => 'Super Administrator',
            'email' => 'superadmin@cornerstone.com',
            'role' => 'super_admin'
        ];
        
        // $admin = $this->requireSuperAdmin();
        
        $admins = $this->getAllAdmins();
        
        $this->view('superadmin.admins', [
            'admin' => $admin,
            'admins' => $admins
        ]);
    }

    public function exportData() {
        // Temporarily bypass authentication for development
        $admin = [
            'id' => 4,
            'name' => 'Super Administrator',
            'email' => 'superadmin@cornerstone.com',
            'role' => 'super_admin'
        ];
        
        // $admin = $this->requireSuperAdmin();
        
        $format = $_GET['format'] ?? 'json';
        $data = $this->getPlatformExportData();
        
        if ($format === 'csv') {
            $this->exportCsv($data);
        } else {
            $this->json($data);
        }
    }

    private function getPlatformStats() {
        $stats = [];
        // Use MySQL database directly for platform stats
        $pdo = \Config\Database::getInstance()->getConnection();
        
        try {
            // Total admins
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admins WHERE deleted_at IS NULL");
            $stmt->execute();
            $stats['total_admins'] = $stmt->fetchColumn() ?: 0;
            
            // Total properties
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM properties");
            $stmt->execute();
            $stats['total_properties'] = $stmt->fetchColumn() ?: 0;
            
            // Active subscriptions (assuming admins with recent activity)
            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT admin_id) as count FROM properties WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $stmt->execute();
            $stats['active_subscriptions'] = $stmt->fetchColumn() ?: 0;
            
            // Platform revenue (sum of all paid payments) - with defensive checks
            try {
                $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = ?");
                $stmt->execute(['paid']);
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $stats['platform_revenue'] = $result['total'] ?? 0;
            } catch (Exception $e) {
                // Log error and set default value
                error_log("Error fetching platform revenue: " . $e->getMessage());
                $stats['platform_revenue'] = 0;
            }
            
        } catch (Exception $e) {
            // Log error and return default stats
            error_log("Error in getPlatformStats: " . $e->getMessage());
            return [
                'total_admins' => 0,
                'total_properties' => 0,
                'active_subscriptions' => 0,
                'platform_revenue' => 0
            ];
        }
        
        return $stats;
    }

    private function getRecentAdmins($limit = 5) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT id, name, email, business_name, role, created_at FROM admins WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function getPlatformActivities($limit = 10) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT action, description, created_at FROM activities ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function getAllAdmins() {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT id, name, email, business_name, phone, role, created_at FROM admins WHERE deleted_at IS NULL ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getPlatformExportData() {
        $pdo = $this->db->getConnection();
        
        // Get all properties
        $stmt = $pdo->prepare("SELECT * FROM properties");
        $stmt->execute();
        $properties = $stmt->fetchAll();
        
        return [
            'admins' => $this->getAllAdmins(),
            'properties' => $properties,
            'stats' => $this->getPlatformStats(),
            'export_date' => date('Y-m-d H:i:s')
        ];
    }

    private function exportCsv($data) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="platform_data.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Export admins
        fputcsv($output, ['ADMINS']);
        fputcsv($output, ['ID', 'Name', 'Email', 'Business', 'Role', 'Created']);
        
        foreach ($data['admins'] as $admin) {
            fputcsv($output, [
                $admin['id'],
                $admin['name'],
                $admin['email'],
                $admin['business_name'],
                $admin['role'],
                $admin['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
}
