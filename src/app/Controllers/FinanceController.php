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

    public function otherFees() {
        View::render('finance/other_fees', [
            'title' => 'Biaya Lain lain'
        ]);
    }

    public function treasurerReports() {
        $db = Database::getInstance();

        $month  = $_GET['month'] ?? date('Y-m');
        $year   = substr($month, 0, 4);
        $mon    = substr($month, 5, 2);

        // Pemasukan per bulan
        $income = $db->query(
            "SELECT b.title, SUM(b.amount) as total, COUNT(*) as count
             FROM bills b
             WHERE b.status = 'PAID' AND YEAR(b.updated_at) = ? AND MONTH(b.updated_at) = ?
             GROUP BY b.title ORDER BY total DESC",
            [$year, $mon]
        )->fetchAll();

        $totalIncome = array_sum(array_column($income, 'total'));

        // Tunggakan (belum bayar)
        $unpaid = $db->query(
            "SELECT b.title, SUM(b.amount) as total, COUNT(*) as count
             FROM bills b WHERE b.status = 'UNPAID'
             GROUP BY b.title ORDER BY total DESC"
        )->fetchAll();
        $totalUnpaid = array_sum(array_column($unpaid, 'total'));

        // Rekap per kelas
        $byClass = $db->query(
            "SELECT c.name as class_name,
                    SUM(CASE WHEN b.status='PAID' THEN b.amount ELSE 0 END) as paid,
                    SUM(CASE WHEN b.status='UNPAID' THEN b.amount ELSE 0 END) as unpaid
             FROM bills b
             JOIN students s ON b.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE YEAR(b.created_at) = ?
             GROUP BY c.name ORDER BY c.name",
            [$year]
        )->fetchAll();

        // Transaksi terbaru
        $recent = $db->query(
            "SELECT b.*, s.full_name, s.nis, c.name as class_name
             FROM bills b JOIN students s ON b.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE b.status = 'PAID' AND YEAR(b.updated_at) = ? AND MONTH(b.updated_at) = ?
             ORDER BY b.updated_at DESC LIMIT 20",
            [$year, $mon]
        )->fetchAll();

        View::render('finance/treasurer_reports', [
            'title'        => 'Laporan Bendahara',
            'income'       => $income,
            'totalIncome'  => $totalIncome,
            'unpaid'       => $unpaid,
            'totalUnpaid'  => $totalUnpaid,
            'byClass'      => $byClass,
            'recent'       => $recent,
            'month'        => $month,
        ]);
    }

    public function notifyBills() {
        $db = Database::getInstance();
        $classId = $_POST['class_id'] ?? '';
        $where = $classId ? "AND s.classroom_id = ?" : "";
        $params = $classId ? [$classId] : [];

        $unpaid = $db->query(
            "SELECT s.full_name, s.parent_phone, s.nis, c.name as class_name,
                    SUM(b.amount) as total_unpaid
             FROM bills b JOIN students s ON b.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE b.status = 'UNPAID' $where
             GROUP BY s.id HAVING total_unpaid > 0",
            $params
        )->fetchAll();

        $sent = 0;
        foreach ($unpaid as $row) {
            if (empty($row['parent_phone'])) continue;
            $msg = "Assalamu'alaikum Wali/Orang Tua,\n\n"
                 . "Kami informasikan bahwa putra/putri Anda:\n"
                 . "*{$row['full_name']}* (Kelas {$row['class_name']})\n\n"
                 . "Masih memiliki tagihan yang belum dibayar sebesar:\n"
                 . "*Rp " . number_format($row['total_unpaid'], 0, ',', '.') . "*\n\n"
                 . "Mohon segera melakukan pembayaran. Terima kasih.\n— SIAKAD Parabek";
            \App\Models\WhatsappService::send($row['parent_phone'], $msg);
            $sent++;
        }

        \App\Core\Session::setFlash('success', "Notifikasi tagihan berhasil dikirim ke $sent orang tua.");
        header('Location: /finance/treasurer-reports');
    }

    public function facilities() {
        View::render('finance/facilities', [
            'title' => 'Sarana dan Prasarana'
        ]);
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
        $db     = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $page   = (int)($_GET['page'] ?? 1);
        $limit  = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $totalData  = $db->query("SELECT COUNT(*) FROM fee_types WHERE name LIKE ?", ["%$search%"])->fetchColumn();
        $totalPages = ceil($totalData / $limit);
        $types      = $db->query("SELECT * FROM fee_types WHERE name LIKE ? ORDER BY id DESC LIMIT $limit OFFSET $offset", ["%$search%"])->fetchAll();

        View::render('finance/fee_types', [
            'title'       => 'Master Jenis Tagihan',
            'types'       => $types,
            'search'      => $search,
            'totalData'   => $totalData,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'limit'       => $limit,
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
    public function exportReports() {
        $db = Database::getInstance();

        $search   = $_GET['search']    ?? '';
        $status   = $_GET['status']    ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo   = $_GET['date_to']   ?? '';
        $format   = $_GET['format']    ?? 'excel';

        $where = "WHERE 1=1";
        $params = [];
        if (!empty($search))   { $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($status))   { $where .= " AND b.status = ?"; $params[] = $status; }
        if (!empty($dateFrom)) { $where .= " AND DATE(b.created_at) >= ?"; $params[] = $dateFrom; }
        if (!empty($dateTo))   { $where .= " AND DATE(b.created_at) <= ?"; $params[] = $dateTo; }

        $rows = $db->query("
            SELECT b.created_at, s.full_name, s.nis, b.title, b.amount, b.status
            FROM bills b JOIN students s ON b.student_id = s.id
            $where ORDER BY b.created_at DESC
        ", $params)->fetchAll();

        $totalIncome = array_sum(array_column(array_filter($rows, fn($r) => $r['status'] == 'PAID'), 'amount'));
        $totalUnpaid = array_sum(array_column(array_filter($rows, fn($r) => $r['status'] == 'UNPAID'), 'amount'));

        if ($format === 'excel') {
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="laporan-keuangan-' . date('Ymd') . '.xls"');
            header('Cache-Control: max-age=0');
            echo "\xEF\xBB\xBF"; // BOM UTF-8
            echo "Tanggal\tNama Siswa\tNIS\tKeterangan\tNominal\tStatus\n";
            foreach ($rows as $r) {
                echo date('d/m/Y', strtotime($r['created_at'])) . "\t"
                    . $r['full_name'] . "\t"
                    . $r['nis'] . "\t"
                    . ($r['title'] ?? 'Tagihan') . "\t"
                    . $r['amount'] . "\t"
                    . $r['status'] . "\n";
            }
            echo "\t\t\t\t\t\n";
            echo "\t\t\tTotal Lunas\t" . $totalIncome . "\t\n";
            echo "\t\t\tTotal Belum Bayar\t" . $totalUnpaid . "\t\n";
            exit;
        }

        // PDF — render HTML print-friendly
        View::render('finance/reports_print', [
            'rows'         => $rows,
            'totalIncome'  => $totalIncome,
            'totalUnpaid'  => $totalUnpaid,
            'search'       => $search,
            'statusFilter' => $status,
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
        ]);
    }

    public function reports() {
        $db = Database::getInstance();

        $search    = $_GET['search'] ?? '';
        $dateFrom  = $_GET['date_from'] ?? '';
        $dateTo    = $_GET['date_to'] ?? '';
        $status    = $_GET['status'] ?? '';
        $page      = (int)($_GET['page'] ?? 1);
        $limit     = (int)($_GET['limit'] ?? 20);
        $offset    = ($page - 1) * $limit;

        $where = "WHERE 1=1";
        $params = [];
        if (!empty($search))   { $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($status))   { $where .= " AND b.status = ?"; $params[] = $status; }
        if (!empty($dateFrom)) { $where .= " AND DATE(b.created_at) >= ?"; $params[] = $dateFrom; }
        if (!empty($dateTo))   { $where .= " AND DATE(b.created_at) <= ?"; $params[] = $dateTo; }

        $totalIncome = $db->query("SELECT COALESCE(SUM(amount),0) FROM bills WHERE status = 'PAID'")->fetchColumn();
        $totalUnpaid = $db->query("SELECT COALESCE(SUM(amount),0) FROM bills WHERE status = 'UNPAID'")->fetchColumn();
        $totalData   = $db->query("SELECT COUNT(*) FROM bills b JOIN students s ON b.student_id = s.id $where", $params)->fetchColumn();
        $totalPages  = ceil($totalData / $limit);

        $transactions = $db->query("
            SELECT b.*, s.full_name, s.nis
            FROM bills b JOIN students s ON b.student_id = s.id
            $where ORDER BY b.created_at DESC LIMIT $limit OFFSET $offset
        ", $params)->fetchAll();

        View::render('finance/reports', [
            'title'               => 'Laporan Keuangan',
            'total_income'        => $totalIncome,
            'total_unpaid'        => $totalUnpaid,
            'transactions'        => $transactions,
            'search'              => $search,
            'statusFilter'        => $status,
            'dateFrom'            => $dateFrom,
            'dateTo'              => $dateTo,
            'totalData'           => $totalData,
            'totalPages'          => $totalPages,
            'currentPage'         => $page,
            'limit'               => $limit,
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
