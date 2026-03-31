<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class KbmPermitController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        
        // Ambil Data Izin KBM
        $sql = "SELECT kp.*, s.full_name, s.nis, c.name as class_name, ay.name as year_name 
                FROM kbm_permits kp
                JOIN students s ON kp.student_id = s.id
                LEFT JOIN classrooms c ON s.classroom_id = c.id
                JOIN academic_years ay ON kp.academic_year_id = ay.id
                WHERE (s.full_name LIKE ? OR kp.type LIKE ?)
                ORDER BY kp.date DESC, kp.created_at DESC";
        
        $permits = $db->query($sql, ["%$search%", "%$search%"])->fetchAll();

        // Data untuk Modal
        $students = $db->query("SELECT id, full_name, nis FROM students WHERE status='ACTIVE' ORDER BY full_name")->fetchAll();
        $years = $db->query("SELECT id, name FROM academic_years WHERE is_active = 1")->fetchAll();

        View::render('academic/kbm_permits/index', [
            'title' => 'Dispensasi KBM (Izin Sekolah)',
            'permits' => $permits,
            'students' => $students,
            'years' => $years,
            'search' => $search
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $adminId = Session::get('user_id');

        $db->query("INSERT INTO kbm_permits (student_id, academic_year_id, date, type, reason, status, created_by) 
                    VALUES (?, ?, ?, ?, ?, 'APPROVED', ?)", [
            $_POST['student_id'],
            $_POST['academic_year_id'],
            $_POST['date'],
            $_POST['type'],
            $_POST['reason'],
            $adminId
        ]);

        Session::setFlash('success', 'Dispensasi KBM berhasil dicatat.');
        header('Location: /academic/kbm-permits');
    }

    public function delete() {
        $db = Database::getInstance();
        $db->query("DELETE FROM kbm_permits WHERE id = ?", [$_GET['id']]);
        Session::setFlash('success', 'Data dihapus.');
        header('Location: /academic/kbm-permits');
    }
}

