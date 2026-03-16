<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use Config\Database;

class PaymentsController extends BaseController {
    private $paymentModel;
    
    public function __construct() {
        parent::__construct();
        $this->paymentModel = new PaymentModel();
    }
    
    /**
     * Display payments listing page
     */
    public function index() {
        // Require authentication
        $admin = $this->requireAuth();
        
        try {
            // Initialize framework (anti-scattering compliant)
            require_once __DIR__ . '/../../config/init_framework.php';
            
            // Get pagination parameters
            $page = max(1, intval($_GET['page'] ?? 1));
            $perPage = 10; // Match existing pagination style
            
            // Get filter parameters
            $filters = [
                'status' => $_GET['status'] ?? '',
                'search' => $_GET['search'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? ''
            ];
            
            // Determine user ID for data scoping
            $userId = null;
            if ($admin['role'] !== 'super_admin') {
                $userId = $admin['id']; // Regular admin sees only their own payments
            }
            
            // Get payments data
            $paymentsResult = $this->paymentModel->getAllPaginated($filters, $page, $perPage, $userId);
            $stats = $this->paymentModel->getSummaryStats($userId);
            
            // Set data through ViewManager (anti-scattering compliant)
            \ViewManager::set('title', 'Payments');
            \ViewManager::set('admin', $admin);
            \ViewManager::set('payments', $paymentsResult['data']);
            \ViewManager::set('pagination', $paymentsResult['pagination']);
            \ViewManager::set('stats', $stats);
            \ViewManager::set('filters', $filters);
            \ViewManager::set('pageTitle', 'Payments');
            \ViewManager::set('pageDescription', 'Track rent collections and payment history');
            
            // Capture payments content (anti-scattering compliant)
            ob_start();
            try {
                include __DIR__ . '/../../views/admin/payments/index.php';
                $content = ob_get_clean();
            } catch (Exception $e) {
                ob_end_clean();
                // Log error to debug checker
                $this->logErrorToDebugChecker('Payments View Error', $e->getMessage(), [
                    'admin_id' => $admin['id'],
                    'filters' => $filters,
                    'page' => $page
                ]);
                
                $content = '<div class="text-center py-8">
                    <h1 class="text-2xl font-bold text-red-600">Payments Error</h1>
                    <p class="text-gray-600 mt-2">Unable to load payments data. Please try again later.</p>
                    <p class="text-sm text-gray-500 mt-2">Error: ' . htmlspecialchars($e->getMessage()) . '</p>
                </div>';
            }
            
            // Set content and render with layout (anti-scattering compliant)
            \ViewManager::set('content', $content);
            
            // Include the layout directly (anti-scattering compliant)
            include __DIR__ . '/../../views/admin/dashboard_layout.php';
            
        } catch (Exception $e) {
            // Log critical error to debug checker
            $this->logErrorToDebugChecker('Payments Controller Critical Error', $e->getMessage(), [
                'admin_id' => $admin['id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            // Show graceful error page
            $this->showErrorPage('Unable to load payments. Please try again later.', $e->getMessage());
        }
    }
    
    /**
     * Show payment details
     */
    public function show($id) {
        $admin = $this->requireAuth();
        
        try {
            // Determine user ID for data scoping
            $userId = null;
            if ($admin['role'] !== 'super_admin') {
                $userId = $admin['id'];
            }
            
            // Get payment details
            $payment = $this->paymentModel->getById($id, $userId);
            
            if (!$payment) {
                $_SESSION['error'] = 'Payment not found.';
                $this->redirect('/admin/payments');
                return;
            }
            
            // Initialize framework
            require_once __DIR__ . '/../../config/init_framework.php';
            
            // Set data through ViewManager
            \ViewManager::set('title', 'Payment Details');
            \ViewManager::set('admin', $admin);
            \ViewManager::set('payment', $payment);
            
            // Capture payment details content
            ob_start();
            include __DIR__ . '/../../views/admin/payments/show.php';
            $content = ob_get_clean();
            
            \ViewManager::set('content', $content);
            include __DIR__ . '/../../views/admin/dashboard_layout.php';
            
        } catch (Exception $e) {
            $this->logErrorToDebugChecker('Payment Show Error', $e->getMessage(), [
                'payment_id' => $id,
                'admin_id' => $admin['id']
            ]);
            
            $_SESSION['error'] = 'Unable to load payment details.';
            $this->redirect('/admin/payments');
        }
    }
    
    /**
     * Update payment status (AJAX endpoint)
     */
    public function updateStatus($id) {
        $admin = $this->requireAuth();
        
        if (!$this->isApiRequest()) {
            $this->json(['error' => 'API endpoint only'], 400);
            return;
        }
        
        try {
            // Validate input
            $data = $this->getPostData();
            if (empty($data['status'])) {
                $this->json(['error' => 'Status is required'], 400);
                return;
            }
            
            // Validate status value
            $validStatuses = ['paid', 'pending', 'overdue', 'failed'];
            if (!in_array($data['status'], $validStatuses)) {
                $this->json(['error' => 'Invalid status'], 400);
                return;
            }
            
            // Determine user ID for authorization
            $userId = null;
            if ($admin['role'] !== 'super_admin') {
                $userId = $admin['id'];
            }
            
            // Prepare additional data
            $additionalData = [];
            if (!empty($data['payment_date'])) {
                $additionalData['payment_date'] = $data['payment_date'];
            }
            if (!empty($data['payment_method'])) {
                $additionalData['payment_method'] = $data['payment_method'];
            }
            if (!empty($data['receipt_reference'])) {
                $additionalData['receipt_reference'] = $data['receipt_reference'];
            } elseif (!empty($data['paystack_reference'])) {
                // Fallback for old form fields during transition
                $additionalData['receipt_reference'] = $data['paystack_reference'];
            }
            
            // Update payment status
            $success = $this->paymentModel->updateStatus($id, $data['status'], $additionalData, $userId);
            
            if ($success) {
                // Log activity
                $this->logActivity($admin['id'], 'update', "Updated payment status to {$data['status']}", 'payment', $id);
                
                $this->json([
                    'success' => true,
                    'message' => 'Payment status updated successfully'
                ]);
            } else {
                $this->json(['error' => 'Failed to update payment status'], 500);
            }
            
        } catch (Exception $e) {
            $this->logErrorToDebugChecker('Payment Update Status Error', $e->getMessage(), [
                'payment_id' => $id,
                'admin_id' => $admin['id'],
                'data' => $this->getPostData()
            ]);
            
            $this->json(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Delete payment (soft delete)
     */
    public function delete($id) {
        $admin = $this->requireAuth();
        
        try {
            // Determine user ID for authorization
            $userId = null;
            if ($admin['role'] !== 'super_admin') {
                $userId = $admin['id'];
            }
            
            // Delete payment
            $success = $this->paymentModel->delete($id, $userId);
            
            if ($success) {
                // Log activity
                $this->logActivity($admin['id'], 'delete', 'Deleted payment', 'payment', $id);
                
                $_SESSION['success'] = 'Payment deleted successfully.';
            } else {
                $_SESSION['error'] = 'Payment not found or access denied.';
            }
            
        } catch (Exception $e) {
            $this->logErrorToDebugChecker('Payment Delete Error', $e->getMessage(), [
                'payment_id' => $id,
                'admin_id' => $admin['id']
            ]);
            
            $_SESSION['error'] = 'Failed to delete payment.';
        }
        
        $this->redirect('/admin/payments');
    }
    
    /**
     * Log errors to debug checker page
     */
    private function logErrorToDebugChecker($title, $message, $context = []) {
        $errorData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'title' => $title,
            'message' => $message,
            'context' => $context,
            'stack_trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ];
        
        // Write to debug log file that gets displayed by debugchecker.php
        $logFile = __DIR__ . '/../../debug_errors.log';
        $logEntry = date('Y-m-d H:i:s') . " - " . json_encode($errorData) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Also log to PHP error log
        error_log("[$title] $message - Context: " . json_encode($context));
    }
    
    /**
     * Show graceful error page
     */
    private function showErrorPage($userMessage, $technicalMessage = '') {
        // Initialize framework
        require_once __DIR__ . '/../../config/init_framework.php';
        
        \ViewManager::set('title', 'Error - Payments');
        \ViewManager::set('userMessage', $userMessage);
        \ViewManager::set('technicalMessage', $technicalMessage);
        
        ob_start();
        ?>
        <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
            <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                </div>
                <h1 class="text-xl font-semibold text-center text-gray-900 dark:text-white mb-2">
                    Something went wrong
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-center mb-6">
                    <?php echo htmlspecialchars($userMessage); ?>
                </p>
                <div class="flex space-x-3">
                    <a href="/admin/payments" class="flex-1 text-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Try Again
                    </a>
                    <a href="/admin/dashboard" class="flex-1 text-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
        
        \ViewManager::set('content', $content);
        include __DIR__ . '/../../views/admin/dashboard_layout.php';
        exit;
    }
}
