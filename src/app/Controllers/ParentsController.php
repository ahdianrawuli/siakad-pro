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
}
