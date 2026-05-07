<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Models\AppConfig;

class SettingsController {
    public function __construct() {
        Middleware::auth();
        // Cek Role, hanya Admin yg boleh
        if (!in_array(Session::get('user_role'), ['super-admin', 'admin'])) {
            die("Akses Ditolak. Hanya Admin.");
        }
    }

    // --- 1. IDENTITAS SEKOLAH ---
    public function school() {
        $config = AppConfig::getAll();
        View::render('settings/school', ['title' => 'Identitas Sekolah', 'config' => $config]);
    }

    public function updateSchool() {
        foreach ($_POST as $key => $value) {
            if ($key !== 'csrf_token') {
                AppConfig::set($key, $value);
            }
        }

        // Handle Upload Logo
        if (isset($_FILES['school_logo']) && $_FILES['school_logo']['error'] == 0) {
            $ext = pathinfo($_FILES['school_logo']['name'], PATHINFO_EXTENSION);
            $filename = 'logo_school.' . $ext;
            move_uploaded_file($_FILES['school_logo']['tmp_name'], __DIR__ . '/../../public/uploads/' . $filename);
            AppConfig::set('school_logo', $filename);
        }

        Session::setFlash('success', 'Identitas Sekolah diperbarui.');
        header('Location: /school/profile');
    }

    // --- 2. MANAJEMEN USER ---
    public function users() {
        $db = Database::getInstance();
        $users = $db->query("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            ORDER BY u.id DESC
        ")->fetchAll();
        
        $roles = $db->query("SELECT * FROM roles")->fetchAll();

        View::render('settings/users', [
            'title' => 'Manajemen User', 
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function storeUser() {
        $db = Database::getInstance();
        
        // Cek username/email duplikat
        $cek = $db->query("SELECT id FROM users WHERE username = ? OR email = ?", [$_POST['username'], $_POST['email']])->fetch();
        if ($cek) {
            Session::setFlash('error', 'Username atau Email sudah terpakai.');
            header('Location: /settings/users');
            exit;
        }

        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        
        $db->query("INSERT INTO users (name, username, email, password, role_id, status) VALUES (?, ?, ?, ?, ?, 'active')", [
            $_POST['name'], $_POST['username'], $_POST['email'], $password, $_POST['role_id']
        ]);

        Session::setFlash('success', 'User baru berhasil ditambahkan.');
        header('Location: /settings/users');
    }

    public function deleteUser() {
        $id = $_POST['user_id'];
        if ($id == Session::get('user_id')) {
            Session::setFlash('error', 'Tidak bisa menghapus akun sendiri.');
        } else {
            $db = Database::getInstance();
            $db->query("DELETE FROM users WHERE id = ?", [$id]);
            Session::setFlash('success', 'User berhasil dihapus.');
        }
        header('Location: /settings/users');
    }

    public function resetPassword() {
        $id = $_POST['user_id'];
        $newPass = password_hash('123456', PASSWORD_BCRYPT);
        
        $db = Database::getInstance();
        $db->query("UPDATE users SET password = ? WHERE id = ?", [$newPass, $id]);
        
        Session::setFlash('success', 'Password user direset menjadi: 123456');
        header('Location: /settings/users');
    }
}
