<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class BoardingReportController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $classId = $_GET['classroom_id'] ?? '';

        [$sw, $sp] = ScopeFilter::apply('c');
        $classrooms = $db->query("SELECT c.* FROM classrooms c WHERE 1=1 $sw ORDER BY c.level, c.name", $sp)->fetchAll();
        $students = [];
        $activeYear = $db->query("SELECT id, name, semester FROM academic_years WHERE is_active=1")->fetch();

        if ($classId && $activeYear) {
            $students = $db->query("
                SELECT s.id, s.full_name, s.nis, bg.tahfidz_grade, bg.language_grade, bg.character_grade
                FROM students s
                LEFT JOIN boarding_grades bg ON s.id = bg.student_id AND bg.academic_year_id = ?
                WHERE s.classroom_id = ? AND s.status = 'ACTIVE'
                ORDER BY s.full_name
            ", [$activeYear['id'], $classId])->fetchAll();
        }

        View::render('report/boarding/index', [
            'title' => 'Rapor Asrama',
            'classrooms' => $classrooms,
            'students' => $students,
            'selectedClass' => $classId,
            'activeYear' => $activeYear
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $studentId = $_POST['student_id'];
        $yearId = $_POST['academic_year_id'];
        $adminId = Session::get('user_id');

        // Cek data lama
        $check = $db->query("SELECT id FROM boarding_grades WHERE student_id = ? AND academic_year_id = ?", [$studentId, $yearId])->fetch();

        if ($check) {
            $db->query("UPDATE boarding_grades SET 
                tahfidz_grade = ?, tahfidz_desc = ?,
                language_grade = ?, language_desc = ?,
                character_grade = ?, character_desc = ?,
                homeroom_note = ?
                WHERE id = ?", [
                $_POST['tahfidz_grade'], $_POST['tahfidz_desc'],
                $_POST['language_grade'], $_POST['language_desc'],
                $_POST['character_grade'], $_POST['character_desc'],
                $_POST['homeroom_note'], $check['id']
            ]);
        } else {
            $db->query("INSERT INTO boarding_grades (
                student_id, academic_year_id, 
                tahfidz_grade, tahfidz_desc, 
                language_grade, language_desc,
                character_grade, character_desc, 
                homeroom_note, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $studentId, $yearId,
                $_POST['tahfidz_grade'], $_POST['tahfidz_desc'],
                $_POST['language_grade'], $_POST['language_desc'],
                $_POST['character_grade'], $_POST['character_desc'],
                $_POST['homeroom_note'], $adminId
            ]);
        }

        Session::setFlash('success', 'Nilai rapor asrama tersimpan.');
        // Redirect kembali ke list
        header("Location: /report/boarding?classroom_id=" . $_POST['classroom_id']);
    }

    public function print() {
        $db = Database::getInstance();
        $studentId = $_GET['student_id'];
        $yearId = $_GET['year_id'] ?? null;

        if (!$yearId) {
            $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1 LIMIT 1")->fetch();
            $yearId = $activeYear['id'] ?? null;
        }

        // Data Siswa
        $student = $db->query("SELECT s.*, c.name as class_name, d.name as dorm_name 
                               FROM students s 
                               LEFT JOIN classrooms c ON s.classroom_id = c.id 
                               LEFT JOIN dorms d ON s.dorm_id = d.id
                               WHERE s.id = ?", [$studentId])->fetch();

        // Data Nilai
        $grade = $db->query("SELECT * FROM boarding_grades WHERE student_id = ? AND academic_year_id = ?", [$studentId, $yearId])->fetch();
        
        // Data Tahun
        $year = $db->query("SELECT * FROM academic_years WHERE id = ?", [$yearId])->fetch();

        if (!$grade) die("Nilai belum diinput.");

        View::render('report/boarding/print', [
            'student' => $student,
            'grade' => $grade,
            'year' => $year
        ]);
    }
}
