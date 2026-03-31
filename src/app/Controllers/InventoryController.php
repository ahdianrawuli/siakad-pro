<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class InventoryController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $catId = $_GET['category_id'] ?? '';
        $cond = $_GET['condition'] ?? '';

        // Pagination
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Filter Query
        $where = "1=1";
        $params = [];

        if ($search) {
            $where .= " AND (i.name LIKE ? OR i.code LIKE ? OR i.brand LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
        }
        if ($catId) {
            $where .= " AND i.category_id = ?";
            $params[] = $catId;
        }
        if ($cond) {
            $where .= " AND i.condition_status = ?";
            $params[] = $cond;
        }

        // Hitung Total Data
        $totalData = $db->query("SELECT COUNT(*) FROM inventory_items i WHERE $where", $params)->fetchColumn();

        // Ambil Data Barang
        $sql = "SELECT i.*, c.name as category_name, u.name as pic_name
                FROM inventory_items i
                LEFT JOIN inventory_categories c ON i.category_id = c.id
                LEFT JOIN users u ON i.created_by = u.id
                WHERE $where
                ORDER BY i.created_at DESC
                LIMIT $limit OFFSET $offset";
        
        $items = $db->query($sql, $params)->fetchAll();

        // Data Master untuk Dropdown
        $categories = $db->query("SELECT * FROM inventory_categories ORDER BY name")->fetchAll();

        // Ringkasan Aset (Total Nilai)
        $summary = $db->query("SELECT SUM(price * quantity) as total_asset, COUNT(*) as total_item FROM inventory_items")->fetch();

        View::render('inventory/index', [
            'title' => 'Sarana & Prasarana (Inventaris)',
            'items' => $items,
            'categories' => $categories,
            'summary' => $summary,
            'search' => $search,
            'catId' => $catId,
            'cond' => $cond,
            'currentPage' => $page,
            'totalPages' => ceil($totalData / $limit)
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $code = $_POST['code'];
        
        // Cek Kode Unik
        $check = $db->query("SELECT id FROM inventory_items WHERE code = ?", [$code])->fetch();
        if ($check) {
            Session::setFlash('error', 'Kode barang sudah ada. Gunakan kode lain.');
            header('Location: /finance/inventory');
            exit;
        }

        $db->query("INSERT INTO inventory_items (
            category_id, code, name, brand, acquisition_date, source_fund, price, quantity, condition_status, location, description, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            $_POST['category_id'], $code, $_POST['name'], $_POST['brand'], 
            $_POST['acquisition_date'], $_POST['source_fund'], $_POST['price'], 
            $_POST['quantity'], $_POST['condition_status'], $_POST['location'], 
            $_POST['description'], Session::get('user_id')
        ]);

        Session::setFlash('success', 'Barang berhasil ditambahkan.');
        header('Location: /finance/inventory');
    }

    public function update() {
        $db = Database::getInstance();
        $id = $_POST['id'];

        $db->query("UPDATE inventory_items SET 
            category_id=?, name=?, brand=?, acquisition_date=?, source_fund=?, price=?, quantity=?, condition_status=?, location=?, description=?
            WHERE id=?", [
            $_POST['category_id'], $_POST['name'], $_POST['brand'], 
            $_POST['acquisition_date'], $_POST['source_fund'], $_POST['price'], 
            $_POST['quantity'], $_POST['condition_status'], $_POST['location'], 
            $_POST['description'], $id
        ]);

        Session::setFlash('success', 'Data barang diperbarui.');
        header('Location: /finance/inventory');
    }

    public function delete() {
        $db = Database::getInstance();
        $db->query("DELETE FROM inventory_items WHERE id = ?", [$_GET['id']]);
        Session::setFlash('success', 'Barang dihapus.');
        header('Location: /finance/inventory');
    }
}
