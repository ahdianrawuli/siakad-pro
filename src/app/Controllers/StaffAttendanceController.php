<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class StaffAttendanceController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $date = $_GET['date'] ?? date('Y-m-d');
        $roleFilter = $_GET['role'] ?? ''; // 'guru' or 'staff'
        
        // 1. Ambil Data Pegawai (Guru & Staff)
        $sqlUsers = "SELECT u.id, u.name, r.slug as role_slug, sp.name as position_name 
                     FROM users u
                     JOIN roles r ON u.role_id = r.id
                     LEFT JOIN staff_members sm ON u.id = sm.user_id
                     LEFT JOIN staff_positions sp ON sm.position_id = sp.id
                     WHERE r.slug IN ('guru', 'staff') AND u.status = 'active'";
        
        if ($roleFilter) {
            $sqlUsers .= " AND r.slug = '$roleFilter'";
        }
        $sqlUsers .= " ORDER BY u.name ASC";
        $users = $db->query($sqlUsers)->fetchAll();

        // 2. Ambil Data Absensi pada Tanggal Terpilih
        $attendances = $db->query("SELECT * FROM staff_attendances WHERE date = ?", [$date])->fetchAll();
        
        // Mapping Absensi ke User ID agar mudah ditampilkan
        $attMap = [];
        foreach ($attendances as $att) {
            $attMap[$att['user_id']] = $att;
        }

        View::render('staff/attendance/index', [
            'title' => 'Absensi Guru & Staff',
            'users' => $users,
            'attMap' => $attMap,
            'date' => $date,
            'roleFilter' => $roleFilter
        ]);
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

        header("Location: /staff/attendance?date=$date");
    }

    public function delete() {
        // Fitur reset absensi per orang hari itu
        $db = Database::getInstance();
        $userId = $_GET['user_id'];
        $date = $_GET['date'];
        
        $db->query("DELETE FROM staff_attendances WHERE user_id = ? AND date = ?", [$userId, $date]);
        Session::setFlash('success', 'Absensi user tersebut di-reset.');
        header("Location: /staff/attendance?date=$date");
    }
}

