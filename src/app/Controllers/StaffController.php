<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class StaffController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $posId = $_GET['position_id'] ?? '';
        $limit = $_GET['limit'] ?? 10;
        $page = $_GET['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $where = "1=1";
        $params = [];

        if ($search) {
            $where .= " AND (sm.full_name LIKE ? OR sm.nip LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%";
        }
        if ($posId) {
            $where .= " AND sm.position_id = ?";
            $params[] = $posId;
        }

        $totalData = $db->query("SELECT COUNT(*) FROM staff_members sm WHERE $where", $params)->fetchColumn();
        
        $sql = "SELECT sm.*, sp.name as position_name, u.username, u.status as user_status
                FROM staff_members sm 
                LEFT JOIN staff_positions sp ON sm.position_id = sp.id
                LEFT JOIN users u ON sm.user_id = u.id
                WHERE $where 
                ORDER BY sm.full_name ASC 
                LIMIT $limit OFFSET $offset";
        
        $staffs = $db->query($sql, $params)->fetchAll();
        $positions = $db->query("SELECT * FROM staff_positions ORDER BY name ASC")->fetchAll();

        View::render('staff/members/index', [
            'title' => 'Data Pegawai / Staff',
            'staffs' => $staffs,
            'positions' => $positions,
            'totalData' => $totalData,
            'totalPages' => ceil($totalData / $limit),
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'selectedPos' => $posId
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        try {
            $db->getConnection()->beginTransaction();

            // Ambil role_id dari jabatan yang dipilih
            $position = $db->query("SELECT role_id FROM staff_positions WHERE id=?", [$_POST['position_id']])->fetch();
            $roleId = $position['role_id'] ?? 7;

            // Buat akun login otomatis
            $username = $_POST['nip'];
            $check = $db->query("SELECT id FROM users WHERE username=?", [$username])->fetch();
            if ($check) throw new \Exception("NIP/Username $username sudah digunakan.");

            $db->query("INSERT INTO users (name, username, email, password, role_id, status) VALUES (?,?,?,?,?,'active')", [
                $_POST['full_name'],
                $username,
                !empty($_POST['email']) ? $_POST['email'] : null,
                password_hash('123456', PASSWORD_BCRYPT),
                $roleId
            ]);
            $userId = $db->getConnection()->lastInsertId();

            $db->query("INSERT INTO staff_members (user_id, position_id, nip, full_name, gender, phone, email, address) VALUES (?,?,?,?,?,?,?,?)", [
                $userId, $_POST['position_id'], $_POST['nip'], $_POST['full_name'],
                $_POST['gender'], $_POST['phone'] ?? null, $_POST['email'] ?? null, $_POST['address'] ?? null
            ]);

            // Sync: jika role guru, tambah juga ke tabel teachers
            if ($roleId == 3) {
                $db->query("INSERT INTO teachers (user_id, nip, full_name, gender, phone, email, status) VALUES (?,?,?,?,?,?,'ACTIVE')", [
                    $userId, $_POST['nip'], $_POST['full_name'], $_POST['gender'], $_POST['phone'] ?? null, $_POST['email'] ?? null
                ]);
            }

            $db->getConnection()->commit();
            Session::setFlash('success', 'Staff berhasil ditambahkan. Password default: 123456');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', $e->getMessage());
        }
        header('Location: /staff/members');
    }

    public function update() {
        $db = Database::getInstance();
        try {
            $db->getConnection()->beginTransaction();

            // Ambil role_id dari jabatan baru
            $position = $db->query("SELECT role_id FROM staff_positions WHERE id=?", [$_POST['position_id']])->fetch();
            $roleId = $position['role_id'] ?? 7;

            $db->query("UPDATE staff_members SET position_id=?, nip=?, full_name=?, gender=?, phone=?, email=?, address=?, status=? WHERE id=?", [
                $_POST['position_id'], $_POST['nip'], $_POST['full_name'], $_POST['gender'],
                $_POST['phone'] ?: null, $_POST['email'] ?: null, $_POST['address'] ?: null,
                $_POST['status'], $_POST['id']
            ]);

            // Sync role user jika ada akun login
            $staff = $db->query("SELECT user_id FROM staff_members WHERE id=?", [$_POST['id']])->fetch();
            if ($staff['user_id']) {
                $db->query("UPDATE users SET name=?, email=?, role_id=? WHERE id=?", [
                    $_POST['full_name'], $_POST['email'] ?: null, $roleId, $staff['user_id']
                ]);
            }

            $db->getConnection()->commit();
            Session::setFlash('success', 'Data staff diperbarui.');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', $e->getMessage());
        }
        header('Location: /staff/members');
    }

    public function resetPassword() {
        $db = Database::getInstance();
        $staffId = $_POST['staff_id'];
        $staff = $db->query("SELECT user_id FROM staff_members WHERE id=?", [$staffId])->fetch();
        if ($staff && $staff['user_id']) {
            $db->query("UPDATE users SET password=? WHERE id=?", [
                password_hash('123456', PASSWORD_BCRYPT), $staff['user_id']
            ]);
            Session::setFlash('success', 'Password direset ke 123456.');
        } else {
            Session::setFlash('error', 'Staff ini belum memiliki akun login.');
        }
        header('Location: /staff/members');
    }

    public function toggleStatus() {
        $db = Database::getInstance();
        $staffId = $_POST['staff_id'];
        $staff = $db->query("SELECT user_id FROM staff_members WHERE id=?", [$staffId])->fetch();
        if ($staff && $staff['user_id']) {
            $current = $db->query("SELECT status FROM users WHERE id=?", [$staff['user_id']])->fetchColumn();
            $new = $current === 'active' ? 'inactive' : 'active';
            $db->query("UPDATE users SET status=? WHERE id=?", [$new, $staff['user_id']]);
            Session::setFlash('success', 'Status akun diubah ke ' . strtoupper($new) . '.');
        }
        header('Location: /staff/members');
    }

    public function delete() {
        $db = Database::getInstance();
        $id = $_GET['id'];
        $staff = $db->query("SELECT user_id FROM staff_members WHERE id=?", [$id])->fetch();
        if ($staff && $staff['user_id']) {
            // Cek apakah user masih dipakai di jadwal/data lain
            $used = $db->query("SELECT COUNT(*) FROM schedules WHERE teacher_id=?", [$staff['user_id']])->fetchColumn();
            if ($used > 0) {
                Session::setFlash('error', 'Gagal hapus: Staff masih terdaftar di jadwal pelajaran. Hapus jadwalnya terlebih dahulu.');
                header('Location: /staff/members');
                return;
            }
            $db->query("DELETE FROM teachers WHERE user_id=?", [$staff['user_id']]);
            $db->query("DELETE FROM users WHERE id=?", [$staff['user_id']]);
        }
        $db->query("DELETE FROM staff_members WHERE id=?", [$id]);
        Session::setFlash('success', 'Staff dihapus.');
        header('Location: /staff/members');
    }
}

