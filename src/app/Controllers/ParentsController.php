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

    // =========================================================================
    // HELPER: ambil daftar anak & resolve student yang dipilih
    // =========================================================================
    private function getStudents() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $students = $db->query(
            "SELECT s.*, c.name as class_name FROM students s
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE s.parent_user_id = ? AND s.status = 'ACTIVE'
             ORDER BY s.full_name",
            [$userId]
        )->fetchAll();
        return $students;
    }

    private function resolveStudent(array $students) {
        if (empty($students)) return null;
        $id = $_GET['student_id'] ?? $students[0]['id'];
        foreach ($students as $s) {
            if ($s['id'] == $id) return $s;
        }
        return $students[0];
    }

    /** Dashboard orang tua: lihat data anak */
    public function portalIndex() {
        $students = $this->getStudents();
        View::render('parents/portal_index', [
            'title'    => 'Portal Orang Tua',
            'students' => $students,
        ]);
    }

    /** Detail anak (legacy, redirect ke dashboard) */
    public function portalChild() {
        header('Location: /portal/orangtua');
        exit;
    }

    public function portalAbsensi() {
        $students = $this->getStudents();
        $student  = $this->resolveStudent($students);
        $db = Database::getInstance();

        $month = $_GET['month'] ?? date('Y-m');
        $attendance = $student ? $db->query(
            "SELECT date, status, notes FROM attendances
             WHERE student_id = ? AND DATE_FORMAT(date,'%Y-%m') = ?
             ORDER BY date DESC",
            [$student['id'], $month]
        )->fetchAll() : [];

        $recap = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        foreach ($attendance as $a) { if (isset($recap[$a['status']])) $recap[$a['status']]++; }

        View::render('parents/portal_absensi', [
            'title'      => 'Absensi',
            'students'   => $students,
            'student'    => $student,
            'attendance' => $attendance,
            'recap'      => $recap,
            'month'      => $month,
        ]);
    }

    public function portalNilai() {
        $students = $this->getStudents();
        $student  = $this->resolveStudent($students);
        $db = Database::getInstance();

        $activeYear = $db->query("SELECT id, name FROM academic_years WHERE is_active=1 LIMIT 1")->fetch();
        $grades = ($student && $activeYear) ? $db->query(
            "SELECT sub.name as subject_name, sub.kkm,
                    MAX(CASE WHEN g.type='TUGAS' THEN g.score END) as task_score,
                    MAX(CASE WHEN g.type='UTS'   THEN g.score END) as mid_score,
                    MAX(CASE WHEN g.type='UAS'   THEN g.score END) as final_exam_score,
                    AVG(g.score) as final_score
             FROM student_grades g
             JOIN schedules sc ON g.schedule_id = sc.id
             JOIN subjects sub ON sc.subject_id = sub.id
             WHERE g.student_id = ? AND sc.academic_year_id = ?
             GROUP BY sub.id, sub.name, sub.kkm
             ORDER BY sub.name",
            [$student['id'], $activeYear['id']]
        )->fetchAll() : [];

        View::render('parents/portal_nilai', [
            'title'      => 'Nilai',
            'students'   => $students,
            'student'    => $student,
            'grades'     => $grades,
            'activeYear' => $activeYear,
        ]);
    }

    public function portalPembayaran() {
        $students = $this->getStudents();
        $student  = $this->resolveStudent($students);
        $db = Database::getInstance();

        $bills = $student ? $db->query(
            "SELECT b.*, ft.name as fee_name FROM bills b
             LEFT JOIN fee_types ft ON b.fee_type_id = ft.id
             WHERE b.student_id = ? ORDER BY b.created_at DESC",
            [$student['id']]
        )->fetchAll() : [];

        $transactions = $student ? $db->query(
            "SELECT t.*, b.title as fee_name FROM transactions t
             JOIN bills b ON t.bill_id = b.id
             WHERE b.student_id = ? ORDER BY t.created_at DESC LIMIT 20",
            [$student['id']]
        )->fetchAll() : [];

        View::render('parents/portal_pembayaran', [
            'title'        => 'Pembayaran',
            'students'     => $students,
            'student'      => $student,
            'bills'        => $bills,
            'transactions' => $transactions,
        ]);
    }

    public function portalKedisiplinan() {
        $students = $this->getStudents();
        $student  = $this->resolveStudent($students);
        $db = Database::getInstance();

        $violations = $student ? $db->query(
            "SELECT sv.*, vt.name as violation_name, vt.points, vt.category
             FROM student_violations sv
             JOIN violation_types vt ON sv.violation_type_id = vt.id
             WHERE sv.student_id = ? ORDER BY sv.date DESC",
            [$student['id']]
        )->fetchAll() : [];

        $totalPoints = array_sum(array_column($violations, 'points'));

        View::render('parents/portal_kedisiplinan', [
            'title'       => 'Kedisiplinan',
            'students'    => $students,
            'student'     => $student,
            'violations'  => $violations,
            'totalPoints' => $totalPoints,
        ]);
    }

    public function portalAsrama() {
        $students = $this->getStudents();
        $student  = $this->resolveStudent($students);
        $db = Database::getInstance();

        $dorm = ($student && $student['dorm_id']) ? $db->query(
            "SELECT * FROM dorms WHERE id = ?", [$student['dorm_id']]
        )->fetch() : null;

        $permits = $student ? $db->query(
            "SELECT * FROM permits WHERE student_id = ? ORDER BY created_at DESC LIMIT 10",
            [$student['id']]
        )->fetchAll() : [];

        View::render('parents/portal_asrama', [
            'title'    => 'Asrama',
            'students' => $students,
            'student'  => $student,
            'dorm'     => $dorm,
            'permits'  => $permits,
        ]);
    }

    public function portalKesehatan() {
        $students = $this->getStudents();
        $student  = $this->resolveStudent($students);
        $db = Database::getInstance();

        $records = $student ? $db->query(
            "SELECT hr.*, u.name as officer_name FROM health_records hr
             LEFT JOIN users u ON hr.officer_id = u.id
             WHERE hr.student_id = ? ORDER BY hr.date DESC",
            [$student['id']]
        )->fetchAll() : [];

        View::render('parents/portal_kesehatan', [
            'title'    => 'Kesehatan',
            'students' => $students,
            'student'  => $student,
            'records'  => $records,
        ]);
    }

    public function portalJadwal() {
        $students = $this->getStudents();
        $student  = $this->resolveStudent($students);
        $db = Database::getInstance();

        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active=1 LIMIT 1")->fetch();
        $schedules = ($student && $student['classroom_id'] && $activeYear) ? $db->query(
            "SELECT sc.*, s.name as subject_name, t.full_name as teacher_name
             FROM schedules sc
             JOIN subjects s ON sc.subject_id = s.id
             LEFT JOIN teachers t ON sc.teacher_id = t.id
             WHERE sc.classroom_id = ? AND sc.academic_year_id = ?
             ORDER BY FIELD(sc.day,'SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','AHAD'), sc.start_time",
            [$student['classroom_id'], $activeYear['id']]
        )->fetchAll() : [];

        $days = ['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','AHAD'];
        $grouped = [];
        foreach ($days as $d) $grouped[$d] = [];
        foreach ($schedules as $sc) $grouped[$sc['day']][] = $sc;

        View::render('parents/portal_jadwal', [
            'title'    => 'Jadwal Pelajaran',
            'students' => $students,
            'student'  => $student,
            'grouped'  => $grouped,
            'days'     => $days,
        ]);
    }

    public function portalPengumuman() {
        $db = Database::getInstance();
        // Gunakan tabel announcements jika ada, fallback ke master_notification
        try {
            $announcements = $db->query(
                "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 30"
            )->fetchAll();
        } catch (\Exception $e) {
            $announcements = [];
        }

        View::render('parents/portal_pengumuman', [
            'title'         => 'Pengumuman',
            'students'      => $this->getStudents(),
            'announcements' => $announcements,
        ]);
    }
}
