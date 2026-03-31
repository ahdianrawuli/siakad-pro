<?php
namespace App\Controllers;

use App\Core\Database; // Kita pakai Database langsung agar fleksibel
use App\Core\Session;
use App\Core\Csrf;
use App\Core\View;

class AuthController {
    
    // 1. Halaman Login (GET)
    public function login() {
        // Jika sudah login, lempar ke dashboard
        if (Session::get('user_id')) {
            header('Location: /dashboard');
            exit;
        }
        View::render('auth/login');
    }

    // 2. Proses Login (POST)
    // Di route.php pastikan: $router->post('/login', [AuthController::class, 'authenticate']);
    public function authenticate() {
        
        // A. Validasi CSRF (PENTING: Di View login.php harus ada Csrf::input())
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Session expired (CSRF), silakan refresh.');
            header('Location: /login');
            exit;
        }

        // B. Ambil Input (Perhatikan: View baru menggunakan name="email")
        $loginInput = $_POST['email'] ?? ''; 
        $password   = $_POST['password'] ?? '';

        if (empty($loginInput) || empty($password)) {
            Session::setFlash('error', 'Email/Username dan Password wajib diisi.');
            header('Location: /login');
            exit;
        }

        // C. Cari User di Database (Cek Email ATAU Username)
        $db = Database::getInstance();
        $user = $db->query(
            "SELECT u.*, r.slug as role_slug, r.id as role_id 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.email = ? OR u.username = ? 
             LIMIT 1", 
            [$loginInput, $loginInput]
        )->fetch();

        // D. Verifikasi Password
        if ($user && password_verify($password, $user['password'])) {
            
            // Cek Status Aktif (Optional)
            if (isset($user['status']) && $user['status'] == 'INACTIVE') {
                Session::setFlash('error', 'Akun dinonaktifkan.');
                header('Location: /login');
                exit;
            }

            // E. Set Session
            Session::set('user_id', $user['id']);
            Session::set('user_name', $user['name']);
            Session::set('user_role', $user['role_slug']); // ex: admin, guru
            
            // PENTING: Sidebar fix sebelumnya butuh 'user_role_id'
            // Kode lama Anda pakai 'role_id', kita set keduanya agar aman.
            Session::set('role_id', $user['role_id']); 
            Session::set('user_role_id', $user['role_id']); 

            // F. Redirect Berdasarkan Role
            if ($user['role_slug'] === 'siswa') {
                header('Location: /student/dashboard'); 
            } else {
                header('Location: /dashboard');
            }
            exit;            
        } else {
            // G. Gagal Login
            Session::setFlash('error', 'Email atau Password salah!');
            header('Location: /login');
            exit;
        }
    }

    public function logout() {
        Session::destroy();
        header('Location: /login');
        exit;
    }
}
