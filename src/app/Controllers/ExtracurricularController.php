<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\Session;

class ExtracurricularController {
    public function __construct() { Middleware::auth(); }

    // --- HALAMAN 1: MASTER DATA (Ekskul, Guru, Jadwal) ---
    public function index() {
        $db = Database::getInstance();
        
        $ekskuls = $db->query("
            SELECT e.*, 
            (SELECT GROUP_CONCAT(u.name SEPARATOR ', ') 
             FROM extracurricular_coaches ec 
             JOIN users u ON ec.user_id = u.id 
             WHERE ec.extracurricular_id = e.id) as coaches,
            (SELECT GROUP_CONCAT(CONCAT(es.day_name, ' ', es.start_time, '-', es.end_time) SEPARATOR '<br>') 
             FROM extracurricular_schedules es 
             WHERE es.extracurricular_id = e.id) as schedules
            FROM extracurriculars e
            ORDER BY e.name
        ")->fetchAll();

        $teachers = $db->query("SELECT id, name FROM users WHERE role_id IN (1,3) ORDER BY name")->fetchAll();

        View::render('extracurricular/master', [
            'title' => 'Master Ekstrakurikuler',
            'ekskuls' => $ekskuls,
            'teachers' => $teachers
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $db->query("INSERT INTO extracurriculars (name, description, status) VALUES (?, ?, 'ACTIVE')", 
            [$_POST['name'], $_POST['description']]);
        Session::setFlash('success', 'Ekstrakurikuler berhasil ditambahkan');
        header("Location: /extracurricular/master");
    }

    public function storeCoach() {
        $db = Database::getInstance();
        $db->query("INSERT INTO extracurricular_coaches (extracurricular_id, user_id) VALUES (?, ?)", 
            [$_POST['extracurricular_id'], $_POST['user_id']]);
        Session::setFlash('success', 'Pembina berhasil ditambahkan');
        header("Location: /extracurricular/master");
    }

    public function storeSchedule() {
        $db = Database::getInstance();
        $db->query("INSERT INTO extracurricular_schedules (extracurricular_id, day_name, start_time, end_time, location) VALUES (?, ?, ?, ?, ?)", 
            [$_POST['extracurricular_id'], $_POST['day_name'], $_POST['start_time'], $_POST['end_time'], $_POST['location']]);
        Session::setFlash('success', 'Jadwal berhasil ditambahkan');
        header("Location: /extracurricular/master");
    }

    // --- HALAMAN 2: ANGGOTA EKSKUL ---
    public function members() {
        $db = Database::getInstance();
        $selectedEkskul = $_GET['id'] ?? null;
        
        $ekskuls = $db->query("SELECT * FROM extracurriculars WHERE status='ACTIVE'")->fetchAll();
        $members = [];
        $students = [];

        // Ambil Tahun Ajaran Aktif
        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch();
        $yearId = $activeYear['id'] ?? null;

        if ($selectedEkskul && $yearId) {
            // FILTER BY ACADEMIC YEAR (Agar data anggota valid per tahun)
            $members = $db->query("
                SELECT se.id as record_id, s.full_name, s.nis, c.name as class_name
                FROM student_extracurriculars se
                JOIN students s ON se.student_id = s.id
                LEFT JOIN classrooms c ON s.classroom_id = c.id
                WHERE se.extracurricular_id = ? AND se.academic_year_id = ?
                ORDER BY s.full_name
            ", [$selectedEkskul, $yearId])->fetchAll();

            // List siswa yang BELUM join di tahun ini
            $students = $db->query("
                SELECT id, full_name, nis FROM students 
                WHERE status='ACTIVE' 
                AND id NOT IN (
                    SELECT student_id FROM student_extracurriculars 
                    WHERE extracurricular_id = ? AND academic_year_id = ?
                )
                ORDER BY full_name
            ", [$selectedEkskul, $yearId])->fetchAll();
        }

        View::render('extracurricular/members', [
            'title' => 'Anggota Ekstrakurikuler',
            'ekskuls' => $ekskuls,
            'selectedEkskul' => $selectedEkskul,
            'members' => $members,
            'students' => $students
        ]);
    }

    public function addMember() {
        $db = Database::getInstance();
        
        // 1. Cari Tahun Ajaran Aktif
        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch();

        if (!$activeYear) {
            Session::setFlash('error', 'Tidak ada tahun ajaran aktif. Harap set tahun ajaran terlebih dahulu.');
            header("Location: /extracurricular/members?id=" . $_POST['extracurricular_id']);
            return;
        }

        // 2. Insert dengan academic_year_id
        $db->query("INSERT INTO student_extracurriculars (student_id, extracurricular_id, academic_year_id) VALUES (?, ?, ?)", 
            [$_POST['student_id'], $_POST['extracurricular_id'], $activeYear['id']]);
            
        Session::setFlash('success', 'Anggota berhasil ditambahkan');
        header("Location: /extracurricular/members?id=" . $_POST['extracurricular_id']);
    }
    
    public function deleteMember() {
        $db = Database::getInstance();
        $db->query("DELETE FROM student_extracurriculars WHERE id = ?", [$_POST['record_id']]);
        Session::setFlash('success', 'Anggota berhasil dihapus');
        header("Location: /extracurricular/members?id=" . $_POST['extracurricular_id']);
    }

    // --- HALAMAN 3: ABSENSI EKSKUL ---
    public function attendance() {
        $db = Database::getInstance();
        $selectedEkskul = $_GET['id'] ?? '';
        $date = $_GET['date'] ?? date('Y-m-d');
        
        $ekskuls = $db->query("SELECT * FROM extracurriculars WHERE status='ACTIVE'")->fetchAll();
        $members = [];
        $existingAttendance = [];

        // Ambil Tahun Ajaran Aktif untuk menampilkan siswa yang terdaftar SAJA
        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch();
        $yearId = $activeYear['id'] ?? null;

        if ($selectedEkskul && $yearId) {
            $members = $db->query("
                SELECT se.student_id, s.full_name, s.nis, c.name as class_name
                FROM student_extracurriculars se
                JOIN students s ON se.student_id = s.id
                LEFT JOIN classrooms c ON s.classroom_id = c.id
                WHERE se.extracurricular_id = ? AND se.academic_year_id = ?
                ORDER BY s.full_name
            ", [$selectedEkskul, $yearId])->fetchAll();

            $logs = $db->query("SELECT * FROM extracurricular_attendances WHERE extracurricular_id = ? AND date = ?", 
                [$selectedEkskul, $date])->fetchAll();
            
            foreach($logs as $log) {
                $existingAttendance[$log['student_id']] = $log['status'];
            }
        }

        View::render('extracurricular/attendance', [
            'title' => 'Absensi Ekstrakurikuler',
            'ekskuls' => $ekskuls,
            'selectedEkskul' => $selectedEkskul,
            'date' => $date,
            'members' => $members,
            'existingAttendance' => $existingAttendance
        ]);
    }

    public function saveAttendance() {
        $db = Database::getInstance();
        $ekskulId = $_POST['extracurricular_id'];
        $date = $_POST['date'];
        $statuses = $_POST['status'] ?? []; 
        $userId = $_SESSION['user']['id'];

        try {
            $db->getConnection()->beginTransaction();
            
            $db->query("DELETE FROM extracurricular_attendances WHERE extracurricular_id = ? AND date = ?", 
                [$ekskulId, $date]);

            $stmt = $db->getConnection()->prepare("INSERT INTO extracurricular_attendances 
                (extracurricular_id, student_id, date, status, created_by) VALUES (?, ?, ?, ?, ?)");

            foreach ($statuses as $studentId => $status) {
                $stmt->execute([$ekskulId, $studentId, $date, $status, $userId]);
            }

            $db->getConnection()->commit();
            Session::setFlash('success', 'Absensi berhasil disimpan');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        header("Location: /extracurricular/attendance?id=$ekskulId&date=$date");
    }
}

