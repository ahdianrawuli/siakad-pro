<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class BoardingActivityController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $day    = $_GET['day'] ?? '';
        $search = $_GET['search'] ?? '';
        $page   = (int)($_GET['page'] ?? 1);
        $limit  = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $where = "WHERE 1=1";
        $params = [];
        if ($day)    { $where .= " AND day = ?";        $params[] = $day; }
        if ($search) { $where .= " AND name LIKE ?";    $params[] = "%$search%"; }

        $totalData  = $db->query("SELECT COUNT(*) FROM boarding_activities $where", $params)->fetchColumn();
        $totalPages = ceil($totalData / $limit);
        $activities = $db->query("SELECT * FROM boarding_activities $where ORDER BY day ASC, start_time ASC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        View::render('boarding/activities/index', [
            'title'       => 'Jadwal Kegiatan Asrama',
            'activities'  => $activities,
            'selectedDay' => $day,
            'search'      => $search,
            'totalData'   => $totalData,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'limit'       => $limit,
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        
        $db->query("INSERT INTO boarding_activities (name, day, start_time, end_time, description) VALUES (?, ?, ?, ?, ?)", [
            $_POST['name'], $_POST['day'], $_POST['start_time'], $_POST['end_time'], $_POST['description']
        ]);

        Session::setFlash('success', 'Kegiatan berhasil ditambahkan.');
        header('Location: /asrama/activities');
    }

    public function delete() {
        $db = Database::getInstance();
        $db->query("DELETE FROM boarding_activities WHERE id = ?", [$_GET['id']]);
        Session::setFlash('success', 'Kegiatan dihapus.');
        header('Location: /asrama/activities');
    }
}
