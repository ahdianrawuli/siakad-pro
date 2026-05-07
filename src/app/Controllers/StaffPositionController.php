<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class StaffPositionController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;

        $totalData = $db->query("SELECT COUNT(*) FROM staff_positions WHERE name LIKE ?", ["%$search%"])->fetchColumn();
        $positions = $db->query("
            SELECT sp.*, r.name as role_name FROM staff_positions sp
            LEFT JOIN roles r ON sp.role_id = r.id
            WHERE sp.name LIKE ? ORDER BY sp.name ASC LIMIT $limit OFFSET $offset
        ", ["%$search%"])->fetchAll();

        $roles = $db->query("SELECT id, name FROM roles ORDER BY name")->fetchAll();

        View::render('staff/positions/index', [
            'title'       => 'Master Jabatan Staff',
            'positions'   => $positions,
            'roles'       => $roles,
            'totalData'   => $totalData,
            'totalPages'  => ceil($totalData / $limit),
            'currentPage' => $page,
            'limit'       => $limit,
            'search'      => $search
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $db->query("INSERT INTO staff_positions (name, code, type, role_id) VALUES (?, ?, ?, ?)", 
            [$_POST['name'], strtoupper($_POST['code']), $_POST['type'], $_POST['role_id'] ?: null]);
        Session::setFlash('success', 'Jabatan berhasil ditambahkan.');
        header('Location: /staff/positions');
    }

    public function update() {
        $db = Database::getInstance();
        $db->query("UPDATE staff_positions SET name=?, code=?, type=?, role_id=? WHERE id=?", 
            [$_POST['name'], strtoupper($_POST['code']), $_POST['type'], $_POST['role_id'] ?: null, $_POST['id']]);
        Session::setFlash('success', 'Jabatan diperbarui.');
        header('Location: /staff/positions');
    }

    public function delete() {
        $db = Database::getInstance();
        try {
            $db->query("DELETE FROM staff_positions WHERE id = ?", [$_GET['id']]);
            Session::setFlash('success', 'Jabatan dihapus.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal hapus: Data sedang digunakan.');
        }
        header('Location: /staff/positions');
    }
}

