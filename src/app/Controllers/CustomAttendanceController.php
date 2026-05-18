<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class CustomAttendanceController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $typeId   = $_GET['type_id'] ?? '';
        $classId  = $_GET['class_id'] ?? '';
        $date     = $_GET['date'] ?? date('Y-m-d');
        $search   = $_GET['search'] ?? '';
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $limit    = 20;
        $scope    = ScopeFilter::get();

        $types = $db->query("SELECT * FROM custom_attendance_types WHERE is_active=1 ORDER BY name")->fetchAll();

        $persons = [];
        $existing = [];
        $selectedType = null;
        $totalPersons = 0;

        if ($typeId) {
            $selectedType = $db->query("SELECT * FROM custom_attendance_types WHERE id=?", [$typeId])->fetch();
            if ($selectedType) {
                $posFilter = '';
                if ($selectedType['position_ids']) {
                    $posIds = $selectedType['position_ids'];
                    $posFilter = " AND sm.position_id IN ($posIds)";
                }

                if ($selectedType['target'] === 'GURU' || $selectedType['target'] === 'SEMUA') {
                    $guruWhere = "u.role_id IN (2,3,7) AND u.status='active'";
                    if ($posFilter) $guruWhere .= " AND EXISTS (SELECT 1 FROM staff_members sm WHERE sm.user_id=u.id $posFilter)";
                    if ($search) $guruWhere .= " AND u.name LIKE '%" . addslashes($search) . "%'";
                    $persons = array_merge($persons, $db->query("SELECT u.id, u.name as full_name, 'guru' as role, COALESCE(sp.name,'') as position_name FROM users u LEFT JOIN staff_members sm ON sm.user_id=u.id LEFT JOIN staff_positions sp ON sm.position_id=sp.id WHERE $guruWhere ORDER BY u.name")->fetchAll());
                }
                if ($selectedType['target'] === 'SISWA' || $selectedType['target'] === 'SEMUA') {
                    $where = "s.status='ACTIVE'";
                    $params = [];
                    if ($scope !== 'GLOBAL') { $where .= " AND c.major=?"; $params[] = $scope; }
                    if ($classId) { $where .= " AND s.classroom_id=?"; $params[] = $classId; }
                    if ($search) { $where .= " AND s.full_name LIKE ?"; $params[] = "%$search%"; }
                    $persons = array_merge($persons, $db->query("SELECT s.id, s.full_name, 'siswa' as role FROM students s LEFT JOIN classrooms c ON s.classroom_id=c.id WHERE $where ORDER BY s.full_name", $params)->fetchAll());
                }

                $totalPersons = count($persons);
                $totalPages = max(1, ceil($totalPersons / $limit));
                $persons = array_slice($persons, ($page - 1) * $limit, $limit);

                // Existing for current page persons
                if (!empty($persons)) {
                    $ids = array_column($persons, 'id');
                    $ph = implode(',', $ids);
                    $rows = $db->query("SELECT person_id, session_num, status, notes, time_in FROM custom_attendances WHERE type_id=? AND date=? AND person_id IN ($ph)", [$typeId, $date])->fetchAll();
                    foreach ($rows as $r) { $existing[$r['person_id']][$r['session_num']] = ['status' => $r['status'], 'notes' => $r['notes'], 'time_in' => $r['time_in']]; }
                }
            }
        }

        [$sw, $sp] = ScopeFilter::apply('c');
        $classrooms = $db->query("SELECT * FROM classrooms c WHERE 1=1 $sw ORDER BY name", $sp)->fetchAll();

        View::render('attendance/custom', [
            'title'        => 'Custom Absen',
            'types'        => $types,
            'selectedType' => $selectedType,
            'typeId'       => $typeId,
            'classId'      => $classId,
            'date'         => $date,
            'search'       => $search,
            'persons'      => $persons,
            'existing'     => $existing,
            'classrooms'   => $classrooms,
            'scope'        => $scope,
            'page'         => $page,
            'totalPages'   => $totalPages ?? 1,
            'totalPersons' => $totalPersons,
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $typeId = $_POST['type_id'];
        $date   = $_POST['date'];
        $data   = $_POST['attendance'] ?? [];
        $notes  = $_POST['notes'] ?? [];
        $times  = $_POST['time_in'] ?? [];
        $userId = Session::get('user_id');

        foreach ($data as $personId => $sessions) {
            if (is_array($sessions)) {
                foreach ($sessions as $sessNum => $status) {
                    if (empty($status)) continue;
                    $timeVal = $times[$personId][$sessNum] ?? null;
                    $db->query("INSERT INTO custom_attendances (type_id, person_id, date, session_num, time_in, status, notes, recorded_by)
                        VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status), notes=VALUES(notes), time_in=VALUES(time_in), recorded_by=VALUES(recorded_by)",
                        [$typeId, $personId, $date, $sessNum, $timeVal ?: null, $status, $notes[$personId] ?? null, $userId]);
                }
            } else {
                if (empty($sessions)) continue;
                $timeVal = $times[$personId][1] ?? null;
                $db->query("INSERT INTO custom_attendances (type_id, person_id, date, session_num, time_in, status, notes, recorded_by)
                    VALUES (?,?,?,1,?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status), notes=VALUES(notes), time_in=VALUES(time_in), recorded_by=VALUES(recorded_by)",
                    [$typeId, $personId, $date, $timeVal ?: null, $sessions, $notes[$personId] ?? null, $userId]);
            }
        }
        Session::setFlash('success', 'Absensi berhasil disimpan.');
        header("Location: /attendance/custom?type_id=$typeId&class_id=" . ($_POST['class_id'] ?? '') . "&date=$date&page=" . ($_POST['page'] ?? 1));
    }

    public function types() {
        $db = Database::getInstance();
        $types = $db->query("SELECT * FROM custom_attendance_types ORDER BY name")->fetchAll();
        $positions = $db->query("SELECT id, name FROM staff_positions ORDER BY name")->fetchAll();
        View::render('attendance/custom_types', ['title' => 'Jenis Absen Custom', 'types' => $types, 'positions' => $positions]);
    }

    public function storeType() {
        $db = Database::getInstance();
        $statuses = !empty($_POST['statuses']) ? $_POST['statuses'] : 'HADIR,TIDAK_HADIR,IZIN,SAKIT,TERLAMBAT';
        $sessions = max(1, (int)($_POST['sessions'] ?? 1));
        $sessionLabels = $_POST['session_labels'] ?? '';
        $positionIds = !empty($_POST['position_ids']) ? implode(',', $_POST['position_ids']) : null;
        $hasTime = ($_POST['has_time'] ?? '0') === '1' ? 1 : 0;
        $sessionTimes = $_POST['session_times'] ?? '';
        $db->query("INSERT INTO custom_attendance_types (name, target, position_ids, statuses, sessions, session_labels, has_time, session_times) VALUES (?,?,?,?,?,?,?,?)",
            [$_POST['name'], $_POST['target'], $positionIds, $statuses, $sessions, $sessionLabels, $hasTime, $sessionTimes]);
        Session::setFlash('success', 'Jenis absen ditambahkan.');
        header('Location: /attendance/custom/types');
    }

    public function updateType() {
        $db = Database::getInstance();
        $sessions = max(1, (int)($_POST['sessions'] ?? 1));
        $positionIds = !empty($_POST['position_ids']) ? implode(',', $_POST['position_ids']) : null;
        $hasTime = ($_POST['has_time'] ?? '0') === '1' ? 1 : 0;
        $db->query("UPDATE custom_attendance_types SET name=?, target=?, position_ids=?, statuses=?, sessions=?, session_labels=?, has_time=?, session_times=? WHERE id=?",
            [$_POST['name'], $_POST['target'], $positionIds, $_POST['statuses'], $sessions, $_POST['session_labels'] ?? '', $hasTime, $_POST['session_times'] ?? '', $_POST['id']]);
        Session::setFlash('success', 'Jenis absen diperbarui.');
        header('Location: /attendance/custom/types');
    }

    public function deleteType() {
        $db = Database::getInstance();
        $db->query("DELETE FROM custom_attendances WHERE type_id=?", [$_GET['id']]);
        $db->query("DELETE FROM custom_attendance_types WHERE id=?", [$_GET['id']]);
        Session::setFlash('success', 'Jenis absen dihapus.');
        header('Location: /attendance/custom/types');
    }

    public function printReport() {
        $db = Database::getInstance();
        $typeId  = $_GET['type_id'] ?? '';
        $classId = $_GET['class_id'] ?? '';
        $dateFrom = !empty($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
        $dateTo   = !empty($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

        $type = $db->query("SELECT * FROM custom_attendance_types WHERE id=?", [$typeId])->fetch();
        if (!$type) { header('Location: /attendance/custom'); exit; }

        $where = "ca.type_id=? AND ca.date BETWEEN ? AND ?";
        $params = [$typeId, $dateFrom, $dateTo];

        if ($type['target'] === 'SISWA' || $type['target'] === 'SEMUA') {
            $report = $db->query("
                SELECT s.full_name as name, s.nis, c.name as class_name, '' as position_name, ca.date, ca.session_num, ca.time_in, ca.status
                FROM custom_attendances ca
                JOIN students s ON ca.person_id = s.id
                LEFT JOIN classrooms c ON s.classroom_id = c.id
                WHERE $where" . ($classId ? " AND s.classroom_id=?" : "") . "
                ORDER BY s.full_name, ca.date, ca.session_num",
                $classId ? array_merge($params, [$classId]) : $params
            )->fetchAll();
        } else {
            $report = $db->query("
                SELECT u.name, '' as nis, '' as class_name, COALESCE(sp.name,'') as position_name, ca.date, ca.session_num, ca.time_in, ca.status
                FROM custom_attendances ca
                JOIN users u ON ca.person_id = u.id
                LEFT JOIN staff_members sm ON sm.user_id = u.id
                LEFT JOIN staff_positions sp ON sm.position_id = sp.id
                WHERE $where ORDER BY u.name, ca.date, ca.session_num", $params)->fetchAll();
        }

        $classroom = $classId ? $db->query("SELECT name FROM classrooms WHERE id=?", [$classId])->fetch() : null;
        View::render('attendance/print_custom', ['type'=>$type,'report'=>$report,'classroom'=>$classroom,'dateFrom'=>$dateFrom,'dateTo'=>$dateTo]);
    }
}
