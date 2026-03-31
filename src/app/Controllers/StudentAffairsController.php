<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class StudentAffairsController {
    public function __construct() {
        Middleware::auth();
    }

    // LIST DATA SISWA (DENGAN FILTER & PAGINATION)
    public function index() {
        $db = Database::getInstance();
        
        // 1. Ambil Parameter Filter
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';

        // 2. Base Query
        $sql = "SELECT s.*, c.name as class_name 
                FROM students s 
                LEFT JOIN classrooms c ON s.classroom_id = c.id 
                WHERE s.status = 'ACTIVE'";
        
        $params = [];

        // 3. Logika Pencarian
        if (!empty($search)) {
            $sql .= " AND (s.full_name LIKE ? OR s.nis LIKE ? OR c.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // 4. Hitung Total Data (untuk Pagination)
        $countSql = "SELECT COUNT(*) as total FROM (" . $sql . ") as subquery";
        // Trik sederhana hitung row tanpa limit
        // Untuk query complex, replace SELECT ... FROM dengan SELECT COUNT(*) tidak selalu aman jika ada GROUP BY
        // Tapi untuk ini cukup aman.
        // Kita gunakan cara replace string agar lebih efisien:
        $sqlCount = preg_replace('/SELECT .* FROM /i', 'SELECT COUNT(*) as total FROM ', $sql, 1);
        // Note: jika query complex, lebih baik pakai subquery seperti di atas line 42, tapi mari kita gunakan fetchAll count manual jika ragu, atau query terpisah.
        // Opsi paling aman untuk pemula: Query count terpisah dengan WHERE yang sama.
        
        $countQuery = "SELECT COUNT(*) as total FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id WHERE s.status = 'ACTIVE'";
        if (!empty($search)) {
            $countQuery .= " AND (s.full_name LIKE ? OR s.nis LIKE ? OR c.name LIKE ?)";
        }
        $totalData = $db->query($countQuery, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        // 5. Order & Limit
        $sql .= " ORDER BY c.name ASC, s.full_name ASC LIMIT $limit OFFSET $offset";
        
        $students = $db->query($sql, $params)->fetchAll();
        $classrooms = $db->query("SELECT * FROM classrooms")->fetchAll();

        // 6. Render View
        View::render('student_affairs/index', [
            'title' => 'Data Induk Siswa',
            'students' => $students,
            'classrooms' => $classrooms,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search
        ]);
    }

    // SIMPAN SISWA BARU (QUICK ADD)
    public function store() {
        $db = Database::getInstance();
        $nis = $_POST['nis'];
        $name = $_POST['full_name'];
        $classId = $_POST['classroom_id'];
        $gender = $_POST['gender'];

        // Cek NIS Duplikat
        $cek = $db->query("SELECT id FROM students WHERE nis = ?", [$nis])->fetch();
        if ($cek) {
            Session::setFlash('error', 'NIS sudah terdaftar!');
            header('Location: /student-affairs/students');
            exit;
        }

        $db->query("INSERT INTO students (nis, full_name, classroom_id, gender, status) VALUES (?, ?, ?, ?, 'ACTIVE')", 
            [$nis, $name, $classId, $gender]);
        
        Session::setFlash('success', 'Siswa berhasil ditambahkan.');
        header('Location: /student-affairs/students');
    }

    // UPDATE DATA SISWA (QUICK EDIT)
    public function update() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        $nis = $_POST['nis'];
        $name = $_POST['full_name'];
        $classId = $_POST['classroom_id'];
        
        $db->query("UPDATE students SET nis = ?, full_name = ?, classroom_id = ? WHERE id = ?", 
            [$nis, $name, $classId, $id]);
        
        Session::setFlash('success', 'Data siswa berhasil diperbarui.');
        header('Location: /student-affairs/students');
    }

    // HAPUS SISWA
    public function delete() {
        $id = $_GET['id'] ?? 0;
        $db = Database::getInstance();
        $db->query("DELETE FROM students WHERE id = ?", [$id]);
        Session::setFlash('success', 'Data siswa berhasil dihapus.');
        header('Location: /student-affairs/students');
    }

// ==========================================================
    // MODUL ABSENSI SISWA (REVISED)
    // ==========================================================

    // 1. HALAMAN UTAMA: RIWAYAT ABSENSI (History Log)
    public function attendance() {
        $db = Database::getInstance();

        // Parameter Filter & Pagination
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $dateFilter = $_GET['date'] ?? '';
        $classFilter = $_GET['class_id'] ?? '';

        // Base Query
        $where = "WHERE 1=1";
        $params = [];

        // Logika Filter
        if (!empty($search)) {
            $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($dateFilter)) {
            $where .= " AND a.date = ?";
            $params[] = $dateFilter;
        }
        if (!empty($classFilter)) {
            $where .= " AND a.classroom_id = ?";
            $params[] = $classFilter;
        }

        // Hitung Total Data
        $countSql = "SELECT COUNT(*) as total FROM attendances a 
                     JOIN students s ON a.student_id = s.id 
                     $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        // Ambil Data Log
        $sql = "SELECT a.*, s.full_name, s.nis, c.name as class_name, u.name as recorder_name
                FROM attendances a
                JOIN students s ON a.student_id = s.id
                LEFT JOIN classrooms c ON a.classroom_id = c.id
                LEFT JOIN users u ON a.recorded_by = u.id
                $where
                ORDER BY a.date DESC, c.name ASC, s.full_name ASC
                LIMIT $limit OFFSET $offset";
        
        $logs = $db->query($sql, $params)->fetchAll();
        $classrooms = $db->query("SELECT * FROM classrooms ORDER BY name ASC")->fetchAll();

        View::render('student_affairs/attendance', [
            'title' => 'Riwayat Absensi Siswa',
            'logs' => $logs,
            'classrooms' => $classrooms,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'dateFilter' => $dateFilter,
            'classFilter' => $classFilter
        ]);
    }

    // 2. HALAMAN FORM INPUT ABSENSI (Massal per Kelas)
    public function createAttendance() {
        $db = Database::getInstance();
        $classId = $_GET['class_id'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d');
        
        $students = [];
        $existing = [];

        if ($classId) {
            // Ambil Siswa di Kelas Tersebut
            $students = $db->query("SELECT * FROM students WHERE classroom_id = ? AND status = 'ACTIVE' ORDER BY full_name ASC", [$classId])->fetchAll();
            
            // Cek data yang sudah ada (untuk Edit Mode)
            $logs = $db->query("SELECT student_id, status, notes FROM attendances WHERE classroom_id = ? AND date = ?", [$classId, $date])->fetchAll();
            foreach($logs as $l) {
                $existing[$l['student_id']] = [
                    'status' => $l['status'],
                    'notes' => $l['notes']
                ];
            }
        }

        $classrooms = $db->query("SELECT * FROM classrooms ORDER BY name ASC")->fetchAll();

        View::render('student_affairs/attendance_form', [
            'title' => 'Input Absensi Harian',
            'classrooms' => $classrooms,
            'students' => $students,
            'selectedClass' => $classId,
            'selectedDate' => $date,
            'existing' => $existing
        ]);
    }

    // 3. PROSES SIMPAN ABSENSI
    public function storeAttendance() {
        $classId = $_POST['classroom_id'];
        $date = $_POST['date'];
        $attendanceData = $_POST['attendance'] ?? []; // Array [student_id => status]
        $notesData = $_POST['notes'] ?? []; // Array [student_id => notes]

        if (empty($classId) || empty($date)) {
            Session::setFlash('error', 'Kelas dan Tanggal wajib diisi.');
            header('Location: /student-affairs/attendance/create');
            exit;
        }

        $db = Database::getInstance();
        try {
            $db->getConnection()->beginTransaction();

            // Hapus data lama di kelas & tanggal tsb (Reset agar tidak duplikat)
            $db->query("DELETE FROM attendances WHERE classroom_id = ? AND date = ?", [$classId, $date]);

            $sql = "INSERT INTO attendances (student_id, classroom_id, date, status, notes, recorded_by) VALUES (?, ?, ?, ?, ?, ?)";
            $adminId = Session::get('user_id');

            foreach ($attendanceData as $studentId => $status) {
                $note = $notesData[$studentId] ?? null;
                $db->query($sql, [$studentId, $classId, $date, $status, $note, $adminId]);
            }

            $db->getConnection()->commit();
            Session::setFlash('success', 'Data absensi berhasil disimpan.');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }

        // Redirect kembali ke form input (agar bisa lihat hasilnya)
        header("Location: /student-affairs/attendance/create?class_id=$classId&date=$date");
    }

    // 4. HAPUS SATU LOG ABSENSI
    public function deleteAttendance() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = Database::getInstance();
            $db->query("DELETE FROM attendances WHERE id = ?", [$id]);
            Session::setFlash('success', 'Data absensi berhasil dihapus.');
        }
        header('Location: /student-affairs/attendance');
    }

}
