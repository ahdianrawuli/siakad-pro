<?php
namespace App\Core;

class Middleware {
    public static function auth() {
        Session::init();
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
    }
}
