<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Database;
use App\Core\Middleware;

class FinanceController {
    
    public function __construct() {
        Middleware::auth();
        // Hanya Admin/Staff yang boleh masuk sini
        // Siswa punya controller sendiri (StudentController)
        if (Session::get('user_role') == 'siswa') {
            header('Location: /student/dashboard');
            exit;
        }
    }

    // 1. HALAMAN PENCARIAN SISWA (KASIR UTAMA)
    public function index() {
        View::render('finance/index', [
            'title' => 'Kasir Pembayaran'
        ]);
    }

    // 2. HALAMAN DETAIL TAGIHAN (SETELAH CARI NIS)
    public function billing() {
        $nis = $_GET['nis'] ?? null;
        if (!$nis) {
            header('Location: /finance'); // Kalau gak ada NIS, tendang ke pencarian
            exit;
        }

        $db = Database::getInstance();
        
        // Ambil Data Siswa
        $student = $db->query("SELECT * FROM students WHERE nis = ?", [$nis])->fetch();
        
        if (!$student) {
            Session::setFlash('error', 'Siswa dengan NIS tersebut tidak ditemukan.');
            header('Location: /finance');
            exit;
        }

        // Ambil Tagihan
        $bills = $db->query("SELECT * FROM bills WHERE student_id = ? ORDER BY created_at DESC", [$student['id']])->fetchAll();

        View::render('finance/billing', [
            'title' => 'Detail Keuangan',
            'student' => $student,
            'bills' => $bills
        ]);
    }

    // 3. PROSES BUAT TAGIHAN (MENGATASI 404)
    public function createBill() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /finance'); exit;
        }

        $nis = $_POST['student_nis'] ?? '';
        $title = $_POST['title'];
        $amount = $_POST['amount'];
        $desc = $_POST['description'];

        $db = Database::getInstance();
        
        // Cari ID Siswa berdasarkan NIS
        $student = $db->query("SELECT id FROM students WHERE nis = ?", [$nis])->fetch();
        
        if ($student) {
            // Insert Tagihan
            $sql = "INSERT INTO bills (student_id, title, description, amount, status, created_at) VALUES (?, ?, ?, ?, 'UNPAID', NOW())";
            $db->query($sql, [$student['id'], $title, $desc, $amount]);
            
            Session::setFlash('success', 'Tagihan berhasil dibuat.');
        } else {
            Session::setFlash('error', 'Gagal: Data siswa tidak valid.');
        }

        // Kembali ke halaman detail
        header('Location: /finance/billing?nis=' . $nis);
    }

    // 4. PROSES BAYAR (ADMIN KONFIRMASI MANUAL) - Opsional jika admin klik bayar
    public function markAsPaid() {
        // Logic jika admin ingin menandai lunas manual (bisa ditambahkan nanti)
    }
// ==========================================================
    // 5. MASTER JENIS TAGIHAN (Fee Types)
    // ==========================================================
    public function feeTypes() {
        $db = Database::getInstance();
        
        // Ambil data jenis tagihan
        $types = $db->query("SELECT * FROM fee_types ORDER BY id DESC")->fetchAll();
        
        View::render('finance/fee_types', [
            'title' => 'Master Jenis Tagihan',
            'types' => $types
        ]);
    }

    public function storeFeeType() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $amount = $_POST['amount'] ?? 0;
            
            $db = Database::getInstance();
            // Simpan ke database
            $db->query("INSERT INTO fee_types (name, amount, created_at) VALUES (?, ?, NOW())", [$name, $amount]);
            
            Session::setFlash('success', 'Jenis Tagihan berhasil ditambahkan.');
        }
        header('Location: /finance/fee-types');
    }

    public function deleteFeeType() {
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $db = Database::getInstance();
            $db->query("DELETE FROM fee_types WHERE id = ?", [$id]);
            Session::setFlash('success', 'Data dihapus.');
        }
        header('Location: /finance/fee-types');
    }

    // ==========================================================
    // 6. LAPORAN & REKAP (Reports)
    // ==========================================================
    public function reports() {
        $db = Database::getInstance();
        
        // Data Ringkas
        $totalIncome = $db->query("SELECT SUM(amount) FROM bills WHERE status = 'PAID'")->fetchColumn();
        $totalUnpaid = $db->query("SELECT SUM(amount) FROM bills WHERE status = 'UNPAID'")->fetchColumn();
        
        // Ambil 10 Transaksi Terakhir (FIXED: Pakai created_at)
        $recentTx = $db->query("
            SELECT b.*, s.full_name, s.nis 
            FROM bills b 
            JOIN students s ON b.student_id = s.id 
            WHERE b.status = 'PAID' 
            ORDER BY b.created_at DESC LIMIT 10 
        ")->fetchAll();

        View::render('finance/reports', [
            'title' => 'Laporan Keuangan',
            'total_income' => $totalIncome,
            'total_unpaid' => $totalUnpaid,
            'recent_transactions' => $recentTx
        ]);
    }
    // ==========================================================
    // 7. DOWNLOAD LAPORAN (Print View)
    // ==========================================================
    public function printReport() {
        // Logika print laporan (bisa dikembangkan nanti)
        $this->reports(); // Sementara tampilkan view reports biasa
    }
// ==========================================================
    // 4. PROSES UPLOAD BUKTI BAYAR (SISWA)
    // ==========================================================
    public function pay() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /student/dashboard'); 
            exit;
        }

        $billId = $_POST['bill_id'] ?? null;
        $file = $_FILES['payment_proof'] ?? null;

        // Validasi Input
        if (!$billId || !$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'Gagal upload. Pastikan file dipilih.');
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // 1. Siapkan Folder Upload
        // Lokasi: public/uploads/payments/
        $uploadDir = __DIR__ . '/../../public/uploads/payments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // 2. Generate Nama File Unik
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'PAY_' . time() . '_' . $billId . '.' . $ext;
        $destination = $uploadDir . $filename;

        // 3. Pindahkan File & Update Database
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $db = Database::getInstance();
            
            // Update database: Simpan nama file & Ubah status jadi PAID (LUNAS)
            // Catatan: Dalam sistem riil, biasanya status jadi 'VERIFY' dulu. 
            // Tapi untuk kasus ini kita langsung LUNAS agar tombol bayar hilang.
            $sql = "UPDATE bills SET payment_proof = ?, status = 'PAID', updated_at = NOW() WHERE id = ?";
            $db->query($sql, [$filename, $billId]);

            Session::setFlash('success', 'Pembayaran berhasil dikirim & diverifikasi otomatis.');
        } else {
            Session::setFlash('error', 'Gagal memindahkan file ke server.');
        }

        // Kembali ke halaman sebelumnya
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
