<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class BoardingMapController {
    public function __construct() { Middleware::auth(); }

    // Denah kamar visual
    public function map() {
        $db = Database::getInstance();
        $scope = ScopeFilter::get();

        $dormWhere = $scope !== 'GLOBAL' ? "WHERE unit = '$scope'" : "";
        $dorms = $db->query("SELECT * FROM dorms $dormWhere ORDER BY unit, gender, name")->fetchAll();

        // Ambil semua santri per kamar
        $dormMap = [];
        foreach ($dorms as $d) {
            $students = $db->query(
                "SELECT s.id, s.full_name, s.nis, c.name as class_name
                 FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id
                 WHERE s.dorm_id = ? AND s.status = 'ACTIVE' ORDER BY s.full_name",
                [$d['id']]
            )->fetchAll();
            $dormMap[$d['id']] = $students;
        }

        View::render('boarding/map', [
            'title'   => 'Denah Kamar Asrama',
            'dorms'   => $dorms,
            'dormMap' => $dormMap,
            'scope'   => $scope,
        ]);
    }

    public function printMap() {
        $db = Database::getInstance();
        $scope = ScopeFilter::get();
        $dormWhere = $scope !== 'GLOBAL' ? "WHERE unit = '$scope'" : "";
        $dorms = $db->query("SELECT * FROM dorms $dormWhere ORDER BY unit, gender, name")->fetchAll();
        $dormMap = [];
        foreach ($dorms as $d) {
            $dormMap[$d['id']] = $db->query(
                "SELECT s.full_name, s.nis, c.name as class_name FROM students s
                 LEFT JOIN classrooms c ON s.classroom_id = c.id
                 WHERE s.dorm_id = ? AND s.status = 'ACTIVE' ORDER BY s.full_name",
                [$d['id']]
            )->fetchAll();
        }
        View::render('boarding/print_map', ['dorms' => $dorms, 'dormMap' => $dormMap]);
    }
    public function printViolations() {
        $db = Database::getInstance();
        $scope  = ScopeFilter::get();
        $dormId = $_GET['dorm_id'] ?? '';
        $from   = $_GET['from'] ?? date('Y-m-01');
        $to     = $_GET['to']   ?? date('Y-m-d');

        $where  = "WHERE s.dorm_id IS NOT NULL AND s.status = 'ACTIVE'";
        $params = [];
        if ($scope !== 'GLOBAL') { $where .= " AND s.classroom_id IN (SELECT id FROM classrooms WHERE major = ?)"; $params[] = $scope; }
        if ($dormId) { $where .= " AND s.dorm_id = ?"; $params[] = $dormId; }

        $students = $db->query(
            "SELECT s.full_name, s.nis, d.name as dorm_name,
                    COUNT(sv.id) as total_violations,
                    COALESCE(SUM(vt.points),0) as total_points
             FROM students s
             LEFT JOIN dorms d ON s.dorm_id = d.id
             LEFT JOIN student_violations sv ON sv.student_id = s.id AND sv.date BETWEEN ? AND ?
             LEFT JOIN violation_types vt ON sv.violation_type_id = vt.id
             $where GROUP BY s.id, s.full_name, s.nis, d.name ORDER BY total_points DESC, s.full_name",
            array_merge([$from, $to], $params)
        )->fetchAll();

        $dorm = $dormId ? $db->query("SELECT name FROM dorms WHERE id=?", [$dormId])->fetch() : null;
        View::render('boarding/print_violations', ['students'=>$students,'dorm'=>$dorm,'from'=>$from,'to'=>$to]);
    }

    public function violations() {
        $db = Database::getInstance();
        $scope  = ScopeFilter::get();
        $dormId = $_GET['dorm_id'] ?? '';
        $from   = $_GET['from'] ?? date('Y-m-01');
        $to     = $_GET['to']   ?? date('Y-m-d');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = (int)($_GET['limit'] ?? 20);
        $offset = ($page - 1) * $limit;

        $where  = "WHERE s.dorm_id IS NOT NULL AND s.status = 'ACTIVE'";
        $params = [];
        if ($scope !== 'GLOBAL') { $where .= " AND s.classroom_id IN (SELECT id FROM classrooms WHERE major = ?)"; $params[] = $scope; }
        if ($dormId) { $where .= " AND s.dorm_id = ?"; $params[] = $dormId; }

        $baseQuery = "FROM students s
             LEFT JOIN dorms d ON s.dorm_id = d.id
             LEFT JOIN student_violations sv ON sv.student_id = s.id AND sv.date BETWEEN ? AND ?
             LEFT JOIN violation_types vt ON sv.violation_type_id = vt.id
             $where
             GROUP BY s.id, s.full_name, s.nis, d.name";
        $baseParams = array_merge([$from, $to], $params);

        $totalData  = $db->query("SELECT COUNT(*) FROM (SELECT s.id $baseQuery) as t", $baseParams)->fetchColumn();
        $totalPages = max(1, ceil($totalData / $limit));

        $students = $db->query(
            "SELECT s.id, s.full_name, s.nis, d.name as dorm_name,
                    COUNT(sv.id) as total_violations,
                    COALESCE(SUM(vt.points),0) as total_points
             $baseQuery ORDER BY total_points DESC, s.full_name LIMIT $limit OFFSET $offset",
            $baseParams
        )->fetchAll();

        $dormWhere = $scope !== 'GLOBAL' ? "WHERE unit = '$scope'" : "";
        $dorms = $db->query("SELECT * FROM dorms $dormWhere ORDER BY name")->fetchAll();

        View::render('boarding/violations', [
            'title'       => 'Rekap Pelanggaran Asrama',
            'students'    => $students,
            'dorms'       => $dorms,
            'dormId'      => $dormId,
            'from'        => $from,
            'to'          => $to,
            'scope'       => $scope,
            'totalData'   => $totalData,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'limit'       => $limit,
        ]);
    }
}
