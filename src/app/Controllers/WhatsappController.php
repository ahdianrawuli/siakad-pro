<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Middleware;
use App\Core\Session;
use App\Core\Database;
use App\Models\WhatsappService;

class WhatsappController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $waStatus = WhatsappService::getStatus();

        // Ambil daftar siswa & orang tua untuk blasting
        $students = $db->query(
            "SELECT s.id, s.full_name, s.parent_phone, c.name as class_name
             FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE s.status = 'ACTIVE' ORDER BY c.name, s.full_name"
        )->fetchAll();

        $classrooms = $db->query("SELECT * FROM classrooms ORDER BY name")->fetchAll();

        View::render('settings/whatsapp', [
            'title'      => 'WhatsApp Gateway',
            'waStatus'   => $waStatus,
            'students'   => $students,
            'classrooms' => $classrooms,
        ]);
    }

    /** AJAX: polling status & QR */
    public function status() {
        header('Content-Type: application/json');
        echo json_encode(WhatsappService::getStatus());
        exit;
    }

    /** Logout WhatsApp */
    public function logout() {
        WhatsappService::logout();
        Session::setFlash('success', 'WhatsApp berhasil di-logout.');
        header('Location: /settings/whatsapp');
    }

    /** Kirim pesan manual ke satu nomor */
    public function sendManual() {
        $number  = trim($_POST['number'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!$number || !$message) {
            Session::setFlash('error', 'Nomor dan pesan wajib diisi.');
            header('Location: /settings/whatsapp');
            exit;
        }

        $res = WhatsappService::send($number, $message);
        if (!empty($res['success'])) {
            Session::setFlash('success', "Pesan berhasil dikirim ke $number.");
        } else {
            Session::setFlash('error', 'Gagal kirim: ' . ($res['error'] ?? 'Unknown error'));
        }
        header('Location: /settings/whatsapp');
    }

    /** Blasting ke siswa / orang tua */
    public function blast() {
        $db      = Database::getInstance();
        $classId = $_POST['blast_class'] ?? '';
        $message = trim($_POST['blast_message'] ?? '');

        if (!$message) {
            Session::setFlash('error', 'Pesan blasting tidak boleh kosong.');
            header('Location: /settings/whatsapp');
            exit;
        }

        $where  = $classId ? "AND s.classroom_id = ?" : "";
        $params = $classId ? [$classId] : [];

        $rows = $db->query(
            "SELECT s.parent_phone FROM students s WHERE s.status = 'ACTIVE' $where",
            $params
        )->fetchAll();

        $numbers = [];
        foreach ($rows as $r) {
            if (!empty($r['parent_phone'])) $numbers[] = $r['parent_phone'];
        }

        if (empty($numbers)) {
            Session::setFlash('error', 'Tidak ada nomor yang ditemukan untuk target tersebut.');
            header('Location: /settings/whatsapp');
            exit;
        }

        $result = WhatsappService::blast($numbers, $message);
        Session::setFlash('success', "Blasting selesai: {$result['success']} berhasil, {$result['failed']} gagal dari " . count($numbers) . " nomor.");
        header('Location: /settings/whatsapp');
    }
}
