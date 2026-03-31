<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class ClassroomManageController {
    public function __construct() {
        Middleware::auth();
    }

    // ==========================================================
    // BAGIAN 1: CRUD DATA KELAS (DIPANGGIL OLEH MENU MASTER DATA)
    // ==========================================================

public function index() {
    $db = Database::getInstance();

    // 1. Ambil Parameter dari URL
    $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $filter_major = isset($_GET['major']) ? $_GET['major'] : '';
    
    $offset = ($page - 1) * $limit;

    // 2. Bangun Query String untuk Filter
    $whereClauses = [];
    $params = [];

    if (!empty($search)) {
        $whereClauses[] = "c.name LIKE ?";
        $params[] = "%$search%";
    }

    if (!empty($filter_major)) {
        $whereClauses[] = "c.major = ?";
        $params[] = $filter_major;
    }

    $whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

    // 3. Hitung Total Data (setelah difilter)
    $totalQuery = $db->query("SELECT COUNT(*) as total FROM classrooms c $whereSql", $params)->fetch();
    $totalData  = $totalQuery['total'];
    $totalPages = ceil($totalData / $limit);

    // 4. Ambil Data dengan Limit, Offset, dan Filter
    $sql = "SELECT c.*, u.name as teacher_name 
            FROM classrooms c
            LEFT JOIN users u ON c.homeroom_teacher_id = u.id
            $whereSql
            ORDER BY c.major ASC, c.level ASC, c.name ASC
            LIMIT $limit OFFSET $offset";
    
    $classrooms = $db->query($sql, $params)->fetchAll();
    $teachers   = $db->query("SELECT id, name FROM users WHERE role_id = 3 ORDER BY name ASC")->fetchAll();

    View::render('master/classrooms/index', [
        'title'       => 'Manajemen Data Kelas',
        'classrooms'  => $classrooms,
        'teachers'    => $teachers,
        'currentPage' => $page,
        'totalPages'  => $totalPages,
        'limit'       => $limit,
        'totalData'   => $totalData,
        'search'      => $search,
        'selectedMajor' => $filter_major
    ]);
}

    public function store() {
        $db = Database::getInstance();
        $name = $_POST['name'];
        $level = $_POST['level'];
        $major = $_POST['major']; // MTS / MA / PDF
        $teacherId = $_POST['homeroom_teacher_id'] ?: null;

        $sql = "INSERT INTO classrooms (name, level, major, homeroom_teacher_id) VALUES (?, ?, ?, ?)";
        $db->query($sql, [$name, $level, $major, $teacherId]);

        Session::setFlash('success', 'Kelas baru berhasil ditambahkan.');
        header('Location: /master/classrooms');
    }

    public function update() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        $name = $_POST['name'];
        $level = $_POST['level'];
        $major = $_POST['major'];
        $teacherId = $_POST['homeroom_teacher_id'] ?: null;

        $sql = "UPDATE classrooms SET name = ?, level = ?, major = ?, homeroom_teacher_id = ? WHERE id = ?";
        $db->query($sql, [$name, $level, $major, $teacherId, $id]);

        Session::setFlash('success', 'Data kelas berhasil diperbarui.');
        header('Location: /master/classrooms');
    }

    public function delete() {
        $db = Database::getInstance();
        $id = $_GET['id'];

        // Cek apakah ada siswa di kelas ini sebelum dihapus
        $checkSiswa = $db->query("SELECT id FROM students WHERE classroom_id = ?", [$id])->fetch();
        if ($checkSiswa) {
            Session::setFlash('error', 'Gagal hapus: Masih ada siswa yang terdaftar di kelas ini.');
        } else {
            $db->query("DELETE FROM classrooms WHERE id = ?", [$id]);
            Session::setFlash('success', 'Kelas berhasil dihapus.');
        }
        
        header('Location: /master/classrooms');
    }

    // ==========================================================
    // BAGIAN 2: KENAIKAN KELAS (PROMOTION)
    // ==========================================================

    public function promotion() {
        $db = Database::getInstance();
        $classrooms = $db->query("SELECT * FROM classrooms ORDER BY level ASC, name ASC")->fetchAll();
        
        $students = [];
        $sourceClassId = $_GET['source_id'] ?? null;
        
        if ($sourceClassId) {
            $students = $db->query("SELECT * FROM students WHERE classroom_id = ? AND status='ACTIVE' ORDER BY full_name", [$sourceClassId])->fetchAll();
        }

        View::render('academic/promotion', [
            'title' => 'Kenaikan Kelas',
            'classrooms' => $classrooms,
            'students' => $students,
            'sourceId' => $sourceClassId
        ]);
    }

    public function processPromotion() {
        $db = Database::getInstance();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /academic/promotion');
            exit;
        }

        $studentIds = $_POST['student_ids'] ?? [];
        $action = $_POST['action'] ?? 'promote';
        $targetClass = $_POST['target_class'] ?? null;
        $sourceId = $_POST['source_id'] ?? '';

        if (empty($studentIds)) {
            Session::setFlash('error', 'Gagal: Anda belum memilih siswa.');
            header("Location: /academic/promotion?source_id=$sourceId");
            exit;
        }

        if ($action === 'promote' && empty($targetClass)) {
            Session::setFlash('error', 'Gagal: Pilih Kelas Tujuan.');
            header("Location: /academic/promotion?source_id=$sourceId");
            exit;
        }

        try {
            $db->getConnection()->beginTransaction();
            foreach ($studentIds as $id) {
                if ($action === 'graduate') {
                    $student = $db->query("SELECT * FROM students WHERE id = ?", [$id])->fetch();
                    if ($student) {
                        $check = $db->query("SELECT id FROM alumni WHERE student_id = ?", [$id])->fetch();
                        if (!$check) {
                            $db->query("INSERT INTO alumni (student_id, nis, full_name, graduation_year, activity, detail_activity, phone, email) 
                                       VALUES (?, ?, ?, ?, 'LAINNYA', 'Lulus Otomatis via Sistem', ?, ?)", 
                                       [$student['id'], $student['nis'], $student['full_name'], date('Y'), $student['phone'] ?? null, $student['email'] ?? null]);
                        }
                        $db->query("UPDATE students SET classroom_id = NULL, status = 'GRADUATED' WHERE id = ?", [$id]);
                    }
                } else {
                    $db->query("UPDATE students SET classroom_id = ? WHERE id = ?", [$targetClass, $id]);
                }
            }
            $db->getConnection()->commit();
            Session::setFlash('success', count($studentIds) . ' Santri berhasil diproses.');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }
        header("Location: /academic/promotion?source_id=$sourceId");
    }

    // ==========================================================
    // BAGIAN 3: MANAJEMEN WALI KELAS (HOMEROOM)
    // ==========================================================

    public function assignHomeroomView() {
        $db = Database::getInstance();
        $classrooms = $db->query("
            SELECT c.*, u.name as teacher_name 
            FROM classrooms c
            LEFT JOIN users u ON c.homeroom_teacher_id = u.id
            ORDER BY c.level ASC, c.name ASC
        ")->fetchAll();

        $teachers = $db->query("SELECT id, name FROM users WHERE role_id = 3 ORDER BY name ASC")->fetchAll();

        View::render('academic/assign_homeroom', [
            'title' => 'Set Wali Kelas',
            'classrooms' => $classrooms,
            'teachers' => $teachers
        ]);
    }

    public function setHomeroom() {
        $classId = $_POST['classroom_id'];
        $teacherId = $_POST['teacher_id'] ?: null;
        $db = Database::getInstance();
        $db->query("UPDATE classrooms SET homeroom_teacher_id = ? WHERE id = ?", [$teacherId, $classId]);
        Session::setFlash('success', 'Wali kelas berhasil diperbarui.');
        header('Location: /academic/homeroom-assign'); 
    }
}
