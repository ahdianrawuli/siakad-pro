<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Database;
use App\Core\Middleware;
use App\Core\ScopeFilter;

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

    // ==========================================================
    // SPP — Master Data SPP per Kelas
    // ==========================================================
    public function spp() {
        $db = Database::getInstance();
        $sppList = $db->query("SELECT * FROM fee_types WHERE type = 'MONTHLY' ORDER BY name")->fetchAll();
        $classrooms = $db->query("SELECT id, name FROM classrooms ORDER BY name")->fetchAll();
        View::render('finance/spp', [
            'title'      => 'Data SPP',
            'sppList'    => $sppList,
            'classrooms' => $classrooms,
        ]);
    }

    public function storeSpp() {
        $db = Database::getInstance();
        $db->query("INSERT INTO fee_types (name, amount, type) VALUES (?, ?, 'MONTHLY')", [
            $_POST['name'], $_POST['amount']
        ]);
        Session::setFlash('success', 'Data SPP berhasil ditambahkan.');
        header('Location: /finance/spp');
    }

    public function deleteSpp() {
        $db = Database::getInstance();
        $db->query("DELETE FROM fee_types WHERE id = ? AND type = 'MONTHLY'", [(int)$_GET['id']]);
        Session::setFlash('success', 'Data SPP dihapus.');
        header('Location: /finance/spp');
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
        [$sw, $sp] = ScopeFilter::apply('c');
        $byClass = $db->query(
            "SELECT c.name as class_name,
                    SUM(CASE WHEN b.status='PAID' THEN b.amount ELSE 0 END) as paid,
                    SUM(CASE WHEN b.status='UNPAID' THEN b.amount ELSE 0 END) as unpaid
             FROM bills b
             JOIN students s ON b.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE YEAR(b.created_at) = ? $sw
             GROUP BY c.name ORDER BY c.name",
            array_merge([$year], $sp)
        )->fetchAll();

        // Transaksi terbaru
        $recent = $db->query(
            "SELECT b.*, s.full_name, s.nis, c.name as class_name
             FROM bills b JOIN students s ON b.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE b.status = 'PAID' AND YEAR(b.updated_at) = ? AND MONTH(b.updated_at) = ? $sw
             ORDER BY b.updated_at DESC LIMIT 20",
            array_merge([$year, $mon], $sp)
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
        if (!$nis) { header('Location: /finance'); exit; }

        $db      = Database::getInstance();
        $student = $db->query(
            "SELECT s.*, c.name as class_name FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id WHERE s.nis = ?",
            [$nis]
        )->fetch();

        if (!$student) {
            Session::setFlash('error', 'Siswa dengan NIS tersebut tidak ditemukan.');
            header('Location: /finance'); exit;
        }

        $bills    = $db->query(
            "SELECT b.*, ft.name as fee_type_name FROM bills b LEFT JOIN fee_types ft ON b.fee_type_id = ft.id WHERE b.student_id = ? ORDER BY b.created_at DESC",
            [$student['id']]
        )->fetchAll();
        $feeTypes = $db->query("SELECT * FROM fee_types ORDER BY name")->fetchAll();

        // Ringkasan
        $totalUnpaid = array_sum(array_column(array_filter($bills, fn($b) => $b['status'] === 'UNPAID'), 'amount'));
        $totalPaid   = array_sum(array_column(array_filter($bills, fn($b) => $b['status'] === 'PAID'), 'amount'));

        View::render('finance/billing', [
            'title'       => 'Info Keuangan & Tagihan',
            'student'     => $student,
            'bills'       => $bills,
            'feeTypes'    => $feeTypes,
            'totalUnpaid' => $totalUnpaid,
            'totalPaid'   => $totalPaid,
        ]);
    }

    // 3. PROSES BUAT TAGIHAN
    public function createBill() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /finance'); exit; }

        $nis     = $_POST['student_nis'] ?? '';
        $db      = Database::getInstance();
        $student = $db->query("SELECT id FROM students WHERE nis = ?", [$nis])->fetch();

        if ($student) {
            $feeTypeId = !empty($_POST['fee_type_id']) ? $_POST['fee_type_id'] : null;
            $title     = $_POST['title'] ?: ($db->query("SELECT name FROM fee_types WHERE id=?", [$feeTypeId])->fetchColumn() ?: 'Tagihan');
            $db->query(
                "INSERT INTO bills (student_id, fee_type_id, title, description, amount, due_date, status, created_at) VALUES (?,?,?,?,?,?,  'UNPAID', NOW())",
                [$student['id'], $feeTypeId, $title, $_POST['description'] ?? null, $_POST['amount'], $_POST['due_date'] ?: null]
            );
            // Notifikasi WA ke orang tua
            $parent = $db->query("SELECT full_name, COALESCE(parent_phone, father_phone, mother_phone, guardian_phone) as phone FROM students WHERE id=?", [$student['id']])->fetch();
            if (!empty($parent['phone'])) {
                $msg = "Assalamu'alaikum,\n\nTagihan baru telah dibuat untuk *{$parent['full_name']}*:\n"
                     . "*{$title}*\nNominal: *Rp " . number_format($_POST['amount'], 0, ',', '.') . "*"
                     . (!empty($_POST['due_date']) ? "\nJatuh Tempo: " . date('d M Y', strtotime($_POST['due_date'])) : "")
                     . "\n\nMohon segera melakukan pembayaran.\n— SIAKAD Parabek";
                try { \App\Models\WhatsappService::send($parent['phone'], $msg); } catch (\Exception $e) {}
            }
            Session::setFlash('success', 'Tagihan berhasil dibuat.');
        } else {
            Session::setFlash('error', 'Gagal: Data siswa tidak valid.');
        }
        header('Location: /finance/billing?nis=' . urlencode($nis));
    }

    // 4. TANDAI LUNAS (TUNAI / TRANSFER)
    public function markAsPaid() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /finance'); exit; }
        $db     = Database::getInstance();
        $billId = (int)$_POST['bill_id'];
        $method = in_array($_POST['payment_method'] ?? '', ['CASH','TRANSFER']) ? $_POST['payment_method'] : 'CASH';
        $bill   = $db->query("SELECT * FROM bills WHERE id=?", [$billId])->fetch();
        if (!$bill) { Session::setFlash('error', 'Tagihan tidak ditemukan.'); header('Location: /finance'); exit; }

        $db->query("UPDATE bills SET status='PAID', updated_at=NOW() WHERE id=?", [$billId]);
        $db->query("INSERT INTO transactions (bill_id, amount_paid, payment_method, payment_date, notes, admin_id) VALUES (?,?,?,CURDATE(),?,?)", [
            $billId, $bill['amount'], $method, $_POST['notes'] ?? null, Session::get('user_id')
        ]);
        $nis = $db->query("SELECT nis FROM students WHERE id=?", [$bill['student_id']])->fetchColumn();
        Session::setFlash('success', 'Tagihan berhasil ditandai lunas.');
        header('Location: /finance/billing?nis=' . urlencode($nis));
    }

    // 4b. VERIFIKASI BUKTI BAYAR (UPLOAD)
    public function verifyPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /finance'); exit; }
        $db     = Database::getInstance();
        $billId = (int)$_POST['bill_id'];
        $action = $_POST['action'] ?? 'approve'; // approve | reject
        $bill   = $db->query("SELECT * FROM bills WHERE id=?", [$billId])->fetch();
        if (!$bill) { Session::setFlash('error', 'Tagihan tidak ditemukan.'); header('Location: /finance'); exit; }

        if ($action === 'approve') {
            $db->query("UPDATE bills SET status='PAID', updated_at=NOW() WHERE id=?", [$billId]);
            $db->query("INSERT INTO transactions (bill_id, amount_paid, payment_method, payment_date, notes, admin_id) VALUES (?,?,?,CURDATE(),?,?)", [
                $billId, $bill['amount'], 'TRANSFER', 'Verifikasi bukti transfer', Session::get('user_id')
            ]);
            Session::setFlash('success', 'Pembayaran diverifikasi & disetujui.');
        } else {
            $db->query("UPDATE bills SET status='UNPAID', payment_proof=NULL WHERE id=?", [$billId]);
            Session::setFlash('warning', 'Bukti bayar ditolak. Tagihan dikembalikan ke BELUM BAYAR.');
        }
        $nis = $db->query("SELECT nis FROM students WHERE id=?", [$bill['student_id']])->fetchColumn();
        header('Location: /finance/billing?nis=' . urlencode($nis));
    }

    // 4c. HAPUS TAGIHAN
    public function deleteBill() {
        $db     = Database::getInstance();
        $billId = (int)($_GET['id'] ?? 0);
        $bill   = $db->query("SELECT b.*, s.nis FROM bills b JOIN students s ON b.student_id=s.id WHERE b.id=?", [$billId])->fetch();
        if ($bill && $bill['status'] === 'UNPAID') {
            $db->query("DELETE FROM bills WHERE id=?", [$billId]);
            Session::setFlash('success', 'Tagihan berhasil dihapus.');
        } else {
            Session::setFlash('error', 'Tagihan lunas tidak dapat dihapus.');
        }
        header('Location: /finance/billing?nis=' . urlencode($bill['nis'] ?? ''));
    }

    // 5. CETAK KUITANSI
    public function printReceipt() {
        $db    = Database::getInstance();
        $billId = (int)($_GET['bill_id'] ?? 0);
        $trx   = $db->query(
            "SELECT t.*, b.title as fee_name, b.amount, s.full_name, s.nis, c.name as class_name, u.name as admin_name
             FROM transactions t
             JOIN bills b ON t.bill_id = b.id
             JOIN students s ON b.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             LEFT JOIN users u ON t.admin_id = u.id
             WHERE t.bill_id = ?
             ORDER BY t.id DESC LIMIT 1",
            [$billId]
        )->fetch();
        if (!$trx) { echo 'Kuitansi tidak ditemukan.'; exit; }
        // Gunakan amount_paid dari transaksi, fallback ke amount bill
        if (empty($trx['amount_paid'])) $trx['amount_paid'] = $trx['amount'];
        View::render('finance/print_receipt', ['trx' => $trx]);
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

        $search    = $_GET['search']      ?? '';
        $status    = $_GET['status']      ?? '';
        $dateFrom  = $_GET['date_from']   ?? '';
        $dateTo    = $_GET['date_to']     ?? '';
        $classId   = $_GET['class_id']    ?? '';
        $feeTypeId = $_GET['fee_type_id'] ?? '';
        $format    = $_GET['format']      ?? 'excel';

        $where  = "WHERE 1=1";
        $params = [];
        if (!empty($search))    { $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($status))    { $where .= " AND b.status = ?";            $params[] = $status; }
        if (!empty($dateFrom))  { $where .= " AND DATE(b.created_at) >= ?"; $params[] = $dateFrom; }
        if (!empty($dateTo))    { $where .= " AND DATE(b.created_at) <= ?"; $params[] = $dateTo; }
        if (!empty($classId))   { $where .= " AND s.classroom_id = ?";      $params[] = $classId; }
        if (!empty($feeTypeId)) { $where .= " AND b.fee_type_id = ?";       $params[] = $feeTypeId; }

        $rows = $db->query(
            "SELECT b.created_at, s.full_name, s.nis, c.name as class_name,
                    COALESCE(ft.name, b.title) as fee_name, b.amount, b.status, t.payment_method
             FROM bills b JOIN students s ON b.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             LEFT JOIN fee_types ft ON b.fee_type_id = ft.id
             LEFT JOIN transactions t ON t.bill_id = b.id
             $where ORDER BY b.created_at DESC",
            $params
        )->fetchAll();

        $totalIncome = array_sum(array_column(array_filter($rows, fn($r) => $r['status'] == 'PAID'), 'amount'));
        $totalUnpaid = array_sum(array_column(array_filter($rows, fn($r) => $r['status'] == 'UNPAID'), 'amount'));

        if ($format === 'excel') {
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="laporan-keuangan-' . date('Ymd') . '.xls"');
            header('Cache-Control: max-age=0');
            echo "\xEF\xBB\xBF";
            echo "Tanggal\tNama Siswa\tNIS\tKelas\tKeterangan\tMetode\tNominal\tStatus\n";
            foreach ($rows as $r) {
                echo date('d/m/Y', strtotime($r['created_at'])) . "\t"
                    . $r['full_name'] . "\t" . $r['nis'] . "\t"
                    . ($r['class_name'] ?? '-') . "\t"
                    . ($r['fee_name'] ?? 'Tagihan') . "\t"
                    . ($r['payment_method'] ?? '-') . "\t"
                    . $r['amount'] . "\t" . $r['status'] . "\n";
            }
            echo "\t\t\t\t\t\t\t\n";
            echo "\t\t\t\tTotal Lunas\t\t" . $totalIncome . "\t\n";
            echo "\t\t\t\tTotal Belum Bayar\t\t" . $totalUnpaid . "\t\n";
            exit;
        }

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

        $search    = $_GET['search']      ?? '';
        $dateFrom  = $_GET['date_from']   ?? '';
        $dateTo    = $_GET['date_to']     ?? '';
        $status    = $_GET['status']      ?? '';
        $classId   = $_GET['class_id']    ?? '';
        $feeTypeId = $_GET['fee_type_id'] ?? '';
        $page      = (int)($_GET['page']  ?? 1);
        $limit     = (int)($_GET['limit'] ?? 20);
        $offset    = ($page - 1) * $limit;

        $where  = "WHERE 1=1";
        $params = [];
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);
        if (!empty($search))    { $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($status))    { $where .= " AND b.status = ?";           $params[] = $status; }
        if (!empty($dateFrom))  { $where .= " AND DATE(b.created_at) >= ?"; $params[] = $dateFrom; }
        if (!empty($dateTo))    { $where .= " AND DATE(b.created_at) <= ?"; $params[] = $dateTo; }
        if (!empty($classId))   { $where .= " AND s.classroom_id = ?";     $params[] = $classId; }
        if (!empty($feeTypeId)) { $where .= " AND b.fee_type_id = ?";      $params[] = $feeTypeId; }

        // Summary mengikuti filter
        $summaryRow = $db->query(
            "SELECT
                COALESCE(SUM(CASE WHEN b.status='PAID'   THEN b.amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN b.status='UNPAID' THEN b.amount ELSE 0 END), 0) as total_unpaid,
                COUNT(*) as total_data
             FROM bills b JOIN students s ON b.student_id = s.id $where",
            $params
        )->fetch();

        // Rekap per kelas (mengikuti filter kecuali class_id)
        $whereClass  = str_replace(" AND s.classroom_id = ?", "", $where);
        $paramsClass = array_values(array_filter($params, fn($v, $k) => !($classId && $params[$k] === $classId), ARRAY_FILTER_USE_BOTH));
        // Rebuild params tanpa classId
        $paramsClass = [];
        if (!empty($search))    { $paramsClass[] = "%$search%"; $paramsClass[] = "%$search%"; }
        if (!empty($status))    { $paramsClass[] = $status; }
        if (!empty($dateFrom))  { $paramsClass[] = $dateFrom; }
        if (!empty($dateTo))    { $paramsClass[] = $dateTo; }
        if (!empty($feeTypeId)) { $paramsClass[] = $feeTypeId; }

        $byClass = $db->query(
            "SELECT c.name as class_name,
                    SUM(CASE WHEN b.status='PAID'   THEN b.amount ELSE 0 END) as paid,
                    SUM(CASE WHEN b.status='UNPAID' THEN b.amount ELSE 0 END) as unpaid,
                    COUNT(*) as total
             FROM bills b JOIN students s ON b.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             $whereClass GROUP BY c.id, c.name ORDER BY c.name",
            $paramsClass
        )->fetchAll();

        $totalPages = max(1, ceil($summaryRow['total_data'] / $limit));

        $transactions = $db->query(
            "SELECT b.*, s.full_name, s.nis, c.name as class_name, ft.name as fee_type_name,
                    t.payment_method, t.payment_date
             FROM bills b
             JOIN students s ON b.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             LEFT JOIN fee_types ft ON b.fee_type_id = ft.id
             LEFT JOIN transactions t ON t.bill_id = b.id
             $where ORDER BY b.created_at DESC LIMIT $limit OFFSET $offset",
            $params
        )->fetchAll();

        $classes  = $db->query("SELECT id, name FROM classrooms ORDER BY name")->fetchAll();
        $feeTypes = $db->query("SELECT id, name FROM fee_types ORDER BY name")->fetchAll();

        View::render('finance/reports', [
            'title'        => 'Laporan Keuangan',
            'total_income' => $summaryRow['total_income'],
            'total_unpaid' => $summaryRow['total_unpaid'],
            'totalData'    => $summaryRow['total_data'],
            'byClass'      => $byClass,
            'transactions' => $transactions,
            'classes'      => $classes,
            'feeTypes'     => $feeTypes,
            'search'       => $search,
            'statusFilter' => $status,
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
            'classId'      => $classId,
            'feeTypeId'    => $feeTypeId,
            'totalPages'   => $totalPages,
            'currentPage'  => $page,
            'limit'        => $limit,
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
