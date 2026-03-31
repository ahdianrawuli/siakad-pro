<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class ReportController {
    public function __construct() {
        Middleware::auth();
    }

    // Cetak Rapor Paripurna (Holistic)
    public function print() {
        $studentId = $_GET['student_id'];
        $db = Database::getInstance();

        // ---------------------------------------------------------
        // 1. DATA UTAMA: SISWA & TAHUN AJARAN
        // ---------------------------------------------------------
        $student = $db->query("
            SELECT s.*, c.name as class_name, c.level, c.major 
            FROM students s
            JOIN classrooms c ON s.classroom_id = c.id
            WHERE s.id = ?
        ", [$studentId])->fetch();

        if (!$student) die("Siswa tidak ditemukan.");

        $activeYear = $db->query("SELECT * FROM academic_years WHERE is_active = 1")->fetch();

        // ---------------------------------------------------------
        // 2. DATA NILAI AKADEMIK (Hitung Bobot)
        // ---------------------------------------------------------
        
        // Ambil Bobot
        $weights = $db->query("SELECT * FROM grading_weights WHERE academic_year_id = ?", [$activeYear['id']])->fetch();
        if (!$weights) $weights = ['weight_daily' => 40, 'weight_uts' => 30, 'weight_uas' => 30];

        // Ambil Nilai Mentah
        $sqlRaw = "
            SELECT 
                sub.id as subject_id, sub.name as subject_name, sub.code, sub.type, sub.kkm,
                sg.type as grade_type, sg.score
            FROM subjects sub
            LEFT JOIN schedules sch ON sch.subject_id = sub.id AND sch.classroom_id = ?
            LEFT JOIN student_grades sg ON sg.schedule_id = sch.id AND sg.student_id = ?
            WHERE sch.academic_year_id = ?
            ORDER BY sub.type ASC, sub.name ASC
        ";
        
        $rawGrades = $db->query($sqlRaw, [$student['classroom_id'], $studentId, $activeYear['id']])->fetchAll();

        // Proses Kalkulasi
        $processedGrades = [];
        foreach ($rawGrades as $row) {
            $subId = $row['subject_id'];
            if (!isset($processedGrades[$subId])) {
                $processedGrades[$subId] = [
                    'name' => $row['subject_name'], 'type' => $row['type'], 
                    'kkm' => $row['kkm'], 'scores' => ['UH' => [], 'UTS' => 0, 'UAS' => 0]
                ];
            }
            if ($row['grade_type']) {
                if (in_array($row['grade_type'], ['UH1', 'UH2', 'TUGAS'])) {
                    $processedGrades[$subId]['scores']['UH'][] = $row['score'];
                } elseif ($row['grade_type'] == 'UTS') {
                    $processedGrades[$subId]['scores']['UTS'] = $row['score'];
                } elseif ($row['grade_type'] == 'UAS') {
                    $processedGrades[$subId]['scores']['UAS'] = $row['score'];
                }
            }
        }

        // Format ke Array Report
        $reportData = ['NASIONAL' => [], 'PESANTREN' => [], 'MULOK' => []];
        foreach ($processedGrades as $p) {
            $sumUH = array_sum($p['scores']['UH']);
            $countUH = count($p['scores']['UH']);
            $avgUH = $countUH > 0 ? ($sumUH / $countUH) : 0;
            
            // Rumus Bobot
            $finalScore = (
                ($avgUH * $weights['weight_daily']) + 
                ($p['scores']['UTS'] * $weights['weight_uts']) + 
                ($p['scores']['UAS'] * $weights['weight_uas'])
            ) / 100;

            $finalScore = round($finalScore);
            $predicate = $this->getPredicate($finalScore, $p['kkm']);

            $reportData[$p['type']][] = [
                'subject_name' => $p['name'],
                'kkm' => $p['kkm'],
                'final_score' => $finalScore,
                'predicate' => $predicate,
                'description' => $this->getDescription($predicate, $p['name'])
            ];
        }

        // ---------------------------------------------------------
        // 3. [BARU] DATA PRESTASI (Integrasi Modul Kesiswaan)
        // ---------------------------------------------------------
        $achievements = $db->query("
            SELECT title, level, date 
            FROM student_achievements 
            WHERE student_id = ? 
            ORDER BY date DESC
        ", [$studentId])->fetchAll();

        // ---------------------------------------------------------
        // 4. [BARU] DATA SIKAP/DISIPLIN (Integrasi Modul Pelanggaran)
        // ---------------------------------------------------------
        $violationPoints = $db->query("
            SELECT SUM(vt.points) 
            FROM student_violations sv
            JOIN violation_types vt ON sv.violation_type_id = vt.id
            WHERE sv.student_id = ?
        ", [$studentId])->fetchColumn();

        // Logika Predikat Sikap Berdasarkan Poin
        $violationPoints = $violationPoints ?? 0; // Handle null
        $attitudeScore = 'SANGAT BAIK';
        
        if ($violationPoints > 100) $attitudeScore = 'KURANG';
        elseif ($violationPoints > 50) $attitudeScore = 'CUKUP';
        elseif ($violationPoints > 20) $attitudeScore = 'BAIK';

        // ---------------------------------------------------------
        // 5. [BARU] DATA TAHFIDZ (Integrasi Modul Asrama)
        // ---------------------------------------------------------
        $tahfidz = $db->query("
            SELECT surah_name, verses, grade 
            FROM worship_logs 
            WHERE student_id = ? AND type = 'ZIYADAH' 
            ORDER BY date DESC LIMIT 5
        ", [$studentId])->fetchAll();

        // ---------------------------------------------------------
        // 6. DATA ABSENSI
        // ---------------------------------------------------------
        $attendanceRaw = $db->query("
            SELECT status, COUNT(*) as total FROM attendances 
            WHERE student_id = ? GROUP BY status
        ", [$studentId])->fetchAll();
        
        $attendance = ['S' => 0, 'I' => 0, 'A' => 0];
        foreach($attendanceRaw as $att) $attendance[$att['status']] = $att['total'];

        // ---------------------------------------------------------
        // 7. RENDER VIEW HOLISTIC
        // ---------------------------------------------------------
        View::render('report/print_holistic', [
            'student' => $student,
            'year' => $activeYear,
            'grades' => $reportData,
            'achievements' => $achievements,     // Data Baru
            'attitude' => $attitudeScore,         // Data Baru
            'violation_points' => $violationPoints, // Data Baru
            'tahfidz' => $tahfidz,                // Data Baru
            'attendance' => $attendance
        ]);
    }

    // --- HELPER FUNCTION ---

    private function getPredicate($score, $kkm) {
        if ($score < $kkm) return 'D';
        if ($score < 80) return 'C';
        if ($score < 90) return 'B';
        return 'A';
    }

    private function getDescription($pred, $subject) {
        switch ($pred) {
            case 'A': return "Sangat baik dalam memahami materi $subject.";
            case 'B': return "Baik dalam memahami materi $subject.";
            case 'C': return "Cukup memahami materi $subject, perlu ditingkatkan.";
            default:  return "Perlu bimbingan khusus dalam materi $subject.";
        }
    }
}
