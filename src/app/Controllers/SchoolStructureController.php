<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class SchoolStructureController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        
        // 1. Ambil Semua Data (Flat List) untuk Dropdown Modal
        $flatStructures = $db->query("
            SELECT ss.*, sm.full_name 
            FROM school_structures ss
            LEFT JOIN staff_members sm ON ss.staff_id = sm.id
            ORDER BY ss.level ASC, ss.order_num ASC
        ")->fetchAll();

        // 2. Build Tree Data (Recursive) untuk Diagram
        $tree = $this->buildTree($flatStructures);

        // 3. Ambil Data Staff untuk Pilihan Pejabat
        $staffs = $db->query("SELECT id, full_name, nip FROM staff_members WHERE status='ACTIVE' ORDER BY full_name")->fetchAll();

        View::render('staff/structure/index', [
            'title' => 'Struktur Organisasi Sekolah',
            'tree'  => $tree,             // Data Hierarki untuk Diagram
            'flat'  => $flatStructures,   // Data Flat untuk Dropdown Parent
            'staffs' => $staffs
        ]);
    }

    // Helper: Mengubah Flat Array menjadi Nested Tree
    private function buildTree(array $elements, $parentId = null) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function store() {
        $db = Database::getInstance();
        $parentId = empty($_POST['parent_id']) ? null : $_POST['parent_id'];
        
        // Tentukan Level Otomatis
        $level = 1;
        if ($parentId) {
            $parent = $db->query("SELECT level FROM school_structures WHERE id = ?", [$parentId])->fetch();
            if ($parent) $level = $parent['level'] + 1;
        }

        $db->query("INSERT INTO school_structures (parent_id, staff_id, title, level, order_num) VALUES (?, ?, ?, ?, ?)", 
            [$parentId, $_POST['staff_id'], $_POST['title'], $level, $_POST['order_num'] ?? 1]);
        
        Session::setFlash('success', 'Node struktur ditambahkan.');
        header('Location: /staff/structure');
    }

    public function delete() {
        $db = Database::getInstance();
        $id = $_GET['id'];
        
        // Cek anak sebelum hapus (Opsional: bisa di-cascade di DB, tapi ini defensive coding)
        $child = $db->query("SELECT id FROM school_structures WHERE parent_id = ?", [$id])->fetch();
        if ($child) {
            Session::setFlash('error', 'Gagal: Node ini memiliki bawahan. Hapus bawahannya dulu.');
        } else {
            $db->query("DELETE FROM school_structures WHERE id = ?", [$id]);
            Session::setFlash('success', 'Node dihapus.');
        }
        header('Location: /staff/structure');
    }
}

