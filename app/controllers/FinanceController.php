<?php

namespace App\Controllers;

use DataProvider;
use ViewManager;

// Manually require the database configuration
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

class FinanceController extends BaseController {
    public function index() {
        // Require authentication
        $admin = $this->requireAuth();
        
        // Ensure this is an admin, not super admin
        if ($admin['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin area only.';
            header('Location: /superadmin/dashboard');
            exit;
        }
        
        // Get finance statistics
        $stats = $this->getFinanceStats($admin['id']);
        
        // Get recent transactions
        $recentTransactions = $this->getRecentTransactions($admin['id'], 10);
        
        // Get revenue data for chart
        $revenueData = $this->getRevenueData($admin['id'], 12);
        
        // Get expense data for chart
        $expenseData = $this->getExpenseData($admin['id'], 12);
        
        // Get upcoming payments
        $upcomingPayments = $this->getUpcomingPayments($admin['id'], 5);
        
        // Get overdue payments
        $overduePayments = $this->getOverduePayments($admin['id'], 5);
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/init_framework.php';
        
        // Load components through registry (anti-scattering compliant)
        \ComponentRegistry::load('ui-components');
        
        // Get data from centralized provider (anti-scattering compliant)
        $user = DataProvider::get('user');
        $notifications = DataProvider::get('notifications');
        
        // Set data through ViewManager (anti-scattering compliant)
        ViewManager::set('user', $user);
        ViewManager::set('notifications', $notifications);
        ViewManager::set('title', 'Finance Management');
        ViewManager::set('pageTitle', 'Finance Management');
        ViewManager::set('stats', $stats);
        ViewManager::set('recentTransactions', $recentTransactions);
        ViewManager::set('revenueData', $revenueData);
        ViewManager::set('expenseData', $expenseData);
        ViewManager::set('upcomingPayments', $upcomingPayments);
        ViewManager::set('overduePayments', $overduePayments);
        
        // Generate finance page content (anti-scattering compliant)
        ob_start();
        include __DIR__ . '/../../views/admin/finances/index.php';
        $content = ob_get_clean();
        
        // Set content for layout (anti-scattering compliant)
        ViewManager::set('content', $content);
        
        // Include the simple layout (without sidebar logout button)
        include __DIR__ . '/../../views/simple_layout.php';
    }
    
    private function getFinanceStats($adminId) {
        $stats = [];
        $pdo = \Config\Database::getInstance()->getConnection();
        
        // Total revenue (current month)
        try {
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE admin_id = ? AND status = ? AND MONTH(payment_date) = ? AND YEAR(payment_date) = ?");
            $stmt->execute([$adminId, 'paid', date('m'), date('Y')]);
            $stats['monthly_revenue'] = $stmt->fetchColumn() ?: 0;
        } catch (Exception $e) {
            $stats['monthly_revenue'] = 0;
        }
        
        // Total expenses (current month)
        try {
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE admin_id = ? AND MONTH(expense_date) = ? AND YEAR(expense_date) = ?");
            $stmt->execute([$adminId, date('m'), date('Y')]);
            $stats['monthly_expenses'] = $stmt->fetchColumn() ?: 0;
        } catch (Exception $e) {
            $stats['monthly_expenses'] = 0;
        }
        
        // Net profit
        $stats['net_profit'] = $stats['monthly_revenue'] - $stats['monthly_expenses'];
        
        // Pending payments
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total FROM payments WHERE admin_id = ? AND status = ?");
            $stmt->execute([$adminId, 'pending']);
            $result = $stmt->fetch();
            $stats['pending_payments_count'] = $result['count'] ?: 0;
            $stats['pending_payments_total'] = $result['total'] ?: 0;
        } catch (Exception $e) {
            $stats['pending_payments_count'] = 0;
            $stats['pending_payments_total'] = 0;
        }
        
        // Overdue payments
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total FROM payments WHERE admin_id = ? AND status = ? AND due_date < ?");
            $stmt->execute([$adminId, 'pending', date('Y-m-d')]);
            $result = $stmt->fetch();
            $stats['overdue_payments_count'] = $result['count'] ?: 0;
            $stats['overdue_payments_total'] = $result['total'] ?: 0;
        } catch (Exception $e) {
            $stats['overdue_payments_count'] = 0;
            $stats['overdue_payments_total'] = 0;
        }
        
        // Total collected (year to date)
        try {
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE admin_id = ? AND status = ? AND YEAR(payment_date) = ?");
            $stmt->execute([$adminId, 'paid', date('Y')]);
            $stats['yearly_revenue'] = $stmt->fetchColumn() ?: 0;
        } catch (Exception $e) {
            $stats['yearly_revenue'] = 0;
        }
        
        return $stats;
    }
    
    private function getRecentTransactions($adminId, $limit = 10) {
        $transactions = [];
        $pdo = \Config\Database::getInstance()->getConnection();
        
        // Get recent payments
        try {
            $stmt = $pdo->prepare("
                SELECT 'payment' as type, p.id, t.name as tenant_name, p.amount, p.payment_date as date, p.status, p.payment_method as method 
                FROM payments p
                LEFT JOIN tenants t ON p.tenant_id = t.id
                WHERE p.admin_id = ? 
                ORDER BY p.payment_date DESC 
                LIMIT ?
            ");
            $stmt->execute([$adminId, $limit]);
            $payments = $stmt->fetchAll();
            
            foreach ($payments as $payment) {
                $transactions[] = [
                    'type' => 'payment',
                    'description' => 'Rent payment from ' . $payment['tenant_name'],
                    'amount' => $payment['amount'],
                    'date' => $payment['date'],
                    'status' => $payment['status'],
                    'method' => $payment['method']
                ];
            }
        } catch (Exception $e) {
            // Handle error silently
        }
        
        // Get recent expenses
        try {
            $stmt = $pdo->prepare("
                SELECT 'expense' as type, id, description, amount, expense_date as date, category 
                FROM expenses 
                WHERE admin_id = ? 
                ORDER BY expense_date DESC 
                LIMIT ?
            ");
            $stmt->execute([$adminId, $limit]);
            $expenses = $stmt->fetchAll();
            
            foreach ($expenses as $expense) {
                $transactions[] = [
                    'type' => 'expense',
                    'description' => $expense['description'],
                    'amount' => -$expense['amount'], // Negative for expenses
                    'date' => $expense['date'],
                    'status' => 'paid',
                    'method' => $expense['category']
                ];
            }
        } catch (Exception $e) {
            // Handle error silently
        }
        
        // Sort by date
        usort($transactions, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($transactions, 0, $limit);
    }
    
    private function getRevenueData($adminId, $months = 12) {
        $pdo = \Config\Database::getInstance()->getConnection();
        
        try {
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
        } catch (Exception $e) {
            // Return empty array on error
            return [];
        }
    }
    
    private function getExpenseData($adminId, $months = 12) {
        $pdo = \Config\Database::getInstance()->getConnection();
        
        try {
            $stmt = $pdo->prepare("
                SELECT SUM(amount) as total, DATE_FORMAT(expense_date, '%Y-%m') as month 
                FROM expenses 
                WHERE admin_id = ? AND expense_date >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
                ORDER BY month
            ");
            $stmt->execute([$adminId, $months]);
            $results = $stmt->fetchAll();
            
            // Initialize all months with zero
            $expenseData = [];
            $currentDate = new \DateTime();
            $currentDate->modify('-' . ($months - 1) . ' months');
            
            for ($i = 0; $i < $months; $i++) {
                $monthKey = $currentDate->format('Y-m');
                $expenseData[$monthKey] = 0;
                $currentDate->modify('+1 month');
            }
            
            // Fill in actual expenses
            foreach ($results as $row) {
                $expenseData[$row['month']] = (float) $row['total'];
            }
            
            return $expenseData;
        } catch (Exception $e) {
            // Return empty array on error
            return [];
        }
    }
    
    private function getUpcomingPayments($adminId, $limit = 5) {
        $pdo = \Config\Database::getInstance()->getConnection();
        
        try {
            $stmt = $pdo->prepare("
                SELECT * FROM payments 
                WHERE admin_id = ? AND status = ? AND due_date >= ? AND due_date <= DATE_ADD(?, INTERVAL 30 DAY)
                ORDER BY due_date ASC 
                LIMIT ?
            ");
            $stmt->execute([$adminId, 'pending', date('Y-m-d'), date('Y-m-d'), $limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function getOverduePayments($adminId, $limit = 5) {
        $pdo = \Config\Database::getInstance()->getConnection();
        
        try {
            $stmt = $pdo->prepare("
                SELECT * FROM payments 
                WHERE admin_id = ? AND status = ? AND due_date < ?
                ORDER BY due_date ASC 
                LIMIT ?
            ");
            $stmt->execute([$adminId, 'pending', date('Y-m-d'), $limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}
