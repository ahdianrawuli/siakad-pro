<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class BoardingSupervisorController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        
        // Ambil Data Wali Asrama
        $supervisors = $db->query("
            SELECT ds.*, d.name as dorm_name, u.name as user_name, r.slug as role_name 
            FROM dorm_supervisors ds
            JOIN dorms d ON ds.dorm_id = d.id
            JOIN users u ON ds.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            WHERE ds.status = 'ACTIVE'
            ORDER BY d.name ASC
        ")->fetchAll();

        // Data Master untuk Modal
        $dorms = $db->query("SELECT * FROM dorms ORDER BY name")->fetchAll();
        // Ambil User Guru (3) & Staff (7)
        $users = $db->query("SELECT u.id, u.name, r.slug FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug IN ('guru', 'staff') AND u.status='active' ORDER BY u.name")->fetchAll();

        View::render('boarding/supervisors/index', [
            'title' => 'Data Wali Asrama',
            'supervisors' => $supervisors,
            'dorms' => $dorms,
            'users' => $users
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $adminId = Session::get('user_id');

        // Cek duplikasi
        $check = $db->query("SELECT id FROM dorm_supervisors WHERE dorm_id = ? AND user_id = ? AND status = 'ACTIVE'", 
                            [$_POST['dorm_id'], $_POST['user_id']])->fetch();

        if ($check) {
            Session::setFlash('error', 'User tersebut sudah menjadi wali di asrama ini.');
        } else {
            $db->query("INSERT INTO dorm_supervisors (dorm_id, user_id, assigned_date, status, created_by) VALUES (?, ?, ?, 'ACTIVE', ?)", [
                $_POST['dorm_id'], $_POST['user_id'], date('Y-m-d'), $adminId
            ]);
            Session::setFlash('success', 'Wali Asrama berhasil ditugaskan.');
        }

        header('Location: /boarding/supervisors');
    }

    public function delete() {
        $db = Database::getInstance();
        // Hard delete untuk membersihkan data
        $db->query("DELETE FROM dorm_supervisors WHERE id = ?", [$_GET['id']]);
        Session::setFlash('success', 'Penugasan dihapus.');
        header('Location: /boarding/supervisors');
    }
}
