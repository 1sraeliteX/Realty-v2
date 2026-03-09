<?php

namespace App\Controllers;

class ProfileController extends BaseController {
    public function index() {
        $admin = $this->requireAuth();
        $this->view('simple.placeholder', [
            'admin' => $admin,
            'title' => 'Profile',
            'message' => 'Profile management module is coming soon.'
        ]);
    }
    
    public function update() {
        $admin = $this->requireAuth();
        $_SESSION['info'] = 'Profile update is not yet implemented.';
        $this->redirect('/admin/profile');
    }
}
