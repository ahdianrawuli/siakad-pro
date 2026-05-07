<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class RoleController {
    public function __construct() {
        Middleware::auth();
    }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM roles WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR slug LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $totalData = $db->query("SELECT COUNT(*) FROM (" . $sql . ") as t", $params)->fetchColumn();
        $sql .= " ORDER BY id ASC LIMIT $limit OFFSET $offset";
        $roles = $db->query($sql, $params)->fetchAll();

        View::render('settings/roles/index', [
            'title' => 'Manajemen Roles',
            'roles' => $roles,
            'totalData' => $totalData,
            'totalPages' => ceil($totalData / $limit),
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $name = trim($_POST['name']);
        $slug = strtolower(str_replace(' ', '-', $name));
        $desc = trim($_POST['description'] ?? '');

        try {
            $db->query("INSERT INTO roles (name, slug, description) VALUES (?, ?, ?)", [$name, $slug, $desc]);
            Session::setFlash('success', 'Role berhasil ditambahkan.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal menambah role. Nama atau slug mungkin sudah ada.');
        }

        header('Location: /settings/roles');
    }

    public function toggle() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        $role = $db->query("SELECT status FROM roles WHERE id = ?", [$id])->fetch();
        if ($role) {
            $newStatus = $role['status'] === 'active' ? 'inactive' : 'active';
            $db->query("UPDATE roles SET status = ? WHERE id = ?", [$newStatus, $id]);
            Session::setFlash('success', 'Status role berhasil diperbarui.');
        }
        header('Location: /settings/roles');
    }

    public function update() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $slug = strtolower(str_replace(' ', '-', $name));
        $desc = trim($_POST['description'] ?? '');

        try {
            $db->query("UPDATE roles SET name = ?, slug = ?, description = ? WHERE id = ?", [$name, $slug, $desc, $id]);
            Session::setFlash('success', 'Role berhasil diperbarui.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal memperbarui role.');
        }

        header('Location: /settings/roles');
    }

    public function delete() {
        $db = Database::getInstance();
        $id = $_POST['id'];

        try {
            $db->query("DELETE FROM roles WHERE id = ?", [$id]);
            Session::setFlash('success', 'Role berhasil dihapus.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal menghapus role karena masih digunakan.');
        }

        header('Location: /settings/roles');
    }

    public function permissions() {
        $db = Database::getInstance();
        $roleId = $_GET['id'] ?? null;
        if (!$roleId) {
            header('Location: /settings/roles');
            exit;
        }

        $role = $db->query("SELECT * FROM roles WHERE id = ?", [$roleId])->fetch();
        if (!$role) {
            header('Location: /settings/roles');
            exit;
        }

        // Fetch all menus to display as checkboxes
        $menus = $db->query("SELECT * FROM menus ORDER BY parent_id ASC, order_num ASC")->fetchAll();

        // Fetch currently assigned permissions
        $assignedMenus = $db->query("SELECT menu_id FROM role_menus WHERE role_id = ?", [$roleId])->fetchAll(\PDO::FETCH_COLUMN);

        View::render('settings/roles/permissions', [
            'title' => 'Hak Akses Role: ' . $role['name'],
            'role' => $role,
            'menus' => $menus,
            'assignedMenus' => $assignedMenus
        ]);
    }

    public function updatePermissions() {
        $db = Database::getInstance();
        $roleId = $_POST['role_id'];
        $selectedMenus = $_POST['menus'] ?? [];

        try {
            $db->getConnection()->beginTransaction();

            // Delete old permissions
            $db->query("DELETE FROM role_menus WHERE role_id = ?", [$roleId]);

            // Insert new permissions
            if (!empty($selectedMenus)) {
                $insertQuery = "INSERT INTO role_menus (role_id, menu_id) VALUES ";
                $values = [];
                $placeholders = [];
                foreach ($selectedMenus as $menuId) {
                    $placeholders[] = "(?, ?)";
                    $values[] = $roleId;
                    $values[] = $menuId;
                }
                $insertQuery .= implode(", ", $placeholders);
                $db->query($insertQuery, $values);
            }

            $db->getConnection()->commit();
            Session::setFlash('success', 'Hak akses role berhasil diperbarui.');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal memperbarui hak akses.');
        }

        header('Location: /settings/roles');
    }
}
