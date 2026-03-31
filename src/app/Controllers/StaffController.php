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
        
        $sql = "SELECT sm.*, sp.name as position_name, u.username 
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
            // Optional: Create User Login
            $userId = null;
            if (!empty($_POST['create_user'])) {
                $password = password_hash('123456', PASSWORD_BCRYPT);
                $username = $_POST['nip'];
                
                // Cek username duplicate
                $check = $db->query("SELECT id FROM users WHERE username = ?", [$username])->fetch();
                if ($check) {
                    throw new \Exception("NIP/Username $username sudah digunakan.");
                }

                $db->query("INSERT INTO users (name, username, email, password, role_id, status) VALUES (?, ?, ?, ?, 7, 'active')", 
                    [
                        $_POST['full_name'], 
                        $username, 
                        !empty($_POST['email']) ? $_POST['email'] : null, 
                        $password
                    ]);
                $userId = $db->getConnection()->lastInsertId();
            }

            // PERBAIKAN UTAMA DI SINI (Menggunakan ?? null agar tidak error)
            $db->query("INSERT INTO staff_members (user_id, position_id, nip, full_name, gender, phone, email, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 
                [
                    $userId, 
                    $_POST['position_id'], 
                    $_POST['nip'], 
                    $_POST['full_name'], 
                    $_POST['gender'], 
                    $_POST['phone'] ?? null,   
                    $_POST['email'] ?? null,   
                    $_POST['address'] ?? null  // Ini yang menyebabkan error sebelumnya
                ]);
            
            Session::setFlash('success', 'Staff berhasil ditambahkan. (Pass Default: 123456)');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }
        header('Location: /staff/members');
    }

    public function update() {
        $db = Database::getInstance();
        
        $phone = !empty($_POST['phone']) ? $_POST['phone'] : null;
        $email = !empty($_POST['email']) ? $_POST['email'] : null;
        $address = !empty($_POST['address']) ? $_POST['address'] : null;

        $db->query("UPDATE staff_members SET position_id=?, nip=?, full_name=?, gender=?, phone=?, email=?, address=?, status=? WHERE id=?", 
            [
                $_POST['position_id'], 
                $_POST['nip'], 
                $_POST['full_name'], 
                $_POST['gender'], 
                $phone, 
                $email, 
                $address, 
                $_POST['status'], 
                $_POST['id']
            ]);
            
        Session::setFlash('success', 'Data staff diperbarui.');
        header('Location: /staff/members');
    }

    public function delete() {
        $db = Database::getInstance();
        $id = $_GET['id'];
        // Hapus User Login jika ada
        $staff = $db->query("SELECT user_id FROM staff_members WHERE id = ?", [$id])->fetch();
        if ($staff && $staff['user_id']) {
            $db->query("DELETE FROM users WHERE id = ?", [$staff['user_id']]);
        }
        $db->query("DELETE FROM staff_members WHERE id = ?", [$id]);
        Session::setFlash('success', 'Staff dihapus.');
        header('Location: /staff/members');
    }
}

