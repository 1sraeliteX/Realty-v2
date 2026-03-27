<?php

namespace App\Controllers;

class CalculatorController extends BaseController {
    /**
     * Show the calculator page
     */
    public function index() {
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Load calculator components through ComponentRegistry
        \ComponentRegistry::load('calculator-component');
        \ComponentRegistry::load('mortgage-calculator-component');
        \ComponentRegistry::load('roi-calculator-component');
        
        // Set page data through ViewManager
        \ViewManager::set('title', 'Calculator');
        \ViewManager::set('page_title', 'Calculator');
        \ViewManager::set('breadcrumb', [
            ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['name' => 'Calculator', 'url' => '/admin/calculator']
        ]);
        
        // Render the calculator page
        return $this->view('admin.calculator.index');
    }
}
