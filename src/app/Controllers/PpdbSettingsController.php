<?php
namespace App\Controllers;

use App\Models\PpdbConfig;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class PpdbSettingsController {
    public function __construct() {
        Middleware::auth();
    }

    public function index() {
        $db = Database::getInstance();
        $tab = $_GET['tab'] ?? 'periode';

        // --- Logic PERIODE ---
        $limitP = isset($_GET['limit_p']) ? (int)$_GET['limit_p'] : 10;
        $pageP = isset($_GET['page_p']) ? (int)$_GET['page_p'] : 1;
        $offsetP = ($pageP - 1) * $limitP;
        $searchP = $_GET['search_p'] ?? '';
        
        // Hitung total periode
        $totalP = $db->query("SELECT COUNT(*) FROM ppdb_batches WHERE name LIKE ?", ["%$searchP%"])->fetchColumn();
        // Ambil data periode
        $periods = $db->query("SELECT * FROM ppdb_batches WHERE name LIKE ? ORDER BY id DESC LIMIT $limitP OFFSET $offsetP", ["%$searchP%"])->fetchAll();

        // --- Logic JALUR ---
        $limitT = isset($_GET['limit_t']) ? (int)$_GET['limit_t'] : 10;
        $pageT = isset($_GET['page_t']) ? (int)$_GET['page_t'] : 1;
        $offsetT = ($pageT - 1) * $limitT;
        $searchT = $_GET['search_t'] ?? '';

        // Hitung total jalur
        $totalT = $db->query("SELECT COUNT(*) FROM ppdb_tracks WHERE name LIKE ? OR code LIKE ?", ["%$searchT%", "%$searchT%"])->fetchColumn();
        // Ambil data jalur
        $tracks = $db->query("SELECT * FROM ppdb_tracks WHERE name LIKE ? OR code LIKE ? ORDER BY level ASC LIMIT $limitT OFFSET $offsetT", ["%$searchT%", "%$searchT%"])->fetchAll();

        View::render('ppdb/settings', [
            'title' => 'Konfigurasi PPDB',
            'tab' => $tab,
            
            // Data Periode
            'periods' => $periods, 
            'totalP' => $totalP, 
            'currentPageP' => $pageP, 
            'limitP' => $limitP, 
            'searchP' => $searchP, 
            'totalPagesP' => ceil($totalP / $limitP),
            
            // Data Jalur
            'tracks' => $tracks, 
            'totalT' => $totalT, 
            'currentPageT' => $pageT, 
            'limitT' => $limitT, 
            'searchT' => $searchT, 
            'totalPagesT' => ceil($totalT / $limitT)
        ]);
    }

    public function storePeriod() {
        $db = Database::getInstance();
        $db->query("INSERT INTO ppdb_batches (name, start_date, end_date, is_active) VALUES (?, ?, ?, ?)", [
            $_POST['name'], $_POST['start_date'], $_POST['end_date'], isset($_POST['is_active']) ? 1 : 0
        ]);
        Session::setFlash('success', 'Periode berhasil ditambahkan.');
        header('Location: /ppdb/settings?tab=periode');
    }

    public function updatePeriod() {
        $db = Database::getInstance();
        $db->query("UPDATE ppdb_batches SET name = ?, start_date = ?, end_date = ? WHERE id = ?", [
            $_POST['name'], $_POST['start_date'], $_POST['end_date'], $_POST['id']
        ]);
        Session::setFlash('success', 'Periode diperbarui.');
        header('Location: /ppdb/settings?tab=periode');
    }

    public function activatePeriod() {
        $db = Database::getInstance();
        $db->getConnection()->beginTransaction();
        $db->query("UPDATE ppdb_batches SET is_active = 0");
        $db->query("UPDATE ppdb_batches SET is_active = 1 WHERE id = ?", [$_GET['id']]);
        $db->getConnection()->commit();
        header('Location: /ppdb/settings?tab=periode');
    }

    public function storeTrack() {
        $db = Database::getInstance();
        $db->query("INSERT INTO ppdb_tracks (name, level, code, quota, is_active) VALUES (?, ?, ?, ?, 1)", [
            $_POST['name'], $_POST['level'], strtoupper($_POST['code']), $_POST['quota']
        ]);
        Session::setFlash('success', 'Jalur berhasil ditambahkan.');
        header('Location: /ppdb/settings?tab=jalur');
    }

    public function updateTrack() {
        $db = Database::getInstance();
        $db->query("UPDATE ppdb_tracks SET name = ?, level = ?, code = ?, quota = ? WHERE id = ?", [
            $_POST['name'], $_POST['level'], strtoupper($_POST['code']), $_POST['quota'], $_POST['id']
        ]);
        Session::setFlash('success', 'Jalur diperbarui.');
        header('Location: /ppdb/settings?tab=jalur');
    }
}

