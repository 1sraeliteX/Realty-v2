<?php

namespace App\Controllers;

class SuperAdminController extends BaseController {
    public function index() {
        $admin = $this->requireSuperAdmin();
        
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
        $admin = $this->requireSuperAdmin();
        
        $admins = $this->getAllAdmins();
        
        $this->view('superadmin.admins', [
            'admin' => $admin,
            'admins' => $admins
        ]);
    }

    public function exportData() {
        $admin = $this->requireSuperAdmin();
        
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
        
        // Total admins
        $sql = "SELECT COUNT(*) as total FROM admins WHERE deleted_at IS NULL";
        $result = $this->db->fetch($sql);
        $stats['total_admins'] = $result['total'];
        
        // Total properties
        $sql = "SELECT COUNT(*) as total FROM properties WHERE deleted_at IS NULL";
        $result = $this->db->fetch($sql);
        $stats['total_properties'] = $result['total'];
        
        // Active subscriptions (assuming admins with recent activity)
        $sql = "SELECT COUNT(DISTINCT admin_id) as total FROM properties 
                WHERE deleted_at IS NULL AND created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)";
        $result = $this->db->fetch($sql);
        $stats['active_subscriptions'] = $result['total'];
        
        // Platform revenue (sum of all payments)
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM payments 
                WHERE status = 'paid' AND deleted_at IS NULL";
        $result = $this->db->fetch($sql);
        $stats['platform_revenue'] = $result['total'];
        
        return $stats;
    }

    private function getRecentAdmins($limit = 5) {
        $sql = "SELECT id, name, email, business_name, role, created_at 
                FROM admins 
                WHERE deleted_at IS NULL 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }

    private function getPlatformActivities($limit = 10) {
        // For now, return recent admin registrations
        $sql = "SELECT 'admin_registered' as action, name as description, created_at 
                FROM admins 
                WHERE deleted_at IS NULL 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }

    private function getAllAdmins() {
        $sql = "SELECT id, name, email, business_name, phone, role, created_at 
                FROM admins 
                WHERE deleted_at IS NULL 
                ORDER BY created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    private function getPlatformExportData() {
        return [
            'admins' => $this->getAllAdmins(),
            'properties' => $this->db->fetchAll("SELECT * FROM properties WHERE deleted_at IS NULL"),
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
