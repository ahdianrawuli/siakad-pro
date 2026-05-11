<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class BoardingSupervisorController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $scope = ScopeFilter::get();

        $search = $_GET['search'] ?? '';
        $page   = (int)($_GET['page'] ?? 1);
        $limit  = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $where = "WHERE ds.status = 'ACTIVE'";
        $params = [];
        if ($scope !== 'GLOBAL') { $where .= " AND d.unit = ?"; $params[] = $scope; }
        if (!empty($search)) { $where .= " AND (u.name LIKE ? OR d.name LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

        $totalData  = $db->query("SELECT COUNT(*) FROM dorm_supervisors ds JOIN dorms d ON ds.dorm_id = d.id JOIN users u ON ds.user_id = u.id $where", $params)->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        $supervisors = $db->query("
            SELECT ds.*, d.name as dorm_name, d.unit as dorm_unit, u.name as user_name, r.slug as role_name
            FROM dorm_supervisors ds
            JOIN dorms d ON ds.dorm_id = d.id
            JOIN users u ON ds.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            $where ORDER BY d.name ASC LIMIT $limit OFFSET $offset
        ", $params)->fetchAll();

        // Dropdown dorms mengikuti scope
        $dormWhere = $scope !== 'GLOBAL' ? "WHERE unit = ?" : "";
        $dormParams = $scope !== 'GLOBAL' ? [$scope] : [];
        $dorms = $db->query("SELECT * FROM dorms $dormWhere ORDER BY name", $dormParams)->fetchAll();

        $users = $db->query("SELECT u.id, u.name, r.slug FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug IN ('guru', 'staff') AND u.status='active' ORDER BY u.name")->fetchAll();

        View::render('boarding/supervisors/index', [
            'title'       => 'Data Wali Asrama',
            'supervisors' => $supervisors,
            'dorms'       => $dorms,
            'users'       => $users,
            'search'      => $search,
            'scope'       => $scope,
            'totalData'   => $totalData,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'limit'       => $limit,
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $adminId = Session::get('user_id');

        $check = $db->query("SELECT id FROM dorm_supervisors WHERE dorm_id = ? AND status = 'ACTIVE'",
                            [$_POST['dorm_id']])->fetch();

        if ($check) {
            Session::setFlash('error', 'Asrama ini sudah memiliki wali aktif. Hapus wali lama terlebih dahulu.');
        } else {
            $db->query("INSERT INTO dorm_supervisors (dorm_id, user_id, assigned_date, status, created_by) VALUES (?, ?, ?, 'ACTIVE', ?)", [
                $_POST['dorm_id'], $_POST['user_id'], date('Y-m-d'), $adminId
            ]);
            Session::setFlash('success', 'Wali Asrama berhasil ditugaskan.');
        }

        header('Location: /asrama/supervisors');
    }

    public function update() {
        $db = Database::getInstance();
        $db->query("UPDATE dorm_supervisors SET user_id=? WHERE id=?", [$_POST['user_id'], $_POST['id']]);
        Session::setFlash('success', 'Wali Asrama diperbarui.');
        header('Location: /asrama/supervisors');
    }

    public function delete() {
        $db = Database::getInstance();
        $db->query("DELETE FROM dorm_supervisors WHERE id = ?", [$_GET['id']]);
        Session::setFlash('success', 'Penugasan dihapus.');
        header('Location: /asrama/supervisors');
    }
}

