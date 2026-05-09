<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Models\WhatsappService;

class InventoryController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $catId  = $_GET['category_id'] ?? '';
        $cond   = $_GET['condition'] ?? '';
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $where  = "1=1";
        $params = [];
        if ($search) { $where .= " AND (i.name LIKE ? OR i.code LIKE ? OR i.brand LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($catId)  { $where .= " AND i.category_id = ?"; $params[] = $catId; }
        if ($cond)   { $where .= " AND i.condition_status = ?"; $params[] = $cond; }

        $totalData = $db->query("SELECT COUNT(*) FROM inventory_items i WHERE $where", $params)->fetchColumn();
        $items     = $db->query("SELECT i.*, c.name as category_name, u.name as pic_name
                FROM inventory_items i
                LEFT JOIN inventory_categories c ON i.category_id = c.id
                LEFT JOIN users u ON i.created_by = u.id
                WHERE $where ORDER BY i.created_at DESC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        $categories = $db->query("SELECT * FROM inventory_categories ORDER BY name")->fetchAll();
        $summary    = $db->query("SELECT SUM(price * quantity) as total_asset, COUNT(*) as total_item FROM inventory_items")->fetch();

        // Ringkasan peminjaman aktif
        $activeLoan = $db->query("SELECT COUNT(*) FROM inventory_loans WHERE status = 'DIPINJAM'")->fetchColumn();
        $overdue    = $db->query("SELECT COUNT(*) FROM inventory_loans WHERE status = 'DIPINJAM' AND due_date < CURDATE()")->fetchColumn();

        // Auto-update status terlambat
        $db->query("UPDATE inventory_loans SET status = 'TERLAMBAT' WHERE status = 'DIPINJAM' AND due_date < CURDATE()");

        View::render('inventory/index', [
            'title'       => 'Inventaris Aset',
            'items'       => $items,
            'categories'  => $categories,
            'summary'     => $summary,
            'activeLoan'  => $activeLoan,
            'overdue'     => $overdue,
            'search'      => $search,
            'catId'       => $catId,
            'cond'        => $cond,
            'currentPage' => $page,
            'totalPages'  => max(1, ceil($totalData / $limit)),
            'limit'       => $limit,
        ]);
    }

    public function store() {
        $db   = Database::getInstance();
        $code = $_POST['code'];
        if ($db->query("SELECT id FROM inventory_items WHERE code = ?", [$code])->fetch()) {
            Session::setFlash('error', 'Kode barang sudah ada.');
            header('Location: /finance/inventory'); exit;
        }
        $db->query("INSERT INTO inventory_items (category_id, code, name, brand, acquisition_date, source_fund, price, quantity, condition_status, location, description, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)", [
            $_POST['category_id'], $code, $_POST['name'], $_POST['brand'],
            $_POST['acquisition_date'], $_POST['source_fund'], $_POST['price'],
            $_POST['quantity'], $_POST['condition_status'], $_POST['location'],
            $_POST['description'], Session::get('user_id')
        ]);
        Session::setFlash('success', 'Barang berhasil ditambahkan.');
        header('Location: /finance/inventory');
    }

    public function update() {
        $db   = Database::getInstance();
        $id   = (int)$_POST['id'];
        $old  = $db->query("SELECT condition_status FROM inventory_items WHERE id = ?", [$id])->fetch();
        $newCond = $_POST['condition_status'];

        $db->query("UPDATE inventory_items SET category_id=?, name=?, brand=?, acquisition_date=?, source_fund=?, price=?, quantity=?, condition_status=?, location=?, description=?, notif_sent=0 WHERE id=?", [
            $_POST['category_id'], $_POST['name'], $_POST['brand'],
            $_POST['acquisition_date'], $_POST['source_fund'], $_POST['price'],
            $_POST['quantity'], $newCond, $_POST['location'], $_POST['description'], $id
        ]);

        // Catat mutasi jika kondisi berubah
        if ($old && $old['condition_status'] !== $newCond) {
            $db->query("INSERT INTO inventory_mutations (item_id, old_condition, new_condition, notes, changed_by) VALUES (?,?,?,?,?)", [
                $id, $old['condition_status'], $newCond, $_POST['mutation_notes'] ?? null, Session::get('user_id')
            ]);
            // Kirim notif WA jika kondisi rusak berat atau hilang
            if (in_array($newCond, ['RUSAK_BERAT', 'HILANG'])) {
                $this->notifyCondition($db, $id, $_POST['name'], $newCond);
            }
        }

        Session::setFlash('success', 'Data barang diperbarui.');
        header('Location: /finance/inventory');
    }

    public function delete() {
        $db = Database::getInstance();
        $db->query("DELETE FROM inventory_items WHERE id = ?", [(int)$_GET['id']]);
        Session::setFlash('success', 'Barang dihapus.');
        header('Location: /finance/inventory');
    }

    // ── MUTASI ──────────────────────────────────────────────────────────────

    public function mutations() {
        $db     = Database::getInstance();
        $itemId = (int)($_GET['item_id'] ?? 0);
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 15;
        $offset = ($page - 1) * $limit;

        $where  = $itemId ? "WHERE m.item_id = $itemId" : "";
        $total  = $db->query("SELECT COUNT(*) FROM inventory_mutations m $where")->fetchColumn();
        $rows   = $db->query("SELECT m.*, i.name as item_name, i.code as item_code, u.name as changed_by_name
                FROM inventory_mutations m
                JOIN inventory_items i ON m.item_id = i.id
                JOIN users u ON m.changed_by = u.id
                $where ORDER BY m.changed_at DESC LIMIT $limit OFFSET $offset")->fetchAll();

        $item = $itemId ? $db->query("SELECT name, code FROM inventory_items WHERE id = ?", [$itemId])->fetch() : null;

        View::render('inventory/mutations', [
            'title'       => 'Riwayat Mutasi Kondisi',
            'rows'        => $rows,
            'item'        => $item,
            'itemId'      => $itemId,
            'currentPage' => $page,
            'totalPages'  => max(1, ceil($total / $limit)),
        ]);
    }

    // ── PEMINJAMAN ───────────────────────────────────────────────────────────

    public function loans() {
        $db     = Database::getInstance();
        $status = $_GET['status'] ?? '';
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 15;
        $offset = ($page - 1) * $limit;

        // Auto-update terlambat
        $db->query("UPDATE inventory_loans SET status = 'TERLAMBAT' WHERE status = 'DIPINJAM' AND due_date < CURDATE()");

        $where  = $status ? "WHERE l.status = ?" : "";
        $params = $status ? [$status] : [];
        $total  = $db->query("SELECT COUNT(*) FROM inventory_loans l $where", $params)->fetchColumn();
        $loans  = $db->query("SELECT l.*, i.name as item_name, i.code as item_code, u.name as created_by_name
                FROM inventory_loans l
                JOIN inventory_items i ON l.item_id = i.id
                JOIN users u ON l.created_by = u.id
                $where ORDER BY l.created_at DESC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        $items = $db->query("SELECT id, name, code, quantity FROM inventory_items WHERE condition_status = 'BAIK' ORDER BY name")->fetchAll();

        View::render('inventory/loans', [
            'title'       => 'Peminjaman Barang',
            'loans'       => $loans,
            'items'       => $items,
            'status'      => $status,
            'currentPage' => $page,
            'totalPages'  => max(1, ceil($total / $limit)),
        ]);
    }

    public function storeLoan() {
        $db = Database::getInstance();
        $db->query("INSERT INTO inventory_loans (item_id, borrower_name, borrower_role, quantity, loan_date, due_date, notes, created_by) VALUES (?,?,?,?,?,?,?,?)", [
            $_POST['item_id'], $_POST['borrower_name'], $_POST['borrower_role'],
            $_POST['quantity'], $_POST['loan_date'], $_POST['due_date'],
            $_POST['notes'] ?? null, Session::get('user_id')
        ]);
        Session::setFlash('success', 'Peminjaman berhasil dicatat.');
        header('Location: /finance/inventory/loans');
    }

    public function returnLoan() {
        $db = Database::getInstance();
        $db->query("UPDATE inventory_loans SET status = 'DIKEMBALIKAN', return_date = CURDATE() WHERE id = ?", [(int)$_POST['id']]);
        Session::setFlash('success', 'Barang berhasil dikembalikan.');
        header('Location: /finance/inventory/loans');
    }

    // ── EXPORT PDF ───────────────────────────────────────────────────────────

    public function export() {
        $db     = Database::getInstance();
        $catId  = $_GET['category_id'] ?? '';
        $cond   = $_GET['condition'] ?? '';
        $where  = "1=1";
        $params = [];
        if ($catId) { $where .= " AND i.category_id = ?"; $params[] = $catId; }
        if ($cond)  { $where .= " AND i.condition_status = ?"; $params[] = $cond; }

        $items      = $db->query("SELECT i.*, c.name as category_name FROM inventory_items i LEFT JOIN inventory_categories c ON i.category_id = c.id WHERE $where ORDER BY c.name, i.name", $params)->fetchAll();
        $categories = $db->query("SELECT * FROM inventory_categories ORDER BY name")->fetchAll();
        $summary    = $db->query("SELECT SUM(price * quantity) as total_asset, COUNT(*) as total_item FROM inventory_items i WHERE $where", $params)->fetch();

        View::render('inventory/export_print', [
            'title'      => 'Laporan Inventaris Aset',
            'items'      => $items,
            'categories' => $categories,
            'summary'    => $summary,
            'catId'      => $catId,
            'cond'       => $cond,
            'printDate'  => date('d F Y'),
        ]);
    }

    // ── NOTIFIKASI WA KONDISI RUSAK/HILANG ──────────────────────────────────

    public function notifyDamaged() {
        $db   = Database::getInstance();
        $rows = $db->query("SELECT i.name, i.code, i.condition_status, i.location,
                u.name as admin_name, u.phone as admin_phone
                FROM inventory_items i
                JOIN users u ON i.created_by = u.id
                WHERE i.condition_status IN ('RUSAK_BERAT','HILANG') AND i.notif_sent = 0")->fetchAll();

        // Ambil nomor admin (role_id = 1)
        $admins = $db->query("SELECT phone FROM users WHERE role_id = 1 AND phone IS NOT NULL AND phone != ''")->fetchAll();
        $phones = array_column($admins, 'phone');

        if (empty($phones) || empty($rows)) {
            Session::setFlash('error', 'Tidak ada data untuk dinotifikasi atau nomor admin tidak tersedia.');
            header('Location: /finance/inventory'); exit;
        }

        $lines = [];
        foreach ($rows as $r) {
            $cond   = str_replace('_', ' ', $r['condition_status']);
            $lines[] = "• [{$r['code']}] {$r['name']} — *{$cond}* (Lokasi: {$r['location']})";
        }
        $msg = "⚠️ *Laporan Kondisi Aset*\n\nBerikut aset dengan kondisi perlu perhatian:\n\n" . implode("\n", $lines) . "\n\n_Harap segera ditindaklanjuti._";

        WhatsappService::blast($phones, $msg);

        // Tandai sudah terkirim
        $db->query("UPDATE inventory_items SET notif_sent = 1 WHERE condition_status IN ('RUSAK_BERAT','HILANG') AND notif_sent = 0");

        Session::setFlash('success', 'Notifikasi kondisi aset berhasil dikirim ke admin.');
        header('Location: /finance/inventory');
    }

    // ── PRIVATE ──────────────────────────────────────────────────────────────

    private function notifyCondition(Database $db, int $itemId, string $itemName, string $condition): void {
        $admins = $db->query("SELECT phone FROM users WHERE role_id = 1 AND phone IS NOT NULL AND phone != ''")->fetchAll();
        $phones = array_column($admins, 'phone');
        if (empty($phones)) return;
        $cond = str_replace('_', ' ', $condition);
        $msg  = "⚠️ *Notifikasi Aset*\n\nAset *{$itemName}* baru saja diperbarui kondisinya menjadi *{$cond}*.\n\nHarap segera ditindaklanjuti.";
        WhatsappService::blast($phones, $msg);
        $db->query("UPDATE inventory_items SET notif_sent = 1 WHERE id = ?", [$itemId]);
    }
}
