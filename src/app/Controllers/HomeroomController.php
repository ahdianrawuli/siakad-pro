<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class HomeroomController {
    public function __construct() {
        Middleware::auth();
        // Pastikan role-nya Guru atau Admin
        if (!in_array(Session::get('user_role'), ['guru', 'admin', 'super-admin'])) {
            die("Akses ditolak. Modul ini khusus Pendidik.");
        }
    }

    public function index() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');

        // 1. Cek apakah user ini adalah Wali Kelas?
        $myClass = $db->query("
            SELECT c.*, 
            (SELECT COUNT(*) FROM students WHERE classroom_id = c.id AND status='ACTIVE') as total_students,
            (SELECT COUNT(*) FROM students WHERE classroom_id = c.id AND gender='L') as total_male,
            (SELECT COUNT(*) FROM students WHERE classroom_id = c.id AND gender='P') as total_female
            FROM classrooms c 
            WHERE c.homeroom_teacher_id = ?
        ", [$userId])->fetch();

        // Jika bukan Wali Kelas, tampilkan view kosong/info
        if (!$myClass) {
            View::render('homeroom/empty', ['title' => 'Dashboard Wali Kelas']);
            return;
        }

        // 2. Ambil Daftar Siswa Perwalian
        $students = $db->query("
            SELECT s.*,
            (SELECT COUNT(*) FROM student_violations WHERE student_id = s.id) as total_violations,
            (SELECT COUNT(*) FROM attendances WHERE student_id = s.id AND status IN ('A','S')) as total_absent
            FROM students s 
            WHERE s.classroom_id = ? AND s.status = 'ACTIVE'
            ORDER BY s.full_name ASC
        ", [$myClass['id']])->fetchAll();

        View::render('homeroom/index', [
            'title' => 'Kelas Saya (' . $myClass['name'] . ')',
            'class' => $myClass,
            'students' => $students
        ]);
    }
}
