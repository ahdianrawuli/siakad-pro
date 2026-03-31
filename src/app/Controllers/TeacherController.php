<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Database;
use App\Core\Middleware;

class TeacherController {
    public function __construct() {
        Middleware::auth();
    }

    public function index() {
        $db = Database::getInstance();
        
        // --- PARAMETER SEARCH & FILTER ---
        $search = $_GET['search'] ?? '';
        $gender = $_GET['gender'] ?? '';
        $education = $_GET['education'] ?? '';
        $status = $_GET['status'] ?? '';
        
        // --- PAGINATION ---
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $query = "SELECT * FROM teachers WHERE 1=1";
        $params = [];

        if ($search) {
            $query .= " AND (full_name LIKE ? OR nip LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($gender) { $query .= " AND gender = ?"; $params[] = $gender; }
        if ($education) { $query .= " AND education = ?"; $params[] = $education; }
        if ($status) { $query .= " AND status = ?"; $params[] = $status; }

        // Hitung Total Data
        $totalSql = str_replace("SELECT *", "SELECT COUNT(*)", $query);
        $totalDataRow = $db->query($totalSql, $params)->fetch();
        $totalData = $totalDataRow['COUNT(*)'] ?? 0;

        // Sorting & Limit
        $query .= " ORDER BY full_name ASC LIMIT $limit OFFSET $offset";
        $teachers = $db->query($query, $params)->fetchAll();

        $totalPages = ceil($totalData / $limit);

        View::render('teachers/index', [
            'title' => 'Data Guru',
            'teachers' => $teachers,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'selectedGender' => $gender,
            'selectedEducation' => $education,
            'selectedStatus' => $status
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        
        try {
            $db->getConnection()->beginTransaction();

            $email = !empty($_POST['email']) ? $_POST['email'] : $_POST['username'] . '@siakad.com';

            // 1. Buat Akun User (Role 3 = Guru)
            $sqlUser = "INSERT INTO users (name, username, email, password, role_id, status) VALUES (?, ?, ?, ?, ?, ?)";
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            $db->query($sqlUser, [
                $_POST['full_name'], 
                $_POST['username'], 
                $email, 
                $pass, 
                3, 
                'active'
            ]);
            
            $userId = $db->getConnection()->lastInsertId();

            // 2. Simpan Data Guru
            $sqlTeacher = "INSERT INTO teachers (user_id, nip, full_name, gender, education, phone, email, address, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $db->query($sqlTeacher, [
                $userId,
                $_POST['nip'],
                $_POST['full_name'],
                $_POST['gender'],
                $_POST['education'],
                $_POST['phone'],
                $email,
                $_POST['address'],
                'ACTIVE'
            ]);

            $db->getConnection()->commit();
            Session::setFlash('success', 'Data Guru dan Akun Login berhasil dibuat.');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        header('Location: /student-affairs/teachers');
    }

    public function update() {
        $db = Database::getInstance();
        try {
            $sql = "UPDATE teachers SET nip=?, full_name=?, gender=?, education=?, phone=?, email=?, address=?, status=? WHERE id=?";
            $db->query($sql, [
                $_POST['nip'], $_POST['full_name'], $_POST['gender'], $_POST['education'], 
                $_POST['phone'], $_POST['email'], $_POST['address'], $_POST['status'], $_POST['id']
            ]);
            Session::setFlash('success', 'Data guru berhasil diperbarui.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
        header('Location: /student-affairs/teachers');
    }

    public function toggleStatus() {
        $db = Database::getInstance();
        $db->query("UPDATE teachers SET status = IF(status='ACTIVE', 'INACTIVE', 'ACTIVE') WHERE id = ?", [$_GET['id']]);
        header('Location: /student-affairs/teachers');
    }

    public function detail() {
        $db = Database::getInstance();
        $teacher = $db->query("SELECT * FROM teachers WHERE id = ?", [$_GET['id']])->fetch();
        if (!$teacher) {
            header('Location: /student-affairs/teachers');
            exit;
        }
        View::render('teachers/detail', ['title' => 'Profil Guru', 'teacher' => $teacher]);
    }
}
