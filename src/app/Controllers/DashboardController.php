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
        $scope = Session::get('active_scope', 'GLOBAL'); 

        // ==========================================
        // 1. FILTER QUERY (SCOPE LOGIC)
        // ==========================================
        
        // Filter PPDB (berdasarkan ppdb_tracks.level)
        $joinPpdb = "";
        $wherePpdb = "";
        if ($scope != 'GLOBAL') {
            $joinPpdb = " JOIN ppdb_tracks t ON sc.ppdb_track_id = t.id ";
            $wherePpdb = " WHERE t.level = '$scope' ";
        }

        // Filter Siswa & Keuangan (berdasarkan classrooms.major)
        // PERBAIKAN: Menggunakan 'major' (MTS/MA/PDF) bukan 'level' (7/8/10)
        $joinClass = "";
        $whereClass = "";
        if ($scope != 'GLOBAL') {
            $joinClass = " JOIN classrooms c ON s.classroom_id = c.id ";
            $whereClass = " AND c.major = '$scope' ";
        }

        // ==========================================
        // 2. DATA PPDB
        // ==========================================
        $sqlGlobal = "SELECT
            COUNT(sc.id) as total,
            SUM(CASE WHEN sc.registration_status = 'PENDING' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN sc.registration_status IN ('PAID', 'VERIFIED', 'ACCEPTED') THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN sc.registration_status = 'REJECTED' THEN 1 ELSE 0 END) as rejected
        FROM student_candidates sc 
        $joinPpdb 
        $wherePpdb";

        $ppdbStats = $db->query($sqlGlobal)->fetch();
        $ppdbSummary = [
            'total'   => $ppdbStats['total'] ?? 0,
            'pending' => $ppdbStats['pending'] ?? 0,
            'active'  => $ppdbStats['active'] ?? 0,
            'failed'  => $ppdbStats['rejected'] ?? 0
        ];

        // List Jalur
        $trackFilter = ($scope != 'GLOBAL') ? " WHERE t.level = '$scope' " : "";
        $sqlTracks = "SELECT t.name, t.level, t.quota, t.code,
                             COUNT(c.id) as registered_count,
                             SUM(CASE WHEN c.registration_status = 'ACCEPTED' THEN 1 ELSE 0 END) as accepted_count
                      FROM ppdb_tracks t
                      LEFT JOIN student_candidates c ON t.id = c.ppdb_track_id
                      $trackFilter
                      GROUP BY t.id
                      ORDER BY t.level ASC, t.name ASC";
        $tracksData = $db->query($sqlTracks)->fetchAll();

        // ==========================================
        // 3. DATA KESISWAAN (SISWA AKTIF)
        // ==========================================
        
        // Total Siswa
        $sqlTotalStudents = "SELECT COUNT(s.id) FROM students s $joinClass WHERE s.status = 'ACTIVE' $whereClass";
        $totalStudents = $db->query($sqlTotalStudents)->fetchColumn();

        // Statistik Per Jenjang (Group by MAJOR)
        // PERBAIKAN: Group by 'major' untuk mendapatkan key 'MTS', 'MA', 'PDF'
        $sqlLevel = "SELECT c.major, COUNT(s.id) as total 
                     FROM students s 
                     JOIN classrooms c ON s.classroom_id = c.id 
                     WHERE s.status = 'ACTIVE' $whereClass
                     GROUP BY c.major";
        
        $levelStatsRaw = $db->query($sqlLevel)->fetchAll();
        $levelStats = [];
        foreach($levelStatsRaw as $row) {
            $levelStats[$row['major']] = $row['total'];
        }

        // Gender
        $sqlGender = "SELECT s.gender, COUNT(s.id) as total 
                      FROM students s 
                      $joinClass 
                      WHERE s.status = 'ACTIVE' $whereClass
                      GROUP BY s.gender";
        $genderStatsRaw = $db->query($sqlGender)->fetchAll();
        $genderStats = ['L' => 0, 'P' => 0];
        foreach($genderStatsRaw as $row) {
            $genderStats[$row['gender']] = $row['total'];
        }

        // ==========================================
        // 4. DATA KEUANGAN
        // ==========================================
        // Pemasukan Hari Ini
        $sqlIncome = "SELECT SUM(t.amount_paid) 
                      FROM transactions t
                      JOIN bills b ON t.bill_id = b.id
                      JOIN students s ON b.student_id = s.id
                      JOIN classrooms c ON s.classroom_id = c.id
                      WHERE date(t.created_at) = CURDATE() 
                      $whereClass"; 
        $incomeToday = $db->query($sqlIncome)->fetchColumn();
        
        // Tagihan Unpaid
        $sqlUnpaid = "SELECT COUNT(b.id) 
                      FROM bills b
                      JOIN students s ON b.student_id = s.id
                      JOIN classrooms c ON s.classroom_id = c.id
                      WHERE b.status = 'UNPAID' 
                      $whereClass";
        $unpaidBills = $db->query($sqlUnpaid)->fetchColumn();

        View::render('dashboard/index', [
            'title'         => 'Dashboard Utama',
            'user'          => Session::get('user_name'),
            'active_scope'  => $scope,
            
            'ppdb_summary'  => $ppdbSummary,
            'tracks_data'   => $tracksData,
            
            'student_stats' => [
                'total' => $totalStudents,
                'mts'   => $levelStats['MTS'] ?? 0,
                'ma'    => $levelStats['MA'] ?? 0,
                'pdf'   => $levelStats['PDF'] ?? 0, // Ditambahkan stats PDF
                'putra' => $genderStats['L'],
                'putri' => $genderStats['P']
            ],
            
            'finance_stats' => [
                'income_today' => $incomeToday ?? 0,
                'unpaid_count' => $unpaidBills
            ]
        ]);
    }
}
