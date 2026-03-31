<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class BoardingController {
    public function __construct() { Middleware::auth(); }

    // --- 1. MANAJEMEN ASRAMA (DORMS) ---
    public function dorms() {
        $db = Database::getInstance();
        
        // Ambil Data Kamar + Jumlah Penghuni saat ini
        $dorms = $db->query("
            SELECT d.*, 
            (SELECT COUNT(*) FROM students WHERE dorm_id = d.id) as occupied
            FROM dorms d ORDER BY d.name
        ")->fetchAll();

        // Ambil Siswa yg belum punya kamar (untuk assign)
        $students = $db->query("SELECT id, full_name, nis FROM students WHERE dorm_id IS NULL AND status='ACTIVE'")->fetchAll();

        View::render('boarding/dorms', [
            'title' => 'Manajemen Asrama',
            'dorms' => $dorms,
            'students' => $students
        ]);
    }

    public function assignDorm() {
        $db = Database::getInstance();
        $studentId = $_POST['student_id'];
        $dormId = $_POST['dorm_id'];
        
        $db->query("UPDATE students SET dorm_id = ? WHERE id = ?", [$dormId, $studentId]);
        Session::setFlash('success', 'Santri berhasil ditempatkan di asrama.');
        header('Location: /boarding/dorms');
    }

    public function storeDorm() {
        $db = Database::getInstance();
        $name = $_POST['name'];
        $capacity = $_POST['capacity'];
        $gender = $_POST['gender'];

        $db->query("INSERT INTO dorms (name, capacity, gender) VALUES (?, ?, ?)", [
            $name, $capacity, $gender
        ]);

        Session::setFlash('success', 'Gedung/Kamar asrama berhasil ditambahkan.');
        header('Location: /boarding/dorms');
    }

    public function deleteDorm() {
        $id = $_POST['id'];
        $db = Database::getInstance();
        
        // Cek apakah ada penghuninya
        $count = $db->query("SELECT COUNT(*) FROM students WHERE dorm_id = ?", [$id])->fetchColumn();
        
        if ($count > 0) {
            Session::setFlash('error', "Gagal hapus: Masih ada $count santri di asrama ini. Pindahkan mereka dulu.");
        } else {
            $db->query("DELETE FROM dorms WHERE id = ?", [$id]);
            Session::setFlash('success', 'Data asrama berhasil dihapus.');
        }

        header('Location: /boarding/dorms');
    }

    // --- 2. PERIZINAN (IZIN KELUAR) ---
    public function permits() {
        $db = Database::getInstance();
        
        // List Perizinan
        $permits = $db->query("
            SELECT p.*, s.full_name, s.nis, d.name as dorm_name
            FROM permits p
            JOIN students s ON p.student_id = s.id
            LEFT JOIN dorms d ON s.dorm_id = d.id
            ORDER BY p.created_at DESC
        ")->fetchAll();

        // Data Siswa untuk Form Izin Baru
        $students = $db->query("SELECT id, full_name, nis FROM students WHERE status='ACTIVE'")->fetchAll();

        View::render('boarding/permits', [
            'title' => 'Perizinan Santri',
            'permits' => $permits,
            'students' => $students
        ]);
    }

    public function storePermit() {
        $db = Database::getInstance();
        $db->query("INSERT INTO permits (student_id, type, reason, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, 'PENDING')", [
            $_POST['student_id'], $_POST['type'], $_POST['reason'], $_POST['start_date'], $_POST['end_date']
        ]);
        Session::setFlash('success', 'Izin tercatat. Menunggu persetujuan.');
        header('Location: /boarding/permits');
    }

    public function approvePermit() {
        $id = $_GET['id'];
        $action = $_GET['action']; // APPROVE / REJECT / RETURN
        $status = ($action == 'APPROVE') ? 'APPROVED' : (($action == 'RETURN') ? 'RETURNED' : 'REJECTED');
        
        $db = Database::getInstance();
        $userId = Session::get('user_id'); // Perbaikan Session
        
        $db->query("UPDATE permits SET status = ?, approved_by = ? WHERE id = ?", [
            $status, $userId, $id
        ]);
        
        Session::setFlash('success', 'Status perizinan diperbarui.');
        header('Location: /boarding/permits');
    }

    // --- 3. POSKESTREN (KESEHATAN) ---
    public function health() {
        $db = Database::getInstance();
        
        // List Pasien (Hari ini & Riwayat Terakhir)
        $records = $db->query("
            SELECT hr.*, s.full_name, s.nis, u.name as officer_name
            FROM health_records hr
            JOIN students s ON hr.student_id = s.id
            JOIN users u ON hr.officer_id = u.id
            ORDER BY hr.date DESC LIMIT 50
        ")->fetchAll();

        // Data untuk dropdown input
        $students = $db->query("SELECT id, full_name, nis FROM students WHERE status='ACTIVE'")->fetchAll();

        View::render('boarding/health', ['title' => 'Poskestren', 'records' => $records, 'students' => $students]);
    }

    public function storeHealth() {
        $db = Database::getInstance();
        $userId = Session::get('user_id'); // Perbaikan Session

        $db->query("INSERT INTO health_records (student_id, date, complaint, diagnosis, treatment, status, officer_id) VALUES (?, ?, ?, ?, ?, ?, ?)", [
            $_POST['student_id'], $_POST['date'], $_POST['complaint'], 
            $_POST['diagnosis'], $_POST['treatment'], $_POST['status'], $userId
        ]);
        
        Session::setFlash('success', 'Data kesehatan santri dicatat.');
        header('Location: /boarding/health');
    }

    // --- 4. MONITORING TAHFIDZ ---
    public function tahfidz() {
        $db = Database::getInstance();
        $userId = Session::get('user_id'); // Perbaikan Session
        $userRole = Session::get('user_role'); // Perbaikan Session

        // List Setoran (Guru hanya lihat inputan dia sendiri agar tidak pusing, Admin lihat semua)
        $sql = "
            SELECT wl.*, s.full_name, s.nis 
            FROM worship_logs wl
            JOIN students s ON wl.student_id = s.id
        ";
        
        if ($userRole == 'guru') {
            $sql .= " WHERE wl.teacher_id = $userId";
        }
        
        $sql .= " ORDER BY wl.date DESC LIMIT 50";
        $logs = $db->query($sql)->fetchAll();

        $students = $db->query("SELECT id, full_name, nis FROM students WHERE status='ACTIVE'")->fetchAll();

        View::render('boarding/tahfidz', ['title' => 'Setoran Hafalan', 'logs' => $logs, 'students' => $students]);
    }

    public function storeTahfidz() {
        $db = Database::getInstance();
        $userId = Session::get('user_id'); // Perbaikan Session

        $db->query("INSERT INTO worship_logs (student_id, teacher_id, date, type, surah_name, verses, grade, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
            $_POST['student_id'], $userId, $_POST['date'], 
            $_POST['type'], $_POST['surah_name'], $_POST['verses'], 
            $_POST['grade'], $_POST['note']
        ]);
        
        Session::setFlash('success', 'Setoran hafalan dicatat.');
        header('Location: /boarding/tahfidz');
    }
}

