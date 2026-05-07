<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class DashboardController {
    public function __construct() {
        Middleware::auth();
    }

    public function index() {
        $db = Database::getInstance();

        // Statistik Utama
        $totalStudents  = $db->query("SELECT COUNT(*) FROM students WHERE status='ACTIVE'")->fetchColumn();
        $totalStaff     = $db->query("SELECT COUNT(*) FROM staff_members WHERE status='ACTIVE'")->fetchColumn();
        $totalTeachers  = $db->query("SELECT COUNT(*) FROM teachers WHERE status='ACTIVE'")->fetchColumn();
        $totalClasses   = $db->query("SELECT COUNT(*) FROM classrooms")->fetchColumn();

        // Siswa per unit
        $unitRaw = $db->query("
            SELECT c.major as unit, COUNT(s.id) as total
            FROM students s JOIN classrooms c ON s.classroom_id = c.id
            WHERE s.status='ACTIVE' GROUP BY c.major
        ")->fetchAll();
        $unitStats = [];
        foreach ($unitRaw as $r) $unitStats[$r['unit']] = $r['total'];

        // Gender
        $genderRaw = $db->query("
            SELECT gender, COUNT(*) as total FROM students WHERE status='ACTIVE' GROUP BY gender
        ")->fetchAll();
        $genderStats = ['L' => 0, 'P' => 0];
        foreach ($genderRaw as $r) $genderStats[$r['gender']] = $r['total'];

        // PPDB
        $ppdbStats = $db->query("
            SELECT
              COUNT(*) as total,
              SUM(registration_status='PENDING') as pending,
              SUM(registration_status IN ('PAID','VERIFIED','ACCEPTED')) as active,
              SUM(registration_status='REJECTED') as rejected
            FROM student_candidates
        ")->fetch();

        // Ekskul
        $totalEkskul = $db->query("SELECT COUNT(*) FROM extracurriculars WHERE status='ACTIVE'")->fetchColumn();

        // Asrama
        $dormStats = $db->query("
            SELECT d.name, d.capacity, d.gender, COUNT(s.id) as occupied
            FROM dorms d LEFT JOIN students s ON s.dorm_id = d.id AND s.status='ACTIVE'
            GROUP BY d.id ORDER BY d.gender, d.name
        ")->fetchAll();

        // Jadwal hari ini
        $today = strtoupper(['Ahad','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][date('w')]);
        $todaySchedules = $db->query("
            SELECT s.day, s.start_time, s.end_time, sub.name as subject, c.name as class_name, u.name as teacher_name
            FROM schedules s
            JOIN subjects sub ON s.subject_id = sub.id
            JOIN classrooms c ON s.classroom_id = c.id
            JOIN users u ON s.teacher_id = u.id
            WHERE s.day = ? AND s.academic_year_id = (SELECT id FROM academic_years WHERE is_active=1 LIMIT 1)
            ORDER BY s.start_time LIMIT 8
        ", [$today])->fetchAll();

        // Aktivitas terbaru (dari student_candidates)
        $recentActivities = $db->query("
            SELECT sc.full_name, sc.registration_status, sc.created_at, t.name as track_name
            FROM student_candidates sc
            LEFT JOIN ppdb_tracks t ON sc.ppdb_track_id = t.id
            ORDER BY sc.created_at DESC LIMIT 6
        ")->fetchAll();

        $activities = [];
        foreach ($recentActivities as $r) {
            $diff = time() - strtotime($r['created_at']);
            if ($diff < 3600) $time = round($diff/60) . ' menit lalu';
            elseif ($diff < 86400) $time = round($diff/3600) . ' jam lalu';
            else $time = round($diff/86400) . ' hari lalu';
            $statusMap = [
                'PENDING'  => ['icon' => 'fa-user-plus',     'color' => 'blue',   'label' => 'Mendaftar'],
                'PAID'     => ['icon' => 'fa-check-circle',  'color' => 'green',  'label' => 'Pembayaran dikonfirmasi'],
                'VERIFIED' => ['icon' => 'fa-file-shield',   'color' => 'teal',   'label' => 'Dokumen diverifikasi'],
                'ACCEPTED' => ['icon' => 'fa-graduation-cap','color' => 'purple', 'label' => 'Diterima'],
                'REJECTED' => ['icon' => 'fa-circle-xmark',  'color' => 'red',    'label' => 'Ditolak'],
            ];
            $s = $statusMap[$r['registration_status']] ?? ['icon' => 'fa-pen', 'color' => 'gray', 'label' => 'Update'];
            $activities[] = ['time' => $time, 'user' => $r['full_name'], 'track' => $r['track_name'] ?? '-', 'icon' => $s['icon'], 'color' => $s['color'], 'label' => $s['label']];
        }

        View::render('dashboard/index', [
            'title'          => 'Dashboard',
            'user'           => Session::get('user_name'),
            'totalStudents'  => $totalStudents,
            'totalStaff'     => $totalStaff,
            'totalTeachers'  => $totalTeachers,
            'totalClasses'   => $totalClasses,
            'unitStats'      => $unitStats,
            'genderStats'    => $genderStats,
            'ppdbStats'      => $ppdbStats,
            'totalEkskul'    => $totalEkskul,
            'dormStats'      => $dormStats,
            'todaySchedules' => $todaySchedules,
            'todayName'      => $today,
            'activities'     => $activities,
        ]);
    }
}
