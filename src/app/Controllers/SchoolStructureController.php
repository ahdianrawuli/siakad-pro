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

        // 3. Ambil Data Staff + Guru untuk Pilihan Pejabat
        $staffs = $db->query("
            SELECT sm.id, sm.full_name, sm.nip, 'staff' as sumber
            FROM staff_members sm WHERE sm.status='ACTIVE'
            UNION ALL
            SELECT t.id + 10000, t.full_name, t.nip, 'guru' as sumber
            FROM teachers t WHERE t.status='ACTIVE'
            ORDER BY sumber, full_name
        ")->fetchAll();

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
        $rawStaffId = (int)$_POST['staff_id'];

        // ID > 10000 berarti guru (offset 10000)
        $staffId = $rawStaffId > 10000 ? null : $rawStaffId;
        $teacherId = $rawStaffId > 10000 ? ($rawStaffId - 10000) : null;

        // Jika guru, simpan ke staff_members dulu jika belum ada, atau gunakan teacher_id langsung
        // Untuk simplisitas: simpan staff_id = null dan gunakan teacher full_name via join di view
        // Alternatif: simpan teacher_id di kolom staff_id dengan flag negatif
        // Solusi terbaik: simpan teacher id asli, join ke teachers di query
        if ($teacherId) {
            $teacher = $db->query("SELECT id, full_name FROM teachers WHERE id=?", [$teacherId])->fetch();
            // Insert ke staff_members sementara jika belum ada
            $existing = $db->query("SELECT id FROM staff_members WHERE user_id = (SELECT user_id FROM teachers WHERE id=?)", [$teacherId])->fetch();
            $staffId = $existing ? $existing['id'] : null;
        }

        $level = 1;
        if ($parentId) {
            $parent = $db->query("SELECT level FROM school_structures WHERE id = ?", [$parentId])->fetch();
            if ($parent) $level = $parent['level'] + 1;
        }

        $db->query("INSERT INTO school_structures (parent_id, staff_id, title, level, order_num) VALUES (?, ?, ?, ?, ?)",
            [$parentId, $staffId, $_POST['title'], $level, $_POST['order_num'] ?? 1]);

        Session::setFlash('success', 'Node struktur ditambahkan.');
        header('Location: /school/structure');
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
        header('Location: /school/structure');
    }
}

