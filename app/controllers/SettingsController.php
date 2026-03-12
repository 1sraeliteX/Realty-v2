<?php

namespace App\Controllers;

class SettingsController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        
        // Initialize anti-scattering system
        require_once __DIR__ . '/../../config/bootstrap.php';
        
        // Set user data for ViewManager (anti-scattering compliant)
        \ViewManager::set('user', [
            'name' => $admin['name'] ?? 'Admin User',
            'email' => $admin['email'] ?? 'admin@example.com'
        ]);
        
        // Centralize settings data in controller (anti-scattering compliant)
        \ViewManager::set('settings', [
            'general' => [
                'site_name' => 'Cornerstone Realty',
                'site_email' => 'admin@cornerstone.com',
                'site_phone' => '+1 (555) 123-4567',
                'site_address' => '123 Business Ave, Suite 100, City, State 12345',
                'timezone' => 'America/New_York',
                'currency' => 'USD',
                'date_format' => 'Y-m-d',
                'time_format' => '12-hour'
            ],
            'email' => [
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => '587',
                'smtp_username' => 'admin@cornerstone.com',
                'smtp_encryption' => 'tls',
                'email_from_name' => 'Cornerstone Realty',
                'email_from_address' => 'noreply@cornerstone.com'
            ],
            'security' => [
                'session_timeout' => '30',
                'password_min_length' => '8',
                'require_2fa' => false,
                'login_attempts' => '5',
                'lockout_duration' => '15'
            ],
            'notifications' => [
                'email_notifications' => true,
                'sms_notifications' => false,
                'payment_reminders' => true,
                'maintenance_alerts' => true,
                'new_application_alerts' => true
            ],
            'appearance' => [
                'default_theme' => 'dark',
                'primary_color' => '#3b82f6',
                'company_logo' => '/assets/images/logo.png',
                'favicon' => '/assets/images/favicon.ico'
            ]
        ]);
        
        // Set page title
        \ViewManager::set('title', 'Settings');
        
        // Include the settings view which handles its own rendering
        include __DIR__ . '/../../views/admin/settings/index.php';
    }
    
    public function update() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Settings update is not yet implemented.';
        $this->redirect('/admin/settings');
    }
}
