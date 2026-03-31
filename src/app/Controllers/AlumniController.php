<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Database;
use App\Core\Middleware;

class AlumniController {
    public function __construct() {
        Middleware::auth();
    }

    public function index() {
        $db = Database::getInstance();
        
        // Parameter Filter & Pagination
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        
        $search = $_GET['search'] ?? '';
        $year = $_GET['year'] ?? '';
        $activity = $_GET['activity'] ?? '';

        // Base Query
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (full_name LIKE ? OR nis LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($year)) {
            $where .= " AND graduation_year = ?";
            $params[] = $year;
        }
        if (!empty($activity)) {
            $where .= " AND activity = ?";
            $params[] = $activity;
        }

        // Hitung Total Data (Optimized Count)
        $countSql = "SELECT COUNT(*) as total FROM alumni $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        // Ambil Data
        $sql = "SELECT * FROM alumni $where ORDER BY graduation_year DESC, full_name ASC LIMIT $limit OFFSET $offset";
        $alumni = $db->query($sql, $params)->fetchAll();

        View::render('alumni/index', [
            'title' => 'Data Alumni',
            'alumni' => $alumni,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'yearFilter' => $year,
            'activityFilter' => $activity
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        
        // Validasi NIS Unik
        $check = $db->query("SELECT id FROM alumni WHERE nis = ?", [$_POST['nis']])->fetch();
        if ($check) {
            Session::setFlash('error', 'NIS Alumni sudah terdaftar.');
            header('Location: /student-affairs/alumni');
            exit;
        }

        $sql = "INSERT INTO alumni (nis, full_name, graduation_year, activity, detail_activity, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $db->query($sql, [
            $_POST['nis'], $_POST['full_name'], $_POST['graduation_year'], 
            $_POST['activity'], $_POST['detail_activity'], $_POST['phone'], $_POST['email']
        ]);
        
        Session::setFlash('success', 'Data alumni berhasil ditambahkan.');
        header('Location: /student-affairs/alumni');
    }

    public function update() {
        $db = Database::getInstance();
        $sql = "UPDATE alumni SET nis=?, full_name=?, graduation_year=?, activity=?, detail_activity=?, phone=?, email=? WHERE id=?";
        $db->query($sql, [
            $_POST['nis'], $_POST['full_name'], $_POST['graduation_year'], 
            $_POST['activity'], $_POST['detail_activity'], $_POST['phone'], $_POST['email'], $_POST['id']
        ]);
        
        Session::setFlash('success', 'Data alumni berhasil diperbarui.');
        header('Location: /student-affairs/alumni');
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = Database::getInstance();
            $db->query("DELETE FROM alumni WHERE id = ?", [$id]);
            Session::setFlash('success', 'Data alumni berhasil dihapus.');
        }
        header('Location: /student-affairs/alumni');
    }
}
