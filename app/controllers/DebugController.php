<?php

namespace App\Controllers;

class DebugController extends BaseController {
    public function index() {
        // Include the debugchecker.php file directly
        include __DIR__ . '/../../public/debugchecker.php';
    }
}
