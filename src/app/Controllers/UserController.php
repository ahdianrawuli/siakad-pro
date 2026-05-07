<?php
namespace App\Controllers;
use App\Models\User;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database; // Untuk ambil roles

class UserController {
    public function __construct() {
        Middleware::auth(); 
        // Idealnya cek permission: if(!can('manage_users')) redirect...
    }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE 1=1";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (u.name LIKE ? OR u.username LIKE ? OR u.email LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
        }

        $totalData = $db->query("SELECT COUNT(*) FROM (" . $sql . ") as t", $params)->fetchColumn();
        $sql .= " ORDER BY u.id DESC LIMIT $limit OFFSET $offset";
        $users = $db->query($sql, $params)->fetchAll();
        $roles = $db->query("SELECT * FROM roles ORDER BY name ASC")->fetchAll();

        View::render('settings/users/index', [
            'title' => 'Manajemen User',
            'users' => $users,
            'roles' => $roles,
            'search' => $search,
            'limit' => $limit,
            'currentPage' => $page,
            'totalData' => $totalData,
            'totalPages' => ceil($totalData / $limit),
        ]);
    }

    public function create() {
        // Ambil data roles untuk dropdown
        $db = Database::getInstance();
        $roles = $db->query("SELECT * FROM roles")->fetchAll();
        View::render('settings/users/create', ['roles' => $roles, 'title' => 'Tambah User']);
    }

// Di dalam UserController.php pada fungsi store()

public function store() {
    $db = Database::getInstance();
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];
    $email = $_POST['email'] ?? $username . '@siakad.com';

    try {
        $db->getConnection()->beginTransaction();

        // 1. Simpan ke tabel users
        $sqlUser = "INSERT INTO users (name, username, email, password, role_id, status) VALUES (?, ?, ?, ?, ?, 'active')";
        $db->query($sqlUser, [$name, $username, $email, $password, $role_id]);
        $userId = $db->getConnection()->lastInsertId();

        // 2. OTOMATIS CREATE KE MASTER GURU JIKA ROLE ADALAH GURU (ID 3)
        if ($role_id == 3) {
            $sqlTeacher = "INSERT INTO teachers (user_id, full_name, email, status) VALUES (?, ?, ?, 'ACTIVE')";
            $db->query($sqlTeacher, [$userId, $name, $email]);
        }

        $db->getConnection()->commit();
        Session::setFlash('success', 'User berhasil dibuat dan otomatis terdaftar di Master Guru.');
    } catch (\Exception $e) {
        $db->getConnection()->rollBack();
        Session::setFlash('error', 'Gagal: ' . $e->getMessage());
    }
    
    header('Location: /settings/users');
}

// Di dalam UserController.php pada fungsi delete()

public function delete() {
    $id = $_GET['id'];
    $db = Database::getInstance();

    try {
        $db->getConnection()->beginTransaction();

        // 1. Hapus data di tabel teachers terlebih dahulu (karena ada Foreign Key user_id)
        $db->query("DELETE FROM teachers WHERE user_id = ?", [$id]);

        // 2. Hapus data di tabel users
        $db->query("DELETE FROM users WHERE id = ?", [$id]);

        $db->getConnection()->commit();
        Session::setFlash('success', 'User dan data profil guru berhasil dihapus.');
    } catch (\Exception $e) {
        $db->getConnection()->rollBack();
        Session::setFlash('error', 'Gagal menghapus: ' . $e->getMessage());
    }

    header('Location: /settings/users');
}

    public function edit() {
        $id = $_GET['id'];
        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM users WHERE id = ?", [$id])->fetch();
        $roles = $db->query("SELECT * FROM roles")->fetchAll();
        View::render('settings/users/edit', ['user' => $user, 'roles' => $roles, 'title' => 'Edit User']);
    }

    public function update() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        $name = $_POST['name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role_id = $_POST['role_id'];

        try {
            $sql = "UPDATE users SET name = ?, username = ?, email = ?, role_id = ? WHERE id = ?";
            $params = [$name, $username, $email, $role_id, $id];

            if (!empty($_POST['password'])) {
                $sql = "UPDATE users SET name = ?, username = ?, email = ?, role_id = ?, password = ? WHERE id = ?";
                $params = [$name, $username, $email, $role_id, password_hash($_POST['password'], PASSWORD_DEFAULT), $id];
            }

            $db->query($sql, $params);
            Session::setFlash('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal memperbarui: ' . $e->getMessage());
        }

        header('Location: /settings/users');
    }

}
