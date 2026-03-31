<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class AcademicSupportController {
    public function __construct() { Middleware::auth(); }

    // --- KALENDER ---
    public function calendar() {
        $db = Database::getInstance();
        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch();
        $events = $db->query("SELECT * FROM academic_calendar WHERE academic_year_id = ? ORDER BY start_date ASC", [$activeYear['id'] ?? 0])->fetchAll();
        
        View::render('academic/calendar', ['title' => 'Kalender Akademik', 'events' => $events]);
    }

    public function storeEvent() {
        $db = Database::getInstance();
        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch();
        
        $db->query("INSERT INTO academic_calendar (academic_year_id, title, start_date, end_date, type, color) VALUES (?, ?, ?, ?, ?, ?)", [
            $activeYear['id'], $_POST['title'], $_POST['start_date'], $_POST['end_date'], $_POST['type'], $_POST['color']
        ]);
        header('Location: /academic/calendar');
    }

    // --- BANK SOAL ---
    public function examBank() {
        $db = Database::getInstance();
        $exams = $db->query("
            SELECT e.*, s.name as subject_name, u.name as teacher_name 
            FROM exam_banks e
            JOIN subjects s ON e.subject_id = s.id
            JOIN users u ON e.teacher_id = u.id
            ORDER BY e.created_at DESC
        ")->fetchAll();
        
        // Data untuk form
        $subjects = $db->query("SELECT * FROM subjects")->fetchAll();
        
        View::render('academic/exams', ['title' => 'Bank Soal', 'exams' => $exams, 'subjects' => $subjects]);
    }

    public function storeExam() {
        // Handle File Upload
        $filename = '';
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['file']['tmp_name'], __DIR__ . '/../../public/uploads/exams/' . $filename);
        }

        $db = Database::getInstance();
        $db->query("INSERT INTO exam_banks (subject_id, teacher_id, title, file_path, type) VALUES (?, ?, ?, ?, ?)", [
            $_POST['subject_id'], Session::get('user_id'), $_POST['title'], $filename, $_POST['type']
        ]);
        
        Session::setFlash('success', 'Soal berhasil diupload.');
        header('Location: /academic/exams');
    }
}
