<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Core\Session;
use App\Core\Csrf;

class PpdbPublicController {
    
    public function index() {
        View::render('ppdb/public/home');
    }

    public function register() {
        $db = Database::getInstance();
        $tracks = $db->query("SELECT * FROM ppdb_tracks WHERE is_active = 1 ORDER BY id ASC")->fetchAll();

        View::render('ppdb/public/register', [
            'title' => 'Pendaftaran Santri Baru',
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

        // 3. Validasi Email Unik
        $db = Database::getInstance();
        $email = trim($_POST['email']);
        $cekEmail = $db->query("SELECT id FROM users WHERE email = ?", [$email])->fetch();
        if ($cekEmail) {
            Session::setFlash('error', 'Email sudah terdaftar. Silakan login.');
            header('Location: /register');
            exit;
        }

        // 4. Generate No. Registrasi (REG-TAHUN-ACAK)
        // Ini akan jadi USERNAME login juga
        $regNo = 'REG-' . date('Y') . '-' . rand(1000, 9999);
        
        // Cek Role Siswa
        $role = $db->query("SELECT id FROM roles WHERE slug = 'siswa'")->fetch();
        $roleId = $role ? $role['id'] : 4; 

        try {
            $db->getConnection()->beginTransaction();

            // A. Insert ke tabel USERS
            // Username = Registration Number (REG-2026-XXXX)
            $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            $sqlUser = "INSERT INTO users (name, username, email, password, role_id, status) VALUES (?, ?, ?, ?, ?, 'active')";
            $db->query($sqlUser, [
                $_POST['full_name'], 
                $regNo,  // <--- Username pakai No Registrasi
                $email, 
                $passwordHash, 
                $roleId
            ]);
            $userId = $db->getConnection()->lastInsertId();

            // B. Insert ke tabel STUDENT_CANDIDATES dengan field baru
            $sqlCandidate = "INSERT INTO student_candidates (
                user_id, ppdb_track_id, registration_no, registration_status, full_name, nisn, nik,
                birth_place, birth_date, gender, whatsapp_number, address, school_origin, school_address,
                father_name, mother_name, created_at,
                education_unit, province, city, district, village, postal_code, child_order, siblings_count,
                info_source, sibling_name, sibling_class, npsn, kk_number, father_education, father_income,
                mother_education, mother_income, guardian_name, guardian_education, guardian_nik, guardian_gender,
                guardian_phone, guardian_email, guardian_birth_place, guardian_birth_date, guardian_address,
                guardian_province, guardian_city, guardian_district, guardian_village, guardian_postal_code,
                guardian_job, guardian_income, father_nik, father_email, father_birth_place, father_birth_date,
                father_address, father_province, father_city, father_district, father_village, father_postal_code,
                mother_nik, mother_email, mother_birth_place, mother_birth_date, mother_address, mother_province,
                mother_city, mother_district, mother_village, mother_postal_code, parent_guardian_type,
                father_job, mother_job
            ) VALUES (
                ?, ?, ?, 'PENDING', ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?,
                ?, ?, NOW(),
                ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?
            )";

            $db->query($sqlCandidate, [
                $userId,
                $_POST['ppdb_track_id'],
                $regNo,
                $_POST['full_name'], 
                $_POST['nisn'],
                $_POST['nik'],
                $_POST['birth_place'],
                !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
                $_POST['gender'],
                $_POST['phone'],
                $_POST['address'],
                $_POST['previous_school'],
                $_POST['school_address'] ?? '-',
                $_POST['father_name'] ?? null,
                $_POST['mother_name'] ?? null,

                $_POST['education_unit'] ?? null,
                $_POST['province'] ?? null,
                $_POST['city'] ?? null,
                $_POST['district'] ?? null,
                $_POST['village'] ?? null,
                $_POST['postal_code'] ?? null,
                $_POST['child_order'] ?? null,
                $_POST['siblings_count'] ?? null,
                $_POST['info_source'] ?? null,
                $_POST['sibling_name'] ?? null,
                $_POST['sibling_class'] ?? null,
                $_POST['npsn'] ?? null,
                $_POST['kk_number'] ?? null,
                $_POST['father_education'] ?? null,
                $_POST['father_income'] ?? null,
                $_POST['mother_education'] ?? null,
                $_POST['mother_income'] ?? null,
                $_POST['guardian_name'] ?? null,
                $_POST['guardian_education'] ?? null,
                $_POST['guardian_nik'] ?? null,
                $_POST['guardian_gender'] ?? null,
                $_POST['guardian_phone'] ?? null,
                $_POST['guardian_email'] ?? null,
                $_POST['guardian_birth_place'] ?? null,
                !empty($_POST['guardian_birth_date']) ? $_POST['guardian_birth_date'] : null,
                $_POST['guardian_address'] ?? null,
                $_POST['guardian_province'] ?? null,
                $_POST['guardian_city'] ?? null,
                $_POST['guardian_district'] ?? null,
                $_POST['guardian_village'] ?? null,
                $_POST['guardian_postal_code'] ?? null,
                $_POST['guardian_job'] ?? null,
                $_POST['guardian_income'] ?? null,
                $_POST['father_nik'] ?? null,
                $_POST['father_email'] ?? null,
                $_POST['father_birth_place'] ?? null,
                !empty($_POST['father_birth_date']) ? $_POST['father_birth_date'] : null,
                $_POST['father_address'] ?? null,
                $_POST['father_province'] ?? null,
                $_POST['father_city'] ?? null,
                $_POST['father_district'] ?? null,
                $_POST['father_village'] ?? null,
                $_POST['father_postal_code'] ?? null,
                $_POST['mother_nik'] ?? null,
                $_POST['mother_email'] ?? null,
                $_POST['mother_birth_place'] ?? null,
                !empty($_POST['mother_birth_date']) ? $_POST['mother_birth_date'] : null,
                $_POST['mother_address'] ?? null,
                $_POST['mother_province'] ?? null,
                $_POST['mother_city'] ?? null,
                $_POST['mother_district'] ?? null,
                $_POST['mother_village'] ?? null,
                $_POST['mother_postal_code'] ?? null,
                $_POST['parent_type'] ?? 'AYAH_IBU',
                $_POST['father_job'] ?? null,
                $_POST['mother_job'] ?? null
            ]);

            $db->getConnection()->commit();

            // C. Redirect ke Halaman Sukses (Bukan Auto Login)
            // Kita kirim No Registrasi via URL parameter (dienkripsi dikit atau raw aja gpp untuk info)
            header('Location: /register/success?id=' . $regNo);
            exit;

        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal mendaftar: ' . $e->getMessage());
            header('Location: /register');
            exit;
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
