<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class KbmPermitController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $type   = $_GET['type'] ?? '';
        $page   = (int)($_GET['page'] ?? 1);
        $limit  = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $where = "WHERE 1=1";
        $params = [];
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);
        if (!empty($search)) { $where .= " AND (s.full_name LIKE ? OR kp.type LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($type))   { $where .= " AND kp.type = ?"; $params[] = $type; }

        $totalData  = $db->query("SELECT COUNT(*) FROM kbm_permits kp JOIN students s ON kp.student_id = s.id LEFT JOIN classrooms c ON s.classroom_id = c.id $where", $params)->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        $permits  = $db->query("SELECT kp.*, s.full_name, s.nis, c.name as class_name, ay.name as year_name 
                FROM kbm_permits kp
                JOIN students s ON kp.student_id = s.id
                LEFT JOIN classrooms c ON s.classroom_id = c.id
                JOIN academic_years ay ON kp.academic_year_id = ay.id
                $where ORDER BY kp.date DESC, kp.created_at DESC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        [$sw2, $sp2] = ScopeFilter::apply('c');
        $students = $db->query("SELECT s.id, s.full_name, s.nis FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id WHERE s.status='ACTIVE' $sw2 ORDER BY s.full_name", $sp2)->fetchAll();
        $years    = $db->query("SELECT id, name FROM academic_years WHERE is_active = 1")->fetchAll();

        View::render('academic/kbm_permits/index', [
            'title' => 'Dispensasi KBM', 'permits' => $permits, 'students' => $students,
            'years' => $years, 'search' => $search, 'typeFilter' => $type,
            'totalData' => $totalData, 'totalPages' => $totalPages, 'currentPage' => $page, 'limit' => $limit,
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $adminId = Session::get('user_id');
        $date = $_POST['date']; $stuId = $_POST['student_id']; $type = $_POST['type'];
        $db->query("INSERT INTO kbm_permits (student_id, academic_year_id, date, type, reason, status, created_by) VALUES (?,?,?,?,?,'APPROVED',?)",
            [$stuId, $_POST['academic_year_id'], $date, $type, $_POST['reason'], $adminId]);
        $absStatus = $type === 'SAKIT' ? 'S' : 'I';
        $existing  = $db->query("SELECT id FROM attendances WHERE student_id = ? AND date = ?", [$stuId, $date])->fetch();
        if ($existing) {
            $db->query("UPDATE attendances SET status = ?, notes = ? WHERE id = ?", [$absStatus, 'Dispensasi KBM: ' . $_POST['reason'], $existing['id']]);
        } else {
            $student = $db->query("SELECT classroom_id FROM students WHERE id = ?", [$stuId])->fetch();
            $db->query("INSERT INTO attendances (student_id, classroom_id, date, status, notes, recorded_by) VALUES (?,?,?,?,?,?)",
                [$stuId, $student['classroom_id'], $date, $absStatus, 'Dispensasi KBM: ' . $_POST['reason'], $adminId]);
        }
        Session::setFlash('success', 'Dispensasi KBM berhasil dicatat dan absensi diperbarui.');
        header('Location: /attendance/kbm-permits');
    }

    public function delete() {
        $db = Database::getInstance();
        $db->query("DELETE FROM kbm_permits WHERE id = ?", [$_GET['id']]);
        Session::setFlash('success', 'Data dihapus.');
        header('Location: /attendance/kbm-permits');
    }
}
