<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class HomeroomReportController {
    public function __construct() { Middleware::auth(); }

    // Halaman Index (List Kelas dengan Filter & Pagination)
    public function index() {
        $db = Database::getInstance();
        
        // Parameter Filter & Pagination
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        
        $search = $_GET['search'] ?? '';
        $level = $_GET['level'] ?? '';

        // Base Query
        $where = "WHERE 1=1";
        $params = [];

        // Logika Filter
        if (!empty($search)) {
            $where .= " AND (c.name LIKE ? OR u.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($level)) {
            $where .= " AND c.level = ?";
            $params[] = $level;
        }

        // Hitung Total Data
        $countSql = "SELECT COUNT(*) as total 
                     FROM classrooms c 
                     LEFT JOIN users u ON c.homeroom_teacher_id = u.id 
                     $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        // Ambil Data Kelas
        $sql = "SELECT c.*, u.name as teacher_name, 
                (SELECT COUNT(*) FROM students WHERE classroom_id = c.id AND status='ACTIVE') as student_count
                FROM classrooms c
                LEFT JOIN users u ON c.homeroom_teacher_id = u.id
                $where
                ORDER BY c.level ASC, c.name ASC
                LIMIT $limit OFFSET $offset";

        $classrooms = $db->query($sql, $params)->fetchAll();

        // Ambil Level untuk Dropdown Filter
        $levels = $db->query("SELECT DISTINCT level FROM classrooms ORDER BY level")->fetchAll();

        View::render('homeroom/reports/index', [
            'title' => 'Laporan Wali Kelas',
            'classrooms' => $classrooms,
            'levels' => $levels,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'levelFilter' => $level
        ]);
    }

    // Cetak Laporan Rekapitulasi
    public function printRecap() {
        $db = Database::getInstance();
        $classId = $_GET['classroom_id'];
        
        // 1. Data Kelas
        $classroom = $db->query("SELECT c.*, u.name as teacher_name FROM classrooms c LEFT JOIN users u ON c.homeroom_teacher_id = u.id WHERE c.id = ?", [$classId])->fetch();
        
        // 2. Data Siswa
        $students = $db->query("SELECT * FROM students WHERE classroom_id = ? AND status='ACTIVE' ORDER BY full_name", [$classId])->fetchAll();

        // 3. Rekap Absensi (Sakit, Izin, Alpa) per Siswa
        $attendance = [];
        foreach ($students as $s) {
            $stats = $db->query("SELECT 
                COUNT(CASE WHEN status='S' THEN 1 END) as sakit,
                COUNT(CASE WHEN status='I' THEN 1 END) as izin,
                COUNT(CASE WHEN status='A' THEN 1 END) as alpa
                FROM attendances WHERE student_id = ?", [$s['id']])->fetch();
            $attendance[$s['id']] = $stats;
        }

        // 4. Rekap Pelanggaran (Poin)
        $violations = [];
        foreach ($students as $s) {
            $points = $db->query("SELECT SUM(vt.points) as total 
                                  FROM student_violations sv 
                                  JOIN violation_types vt ON sv.violation_type_id = vt.id 
                                  WHERE sv.student_id = ?", [$s['id']])->fetchColumn();
            $violations[$s['id']] = $points ?? 0;
        }

        View::render('homeroom/reports/print_recap', [
            'classroom' => $classroom,
            'students' => $students,
            'attendance' => $attendance,
            'violations' => $violations
        ]);
    }
}
