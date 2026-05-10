<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class AcademicSupportController {
    public function __construct() { Middleware::auth(); }

    // --- KALENDER ---
    public function calendar() {
        $db = Database::getInstance();
        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch();

        $search = $_GET['search'] ?? '';
        $filter = $_GET['type']   ?? '';
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $where  = "WHERE academic_year_id = ?";
        $params = [$activeYear['id'] ?? 0];
        if ($search) { $where .= " AND title LIKE ?"; $params[] = "%$search%"; }
        if ($filter) { $where .= " AND type = ?";    $params[] = $filter; }

        $total      = $db->query("SELECT COUNT(*) FROM academic_calendar $where", $params)->fetchColumn();
        $totalPages = max(1, ceil($total / $limit));
        $events     = $db->query("SELECT * FROM academic_calendar $where ORDER BY start_date ASC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        View::render('academic/calendar', [
            'title'       => 'Kalender Akademik',
            'events'      => $events,
            'total'       => $total,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'search'      => $search,
            'filter'      => $filter,
        ]);
    }

    public function storeEvent() {
        $db = Database::getInstance();
        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch();
        $db->query("INSERT INTO academic_calendar (academic_year_id, title, start_date, end_date, type, color) VALUES (?, ?, ?, ?, ?, ?)", [
            $activeYear['id'], $_POST['title'], $_POST['start_date'], $_POST['end_date'], $_POST['type'], $_POST['color']
        ]);
        Session::setFlash('success', 'Event berhasil ditambahkan.');
        header('Location: /academic/calendar');
    }

    public function updateEvent() {
        $db = Database::getInstance();
        $db->query("UPDATE academic_calendar SET title=?, start_date=?, end_date=?, type=?, color=? WHERE id=?", [
            $_POST['title'], $_POST['start_date'], $_POST['end_date'], $_POST['type'], $_POST['color'], (int)$_POST['id']
        ]);
        Session::setFlash('success', 'Event berhasil diperbarui.');
        header('Location: /academic/calendar');
    }

    public function deleteEvent() {
        $db = Database::getInstance();
        $db->query("DELETE FROM academic_calendar WHERE id = ?", [(int)$_POST['id']]);
        Session::setFlash('success', 'Event berhasil dihapus.');
        header('Location: /academic/calendar');
    }

    public function printCalendar() {
        $db = Database::getInstance();
        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch();

        $month = max(1, min(12, (int)($_GET['month'] ?? date('n'))));
        $year  = (int)($_GET['year'] ?? date('Y'));

        $monthStart = sprintf('%04d-%02d-01', $year, $month);
        $monthEnd   = sprintf('%04d-%02d-%02d', $year, $month, (int)date('t', mktime(0,0,0,$month,1,$year)));

        $events = $db->query(
            "SELECT * FROM academic_calendar
             WHERE academic_year_id = ?
               AND start_date <= ? AND end_date >= ?
             ORDER BY start_date ASC",
            [$activeYear['id'] ?? 0, $monthEnd, $monthStart]
        )->fetchAll();

        $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        View::render('academic/calendar_print', [
            'events'    => $events,
            'month'     => $month,
            'year'      => $year,
            'monthName' => $monthNames[$month],
        ]);
    }

    // --- BANK SOAL ---
    public function examBank() {
        $db = Database::getInstance();

        $page   = (int)($_GET['page'] ?? 1);
        $limit  = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $type   = $_GET['type'] ?? '';

        $where = "WHERE 1=1";
        $params = [];
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);
        if (!empty($search)) { $where .= " AND (e.title LIKE ? OR s.name LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($type))   { $where .= " AND e.type = ?"; $params[] = $type; }

        $totalData  = $db->query("SELECT COUNT(*) FROM exam_banks e JOIN subjects s ON e.subject_id = s.id LEFT JOIN classrooms c ON e.classroom_id = c.id $where", $params)->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        $exams = $db->query("
            SELECT e.*, s.name as subject_name, u.name as teacher_name, c.name as class_name
            FROM exam_banks e
            JOIN subjects s ON e.subject_id = s.id
            JOIN users u ON e.teacher_id = u.id
            LEFT JOIN classrooms c ON e.classroom_id = c.id
            $where ORDER BY e.created_at DESC LIMIT $limit OFFSET $offset
        ", $params)->fetchAll();

        $subjects = $db->query("SELECT * FROM subjects ORDER BY name")->fetchAll();
        [$sw2, $sp2] = ScopeFilter::apply('c2');
        $classrooms = $db->query("SELECT c2.id, c2.name FROM classrooms c2 WHERE 1=1 $sw2 ORDER BY c2.level, c2.name", $sp2)->fetchAll();

        View::render('academic/exams', [
            'title'       => 'Bank Soal',
            'exams'       => $exams,
            'subjects'    => $subjects,
            'classrooms'  => $classrooms,
            'totalData'   => $totalData,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'limit'       => $limit,
            'search'      => $search,
            'typeFilter'  => $type,
        ]);
    }

    public function storeExam() {
        $filename = '';
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['file']['tmp_name'], __DIR__ . '/../../public/uploads/exams/' . $filename);
        }

        $db = Database::getInstance();
        $db->query("INSERT INTO exam_banks (subject_id, classroom_id, teacher_id, title, file_path, type) VALUES (?,?,?,?,?,?)", [
            $_POST['subject_id'], $_POST['classroom_id'] ?: null,
            Session::get('user_id'), $_POST['title'], $filename, $_POST['type']
        ]);

        Session::setFlash('success', 'Soal berhasil diupload.');
        header('Location: /academic/exams');
    }
}
