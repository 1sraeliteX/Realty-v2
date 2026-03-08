<?php

namespace App\Controllers;

class LegacyController extends BaseController {
    public function redirectToAdminLogin() {
        header('Location: /admin/login');
        exit;
    }
    
    public function redirectToAdminRegister() {
        header('Location: /admin/register');
        exit;
    }
    
    public function redirectToAdminDashboard() {
        header('Location: /admin/dashboard');
        exit;
    }
}
