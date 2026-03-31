<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class LetterController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $templates = $db->query("SELECT * FROM letter_templates")->fetchAll();
        View::render('letters/index', ['title' => 'Template Surat', 'templates' => $templates]);
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $db = Database::getInstance();
        $template = $db->query("SELECT * FROM letter_templates WHERE id = ?", [$id])->fetch();
        View::render('letters/form', ['template' => $template]);
    }

    public function update() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        $db->query("UPDATE letter_templates SET name = ?, content = ? WHERE id = ?", [
            $_POST['name'], $_POST['content'], $id
        ]);
        Session::setFlash('success', 'Template surat diperbarui.');
        header('Location: /settings/letters');
    }

    // --- GENERATE SURAT (CETAK) ---
    public function print() {
        $templateId = $_GET['template_id'];
        $studentId = $_GET['student_id']; // Target Siswa

        $db = Database::getInstance();
        
        // 1. Ambil Template
        $tpl = $db->query("SELECT * FROM letter_templates WHERE id = ?", [$templateId])->fetch();
        
        // 2. Ambil Data Siswa Lengkap
        $s = $db->query("
            SELECT s.*, c.name as class_name 
            FROM students s 
            LEFT JOIN classrooms c ON s.classroom_id = c.id
            WHERE s.id = ?
        ", [$studentId])->fetch();

        // 3. Replace Placeholder
        $content = $tpl['content'];
        $content = str_replace('{nama}', $s['full_name'], $content);
        $content = str_replace('{nis}', $s['nis'], $content);
        $content = str_replace('{kelas}', $s['class_name'] ?? 'Belum ada kelas', $content);
        $content = str_replace('{tempat_lahir}', $s['birth_place'], $content);
        $content = str_replace('{tgl_lahir}', $s['birth_date'], $content);
        $content = str_replace('{alamat}', $s['address'], $content);

        // Render View Cetak (Reuse logic A4 dari Rapor)
        View::render('letters/print_a4', ['content' => $content, 'title' => $tpl['name']]);
    }
}
