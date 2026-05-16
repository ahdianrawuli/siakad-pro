<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class PrayerAttendanceController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $scope = ScopeFilter::get();
        $classId = $_GET['class_id'] ?? '';
        $search  = $_GET['search'] ?? '';
        $date    = $_GET['date'] ?? date('Y-m-d');
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo   = $_GET['date_to'] ?? date('Y-m-d');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = 20;
        $offset  = ($page - 1) * $limit;

        $where = "WHERE s.status='ACTIVE'";
        $params = [];
        if ($scope !== 'GLOBAL') { $where .= " AND c.major = ?"; $params[] = $scope; }
        if ($classId) { $where .= " AND s.classroom_id = ?"; $params[] = $classId; }
        if ($search) { $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

        $totalData  = $db->query("SELECT COUNT(*) FROM students s LEFT JOIN classrooms c ON s.classroom_id=c.id $where", $params)->fetchColumn();
        $totalPages = max(1, ceil($totalData / $limit));

        $students = $db->query("SELECT s.id, s.full_name, s.nis, c.name as class_name
            FROM students s LEFT JOIN classrooms c ON s.classroom_id=c.id
            $where ORDER BY c.name, s.full_name LIMIT $limit OFFSET $offset", $params)->fetchAll();

        $prayerTypes = $db->query("SELECT * FROM prayer_types WHERE is_active=1 ORDER BY order_num")->fetchAll();

        // Ambil data absensi hari ini
        $existing = [];
        if (!empty($students)) {
            $sids = implode(',', array_column($students, 'id'));
            $rows = $db->query("SELECT student_id, prayer_type_id, status FROM prayer_attendances WHERE date=? AND student_id IN ($sids)", [$date])->fetchAll();
            foreach ($rows as $r) { $existing[$r['student_id']][$r['prayer_type_id']] = $r['status']; }
        }

        [$sw, $sp] = ScopeFilter::apply('c');
        $classrooms = $db->query("SELECT * FROM classrooms c WHERE 1=1 $sw ORDER BY name", $sp)->fetchAll();

        View::render('boarding/prayer_attendance', [
            'title'       => 'Absensi Sholat',
            'students'    => $students,
            'prayerTypes' => $prayerTypes,
            'existing'    => $existing,
            'classrooms'  => $classrooms,
            'classId'     => $classId,
            'search'      => $search,
            'date'        => $date,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'scope'       => $scope,
            'totalData'   => $totalData,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'limit'       => $limit,
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $date = $_POST['date'];
        $data = $_POST['prayer'] ?? []; // [student_id][prayer_type_id] = status
        $userId = Session::get('user_id');

        foreach ($data as $studentId => $prayers) {
            foreach ($prayers as $typeId => $status) {
                if (empty($status)) continue;
                $db->query("INSERT INTO prayer_attendances (student_id, prayer_type_id, date, status, recorded_by)
                    VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status), recorded_by=VALUES(recorded_by)",
                    [$studentId, $typeId, $date, $status, $userId]);
            }
        }

        Session::setFlash('success', 'Absensi sholat berhasil disimpan.');
        header('Location: /boarding/prayer?class_id=' . ($_POST['class_id'] ?? '') . '&date=' . $date);
    }

    public function types() {
        $db = Database::getInstance();
        $types = $db->query("SELECT * FROM prayer_types ORDER BY order_num")->fetchAll();
        View::render('boarding/prayer_types', ['title' => 'Jenis Sholat', 'types' => $types]);
    }

    public function storeType() {
        $db = Database::getInstance();
        $db->query("INSERT INTO prayer_types (name, category, order_num) VALUES (?,?,?)",
            [$_POST['name'], $_POST['category'], $_POST['order_num'] ?? 99]);
        Session::setFlash('success', 'Jenis sholat ditambahkan.');
        header('Location: /boarding/prayer/types');
    }

    public function deleteType() {
        $db = Database::getInstance();
        $db->query("DELETE FROM prayer_types WHERE id=?", [$_GET['id']]);
        Session::setFlash('success', 'Jenis sholat dihapus.');
        header('Location: /boarding/prayer/types');
    }

    public function printReport() {
        $db = Database::getInstance();
        $scope   = ScopeFilter::get();
        $classId = $_GET['class_id'] ?? '';
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo   = $_GET['date_to'] ?? date('Y-m-d');

        $where = "WHERE s.status='ACTIVE'";
        $params = [];
        if ($scope !== 'GLOBAL') { $where .= " AND c.major = ?"; $params[] = $scope; }
        if ($classId) { $where .= " AND s.classroom_id = ?"; $params[] = $classId; }

        $students = $db->query("SELECT s.id, s.full_name, s.nis, c.name as class_name
            FROM students s LEFT JOIN classrooms c ON s.classroom_id=c.id $where ORDER BY c.name, s.full_name", $params)->fetchAll();

        $prayerTypes = $db->query("SELECT * FROM prayer_types WHERE is_active=1 ORDER BY order_num")->fetchAll();

        // Rekap per siswa
        $report = [];
        foreach ($students as $s) {
            $row = ['full_name' => $s['full_name'], 'nis' => $s['nis'], 'class_name' => $s['class_name']];
            foreach ($prayerTypes as $pt) {
                $counts = $db->query("SELECT status, COUNT(*) as c FROM prayer_attendances WHERE student_id=? AND prayer_type_id=? AND date BETWEEN ? AND ? GROUP BY status",
                    [$s['id'], $pt['id'], $dateFrom, $dateTo])->fetchAll(\PDO::FETCH_KEY_PAIR);
                $row['prayer_' . $pt['id'] . '_hadir'] = $counts['HADIR'] ?? 0;
                $row['prayer_' . $pt['id'] . '_terlambat'] = $counts['TERLAMBAT'] ?? 0;
                $row['prayer_' . $pt['id'] . '_izin'] = $counts['IZIN'] ?? 0;
                $row['prayer_' . $pt['id'] . '_sakit'] = $counts['SAKIT'] ?? 0;
                $row['prayer_' . $pt['id'] . '_tidak'] = $counts['TIDAK_HADIR'] ?? 0;
            }
            $report[] = $row;
        }

        $classroom = $classId ? $db->query("SELECT name FROM classrooms WHERE id=?", [$classId])->fetch() : null;

        View::render('boarding/print_prayer', [
            'report'      => $report,
            'prayerTypes' => $prayerTypes,
            'classroom'   => $classroom,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
        ]);
    }
}
