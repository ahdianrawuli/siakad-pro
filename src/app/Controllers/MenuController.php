<?php
namespace App\Controllers;
use App\Models\Menu;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class MenuController {
    public function __construct() {
        Middleware::auth();
    }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT m.*, p.title as parent_name FROM menus m LEFT JOIN menus p ON m.parent_id = p.id WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (m.title LIKE ? OR m.url LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $totalData = $db->query("SELECT COUNT(*) FROM (" . $sql . ") as t", $params)->fetchColumn();
        $sql .= " ORDER BY m.parent_id ASC, m.order_num ASC LIMIT $limit OFFSET $offset";
        $menus = $db->query($sql, $params)->fetchAll();
        // Ambil role assignment per menu
        $menuRoles = [];
        $rmAll = $db->query("SELECT menu_id, role_id FROM role_menus")->fetchAll();
        foreach ($rmAll as $rm) { $menuRoles[$rm['menu_id']][] = $rm['role_id']; }

        $parents = $db->query("SELECT id, title FROM menus WHERE parent_id IS NULL ORDER BY order_num ASC")->fetchAll();

        View::render('settings/menus/index', [
            'title' => 'Manajemen Menu',
            'menus' => $menus,
            'menuRoles' => $menuRoles,
            'parents' => $parents,
            'roles' => $db->query("SELECT id, name FROM roles ORDER BY id")->fetchAll(),
            'totalData' => $totalData,
            'totalPages' => ceil($totalData / $limit),
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $data = [
            $_POST['parent_id'] ?: null,
            $_POST['title'],
            $_POST['url'] ?: '#',
            $_POST['icon'] ?: 'circle',
            $_POST['order_num'] ?: 0,
            isset($_POST['is_active']) ? 1 : 0
        ];

        try {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) VALUES (?, ?, ?, ?, ?, ?)", $data);
            $newId = $db->getConnection()->lastInsertId();
            // Assign ke role yang dipilih
            $roles = $_POST['roles'] ?? [1];
            foreach ($roles as $roleId) {
                $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$roleId, $newId]);
            }
            Session::setFlash('success', 'Menu berhasil ditambahkan.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal menambah menu.');
        }
        header('Location: /settings/menus');
    }

    public function update() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        $data = [
            $_POST['parent_id'] ?: null,
            $_POST['title'],
            $_POST['url'] ?: '#',
            $_POST['icon'] ?: 'circle',
            $_POST['order_num'] ?: 0,
            $id
        ];

        try {
            $db->query("UPDATE menus SET parent_id = ?, title = ?, url = ?, icon = ?, order_num = ? WHERE id = ?", $data);
            // Update role assignment
            $roles = $_POST['roles'] ?? [];
            $db->query("DELETE FROM role_menus WHERE menu_id = ?", [$id]);
            foreach ($roles as $roleId) {
                $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (?, ?)", [$roleId, $id]);
            }
            Session::setFlash('success', 'Menu berhasil diperbarui.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal memperbarui menu.');
        }
        header('Location: /settings/menus');
    }

    public function toggle() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        $menu = $db->query("SELECT is_active FROM menus WHERE id = ?", [$id])->fetch();
        if ($menu) {
            $newStatus = $menu['is_active'] ? 0 : 1;
            $db->query("UPDATE menus SET is_active = ? WHERE id = ?", [$newStatus, $id]);
            Session::setFlash('success', 'Status menu diperbarui.');
        }
        header('Location: /settings/menus');
    }

    public function delete() {
        $db = Database::getInstance();
        try {
            $db->query("DELETE FROM menus WHERE id = ?", [$_POST['id']]);
            Session::setFlash('success', 'Menu berhasil dihapus.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal menghapus menu.');
        }
        header('Location: /settings/menus');
    }
}
