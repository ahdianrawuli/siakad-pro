<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class TeachingAssignmentController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        
        // Filter & Pagination
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $where = "1=1";
        $params = [];
        if ($search) {
            $where .= " AND (u.name LIKE ? OR s.name LIKE ? OR c.name LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
        }

        // Ambil Data Assignment
        $sql = "SELECT ta.*, u.name as teacher_name, s.name as subject_name, c.name as class_name, ay.name as year_name
                FROM teaching_assignments ta
                JOIN users u ON ta.teacher_id = u.id
                JOIN subjects s ON ta.subject_id = s.id
                JOIN classrooms c ON ta.classroom_id = c.id
                JOIN academic_years ay ON ta.academic_year_id = ay.id
                WHERE $where
                ORDER BY ta.created_at DESC LIMIT $limit OFFSET $offset";
        
        $total = $db->query("SELECT COUNT(*) FROM teaching_assignments ta 
                             JOIN users u ON ta.teacher_id = u.id 
                             JOIN subjects s ON ta.subject_id = s.id 
                             JOIN classrooms c ON ta.classroom_id = c.id 
                             WHERE $where", $params)->fetchColumn();
        
        $assignments = $db->query($sql, $params)->fetchAll();

        // Data Master untuk Modal
        $teachers = $db->query("SELECT id, name FROM users WHERE role_id = 3 AND status='active' ORDER BY name")->fetchAll();
        $subjects = $db->query("SELECT id, name FROM subjects ORDER BY name")->fetchAll();
        $classrooms = $db->query("SELECT id, name FROM classrooms ORDER BY level, name")->fetchAll();
        $years = $db->query("SELECT id, name, semester FROM academic_years ORDER BY id DESC")->fetchAll();

        View::render('academic/assignments/index', [
            'title' => 'SK Mengajar (Pembagian Tugas)',
            'assignments' => $assignments,
            'teachers' => $teachers,
            'subjects' => $subjects,
            'classrooms' => $classrooms,
            'years' => $years,
            'currentPage' => $page,
            'totalPages' => ceil($total / $limit),
            'search' => $search
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        
        // Simpan Data
        $db->query("INSERT INTO teaching_assignments (academic_year_id, teacher_id, subject_id, classroom_id, sk_number) VALUES (?, ?, ?, ?, ?)",
            [
                $_POST['academic_year_id'],
                $_POST['teacher_id'],
                $_POST['subject_id'],
                $_POST['classroom_id'],
                $_POST['sk_number'] ?? '-'
            ]);
            
        Session::setFlash('success', 'Penugasan berhasil disimpan.');
        header('Location: /academic/assignments');
    }

    public function delete() {
        $db = Database::getInstance();
        $db->query("DELETE FROM teaching_assignments WHERE id=?", [$_GET['id']]);
        Session::setFlash('success', 'Data dihapus.');
        header('Location: /academic/assignments');
    }
}

