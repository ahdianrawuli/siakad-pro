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

        $where  = "r.slug = ? AND u.status = 'active'";
        $params = [$roleFilter];
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
        $this->processAttendance('staff', 'Absensi Staff Sekolah', '/attendance/staff');
    }

    public function store() {
        $db = Database::getInstance();
        $date = $_POST['date'];
        $attendances = $_POST['attendance']; // Array [user_id => status]
        $notes = $_POST['notes'] ?? [];
        $adminId = Session::get('user_id');

        try {
            $db->getConnection()->beginTransaction();

            foreach ($attendances as $userId => $status) {
                // Cek apakah data sudah ada
                $check = $db->query("SELECT id FROM staff_attendances WHERE user_id = ? AND date = ?", [$userId, $date])->fetch();
                
                $note = $notes[$userId] ?? '';
                // Default jam masuk jika hadir (opsional, bisa dikosongkan)
                $timeIn = ($status == 'HADIR') ? '07:00:00' : null;

                if ($check) {
                    // Update
                    $db->query("UPDATE staff_attendances SET status = ?, notes = ?, created_by = ? WHERE id = ?", 
                        [$status, $note, $adminId, $check['id']]);
                } else {
                    // Insert
                    $db->query("INSERT INTO staff_attendances (user_id, date, status, time_in, notes, created_by) VALUES (?, ?, ?, ?, ?, ?)", 
                        [$userId, $date, $status, $timeIn, $note, $adminId]);
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

