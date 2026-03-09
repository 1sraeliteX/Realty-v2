<?php

namespace App\Controllers;

class LandingController extends BaseController {
    public function index() {
        $this->view('landing', [
            'title' => 'Cornerstone Realty - Complete Property Management Solution'
        ]);
    }
}
