<?php
namespace App\Controllers;

use App\Models\WhatsappService;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class PpdbAdminController {
    public function __construct() {
        Middleware::auth();
    }

    // --- 1. DATA PENDAFTAR ---
    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $trackId = $_GET['track_id'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT sc.*, t.name as track_name, t.level as track_level,
                (SELECT status FROM ppdb_payments WHERE candidate_id = sc.id ORDER BY id DESC LIMIT 1) as payment_status
                FROM student_candidates sc
                LEFT JOIN ppdb_tracks t ON sc.ppdb_track_id = t.id
                WHERE 1=1";

        $params = [];
        if (!empty($search)) {
            $sql .= " AND (sc.full_name LIKE ? OR sc.registration_no LIKE ? OR sc.nisn LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
        }
        if (!empty($status)) { $sql .= " AND sc.registration_status = ?"; $params[] = $status; }
        if (!empty($trackId)) { $sql .= " AND sc.ppdb_track_id = ?"; $params[] = $trackId; }

        $totalData = $db->query("SELECT COUNT(*) FROM (" . $sql . ") as t", $params)->fetchColumn();
        $sql .= " ORDER BY sc.created_at DESC LIMIT $limit OFFSET $offset";

        try {
            $candidates = $db->query($sql, $params)->fetchAll();
        } catch (\Exception $e) {
            $candidates = [];
        }

        try {
            $tracks = $db->query("SELECT * FROM ppdb_tracks WHERE is_active = 1")->fetchAll();
        } catch (\Exception $e) {
            $tracks = [];
        }

        View::render('ppdb/admin/index', [
            'title' => 'Data Pendaftar PPDB',
            'candidates' => $candidates,
            'tracks' => $tracks,
            'totalData' => $totalData,
            'totalPages' => ceil($totalData / $limit),
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'selectedStatus' => $status,
            'selectedTrack' => $trackId
        ]);
    }

    public function storeCandidate() {
        $db = Database::getInstance();
        try {
            $regNo = "REG-" . date('Ymd') . "-" . rand(1000, 9999);
            $sql = "INSERT INTO student_candidates (registration_no, ppdb_track_id, full_name, gender, whatsapp_number, registration_status, exam_location) 
                    VALUES (?, ?, ?, ?, ?, 'PENDING', 'OFFLINE')";
            $db->query($sql, [$regNo, $_POST['ppdb_track_id'], $_POST['full_name'], $_POST['gender'], $_POST['whatsapp_number']]);
            Session::setFlash('success', "Pendaftar berhasil ditambahkan: $regNo");
        } catch (\Exception $e) { Session::setFlash('error', $e->getMessage()); }
        header('Location: /ppdb/registrations');
    }

    public function detail() {
        $id = $_GET['id'] ?? 0;
        $db = Database::getInstance();
        $candidate = $db->query("SELECT sc.*, t.name as track_name, t.level FROM student_candidates sc JOIN ppdb_tracks t ON sc.ppdb_track_id = t.id WHERE sc.id = ?", [$id])->fetch();
        if (!$candidate) { header('Location: /ppdb/registrations'); exit; }
        View::render('ppdb/admin/detail', [
            'title' => 'Detail Pendaftar',
            'candidate' => $candidate,
            'payments' => $db->query("SELECT * FROM ppdb_payments WHERE candidate_id = ? ORDER BY id DESC", [$id])->fetchAll(),
            'documents' => $db->query("SELECT * FROM ppdb_documents WHERE candidate_id = ?", [$id])->fetchAll()
        ]);
    }

    public function verifyPayment() {
        $db = Database::getInstance();
        $db->query("UPDATE ppdb_payments SET status = 'VERIFIED' WHERE id = ?", [$_POST['payment_id']]);
        $db->query("UPDATE student_candidates SET registration_status = 'PAID' WHERE id = ?", [$_POST['candidate_id']]);
        Session::setFlash('success', 'Pembayaran diverifikasi.');
        header("Location: /ppdb/registrations/detail?id=" . $_POST['candidate_id']);
    }

    public function setGraduation() {
        $db = Database::getInstance();
        $db->query("UPDATE student_candidates SET registration_status = ? WHERE id = ?", [$_POST['status'], $_POST['candidate_id']]);
        Session::setFlash('success', 'Status kelulusan diperbarui.');
        header("Location: /ppdb/registrations/detail?id=" . $_POST['candidate_id']);
    }

    public function promoteToStudent() {
        $db = Database::getInstance();
        $candidateId = $_POST['candidate_id'] ?? null;

        if (!$candidateId) {
            Session::setFlash('error', 'Data kandidat tidak valid.');
            header("Location: /ppdb/registrations");
            exit;
        }

        $candidate = $db->query("SELECT * FROM student_candidates WHERE id = ? AND registration_status = 'ACCEPTED'", [$candidateId])->fetch();

        if (!$candidate) {
            Session::setFlash('error', 'Kandidat tidak ditemukan atau belum diterima.');
            header("Location: /ppdb/registrations/detail?id=" . $candidateId);
            exit;
        }

        // Cek apakah sudah pernah di-promote
        $existing = $db->query("SELECT id FROM students WHERE candidate_id = ?", [$candidateId])->fetch();
        if ($existing) {
            Session::setFlash('error', 'Siswa ini sudah pernah di-generate sebelumnya.');
            header("Location: /ppdb/registrations/detail?id=" . $candidateId);
            exit;
        }

        $db->getConnection()->beginTransaction();

        // Generate NIS: format YYYYXXXX
        $year = date('Y');
        $lastNis = $db->query("SELECT nis FROM students WHERE nis LIKE ? ORDER BY id DESC LIMIT 1", [$year . '%'])->fetchColumn();
        $seq = $lastNis ? (intval(substr($lastNis, 4)) + 1) : 1;
        $nis = $year . str_pad($seq, 4, '0', STR_PAD_LEFT);

        // Buat akun login dengan password = tanggal lahir (ddmmyyyy)
        $passPlain = date('dmY', strtotime($candidate['birth_date']));
        $passHash  = password_hash($passPlain, PASSWORD_BCRYPT);
        $username  = $nis;
        $email     = $candidate['whatsapp_number'] . '@santri.thawalib.sch.id';

        $db->query("INSERT INTO users (name, username, email, password, role_id, status) VALUES (?,?,?,?,4,'active')",
            [$candidate['full_name'], $username, $email, $passHash]);
        $userId = $db->getConnection()->lastInsertId();

        $db->query(
            "INSERT INTO students (candidate_id, user_id, nis, nisn, full_name, gender, birth_place, birth_date, address, parent_name, parent_phone, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVE')",
            [
                $candidateId, $userId, $nis,
                $candidate['nisn'], $candidate['full_name'], $candidate['gender'],
                $candidate['birth_place'], $candidate['birth_date'], $candidate['address'],
                $candidate['father_name'], $candidate['father_phone'],
            ]
        );
        $studentId = $db->getConnection()->lastInsertId();

        // Auto-create akun orang tua
        // Username = no HP ayah (prioritas), fallback ke ibu
        // Password = NIS siswa
        $parentPhone = preg_replace('/\D/', '', $candidate['father_phone'] ?? $candidate['mother_phone'] ?? '');
        if ($parentPhone) {
            $parentPassHash = password_hash($nis, PASSWORD_BCRYPT);
            $parentName     = $candidate['father_name'] ?: ($candidate['mother_name'] ?: 'Orang Tua ' . $candidate['full_name']);

            // Cek apakah username sudah ada (HP bisa sama jika punya anak lebih dari 1)
            $existingParent = $db->query("SELECT id FROM users WHERE username = ?", [$parentPhone])->fetch();
            if ($existingParent) {
                // Akun sudah ada, langsung link ke siswa baru
                $db->query("UPDATE students SET parent_user_id = ? WHERE id = ?", [$existingParent['id'], $studentId]);
            } else {
                $parentEmail = $parentPhone . '@wali.thawalib.sch.id';
                $db->query("INSERT INTO users (name, username, email, password, role_id, status) VALUES (?,?,?,?,5,'active')",
                    [$parentName, $parentPhone, $parentEmail, $parentPassHash]);
                $parentUserId = $db->getConnection()->lastInsertId();
                $db->query("UPDATE students SET parent_user_id = ? WHERE id = ?", [$parentUserId, $studentId]);
            }
        }

        $db->getConnection()->commit();
        $parentInfo = $parentPhone ? " | Login Ortu: $parentPhone / $nis" : '';
        Session::setFlash('success', "Siswa aktif berhasil dibuat. NIS: $nis | Username: $nis | Password: $passPlain (tanggal lahir)$parentInfo");
        header("Location: /ppdb/registrations/detail?id=" . $candidateId);
        exit;
    }

    // --- PENGELOLAAN SETTINGS PPDB (Jalur & Gelombang) ---
    public function settings() {
        $db = Database::getInstance();
        $paths   = $db->query("SELECT * FROM ppdb_tracks ORDER BY level, name ASC")->fetchAll();
        $batches = $db->query("SELECT * FROM ppdb_batches ORDER BY start_date DESC")->fetchAll();
        View::render('ppdb/admin/settings', [
            'title'   => 'Pengaturan PPDB',
            'paths'   => $paths,
            'batches' => $batches
        ]);
    }

    public function storePath() {
        $db = Database::getInstance();
        $db->query("INSERT INTO ppdb_tracks (name, level, code, quota, is_active) VALUES (?, ?, ?, ?, 1)", [
            $_POST['name'], $_POST['level'], $_POST['code'], (int)($_POST['quota'] ?? 0)
        ]);
        Session::setFlash('success', 'Jalur pendaftaran ditambahkan.');
        header('Location: /school/ppdb');
    }

    public function togglePath($id) {
        $db = Database::getInstance();
        $path = $db->query("SELECT is_active FROM ppdb_tracks WHERE id = ?", [$id])->fetch();
        if ($path) {
            $db->query("UPDATE ppdb_tracks SET is_active = ? WHERE id = ?", [$path['is_active'] ? 0 : 1, $id]);
        }
        header('Location: /school/ppdb');
    }

    public function storeBatch() {
        $db = Database::getInstance();
        $db->query("INSERT INTO ppdb_batches (name, start_date, end_date, is_active) VALUES (?, ?, ?, 0)", [
            $_POST['name'], $_POST['start_date'], $_POST['end_date']
        ]);
        Session::setFlash('success', 'Gelombang PPDB ditambahkan.');
        header('Location: /school/ppdb');
    }

    public function activateBatch($id) {
        $db = Database::getInstance();
        $db->query("UPDATE ppdb_batches SET is_active = 0");
        $db->query("UPDATE ppdb_batches SET is_active = 1 WHERE id = ?", [$id]);
        Session::setFlash('success', 'Gelombang berhasil diaktifkan.');
        header('Location: /school/ppdb');
    }

    // --- PENGELOLAAN PERIODE (Agar rute lama /ppdb/periods tetap jalan) ---
    public function periods() {
        $db = Database::getInstance();
        // Redirect ke tampilan baru jika diinginkan, atau render tampilan lama
        // Disini kita render tampilan admin tracks/periods seperti yang diminta sebelumnya
        $search = $_GET['search'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM ppdb_batches WHERE 1=1";
        $params = [];
        if ($search) { $sql .= " AND name LIKE ?"; $params[] = "%$search%"; }

        $totalData = $db->query("SELECT COUNT(*) FROM ppdb_batches WHERE 1=1" . ($search ? " AND name LIKE ?" : ""), $params)->fetchColumn();
        $sql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";
        $batches = $db->query($sql, $params)->fetchAll();

        View::render('ppdb_admin/periods', [
            'title' => 'Atur Periode PPDB',
            'batches' => $batches,
            'totalData' => $totalData,
            'totalPages' => ceil($totalData / $limit),
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search
        ]);
    }
    
    public function storePeriod() {
        $db = Database::getInstance();
        $db->query("INSERT INTO ppdb_batches (name, start_date, end_date, is_active) VALUES (?, ?, ?, 0)", [$_POST['name'], $_POST['start_date'], $_POST['end_date']]);
        Session::setFlash('success', 'Periode disimpan.');
        header('Location: /ppdb/periods');
    }

    public function updatePeriod() {
        $db = Database::getInstance();
        $db->query("UPDATE ppdb_batches SET name = ?, start_date = ?, end_date = ? WHERE id = ?", [$_POST['name'], $_POST['start_date'], $_POST['end_date'], $_POST['id']]);
        Session::setFlash('success', 'Periode diperbarui.');
        header('Location: /ppdb/periods');
    }

    public function activatePeriod() {
        $db = Database::getInstance();
        $db->getConnection()->beginTransaction();
        $db->query("UPDATE ppdb_batches SET is_active = 0");
        $db->query("UPDATE ppdb_batches SET is_active = 1 WHERE id = ?", [$_GET['id']]);
        $db->getConnection()->commit();
        header('Location: /ppdb/periods');
    }

    public function deletePeriod() {
        $db = Database::getInstance();
        try { $db->query("DELETE FROM ppdb_batches WHERE id = ?", [$_POST['id']]); Session::setFlash('success', 'Dihapus.'); } 
        catch (\Exception $e) { Session::setFlash('error', 'Gagal hapus.'); }
        header('Location: /ppdb/periods');
    }

    // --- PENGELOLAAN JALUR (FIX: Method ini yang menyebabkan error jika hilang) ---
    public function tracks() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM ppdb_tracks WHERE 1=1";
        $params = [];
        if ($search) { $sql .= " AND (name LIKE ? OR code LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

        $totalData = $db->query("SELECT COUNT(*) FROM ppdb_tracks WHERE 1=1" . ($search ? " AND (name LIKE ? OR code LIKE ?)" : ""), $params)->fetchColumn();
        $sql .= " ORDER BY level ASC, name ASC LIMIT $limit OFFSET $offset";
        $tracks = $db->query($sql, $params)->fetchAll();

        View::render('ppdb_admin/tracks', [
            'title' => 'Atur Jalur Pendaftaran',
            'tracks' => $tracks,
            'totalData' => $totalData,
            'totalPages' => ceil($totalData / $limit),
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search
        ]);
    }

    public function storeTrack() {
        $db = Database::getInstance();
        $code = strtoupper(trim($_POST['code']));
        if ($db->query("SELECT id FROM ppdb_tracks WHERE code = ?", [$code])->fetch()) { Session::setFlash('error', 'Kode ada.'); header('Location: /ppdb/tracks'); exit; }
        $db->query("INSERT INTO ppdb_tracks (name, level, code, description, quota, is_active) VALUES (?, ?, ?, ?, ?, 1)", [$_POST['name'], $_POST['level'], $code, $_POST['description'], $_POST['quota']]);
        Session::setFlash('success', 'Jalur disimpan.');
        header('Location: /ppdb/tracks');
    }

    public function updateTrack() {
        $db = Database::getInstance();
        $db->query("UPDATE ppdb_tracks SET name = ?, level = ?, code = ?, quota = ?, description = ? WHERE id = ?", [$_POST['name'], $_POST['level'], strtoupper($_POST['code']), $_POST['quota'], $_POST['description'], $_POST['id']]);
        Session::setFlash('success', 'Jalur diperbarui.');
        header('Location: /ppdb/tracks');
    }

    public function deleteTrack() {
        $db = Database::getInstance();
        try { $db->query("DELETE FROM ppdb_tracks WHERE id = ?", [$_POST['id']]); Session::setFlash('success', 'Dihapus.'); }
        catch (\Exception $e) { Session::setFlash('error', 'Gagal hapus.'); }
        header('Location: /ppdb/tracks');
    }
}

