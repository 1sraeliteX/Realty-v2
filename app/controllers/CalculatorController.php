<?php

namespace App\Controllers;

class CalculatorController extends BaseController {
    /**
     * Show the calculator page
     */
    public function index() {
        // Temporarily bypass all authentication for testing
        // TODO: Re-enable authentication after testing
        /*
        if (!headers_sent()) {
            if (!isset($_SESSION['admin_id']) && !isset($_SESSION['superadmin_id'])) {
                header('Location: /admin/login');
                exit;
            }
        }
        */

        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/init_framework.php';
        
        // Load calculator component
        \ComponentRegistry::load('calculator-component');
        
        // Set page data
        $data = [
            'title' => 'Calculator',
            'page_title' => 'Calculator',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['name' => 'Calculator', 'url' => '/admin/calculator']
            ]
        ];
        
        // Render the calculator page
        return $this->view('admin.calculator.index', $data);
    }
}
