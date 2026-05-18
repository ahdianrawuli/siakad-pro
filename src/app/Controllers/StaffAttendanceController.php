<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class StaffAttendanceController {
    public function __construct() { Middleware::auth(); }

    private function processAttendance($roleFilter, $title, $redirectUrl) {
        $db = Database::getInstance();
        $date   = $_GET['date']   ?? date('Y-m-d');
        $search = trim($_GET['search'] ?? '');
        $limit  = (int)($_GET['limit'] ?? 10);
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $where  = "u.status = 'active'";
        $params = [];
        if ($roleFilter) {
            $where .= " AND r.slug = ?";
            $params[] = $roleFilter;
        } else {
            $where .= " AND r.slug IN ('guru','staff')";
        }
        if ($search !== '') {
            $where   .= " AND u.name LIKE ?";
            $params[] = "%$search%";
        }

        $total = $db->query(
            "SELECT COUNT(*) as c FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE $where", $params
        )->fetch()['c'];

        $users = $db->query(
            "SELECT u.id, u.name, r.slug as role_slug, sp.name as position_name
             FROM users u
             JOIN roles r ON u.role_id = r.id
             LEFT JOIN staff_members sm ON u.id = sm.user_id
             LEFT JOIN staff_positions sp ON sm.position_id = sp.id
             WHERE $where ORDER BY u.name ASC LIMIT $limit OFFSET $offset",
            $params
        )->fetchAll();

        $attendances = $db->query("SELECT * FROM staff_attendances WHERE date = ?", [$date])->fetchAll();
        $attMap = [];
        foreach ($attendances as $att) { $attMap[$att['user_id']] = $att; }

        View::render('staff/attendance/index', [
            'title'       => $title,
            'users'       => $users,
            'attMap'      => $attMap,
            'date'        => $date,
            'roleFilter'  => $roleFilter,
            'redirectUrl' => $redirectUrl,
            'search'      => $search,
            'limit'       => $limit,
            'currentPage' => $page,
            'totalPages'  => $limit > 0 ? (int)ceil($total / $limit) : 1,
            'totalData'   => $total,
        ]);
    }

    public function teachers() {
        $this->processAttendance('guru', 'Absensi Guru', '/attendance/teachers');
    }

    public function staff() {
        $this->processAttendance('', 'Absensi Pegawai', '/staff/attendance');
    }

    public function store() {
        $db = Database::getInstance();
        $date = $_POST['date'];
        $attendances = $_POST['attendance'];
        $notes = $_POST['notes'] ?? [];
        $timeIns = $_POST['time_in'] ?? [];
        $timeOuts = $_POST['time_out'] ?? [];
        $adminId = Session::get('user_id');

        try {
            $db->getConnection()->beginTransaction();

            foreach ($attendances as $userId => $status) {
                $check = $db->query("SELECT id FROM staff_attendances WHERE user_id = ? AND date = ?", [$userId, $date])->fetch();
                
                $note = $notes[$userId] ?? '';
                $timeIn = !empty($timeIns[$userId]) ? $timeIns[$userId] : null;
                $timeOut = !empty($timeOuts[$userId]) ? $timeOuts[$userId] : null;

                if ($check) {
                    $db->query("UPDATE staff_attendances SET status = ?, time_in = ?, time_out = ?, notes = ?, created_by = ? WHERE id = ?", 
                        [$status, $timeIn, $timeOut, $note, $adminId, $check['id']]);
                } else {
                    $db->query("INSERT INTO staff_attendances (user_id, date, status, time_in, time_out, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)", 
                        [$userId, $date, $status, $timeIn, $timeOut, $note, $adminId]);
                }
            }

            $db->getConnection()->commit();
            Session::setFlash('success', 'Data absensi berhasil disimpan.');

        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function delete() {
        // Fitur reset absensi per orang hari itu
        $db = Database::getInstance();
        $userId = $_GET['user_id'];
        $date = $_GET['date'];
        
        $db->query("DELETE FROM staff_attendances WHERE user_id = ? AND date = ?", [$userId, $date]);
        Session::setFlash('success', 'Absensi user tersebut di-reset.');
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}

