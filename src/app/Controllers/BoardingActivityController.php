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
        $day = $_GET['day'] ?? '';

        $sql = "SELECT * FROM boarding_activities WHERE 1=1";
        $params = [];

        if ($day) {
            $sql .= " AND day = ?";
            $params[] = $day;
        }

        $sql .= " ORDER BY day ASC, start_time ASC";
        $activities = $db->query($sql, $params)->fetchAll();

        View::render('boarding/activities/index', [
            'title' => 'Jadwal Kegiatan Asrama',
            'activities' => $activities,
            'selectedDay' => $day
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        
        $db->query("INSERT INTO boarding_activities (name, day, start_time, end_time, description) VALUES (?, ?, ?, ?, ?)", [
            $_POST['name'], $_POST['day'], $_POST['start_time'], $_POST['end_time'], $_POST['description']
        ]);

        Session::setFlash('success', 'Kegiatan berhasil ditambahkan.');
        header('Location: /boarding/activities');
    }

    public function delete() {
        $db = Database::getInstance();
        $db->query("DELETE FROM boarding_activities WHERE id = ?", [$_GET['id']]);
        Session::setFlash('success', 'Kegiatan dihapus.');
        header('Location: /boarding/activities');
    }
}
