<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class KitabController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        
        // Hanya tampilkan jurnal user tersebut
        $journals = $db->query("SELECT * FROM kitab_journals WHERE teacher_id = ? ORDER BY date DESC", [$userId])->fetchAll();
        
        View::render('academic/kitab', ['title' => 'Jurnal Kitab', 'journals' => $journals]);
    }

    public function store() {
        $db = Database::getInstance();
        $db->query("INSERT INTO kitab_journals (teacher_id, class_name, kitab_name, date, start_page, end_page, chapter, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
            Session::get('user_id'), $_POST['class_name'], $_POST['kitab_name'], $_POST['date'],
            $_POST['start_page'], $_POST['end_page'], $_POST['chapter'], $_POST['notes']
        ]);
        
        Session::setFlash('success', 'Jurnal Kitab tersimpan.');
        header('Location: /academic/kitab');
    }
}
