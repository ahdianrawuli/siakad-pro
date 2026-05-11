<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Core\Session;
use App\Core\Csrf;
use App\Services\BniVaService;

class PpdbPublicController {
    
    public function index() {
        View::render('public/home');
    }

    public function prosedur() {
        View::render('public/prosedur');
    }

    public function register() {
        $db = Database::getInstance();
        $tracks = $db->query("SELECT id, name, level FROM ppdb_tracks WHERE is_active = 1 ORDER BY level, name")->fetchAll();

        View::render('ppdb/public/register', [
            'title'  => 'Pendaftaran Santri Baru',
            'tracks' => $tracks
        ]);
    }

    public function processRegister() {
        // 1. Validasi CSRF
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token keamanan tidak valid. Silakan refresh halaman.');
            header('Location: /register');
            exit;
        }

        // 2. Validasi Password
        if ($_POST['password'] !== $_POST['password_confirm']) {
            Session::setFlash('error', 'Konfirmasi password tidak cocok.');
            header('Location: /register');
            exit;
        }

        $db = Database::getInstance();
        $email = trim($_POST['email']);

        // 3. Validasi Email Unik
        if ($db->query("SELECT id FROM users WHERE email = ?", [$email])->fetch()) {
            Session::setFlash('error', 'Email sudah terdaftar. Silakan login.');
            header('Location: /register');
            exit;
        }

        // 4. Generate No. Registrasi
        do {
            $regNo = 'REG-' . date('Y') . '-' . rand(1000, 9999);
        } while ($db->query("SELECT id FROM student_candidates WHERE registration_no = ?", [$regNo])->fetch());

        $role = $db->query("SELECT id FROM roles WHERE slug = 'siswa'")->fetch();
        $roleId = $role ? $role['id'] : 4;

        try {
            $db->getConnection()->beginTransaction();

            // A. Insert ke users
            $db->query("INSERT INTO users (name, username, email, password, role_id, status) VALUES (?, ?, ?, ?, ?, 'active')", [
                $_POST['full_name'],
                $regNo,
                $email,
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                $roleId
            ]);
            $userId = $db->getConnection()->lastInsertId();

            // B. Insert ke student_candidates
            $db->query("INSERT INTO student_candidates (
                registration_no, user_id, ppdb_track_id, full_name, gender,
                nisn, nik, kk_number, birth_place, birth_date,
                address, province, city, district, village, postal_code,
                whatsapp_number, school_origin, npsn, education_unit,
                child_order, siblings_count, info_source,
                father_name, father_nik, father_phone, father_job, father_education, father_income,
                mother_name, mother_nik, mother_phone, mother_job, mother_education, mother_income,
                guardian_name, guardian_phone, guardian_nik, parent_guardian_type,
                registration_status
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'AYAH_IBU','PENDING')", [
                $regNo,
                $userId,
                $_POST['ppdb_track_id'] ?? null,
                $_POST['full_name'],
                $_POST['gender'] ?? 'L',
                $_POST['nisn'] ?? null,
                $_POST['nik'] ?? null,
                $_POST['kk_number'] ?? null,
                $_POST['birth_place'] ?? null,
                !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
                $_POST['address'] ?? null,
                $_POST['province'] ?? null,
                $_POST['city'] ?? null,
                $_POST['district'] ?? null,
                $_POST['village'] ?? null,
                $_POST['postal_code'] ?? null,
                $_POST['phone'] ?? null,
                $_POST['previous_school'] ?? null,
                $_POST['npsn'] ?? null,
                $_POST['education_unit'] ?? null,
                $_POST['child_order'] ?? null,
                $_POST['siblings_count'] ?? null,
                $_POST['info_source'] ?? null,
                $_POST['father_name'] ?? null,
                $_POST['father_nik'] ?? null,
                $_POST['father_phone'] ?? null,
                $_POST['father_job'] ?? null,
                $_POST['father_education'] ?? null,
                $_POST['father_income'] ?? null,
                $_POST['mother_name'] ?? null,
                $_POST['mother_nik'] ?? null,
                $_POST['mother_phone'] ?? null,
                $_POST['mother_job'] ?? null,
                $_POST['mother_education'] ?? null,
                $_POST['mother_income'] ?? null,
                $_POST['guardian_name'] ?? null,
                $_POST['guardian_phone'] ?? null,
                $_POST['guardian_nik'] ?? null,
            ]);

            $db->getConnection()->commit();

            // Kirim notifikasi WA
            $waMessage = "PENDAFTARAN BERHASIL\n(Pondok Pesantren Sumatera Thawalib Parabek)\n\nAssalamu'alaikum Wr. Wb.\nTerima kasih telah mendaftarkan ananda *{$_POST['full_name']}*.\n\n📋 Detail Akun:\n• No. Pendaftaran: *{$regNo}*\n• Username: *{$regNo}*\n• Password: *{$_POST['password']}*\n\nSilakan login untuk melengkapi berkas dan memantau status pendaftaran.\n\nWassalamu'alaikum Wr. Wb.";
            $this->sendWhatsApp($_POST['phone'] ?? '', $waMessage);

            header('Location: /register/success?id=' . $regNo);
            exit;

        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal mendaftar: ' . $e->getMessage());
            header('Location: /register');
            exit;
        }
    }

    private function sendWhatsApp($number, $message) {
        if (empty($number)) return;

        // Format nomor 08 ke 628
        $number = preg_replace('/[^0-9]/', '', $number);
        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }

        $url = 'https://wab.asiahub.id/api/send';
        $apiKey = getenv('WA_API_KEY');

        $payload = json_encode([
            'number' => $number,
            'message' => $message
        ]);

        $response = 'No API Key configured';
        $httpCode = 500;

        if ($apiKey) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Prevent hanging

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        }

        $status = ($httpCode >= 200 && $httpCode < 300) ? 'SUCCESS' : 'FAILED';

        try {
            $db = Database::getInstance();
            $db->query("INSERT INTO master_wa_record (type, phone_number, message, status, response) VALUES (?, ?, ?, ?, ?)", [
                'REGISTRATION', $number, $message, $status, $response ?? 'No Response'
            ]);
        } catch (\Exception $e) {
            // Ignore DB log error to not break registration flow
        }
    }

    public function success() {
        $regNo = $_GET['id'] ?? '-';
        View::render('ppdb/public/success', [
            'title' => 'Pendaftaran Berhasil',
            'reg_no' => htmlspecialchars($regNo)
        ]);
    }

    public function checkStatus() {
        $result = null;
        $error = null;
        $searchQuery = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $searchQuery = trim($_POST['search_query'] ?? '');

            if (!empty($searchQuery)) {
                $db = Database::getInstance();
                $sql = "SELECT c.*, t.name as track_name
                        FROM student_candidates c
                        LEFT JOIN ppdb_tracks t ON c.ppdb_track_id = t.id
                        WHERE c.registration_no = ? OR c.nisn = ?";
                $result = $db->query($sql, [$searchQuery, $searchQuery])->fetch();

                if (!$result) {
                    $error = "Data pendaftaran tidak ditemukan. Pastikan No. Pendaftaran atau NISN sudah benar.";
                }
            } else {
                $error = "Silakan masukkan No. Pendaftaran atau NISN.";
            }
        }

        View::render('ppdb/public/check_status', [
            'title' => 'Cek Status Pendaftaran',
            'result' => $result,
            'error' => $error,
            'searchQuery' => htmlspecialchars($searchQuery)
        ]);
    }
}
