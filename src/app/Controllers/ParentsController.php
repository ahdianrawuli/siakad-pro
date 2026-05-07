<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class ParentsController {
    public function __construct() {
        Middleware::auth();
    }

    // 1. TAMPILKAN DATA DENGAN FILTER & PAGINATION
    public function index() {
        $db = Database::getInstance();
        
        // Parameter Pagination & Filter
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';

        // Base Condition (Hanya siswa aktif)
        $whereClause = "WHERE status = 'ACTIVE'";
        $params = [];

        // Logika Pencarian (Cari Siswa, Ayah, Ibu, atau Wali)
        if (!empty($search)) {
            $whereClause .= " AND (full_name LIKE ? OR father_name LIKE ? OR mother_name LIKE ? OR guardian_name LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        // Hitung Total Data (Untuk Pagination)
        $countSql = "SELECT COUNT(*) as total FROM students $whereClause";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        // Ambil Data
        $sql = "SELECT id, full_name, nis, 
                       father_name, father_phone, father_job,
                       mother_name, mother_phone, mother_job,
                       guardian_name, guardian_phone, guardian_relation 
                FROM students 
                $whereClause 
                ORDER BY full_name ASC 
                LIMIT $limit OFFSET $offset";
        
        $parents = $db->query($sql, $params)->fetchAll();

        View::render('parents/index', [
            'title' => 'Data Orang Tua & Wali',
            'parents' => $parents,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search
        ]);
    }

    // 2. FORM EDIT (TETAP MENGGUNAKAN HALAMAN TERPISAH AGAR LEBIH LELUASA)
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /student-affairs/parents');
            exit;
        }

        $db = Database::getInstance();
        $student = $db->query("SELECT * FROM students WHERE id = ?", [$id])->fetch();

        View::render('parents/edit', [
            'title' => 'Edit Data Orang Tua/Wali',
            'student' => $student
        ]);
    }

    // 3. UPDATE DATA
    public function update() {
        $id = $_POST['id'];
        
        $data = [
            $_POST['father_name'],
            $_POST['father_job'],
            $_POST['father_phone'],
            $_POST['mother_name'],
            $_POST['mother_job'],
            $_POST['mother_phone'],
            $_POST['guardian_name'],
            $_POST['guardian_relation'],
            $_POST['guardian_phone'],
            $_POST['guardian_address'],
            $id
        ];

        $sql = "UPDATE students SET 
                father_name=?, father_job=?, father_phone=?, 
                mother_name=?, mother_job=?, mother_phone=?, 
                guardian_name=?, guardian_relation=?, guardian_phone=?, guardian_address=?
                WHERE id=?";

        $db = Database::getInstance();
        $db->query($sql, $data);

        Session::setFlash('success', 'Data Orang Tua & Wali berhasil diperbarui.');
        header('Location: /student-affairs/parents');
    }

    // =========================================================================
    // PORTAL ORANG TUA (role: orangtua)
    // =========================================================================

    /** Dashboard orang tua: lihat data anak */
    public function portalIndex() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        // Cari siswa yang terhubung ke akun orang tua ini
        $students = $db->query(
            "SELECT s.*, c.name as class_name
             FROM students s
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE s.parent_user_id = ? AND s.status = 'ACTIVE'
             ORDER BY s.full_name",
            [$userId]
        )->fetchAll();

        // Jika tidak ada relasi parent_user_id, coba cari via nomor HP
        if (empty($students)) {
            $user = $db->query("SELECT phone FROM users WHERE id = ?", [$userId])->fetch();
            if ($user && $user['phone']) {
                $students = $db->query(
                    "SELECT s.*, c.name as class_name
                     FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id
                     WHERE (s.father_phone = ? OR s.mother_phone = ? OR s.guardian_phone = ?)
                     AND s.status = 'ACTIVE'",
                    [$user['phone'], $user['phone'], $user['phone']]
                )->fetchAll();
            }
        }

        View::render('parents/portal_index', [
            'title'    => 'Portal Orang Tua',
            'students' => $students,
        ]);
    }

    /** Detail anak: nilai, absensi, tagihan */
    public function portalChild() {
        $studentId = $_GET['id'] ?? null;
        if (!$studentId) { header('Location: /portal/orangtua'); exit; }

        $db = Database::getInstance();
        $student = $db->query(
            "SELECT s.*, c.name as class_name FROM students s
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE s.id = ? AND s.status = 'ACTIVE'",
            [$studentId]
        )->fetch();
        if (!$student) { header('Location: /portal/orangtua'); exit; }

        $activeYear = $db->query("SELECT id, name FROM academic_years WHERE is_active = 1 LIMIT 1")->fetch();

        // Nilai
        $grades = $activeYear ? $db->query(
            "SELECT g.*, s.name as subject_name, s.kkm FROM grades g
             JOIN subjects s ON g.subject_id = s.id
             WHERE g.student_id = ? AND g.academic_year_id = ?
             ORDER BY s.name",
            [$studentId, $activeYear['id']]
        )->fetchAll() : [];

        // Absensi bulan ini
        $month = date('Y-m');
        $attendance = $db->query(
            "SELECT date, status, notes FROM attendances
             WHERE student_id = ? AND DATE_FORMAT(date,'%Y-%m') = ?
             ORDER BY date DESC",
            [$studentId, $month]
        )->fetchAll();
        $recap = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        foreach ($attendance as $a) { if (isset($recap[$a['status']])) $recap[$a['status']]++; }

        // Tagihan
        $bills = $db->query(
            "SELECT * FROM bills WHERE student_id = ? ORDER BY created_at DESC LIMIT 10",
            [$studentId]
        )->fetchAll();

        // Pelanggaran terbaru
        $violations = $db->query(
            "SELECT dv.*, mv.name as violation_name, mv.points
             FROM discipline_violations dv
             JOIN master_violations mv ON dv.violation_id = mv.id
             WHERE dv.student_id = ? ORDER BY dv.date DESC LIMIT 5",
            [$studentId]
        )->fetchAll();

        View::render('parents/portal_child', [
            'title'      => 'Detail Anak: ' . $student['full_name'],
            'student'    => $student,
            'grades'     => $grades,
            'activeYear' => $activeYear,
            'attendance' => $attendance,
            'recap'      => $recap,
            'bills'      => $bills,
            'violations' => $violations,
        ]);
    }
}
