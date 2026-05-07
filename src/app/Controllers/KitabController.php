<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class KitabController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db     = Database::getInstance();
        $userId = Session::get('user_id');
        $role   = Session::get('user_role');
        $search = $_GET['kitab'] ?? '';
        $teacherFilter = $_GET['teacher_id'] ?? '';
        $page   = (int)($_GET['page'] ?? 1);
        $limit  = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $where = "WHERE 1=1";
        $params = [];
        if ($role == 'guru') { $where .= " AND j.teacher_id = ?"; $params[] = $userId; }
        if (!empty($search))        { $where .= " AND j.kitab_name LIKE ?"; $params[] = "%$search%"; }
        if (!empty($teacherFilter)) { $where .= " AND j.teacher_id = ?";   $params[] = $teacherFilter; }

        $totalData  = $db->query("SELECT COUNT(*) FROM kitab_journals j $where", $params)->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        $journals = $db->query("SELECT j.*, u.name as teacher_name FROM kitab_journals j LEFT JOIN users u ON j.teacher_id = u.id $where ORDER BY j.date DESC LIMIT $limit OFFSET $offset", $params)->fetchAll();
        $teachers = $db->query("SELECT id, name FROM users WHERE role_id = 3 ORDER BY name")->fetchAll();

        View::render('academic/kitab', [
            'title'          => 'Jurnal Kitab',
            'journals'       => $journals,
            'teachers'       => $teachers,
            'role'           => $role,
            'search'         => $search,
            'teacherFilter'  => $teacherFilter,
            'totalData'      => $totalData,
            'totalPages'     => $totalPages,
            'currentPage'    => $page,
            'limit'          => $limit,
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $db->query("INSERT INTO kitab_journals (teacher_id, class_name, kitab_name, date, start_page, end_page, chapter, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
            Session::get('user_id'), $_POST['class_name'], $_POST['kitab_name'], $_POST['date'],
            $_POST['start_page'], $_POST['end_page'], $_POST['chapter'], $_POST['notes']
        ]);
        
        Session::setFlash('success', 'Jurnal Kitab tersimpan.');
        header('Location: /academic/kitab');
    }
}
