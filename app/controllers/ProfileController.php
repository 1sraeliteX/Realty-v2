<?php

namespace App\Controllers;

class ProfileController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize framework (anti-scattering compliant)
        require_once __DIR__ . '/../../config/init_framework.php';
        
        // Set user data through ViewManager (anti-scattering compliant)
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@cornerstonerealty.com',
            'phone_number' => $admin['phone_number'] ?? '(555) 123-4567',
            'company' => 'Cornerstone Realty',
            'role_position' => 'Property Manager',
            'address' => '123 Main St, Anytown, USA',
            'bio' => 'Property management professional with over 10 years of experience in residential and commercial real estate.'
        ]);
        
        // Set additional profile data
        \ViewManager::set('propertiesManaged', 24);
        \ViewManager::set('activeTenants', 36);
        \ViewManager::set('monthlyRevenue', '$12,500');
        \ViewManager::set('pendingPayments', 3);
        
        // Include the profile view
        include __DIR__ . '/../../views/admin/profile/index.php';
    }
    
    public function update() {
        $admin = $this->requireAuth();
        
        // In a real implementation, this would update the database
        $_SESSION['success'] = 'Profile updated successfully!';
        $this->redirect('/admin/profile');
    }
}
