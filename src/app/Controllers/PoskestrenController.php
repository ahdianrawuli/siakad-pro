<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class PoskestrenController {
    public function __construct() {
        Middleware::auth();
    }

    public function patients() {
        $db = \App\Core\Database::getInstance();
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $classFilter = $_GET['class_id'] ?? '';
        $dateFrom = !empty($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
        $dateTo   = !empty($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
        $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $where  = "WHERE hr.source = 'POSKESTREN'";
        $params = [];

        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        if (!empty($search)) { $where .= " AND (s.full_name LIKE ? OR hr.complaint LIKE ? OR hr.diagnosis LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($status)) { $where .= " AND hr.status = ?"; $params[] = $status; }
        if (!empty($classFilter)) { $where .= " AND s.classroom_id = ?"; $params[] = $classFilter; }
        $where .= " AND hr.date BETWEEN ? AND ?"; $params[] = $dateFrom; $params[] = $dateTo;

        $totalData = $db->query("SELECT COUNT(*) FROM health_records hr LEFT JOIN students s ON hr.student_id = s.id LEFT JOIN classrooms c ON s.classroom_id = c.id $where", $params)->fetchColumn();

        $records = $db->query("SELECT hr.*, s.full_name, s.nis, c.name as class_name, sm.full_name as officer_name
             FROM health_records hr LEFT JOIN students s ON hr.student_id = s.id
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             LEFT JOIN staff_members sm ON hr.officer_id = sm.id
             $where ORDER BY hr.date DESC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        [$sw2, $sp2] = ScopeFilter::apply('c');
        $students = $db->query("SELECT s.id, s.full_name, s.nis FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id WHERE s.status = 'ACTIVE' $sw2 ORDER BY s.full_name", $sp2)->fetchAll();
        $officers = $db->query("SELECT id, full_name FROM staff_members WHERE status = 'ACTIVE' ORDER BY full_name")->fetchAll();
        $classrooms = $db->query("SELECT c.* FROM classrooms c WHERE 1=1 $sw2 ORDER BY c.name", $sp2)->fetchAll();

        View::render('poskestren/patients', [
            'title'       => 'Data Pasien Poskestren',
            'records'     => $records,
            'students'    => $students,
            'officers'    => $officers,
            'classrooms'  => $classrooms,
            'search'      => $search,
            'status'      => $status,
            'classFilter' => $classFilter,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'limit'       => $limit,
            'currentPage' => $page,
            'totalData'   => $totalData,
            'totalPages'  => ceil($totalData / $limit),
        ]);
    }

    public function printPatients() {
        $db = \App\Core\Database::getInstance();
        $classFilter = $_GET['class_id'] ?? '';
        $status = $_GET['status'] ?? '';
        $dateFrom = !empty($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
        $dateTo   = !empty($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

        $where = "WHERE hr.source = 'POSKESTREN' AND hr.date BETWEEN ? AND ?";
        $params = [$dateFrom, $dateTo];
        if (!empty($classFilter)) { $where .= " AND s.classroom_id = ?"; $params[] = $classFilter; }
        if (!empty($status)) { $where .= " AND hr.status = ?"; $params[] = $status; }
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        $records = $db->query("SELECT hr.*, s.full_name, s.nis, c.name as class_name, sm.full_name as officer_name
            FROM health_records hr LEFT JOIN students s ON hr.student_id=s.id
            LEFT JOIN classrooms c ON s.classroom_id=c.id
            LEFT JOIN staff_members sm ON hr.officer_id=sm.id
            $where ORDER BY hr.date DESC", $params)->fetchAll();

        $classroom = $classFilter ? $db->query("SELECT name FROM classrooms WHERE id=?", [$classFilter])->fetch() : null;
        View::render('poskestren/print_patients', ['records'=>$records,'classroom'=>$classroom,'dateFrom'=>$dateFrom,'dateTo'=>$dateTo]);
    }

    public function storePatient() {
        $db = \App\Core\Database::getInstance();
        try {
            $db->query(
                "INSERT INTO health_records (student_id, date, complaint, diagnosis, treatment, status, officer_id, source) VALUES (?,?,?,?,?,?,?,'POSKESTREN')",
                [
                    $_POST['student_id'], $_POST['date'], $_POST['complaint'],
                    $_POST['diagnosis'] ?? null, $_POST['treatment'] ?? null,
                    $_POST['status'] ?? 'RAWAT_JALAN', $_POST['officer_id']
                ]
            );
            \App\Core\Session::setFlash('success', 'Data pasien berhasil disimpan.');
        } catch (\Exception $e) {
            \App\Core\Session::setFlash('error', 'Gagal: ' . $e->getMessage());
        }
        header('Location: /poskestren/patients');
    }

    public function deletePatient() {
        $db = \App\Core\Database::getInstance();
        try {
            $db->query("DELETE FROM health_records WHERE id = ?", [$_POST['id']]);
            \App\Core\Session::setFlash('success', 'Data dihapus.');
        } catch (\Exception $e) {
            \App\Core\Session::setFlash('error', 'Gagal menghapus.');
        }
        header('Location: /poskestren/patients');
    }

    public function staff() {
        $db = \App\Core\Database::getInstance();
        $search = $_GET['search'] ?? '';
        $sql = "SELECT sm.*, sp.name as position_name FROM staff_members sm LEFT JOIN staff_positions sp ON sm.position_id = sp.id WHERE 1=1";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (sm.full_name LIKE ? OR sm.nip LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%";
        }
        $staff = $db->query($sql . " ORDER BY sm.full_name ASC", $params)->fetchAll();

        View::render('poskestren/staff', [
            'title' => 'Data Petugas Poskestren',
            'staff' => $staff,
            'search' => $search,
        ]);
    }
}
