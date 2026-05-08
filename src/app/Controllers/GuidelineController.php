<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;

class GuidelineController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $role = Session::get('user_role');
        if ($role === 'siswa') {
            View::render('guideline/student', ['title' => 'Panduan Portal Santri']);
        } elseif ($role === 'orang-tua') {
            View::render('guideline/parent', ['title' => 'Panduan Portal Orang Tua']);
        } else {
            View::render('guideline/admin', ['title' => 'Panduan Penggunaan']);
        }
    }
}
