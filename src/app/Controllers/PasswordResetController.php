<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Csrf;
use App\Core\Database;
use App\Models\WhatsappService;

class PasswordResetController {

    // Ambil nomor HP user dari tabel terkait
    private function getPhone(array $user): ?string {
        $db = Database::getInstance();

        // Cek students (siswa aktif)
        $s = $db->query("SELECT parent_phone, father_phone FROM students WHERE user_id = ?", [$user['id']])->fetch();
        if ($s) {
            $phone = $s['parent_phone'] ?: $s['father_phone'];
            if ($phone) return $phone;
        }

        // Cek student_candidates (calon santri)
        $c = $db->query("SELECT whatsapp_number FROM student_candidates WHERE user_id = ?", [$user['id']])->fetch();
        if ($c && $c['whatsapp_number']) return $c['whatsapp_number'];

        // Cek parents
        $p = $db->query("SELECT phone FROM parents WHERE user_id = ? LIMIT 1", [$user['id']])->fetch();
        if ($p && $p['phone']) return $p['phone'];

        return null;
    }

    // Mask nomor: 0812****5678
    private function maskPhone(string $phone): string {
        $clean = preg_replace('/\D/', '', $phone);
        if (strlen($clean) < 8) return '****';
        return substr($clean, 0, 4) . str_repeat('*', strlen($clean) - 8) . substr($clean, -4);
    }

    // =========================================================================
    // STEP 1: Form input email/username
    // =========================================================================
    public function form() {
        if (Session::get('user_id')) { header('Location: /dashboard'); exit; }
        View::render('auth/forgot_password', ['title' => 'Lupa Password']);
    }

    // =========================================================================
    // STEP 2: Proses kirim OTP
    // =========================================================================
    public function sendOtp() {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Session expired, silakan refresh.');
            header('Location: /forgot-password'); exit;
        }

        $input = trim($_POST['email'] ?? '');
        if (!$input) {
            Session::setFlash('error', 'Email atau username wajib diisi.');
            header('Location: /forgot-password'); exit;
        }

        $db = Database::getInstance();

        // Pastikan tabel ada
        $db->query("CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            otp VARCHAR(6) NOT NULL,
            token VARCHAR(64) NULL,
            used TINYINT(1) DEFAULT 0,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");

        $user = $db->query(
            "SELECT u.*, r.slug as role_slug FROM users u JOIN roles r ON u.role_id=r.id
             WHERE u.email = ? OR u.username = ? LIMIT 1",
            [$input, $input]
        )->fetch();

        if (!$user) {
            Session::setFlash('error', 'Akun tidak ditemukan.');
            header('Location: /forgot-password'); exit;
        }

        $phone = $this->getPhone($user);
        if (!$phone) {
            Session::setFlash('error', 'Nomor WhatsApp tidak terdaftar. Hubungi admin.');
            header('Location: /forgot-password'); exit;
        }

        // Generate OTP 6 digit
        $otp     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + 300); // 5 menit

        // Simpan OTP ke DB (upsert)
        $existing = $db->query("SELECT id FROM password_resets WHERE user_id = ?", [$user['id']])->fetch();
        if ($existing) {
            $db->query("UPDATE password_resets SET otp = ?, expires_at = ?, used = 0 WHERE user_id = ?",
                [$otp, $expires, $user['id']]);
        } else {
            $db->query("INSERT INTO password_resets (user_id, otp, expires_at) VALUES (?, ?, ?)",
                [$user['id'], $otp, $expires]);
        }

        // Kirim via WhatsApp
        $message = "🔐 *SIAKAD Thawalib Parabek*\n\nKode OTP reset password Anda:\n\n*{$otp}*\n\nBerlaku 5 menit. Jangan bagikan ke siapapun.";
        WhatsappService::send($phone, $message);

        // Simpan user_id di session untuk step berikutnya
        Session::set('otp_user_id', $user['id']);
        Session::set('otp_phone_masked', $this->maskPhone($phone));

        header('Location: /forgot-password/verify'); exit;
    }

    // =========================================================================
    // STEP 3: Form input OTP
    // =========================================================================
    public function verifyForm() {
        if (!Session::get('otp_user_id')) { header('Location: /forgot-password'); exit; }
        View::render('auth/otp_verify', [
            'title'       => 'Verifikasi OTP',
            'phoneMasked' => Session::get('otp_phone_masked'),
        ]);
    }

    // =========================================================================
    // STEP 4: Proses verifikasi OTP
    // =========================================================================
    public function verifyOtp() {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Session expired.');
            header('Location: /forgot-password/verify'); exit;
        }

        $userId = Session::get('otp_user_id');
        if (!$userId) { header('Location: /forgot-password'); exit; }

        $inputOtp = trim($_POST['otp'] ?? '');
        $db = Database::getInstance();

        $record = $db->query(
            "SELECT * FROM password_resets WHERE user_id = ? AND used = 0 AND expires_at > NOW()",
            [$userId]
        )->fetch();

        if (!$record || $record['otp'] !== $inputOtp) {
            Session::setFlash('error', 'OTP salah atau sudah kadaluarsa.');
            header('Location: /forgot-password/verify'); exit;
        }

        // OTP valid — tandai sudah dipakai, set token reset
        $token = bin2hex(random_bytes(32));
        $db->query("UPDATE password_resets SET used = 1, token = ? WHERE id = ?", [$token, $record['id']]);

        Session::set('reset_token', $token);
        Session::set('reset_user_id', $userId);
        Session::remove('otp_user_id');
        Session::remove('otp_phone_masked');

        header('Location: /forgot-password/reset'); exit;
    }

    // =========================================================================
    // STEP 5: Form reset password baru
    // =========================================================================
    public function resetForm() {
        if (!Session::get('reset_token')) { header('Location: /forgot-password'); exit; }
        View::render('auth/reset_password', ['title' => 'Reset Password']);
    }

    // =========================================================================
    // STEP 6: Proses simpan password baru
    // =========================================================================
    public function resetPassword() {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Session expired.');
            header('Location: /forgot-password/reset'); exit;
        }

        $token  = Session::get('reset_token');
        $userId = Session::get('reset_user_id');
        if (!$token || !$userId) { header('Location: /forgot-password'); exit; }

        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (strlen($password) < 6) {
            Session::setFlash('error', 'Password minimal 6 karakter.');
            header('Location: /forgot-password/reset'); exit;
        }
        if ($password !== $confirm) {
            Session::setFlash('error', 'Konfirmasi password tidak cocok.');
            header('Location: /forgot-password/reset'); exit;
        }

        // Verifikasi token masih valid
        $db = Database::getInstance();
        $record = $db->query(
            "SELECT id FROM password_resets WHERE user_id = ? AND token = ? AND used = 1",
            [$userId, $token]
        )->fetch();

        if (!$record) {
            Session::setFlash('error', 'Sesi reset tidak valid. Ulangi dari awal.');
            header('Location: /forgot-password'); exit;
        }

        // Update password
        $db->query("UPDATE users SET password = ? WHERE id = ?",
            [password_hash($password, PASSWORD_BCRYPT), $userId]);

        // Hapus record reset
        $db->query("DELETE FROM password_resets WHERE user_id = ?", [$userId]);

        Session::remove('reset_token');
        Session::remove('reset_user_id');

        Session::setFlash('success', 'Password berhasil diubah. Silakan masuk.');
        header('Location: /login'); exit;
    }
}
