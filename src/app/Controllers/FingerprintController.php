<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class FingerprintController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $devices = $db->query("SELECT * FROM fingerprint_devices ORDER BY name")->fetchAll();

        $mapPage = max(1, (int)($_GET['map_page'] ?? 1));
        $mapLimit = 10;
        $mapOffset = ($mapPage - 1) * $mapLimit;
        $mapTotal = $db->query("SELECT COUNT(*) FROM fingerprint_users")->fetchColumn();
        $mapTotalPages = max(1, ceil($mapTotal / $mapLimit));

        $mappings = $db->query("
            SELECT fu.*, fd.name as device_name,
                COALESCE(u.name, s.full_name) as person_name,
                IF(fu.user_id IS NOT NULL, 'Staff', 'Siswa') as person_type
            FROM fingerprint_users fu
            JOIN fingerprint_devices fd ON fu.device_id = fd.id
            LEFT JOIN users u ON fu.user_id = u.id
            LEFT JOIN students s ON fu.student_id = s.id
            ORDER BY fd.name, fu.finger_id LIMIT $mapLimit OFFSET $mapOffset
        ")->fetchAll();

        View::render('settings/fingerprint', [
            'title'         => 'Fingerprint Device',
            'devices'       => $devices,
            'mappings'      => $mappings,
            'mapPage'       => $mapPage,
            'mapTotalPages' => $mapTotalPages,
            'mapTotal'      => $mapTotal,
            'users'         => $db->query("SELECT id, name FROM users WHERE role_id IN (2,3,7) AND status='active' ORDER BY name")->fetchAll(),
            'students'      => $db->query("SELECT id, full_name, nis FROM students WHERE status='ACTIVE' ORDER BY full_name")->fetchAll(),
        ]);
    }

    public function storeDevice() {
        $db = Database::getInstance();
        $db->query("INSERT INTO fingerprint_devices (name, ip_address, port, location, type, api_key) VALUES (?,?,?,?,?,?)", [
            $_POST['name'], $_POST['ip_address'], $_POST['port'] ?? 4370,
            $_POST['location'] ?? null, $_POST['type'] ?? 'STAFF',
            bin2hex(random_bytes(16))
        ]);
        Session::setFlash('success', 'Device berhasil ditambahkan.');
        header('Location: /settings/fingerprint');
    }

    public function deleteDevice() {
        $db = Database::getInstance();
        $db->query("DELETE FROM fingerprint_users WHERE device_id=?", [$_GET['id']]);
        $db->query("DELETE FROM fingerprint_devices WHERE id=?", [$_GET['id']]);
        Session::setFlash('success', 'Device dihapus.');
        header('Location: /settings/fingerprint');
    }

    public function storeMapping() {
        $db = Database::getInstance();
        $deviceId = $_POST['device_id'];
        $fingerId = $_POST['finger_id'];
        $userId = $_POST['user_id'] ?: null;
        $studentId = $_POST['student_id'] ?: null;

        if (!$userId && !$studentId) {
            Session::setFlash('error', 'Pilih user atau siswa.');
            header('Location: /settings/fingerprint'); return;
        }

        try {
            $db->query("INSERT INTO fingerprint_users (device_id, finger_id, user_id, student_id) VALUES (?,?,?,?)
                ON DUPLICATE KEY UPDATE user_id=VALUES(user_id), student_id=VALUES(student_id)",
                [$deviceId, $fingerId, $userId, $studentId]);
            Session::setFlash('success', 'Mapping Finger ID berhasil disimpan.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal: ' . $e->getMessage());
        }
        header('Location: /settings/fingerprint');
    }

    public function deleteMapping() {
        $db = Database::getInstance();
        $db->query("DELETE FROM fingerprint_users WHERE id=?", [$_GET['id']]);
        Session::setFlash('success', 'Mapping dihapus.');
        header('Location: /settings/fingerprint');
    }

    // === API ENDPOINT: Terima data clock dari mesin ===
    public function apiClock() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $apiKey = $input['api_key'] ?? ($_SERVER['HTTP_X_API_KEY'] ?? '');
        $fingerId = $input['finger_id'] ?? null;
        $timestamp = $input['timestamp'] ?? date('Y-m-d H:i:s');
        $type = $input['type'] ?? 'IN'; // IN or OUT

        if (!$apiKey || !$fingerId) {
            echo json_encode(['error' => 'api_key and finger_id required']); exit;
        }

        $db = Database::getInstance();

        // Cari device berdasarkan API key
        $device = $db->query("SELECT * FROM fingerprint_devices WHERE api_key=? AND is_active=1", [$apiKey])->fetch();
        if (!$device) { echo json_encode(['error' => 'Invalid API key']); exit; }

        // Cari mapping user/student
        $mapping = $db->query("SELECT * FROM fingerprint_users WHERE device_id=? AND finger_id=?", [$device['id'], $fingerId])->fetch();
        if (!$mapping) { echo json_encode(['error' => 'Finger ID not registered']); exit; }

        $date = date('Y-m-d', strtotime($timestamp));
        $time = date('H:i:s', strtotime($timestamp));

        if ($device['type'] === 'STAFF' && $mapping['user_id']) {
            // Absensi staff: clock in/out
            $existing = $db->query("SELECT id, time_in, time_out FROM staff_attendances WHERE user_id=? AND date=?", [$mapping['user_id'], $date])->fetch();
            if ($existing) {
                if ($type === 'IN') {
                    $db->query("UPDATE staff_attendances SET time_in=?, status='HADIR' WHERE id=?", [$time, $existing['id']]);
                } else {
                    $db->query("UPDATE staff_attendances SET time_out=? WHERE id=?", [$time, $existing['id']]);
                }
            } else {
                $db->query("INSERT INTO staff_attendances (user_id, date, status, time_in, created_by) VALUES (?,?,'HADIR',?,?)",
                    [$mapping['user_id'], $date, $time, $mapping['user_id']]);
            }
            echo json_encode(['success' => true, 'type' => 'staff', 'action' => $type]);

        } elseif ($device['type'] === 'STUDENT' && $mapping['student_id']) {
            // Absensi sholat siswa: cari sholat terdekat berdasarkan jam
            $hour = (int)date('H', strtotime($timestamp));
            $prayerId = null;
            if ($hour >= 3 && $hour < 6) $prayerId = $db->query("SELECT id FROM prayer_types WHERE name='Subuh'")->fetchColumn();
            elseif ($hour >= 11 && $hour < 14) $prayerId = $db->query("SELECT id FROM prayer_types WHERE name='Dzuhur'")->fetchColumn();
            elseif ($hour >= 14 && $hour < 17) $prayerId = $db->query("SELECT id FROM prayer_types WHERE name='Ashar'")->fetchColumn();
            elseif ($hour >= 17 && $hour < 19) $prayerId = $db->query("SELECT id FROM prayer_types WHERE name='Maghrib'")->fetchColumn();
            elseif ($hour >= 19 && $hour < 22) $prayerId = $db->query("SELECT id FROM prayer_types WHERE name='Isya'")->fetchColumn();

            if ($prayerId) {
                $db->query("INSERT INTO prayer_attendances (student_id, prayer_type_id, date, status, recorded_by)
                    VALUES (?,?,?,'HADIR',?) ON DUPLICATE KEY UPDATE status='HADIR'",
                    [$mapping['student_id'], $prayerId, $date, $mapping['student_id']]);
                echo json_encode(['success' => true, 'type' => 'student_prayer', 'prayer_id' => $prayerId]);
            } else {
                echo json_encode(['error' => 'No prayer time matched for this hour']);
            }
        } else {
            echo json_encode(['error' => 'Invalid mapping']);
        }
        exit;
    }
}
