<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class StudentTrackingController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        
        // Parameter Filter & Pagination
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        
        $search = $_GET['search'] ?? '';
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Base Query
        $where = "WHERE DATE(sal.logged_at) = ?";
        $params = [$date];

        if (!empty($search)) {
            $where .= " AND (s.full_name LIKE ? OR sal.location LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Hitung Total Data (Pagination)
        $countSql = "SELECT COUNT(*) as total 
                     FROM student_activity_logs sal 
                     JOIN students s ON sal.student_id = s.id 
                     $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        // Ambil Data Log
        $sql = "SELECT sal.*, s.full_name, s.nis, u.name as reporter_name 
                FROM student_activity_logs sal
                JOIN students s ON sal.student_id = s.id
                LEFT JOIN users u ON sal.created_by = u.id
                $where
                ORDER BY sal.logged_at DESC
                LIMIT $limit OFFSET $offset";

        $logs = $db->query($sql, $params)->fetchAll();
        $students = $db->query("SELECT id, full_name, nis FROM students WHERE status='ACTIVE' ORDER BY full_name")->fetchAll();

        View::render('discipline/tracking/index', [
            'title' => 'Pelacakan Aktivitas Santri',
            'logs' => $logs,
            'students' => $students,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'date' => $date,
            'search' => $search
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $loggedAt = $_POST['date'] . ' ' . $_POST['time']; // Gabung Date & Time
        $reporterId = Session::get('user_id'); 
        
        $db->query("INSERT INTO student_activity_logs (student_id, activity_type, location, description, logged_at, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?)", [
            $_POST['student_id'],
            $_POST['activity_type'],
            $_POST['location'],
            $_POST['description'],
            $loggedAt,
            $reporterId
        ]);

        Session::setFlash('success', 'Aktivitas berhasil dicatat.');
        header('Location: /discipline/tracking?date=' . $_POST['date']);
    }

    public function update() {
        $db = Database::getInstance();
        $loggedAt = $_POST['date'] . ' ' . $_POST['time'];
        
        $db->query("UPDATE student_activity_logs SET student_id=?, activity_type=?, location=?, description=?, logged_at=? WHERE id=?", [
            $_POST['student_id'],
            $_POST['activity_type'],
            $_POST['location'],
            $_POST['description'],
            $loggedAt,
            $_POST['id']
        ]);

        Session::setFlash('success', 'Log aktivitas berhasil diperbarui.');
        header('Location: /discipline/tracking?date=' . $_POST['date']);
    }

    public function delete() {
        $db = Database::getInstance();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db->query("DELETE FROM student_activity_logs WHERE id = ?", [$id]);
            Session::setFlash('success', 'Log aktivitas berhasil dihapus.');
        }
        header('Location: /discipline/tracking');
    }
}
