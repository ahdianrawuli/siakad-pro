<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class CurriculumController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        
        $sql = "SELECT * FROM curriculums WHERE name LIKE ? ORDER BY is_active DESC, name ASC";
        $curriculums = $db->query($sql, ["%$search%"])->fetchAll();

        View::render('academic/curriculum/index', [
            'title' => 'Data Kurikulum',
            'curriculums' => $curriculums,
            'search' => $search
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        // Jika diset active, matikan yang lain (opsional, tergantung kebijakan sekolah)
        // Disini kita biarkan multiple active untuk fleksibilitas
        
        $db->query("INSERT INTO curriculums (name, code, description, is_active) VALUES (?, ?, ?, ?)", 
            [$_POST['name'], $_POST['code'], $_POST['description'], $isActive]);
            
        Session::setFlash('success', 'Kurikulum ditambahkan.');
        header('Location: /academic/curriculum');
    }

    public function update() {
        $db = Database::getInstance();
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        $db->query("UPDATE curriculums SET name=?, code=?, description=?, is_active=? WHERE id=?", 
            [$_POST['name'], $_POST['code'], $_POST['description'], $isActive, $_POST['id']]);
            
        Session::setFlash('success', 'Kurikulum diperbarui.');
        header('Location: /academic/curriculum');
    }

    public function delete() {
        $db = Database::getInstance();
        $db->query("DELETE FROM curriculums WHERE id=?", [$_GET['id']]);
        Session::setFlash('success', 'Kurikulum dihapus.');
        header('Location: /academic/curriculum');
    }
}

