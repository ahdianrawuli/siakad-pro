<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class BoardingController {
    public function __construct() { Middleware::auth(); }

    // --- 1. MANAJEMEN ASRAMA (DORMS) ---
    public function dorms() {
        $db = Database::getInstance();

        $search = $_GET['search'] ?? '';
        $gender = $_GET['gender'] ?? '';
        $unit   = $_GET['unit'] ?? '';

        $scope = ScopeFilter::get(); // GLOBAL | MTS | MA | PDF

        $where = "WHERE 1=1";
        $params = [];
        if (!empty($search)) { $where .= " AND d.name LIKE ?"; $params[] = "%$search%"; }
        if (!empty($gender))  { $where .= " AND d.gender = ?"; $params[] = $gender; }

        // Scope filter: jika scope aktif, paksa unit = scope (abaikan filter manual)
        if ($scope !== 'GLOBAL') {
            $where .= " AND d.unit = ?"; $params[] = $scope;
        } elseif (!empty($unit)) {
            $where .= " AND d.unit = ?"; $params[] = $unit;
        }

        $dorms = $db->query("
            SELECT d.*, (SELECT COUNT(*) FROM students WHERE dorm_id = d.id) as occupied
            FROM dorms d $where ORDER BY d.unit, d.gender, d.name
        ", $params)->fetchAll();

        // Santri non-asrama mengikuti scope
        $studentWhere = "dorm_id IS NULL AND status='ACTIVE'";
        $studentParams = [];
        if ($scope !== 'GLOBAL') {
            $studentWhere .= " AND s.classroom_id IN (SELECT id FROM classrooms WHERE major = ?)";
            $studentParams[] = $scope;
        }
        $students = $db->query("SELECT s.id, s.full_name, s.nis FROM students s WHERE $studentWhere ORDER BY s.full_name", $studentParams)->fetchAll();

        View::render('boarding/dorms', [
            'title'        => 'Manajemen Asrama',
            'dorms'        => $dorms,
            'students'     => $students,
            'search'       => $search,
            'genderFilter' => $gender,
            'unitFilter'   => $unit,
            'scope'        => $scope,
            'totalDorms'   => $db->query("SELECT COUNT(*) FROM dorms")->fetchColumn(),
        ]);
    }

    public function assignDorm() {
        $db = Database::getInstance();
        $studentId = $_POST['student_id'];
        $dormId = $_POST['dorm_id'];
        
        $db->query("UPDATE students SET dorm_id = ? WHERE id = ?", [$dormId, $studentId]);
        Session::setFlash('success', 'Santri berhasil ditempatkan di asrama.');
        header('Location: /asrama/dorms');
    }

    public function dormStudents() {
        $dormId = $_GET['id'] ?? null;
        if (!$dormId) { header('Location: /asrama/dorms'); exit; }

        $db = Database::getInstance();
        $dorm = $db->query("SELECT * FROM dorms WHERE id = ?", [$dormId])->fetch();
        if (!$dorm) { header('Location: /asrama/dorms'); exit; }

        $search = trim($_GET['search'] ?? '');
        $limit  = (int)($_GET['limit'] ?? 10);
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $where  = "s.dorm_id = ? AND s.status = 'ACTIVE'";
        $params = [$dormId];
        if ($search !== '') {
            $where   .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $total = $db->query("SELECT COUNT(*) FROM students s WHERE $where", $params)->fetchColumn();

        $students = $db->query(
            "SELECT s.*, c.name as class_name FROM students s
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE $where ORDER BY s.full_name LIMIT $limit OFFSET $offset",
            $params
        )->fetchAll();

        $allDorms = $db->query("SELECT * FROM dorms WHERE id != ? ORDER BY name", [$dormId])->fetchAll();

        View::render('boarding/dorm_students', [
            'title'       => 'Santri Asrama: ' . $dorm['name'],
            'dorm'        => $dorm,
            'students'    => $students,
            'allDorms'    => $allDorms,
            'search'      => $search,
            'limit'       => $limit,
            'currentPage' => $page,
            'totalPages'  => $limit > 0 ? (int)ceil($total / $limit) : 1,
            'totalData'   => $total,
        ]);
    }

    public function moveDorm() {
        $studentId = $_POST['student_id'];
        $newDormId = ($_POST['new_dorm_id'] === 'null' || $_POST['new_dorm_id'] === '') ? null : $_POST['new_dorm_id'];
        $fromDormId = $_POST['from_dorm_id'];

        $db = Database::getInstance();
        $db->query("UPDATE students SET dorm_id = ? WHERE id = ?", [$newDormId, $studentId]);
        Session::setFlash('success', 'Santri berhasil dipindahkan.');
        header("Location: /asrama/dorms/students?id=$fromDormId");
    }

    public function units() {
        View::render('boarding/units', ['title' => 'Unit Asrama']);
    }

    public function tilawah() {
        View::render('boarding/tilawah', ['title' => 'Absen Tilawah']);
    }

    public function storeDorm() {
        $db = Database::getInstance();
        $name = $_POST['name'];
        $capacity = $_POST['capacity'];
        $gender = $_POST['gender'];

        $db->query("INSERT INTO dorms (name, capacity, gender, unit) VALUES (?, ?, ?, ?)", [
            $_POST['name'], $_POST['capacity'], $_POST['gender'], $_POST['unit'] ?? 'MTS'
        ]);

        Session::setFlash('success', 'Gedung/Kamar asrama berhasil ditambahkan.');
        header('Location: /asrama/dorms');
    }

    public function deleteDorm() {
        $id = $_POST['id'];
        $db = Database::getInstance();
        
        // Cek apakah ada penghuninya
        $count = $db->query("SELECT COUNT(*) FROM students WHERE dorm_id = ?", [$id])->fetchColumn();
        
        if ($count > 0) {
            Session::setFlash('error', "Gagal hapus: Masih ada $count santri di asrama ini. Pindahkan mereka dulu.");
        } else {
            $db->query("DELETE FROM dorms WHERE id = ?", [$id]);
            Session::setFlash('success', 'Data asrama berhasil dihapus.');
        }

        header('Location: /asrama/dorms');
    }

    // --- 2. PERIZINAN (IZIN KELUAR) ---
    public function permits() {
        $db = Database::getInstance();
        $scope = ScopeFilter::get();

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $page   = (int)($_GET['page'] ?? 1);
        $limit  = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $where = "WHERE 1=1";
        $params = [];
        if ($scope !== 'GLOBAL') {
            $where .= " AND s.classroom_id IN (SELECT id FROM classrooms WHERE major = ?)";
            $params[] = $scope;
        }
        if (!empty($search)) { $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($status))  { $where .= " AND p.status = ?"; $params[] = $status; }

        $totalData  = $db->query("SELECT COUNT(*) FROM permits p JOIN students s ON p.student_id = s.id $where", $params)->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        $permits = $db->query("
            SELECT p.*, s.full_name, s.nis, d.name as dorm_name
            FROM permits p
            JOIN students s ON p.student_id = s.id
            LEFT JOIN dorms d ON s.dorm_id = d.id
            $where ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset
        ", $params)->fetchAll();

        // Dropdown santri mengikuti scope
        $sWhere = "status='ACTIVE'";
        $sParams = [];
        if ($scope !== 'GLOBAL') {
            $sWhere .= " AND s.classroom_id IN (SELECT id FROM classrooms WHERE major = ?)";
            $sParams[] = $scope;
        }
        $students = $db->query("SELECT s.id, s.full_name, s.nis FROM students s WHERE $sWhere ORDER BY s.full_name", $sParams)->fetchAll();

        View::render('boarding/permits', [
            'title'        => 'Perizinan Santri',
            'permits'      => $permits,
            'students'     => $students,
            'search'       => $search,
            'statusFilter' => $status,
            'scope'        => $scope,
            'totalData'    => $totalData,
            'totalPages'   => $totalPages,
            'currentPage'  => $page,
            'limit'        => $limit,
        ]);
    }

    public function storePermit() {
        $db = Database::getInstance();
        $db->query("INSERT INTO permits (student_id, type, reason, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, 'PENDING')", [
            $_POST['student_id'], $_POST['type'], $_POST['reason'], $_POST['start_date'], $_POST['end_date']
        ]);
        Session::setFlash('success', 'Izin tercatat. Menunggu persetujuan.');
        header('Location: /boarding/permits');
    }

    public function approvePermit() {
        $id = $_GET['id'];
        $action = $_GET['action']; // APPROVE / REJECT / RETURN
        $status = ($action == 'APPROVE') ? 'APPROVED' : (($action == 'RETURN') ? 'RETURNED' : 'REJECTED');
        
        $db = Database::getInstance();
        $userId = Session::get('user_id'); // Perbaikan Session
        
        $db->query("UPDATE permits SET status = ?, approved_by = ? WHERE id = ?", [
            $status, $userId, $id
        ]);
        
        Session::setFlash('success', 'Status perizinan diperbarui.');
        header('Location: /boarding/permits');
    }

    // --- 3. POSKESTREN (KESEHATAN) ---
    public function health() {
        $db = Database::getInstance();
        $scope = ScopeFilter::get();

        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';
        $limit  = (int)($_GET['limit'] ?? 10);
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $where  = "hr.source = 'ASRAMA'";
        $params = [];

        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        if ($search !== '') {
            $where   .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%";
        }
        if ($status !== '') { $where .= " AND hr.status = ?"; $params[] = $status; }

        $total = $db->query(
            "SELECT COUNT(*) FROM health_records hr
             JOIN students s ON hr.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE $where",
            $params
        )->fetchColumn();

        $records = $db->query(
            "SELECT hr.*, s.full_name, s.nis, d.name as dorm_name, u.name as officer_name
             FROM health_records hr
             JOIN students s ON hr.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             LEFT JOIN dorms d ON s.dorm_id = d.id
             LEFT JOIN users u ON hr.officer_id = u.id
             WHERE $where ORDER BY hr.date DESC LIMIT $limit OFFSET $offset",
            $params
        )->fetchAll();

        [$sw2, $sp2] = ScopeFilter::apply('c');
        $students = $db->query(
            "SELECT s.id, s.full_name, s.nis, d.name as dorm_name
             FROM students s
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             LEFT JOIN dorms d ON s.dorm_id = d.id
             WHERE s.status='ACTIVE' $sw2 ORDER BY s.full_name",
            $sp2
        )->fetchAll();

        View::render('boarding/health', [
            'title'       => 'Laporan Kesehatan Asrama',
            'records'     => $records,
            'students'    => $students,
            'search'      => $search,
            'status'      => $status,
            'scope'       => $scope,
            'limit'       => $limit,
            'currentPage' => $page,
            'totalPages'  => $limit > 0 ? (int)ceil($total / $limit) : 1,
            'totalData'   => $total,
        ]);
    }

    public function storeHealth() {
        $db     = Database::getInstance();
        $userId = Session::get('user_id');

        $db->query(
            "INSERT INTO health_records (student_id, date, complaint, treatment, status, officer_id, source) VALUES (?,?,?,?,?,?,'ASRAMA')",
            [
                $_POST['student_id'], $_POST['date'], $_POST['complaint'],
                $_POST['treatment'] ?? null, $_POST['status'] ?? 'RAWAT_JALAN', $userId
            ]
        );

        Session::setFlash('success', 'Laporan kesehatan santri dicatat.');
        header('Location: /boarding/health');
    }

    // --- 4. MONITORING TAHFIDZ ---
    public function tahfidz() {
        $db = Database::getInstance();
        $userId   = Session::get('user_id');
        $userRole = Session::get('user_role');
        $scope    = ScopeFilter::get();
        $search   = $_GET['search'] ?? '';
        $type     = $_GET['type'] ?? '';
        $page     = (int)($_GET['page'] ?? 1);
        $limit    = (int)($_GET['limit'] ?? 10);
        $offset   = ($page - 1) * $limit;

        $where = "WHERE 1=1";
        $params = [];
        if ($scope !== 'GLOBAL') {
            $where .= " AND s.classroom_id IN (SELECT id FROM classrooms WHERE major = ?)";
            $params[] = $scope;
        }
        if ($userRole == 'guru') { $where .= " AND wl.teacher_id = ?"; $params[] = $userId; }
        if (!empty($search)) { $where .= " AND (s.full_name LIKE ? OR wl.surah_name LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($type))   { $where .= " AND wl.type = ?"; $params[] = $type; }

        $totalData  = $db->query("SELECT COUNT(*) FROM worship_logs wl JOIN students s ON wl.student_id = s.id $where", $params)->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        $logs = $db->query("SELECT wl.*, s.full_name, s.nis FROM worship_logs wl JOIN students s ON wl.student_id = s.id $where ORDER BY wl.date DESC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        $sWhere = "status='ACTIVE'";
        $sParams = [];
        if ($scope !== 'GLOBAL') { $sWhere .= " AND s.classroom_id IN (SELECT id FROM classrooms WHERE major = ?)"; $sParams[] = $scope; }
        $students = $db->query("SELECT s.id, s.full_name, s.nis FROM students s WHERE $sWhere ORDER BY s.full_name", $sParams)->fetchAll();

        View::render('boarding/tahfidz', [
            'title'       => 'Setoran Hafalan',
            'logs'        => $logs,
            'students'    => $students,
            'search'      => $search,
            'typeFilter'  => $type,
            'scope'       => $scope,
            'totalData'   => $totalData,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'limit'       => $limit,
        ]);
    }

    public function storeTahfidz() {
        $db = Database::getInstance();
        $userId = Session::get('user_id'); // Perbaikan Session

        $db->query("INSERT INTO worship_logs (student_id, teacher_id, date, type, surah_name, verses, grade, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
            $_POST['student_id'], $userId, $_POST['date'], 
            $_POST['type'], $_POST['surah_name'], $_POST['verses'], 
            $_POST['grade'], $_POST['note']
        ]);
        
        Session::setFlash('success', 'Setoran hafalan dicatat.');
        header('Location: /boarding/tahfidz');
    }
}

