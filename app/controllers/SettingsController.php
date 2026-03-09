<?php

namespace App\Controllers;

class SettingsController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Settings',
            'message' => 'Settings management module is coming soon.'
        ]);
    }
    
    public function update() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Settings update is not yet implemented.';
        $this->redirect('/admin/settings');
    }
}
