<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Middleware;
use App\Core\Session;
use App\Models\AppConfig;
use App\Models\WhatsappService;

class WhatsappController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $config = AppConfig::getAll();
        View::render('settings/whatsapp', ['title' => 'Konfigurasi WhatsApp', 'config' => $config]);
    }

    public function update() {
        AppConfig::set('wa_api_url', $_POST['wa_api_url']);
        AppConfig::set('wa_api_token', $_POST['wa_api_token']);
        Session::setFlash('success', 'Konfigurasi WhatsApp disimpan.');
        header('Location: /settings/whatsapp');
    }

    public function test() {
        $target = $_POST['test_number'];
        $message = "Halo! Ini adalah pesan tes dari SIAKAD Parabek.";
        
        $res = WhatsappService::send($target, $message);
        
        Session::setFlash('info', 'Respon Server: ' . $res);
        header('Location: /settings/whatsapp');
    }
}
