<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Middleware;

class GuardianController {
    public function __construct() {
        Middleware::auth();
    }

    public function index() {
        View::render('guardians/index', [
            'title' => 'Data Wali'
        ]);
    }
}
