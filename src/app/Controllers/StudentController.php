<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class StudentController {
    
    public function __construct() {
        // 1. Cek Login
        Middleware::auth(); 
        
        // 2. Cek Role (Hanya Siswa yang boleh akses)
        if (Session::get('user_role') !== 'siswa') {
            header('Location: /dashboard'); 
            exit;
        }
    }

    public function index() { 
        $this->dashboard(); 
    }

    // =========================================================================
    // 1. DASHBOARD (HYBRID: OVERVIEW CALON VS DASHBOARD SISWA)
    // =========================================================================
    public function dashboard() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        // SKENARIO A: CEK SISWA AKTIF (Tabel students)
        // Prioritas pertama: Jika data ada di students, berarti dia sudah diterima resmi.
        $student = $db->query("
            SELECT s.*, c.name as class_name 
            FROM students s
            LEFT JOIN classrooms c ON s.classroom_id = c.id
            WHERE s.user_id = :uid
        ", ['uid' => $userId])->fetch();

        if ($student) {
            // Hitung tagihan belum lunas untuk notifikasi dashboard
            $unpaidBills = $db->query("SELECT COUNT(*) FROM bills WHERE student_id = ? AND status = 'UNPAID'", [$student['id']])->fetchColumn();
            
            View::render('student/dashboard', [
                'title' => 'Dashboard Siswa',
                'student' => $student,
                'is_active' => true,      // Flag untuk View (Tampilan Biru)
                'unpaid_bills' => $unpaidBills
            ]);
            return;
        }

        // SKENARIO B: CEK CALON SISWA (Tabel student_candidates)
        // Jika tidak ada di students, cek apakah dia pendaftar PPDB
        $candidate = $db->query("
            SELECT sc.*, t.name as track_name 
            FROM student_candidates sc
            JOIN ppdb_tracks t ON sc.ppdb_track_id = t.id
            WHERE sc.user_id = :uid
        ", ['uid' => $userId])->fetch();

        if ($candidate) {
            View::render('student/dashboard', [
                'title' => 'Panel Santri', 
                'candidate' => $candidate,
                'is_active' => false      // Flag untuk View (Tampilan Hijau / Panel Santri)
            ]);
            return;
        }

        // SKENARIO C: DATA HILANG / TIDAK TERHUBUNG
        die("Error: Akun Anda tidak terhubung ke Data Siswa maupun Data Pendaftaran PPDB. Hubungi Admin.");
    }

    // =========================================================================
    // 2. DATA DIRI / PROFIL
    // =========================================================================
    public function profile() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        // A. Cek Calon Santri (Tampilkan Data Pendaftaran)
	$candidate = $db->query("
            SELECT sc.*, t.name as track_name, t.level,
                   u.name as account_name, u.email as account_email, u.username as account_username
            FROM student_candidates sc
            JOIN ppdb_tracks t ON sc.ppdb_track_id = t.id
            JOIN users u ON sc.user_id = u.id 
            WHERE sc.user_id = ?
        ", [$userId])->fetch();
        if ($candidate) {
            View::render('student/profile', [
                'title' => 'Data Santri',
                'candidate' => $candidate,
                'is_candidate' => true // Flag tampilan hijau
            ]);
            return;
        }

        // B. Cek Siswa Aktif (Tampilkan Data Akademik)
        $student = $db->query("
            SELECT s.*, c.name as class_name, d.name as dorm_name
            FROM students s
            LEFT JOIN classrooms c ON s.classroom_id = c.id
            LEFT JOIN dorms d ON s.dorm_id = d.id
            WHERE s.user_id = ?
        ", [$userId])->fetch();

        if ($student) {
            View::render('student/profile', [
                'title' => 'Profil Saya',
                'student' => $student,
                'is_candidate' => false // Flag tampilan biru
            ]);
            return;
        }

        die("Data profil tidak ditemukan.");
    }

    // =========================================================================
    // 3. PEMBAYARAN (HYBRID)
    // =========================================================================
    public function payment() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        // A. Jika Siswa Aktif -> Redirect ke Modul Finance (Lihat Tagihan SPP)
        $student = $db->query("SELECT nis FROM students WHERE user_id = ?", [$userId])->fetch();
        if ($student) {
            header("Location: /finance/billing?nis=" . $student['nis']);
            exit;
        }

        // B. Jika Calon Santri -> Tampilkan Halaman Upload Bukti Transfer PPDB
        $candidate = $db->query("SELECT id FROM student_candidates WHERE user_id = ?", [$userId])->fetch();
        if ($candidate) {
            // Ambil Data Pembayaran Terakhir
            $payment = $db->query("SELECT * FROM ppdb_payments WHERE candidate_id = ? ORDER BY id DESC LIMIT 1", [$candidate['id']])->fetch();
            
            View::render('student/payment', [
                'title' => 'Pembayaran Pendaftaran',
                'payment' => $payment
            ]);
            return;
        }

        header('Location: /student/dashboard');
        exit;
    }

    // Proses Upload Bukti Bayar (Hanya untuk Calon Santri)
    public function storePayment() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $candidate = $db->query("SELECT id FROM student_candidates WHERE user_id = ?", [$userId])->fetch();

        if (!$candidate) { header('Location: /dashboard'); exit; }

        if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $filename = $_FILES['proof_file']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $newFilename = 'PAY-' . $candidate['id'] . '-' . time() . '.' . $ext;
                $destination = __DIR__ . '/../../public/uploads/payments/' . $newFilename;

                if (!is_dir(dirname($destination))) mkdir(dirname($destination), 0755, true);

                if (move_uploaded_file($_FILES['proof_file']['tmp_name'], $destination)) {
                    $sql = "INSERT INTO ppdb_payments (candidate_id, amount, payment_date, proof_file, status) VALUES (?, ?, ?, ?, ?)";
                    $db->query($sql, [$candidate['id'], $_POST['amount'], $_POST['payment_date'], $newFilename, 'PENDING']);
                    
                    // Opsional: Update status jadi PAID langsung (atau tunggu admin)
                    // $db->query("UPDATE student_candidates SET registration_status = 'PAID' WHERE id = ?", [$candidate['id']]);
                    
                    Session::setFlash('success', 'Bukti pembayaran dikirim. Tunggu verifikasi.');
                } else {
                    Session::setFlash('error', 'Gagal upload.');
                }
            } else {
                Session::setFlash('error', 'Format salah. Gunakan JPG, PNG, atau PDF.');
            }
        } else {
            Session::setFlash('error', 'Pilih file bukti transfer.');
        }
        header('Location: /student/payment');
    }

    // =========================================================================
    // 4. DOKUMEN (UPLOAD BERKAS)
    // =========================================================================
    public function documents() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        
        // Cari ID Target (Bisa Student ID atau Candidate ID)
        // Kita gunakan tabel 'ppdb_documents' dengan kolom 'candidate_id' sebagai referensi ID Siswa/Kandidat
        
        $targetId = null;
        
        // Cek Siswa Aktif
        $sData = $db->query("SELECT id FROM students WHERE user_id = ?", [$userId])->fetch();
        if ($sData) {
            $targetId = $sData['id'];
        } else {
            // Cek Calon Santri
            $cData = $db->query("SELECT id FROM student_candidates WHERE user_id = ?", [$userId])->fetch();
            if ($cData) $targetId = $cData['id'];
        }
        
        if (!$targetId) {
            Session::setFlash('error', 'Data siswa tidak ditemukan.');
            header('Location: /student/dashboard');
            exit;
        }

        // Ambil dokumen
        $docsRaw = $db->query("SELECT * FROM ppdb_documents WHERE candidate_id = ?", [$targetId])->fetchAll();
        $documents = [];
        foreach($docsRaw as $d) $documents[$d['doc_type']] = $d;

        View::render('student/documents', [
            'title' => 'Kelengkapan Dokumen',
            'documents' => $documents,
            'student_id' => $targetId
        ]);
    }

    public function storeDocument() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        
        // Cari ID Target lagi untuk keamanan
        $targetId = null;
        $sData = $db->query("SELECT id FROM students WHERE user_id = ?", [$userId])->fetch();
        if ($sData) $targetId = $sData['id'];
        else {
            $cData = $db->query("SELECT id FROM student_candidates WHERE user_id = ?", [$userId])->fetch();
            if ($cData) $targetId = $cData['id'];
        }

        if (!$targetId) { header('Location: /student/dashboard'); exit; }

        $type = $_POST['doc_type'] ?? 'LAINNYA'; 

        if (isset($_FILES['doc_file']) && $_FILES['doc_file']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $filename = $_FILES['doc_file']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $newFilename = $type . '-' . $targetId . '-' . time() . '.' . $ext;
                $destination = __DIR__ . '/../../public/uploads/documents/' . $newFilename;

                if (!is_dir(dirname($destination))) mkdir(dirname($destination), 0755, true);

                if (move_uploaded_file($_FILES['doc_file']['tmp_name'], $destination)) {
                    // Update DB
                    $exist = $db->query("SELECT id FROM ppdb_documents WHERE candidate_id = ? AND doc_type = ?", [$targetId, $type])->fetch();

                    if ($exist) {
                        $db->query("UPDATE ppdb_documents SET file_path = ?, status = 'PENDING' WHERE id = ?", [$newFilename, $exist['id']]);
                    } else {
                        $db->query("INSERT INTO ppdb_documents (candidate_id, doc_type, file_path, status) VALUES (?, ?, ?, ?)", [
                            $targetId, $type, $newFilename, 'PENDING'
                        ]);
                    }
                    Session::setFlash('success', "Dokumen $type berhasil diupload.");
                } else {
                    Session::setFlash('error', 'Gagal upload file.');
                }
            } else {
                Session::setFlash('error', 'Format file salah (JPG/PNG/PDF only).');
            }
        }
        header('Location: /student/documents');
    }

    // =========================================================================
    // 5. KARTU UJIAN
    // =========================================================================
    public function examCard() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        // A. LOGIKA SISWA AKTIF (Kunci Kartu jika ada tunggakan SPP)
        $student = $db->query("SELECT s.*, c.name as class_name FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id WHERE s.user_id = ?", [$userId])->fetch();
        
        if ($student) {
            $unpaid = $db->query("SELECT COUNT(*) FROM bills WHERE student_id = ? AND status = 'UNPAID'", [$student['id']])->fetchColumn();
            
            if ($unpaid > 0) {
                Session::setFlash('error', "Kartu Ujian terkunci. Anda memiliki $unpaid tagihan belum lunas.");
                header('Location: /finance/billing?nis=' . $student['nis']);
                exit;
            }

            // Data Jadwal Ujian Siswa
             $examSchedule = [
                'period' => 'Ujian Akhir Semester',
                'dates' => date('d M Y', strtotime('+1 week')),
                'location' => 'Kelas Masing-masing'
            ];
            
            View::render('student/exam_card', [
                'title' => 'Kartu Ujian', 
                'student' => $student, 
                'exam' => $examSchedule
            ]);
            return;
        }

        // B. LOGIKA CALON SISWA (Kunci Kartu jika belum APPROVED)
        $candidate = $db->query("SELECT * FROM student_candidates WHERE user_id = ?", [$userId])->fetch();
        
        if ($candidate) {
            if ($candidate['registration_status'] != 'APPROVED') {
                Session::setFlash('error', "Status pendaftaran belum DITERIMA. Tunggu verifikasi admin.");
                header('Location: /student/dashboard');
                exit;
            }

            // Mapping Data Candidate agar sesuai dengan format View Exam Card
            $fakeStudent = [
                'full_name' => $candidate['full_name'],
                'nis' => 'REG-' . $candidate['id'], // Pakai No Reg sementara
                'class_name' => 'Calon Santri Baru',
                'photo' => null // Bisa ambil dari dokumen foto jika ada
            ];
            
            $examSchedule = [
                'period' => 'Seleksi Masuk PPDB',
                'dates' => 'Ahad, 20 Juli 2025',
                'location' => 'Aula Utama Pesantren'
            ];

            View::render('student/exam_card', [
                'title' => 'Kartu Ujian', 
                'student' => $fakeStudent, 
                'exam' => $examSchedule
            ]);
            return;
        }
        
        header('Location: /student/dashboard');
    }
    // 5. KARTU UJIAN (Fixed Name: printExamCard)
    // =========================================================================
public function printExamCard() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        // ============================================================
        // A. LOGIKA SISWA AKTIF (DITUTUP AKSESNYA)
        // ============================================================
        // Cek apakah user adalah siswa aktif
        $student = $db->query("SELECT id FROM students WHERE user_id = ?", [$userId])->fetch();
        
        if ($student) {
            // Sesuai Request: Siswa aktif TIDAK BOLEH melihat kartu ujian.
            // Maka kita hapus logika cek tagihan & render view, ganti dengan redirect info.
            Session::setFlash('info', 'Belum ada jadwal ujian semester yang aktif untuk Siswa.');
            header('Location: /student/dashboard');
            exit; 
        }

        // ============================================================
        // B. LOGIKA CALON SISWA (TETAP ADA & BISA CETAK)
        // ============================================================
        $candidate = $db->query("
            SELECT sc.*, t.name as track_name, t.level,
                   (SELECT file_path FROM ppdb_documents WHERE candidate_id = sc.id AND doc_type = 'FOTO' LIMIT 1) as photo
            FROM student_candidates sc
            JOIN ppdb_tracks t ON sc.ppdb_track_id = t.id
            WHERE sc.user_id = ?
        ", [$userId])->fetch();
        
        if ($candidate) {
            $status = strtoupper($candidate['registration_status']);
            
            // Cek status Lulus (Support APPROVED, LULUS, ACCEPTED)
            if ($status != 'APPROVED' && $status != 'LULUS' && $status != 'ACCEPTED') {
                Session::setFlash('error', "Status pendaftaran belum DITERIMA. Tunggu verifikasi admin.");
                header('Location: /student/dashboard');
                exit;
            }

            // Mapping Data Candidate agar formatnya sama dengan view
            $fakeStudent = [
                'full_name' => $candidate['full_name'],
                'nis' => 'REG-' . $candidate['id'], 
                'class_name' => 'Calon Santri (' . $candidate['track_name'] . ')',
                'photo' => $candidate['photo'] ?? null 
            ];
            
            $examSchedule = [
                'period' => 'Seleksi Masuk PPDB',
                'dates' => 'Ahad, 20 Juli 2026',
                'location' => 'Aula Utama Pesantren'
            ];

            View::render('student/exam_card', [
                'title' => 'Kartu Ujian', 
                'student' => $fakeStudent, 
                'exam' => $examSchedule
            ]);
            return;
        }
        
        // Jika bukan siswa aktif dan bukan calon siswa
        header('Location: /student/dashboard');
    }
}
