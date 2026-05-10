<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class ReportController {
    public function __construct() {
        Middleware::auth();
    }

    public function printReport() {
        $studentId = $_GET['student_id'] ?? null;
        $yearId    = $_GET['year_id'] ?? null;
        if (!$studentId) { header('Location: /reports/students'); exit; }

        $db = Database::getInstance();

        $student = $db->query(
            "SELECT s.*, c.name as class_name FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id WHERE s.id = ?",
            [$studentId]
        )->fetch();
        if (!$student) { header('Location: /reports/students'); exit; }

        $year = $yearId
            ? $db->query("SELECT * FROM academic_years WHERE id = ?", [$yearId])->fetch()
            : $db->query("SELECT * FROM academic_years WHERE is_active = 1 LIMIT 1")->fetch();

        $weights = $db->query(
            "SELECT * FROM grading_weights WHERE academic_year_id = ?", [$year['id'] ?? 0]
        )->fetch() ?: ['weight_daily' => 40, 'weight_uts' => 30, 'weight_uas' => 30];

        // Ambil semua nilai siswa untuk tahun ajaran ini
        $gradesRaw = $db->query("
            SELECT sg.type, sg.score, s.name as subject_name, s.type as subject_type, s.kkm, sch.id as schedule_id
            FROM student_grades sg
            JOIN schedules sch ON sg.schedule_id = sch.id
            JOIN subjects s ON sch.subject_id = s.id
            WHERE sg.student_id = ? AND sch.academic_year_id = ?
        ", [$studentId, $year['id'] ?? 0])->fetchAll();

        // Kelompokkan per mata pelajaran
        $subjectMap = [];
        foreach ($gradesRaw as $g) {
            $key = $g['subject_name'];
            if (!isset($subjectMap[$key])) {
                $subjectMap[$key] = [
                    'subject_name' => $g['subject_name'],
                    'subject_type' => $g['subject_type'],
                    'kkm'          => $g['kkm'],
                    'scores'       => []
                ];
            }
            $subjectMap[$key]['scores'][$g['type']] = $g['score'];
        }

        // Hitung nilai akhir per mapel
        $grades = ['NASIONAL' => [], 'PESANTREN' => [], 'MULOK' => []];
        foreach ($subjectMap as $subj) {
            $s = $subj['scores'];
            $daily = (($s['UH1'] ?? 0) + ($s['UH2'] ?? 0) + ($s['TUGAS'] ?? 0)) / 3;
            $uts   = $s['UTS'] ?? 0;
            $uas   = $s['UAS'] ?? 0;
            $final = round(($daily * $weights['weight_daily'] + $uts * $weights['weight_uts'] + $uas * $weights['weight_uas']) / 100, 1);
            $predicate = $final >= 90 ? 'A' : ($final >= 80 ? 'B' : ($final >= 70 ? 'C' : 'D'));
            $type = in_array($subj['subject_type'], ['NASIONAL','PESANTREN','MULOK']) ? $subj['subject_type'] : 'NASIONAL';
            $grades[$type][] = [
                'subject_name' => $subj['subject_name'],
                'kkm'          => $subj['kkm'],
                'final_score'  => $final,
                'predicate'    => $predicate,
                'description'  => '',
            ];
        }

        // Absensi
        $att = $db->query("
            SELECT status, COUNT(*) as total FROM attendances
            WHERE student_id = ? GROUP BY status
        ", [$studentId])->fetchAll();
        $attendance = ['S' => 0, 'I' => 0, 'A' => 0];
        foreach ($att as $a) $attendance[$a['status']] = $a['total'];

        View::render('report/print_a4', [
            'title'      => 'Rapor - ' . $student['full_name'],
            'student'    => $student,
            'year'       => $year,
            'grades'     => $grades,
            'attendance' => $attendance,
        ]);
    }

    public function students() {
        $db = Database::getInstance();
        $search      = $_GET['search'] ?? '';
        $classroomId = $_GET['classroom_id'] ?? '';
        $yearId      = $_GET['year_id'] ?? '';
        $page        = (int)($_GET['page'] ?? 1);
        $limit       = (int)($_GET['limit'] ?? 20);
        $offset      = ($page - 1) * $limit;

        $where = "WHERE s.status = 'ACTIVE'";
        $params = [];
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);
        if (!empty($search))      { $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($classroomId)) { $where .= " AND s.classroom_id = ?"; $params[] = $classroomId; }
        if (!empty($yearId))      { $where .= " AND EXISTS (SELECT 1 FROM student_grades sg JOIN schedules sch ON sg.schedule_id = sch.id WHERE sg.student_id = s.id AND sch.academic_year_id = ?)"; $params[] = $yearId; }

        $totalData  = $db->query("SELECT COUNT(*) FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id $where", $params)->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        $students   = $db->query("SELECT s.*, c.name as classroom_name, c.level FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id $where ORDER BY c.name, s.full_name LIMIT $limit OFFSET $offset", $params)->fetchAll();

        // Classrooms dropdown mengikuti scope
        $cWhere = ''; $cParams = [];
        [$cWhere, $cParams] = ScopeFilter::apply('c');
        $classrooms = $db->query("SELECT * FROM classrooms c WHERE 1=1 $cWhere ORDER BY level, name", $cParams)->fetchAll();
        $years      = $db->query("SELECT * FROM academic_years ORDER BY id DESC")->fetchAll();
        $activeYear = $db->query("SELECT * FROM academic_years WHERE is_active = 1 LIMIT 1")->fetch();

        View::render('reports/students', [
            'title'             => 'Rapor Siswa',
            'students'          => $students,
            'classrooms'        => $classrooms,
            'years'             => $years,
            'activeYear'        => $activeYear,
            'search'            => $search,
            'selectedClassroom' => $classroomId,
            'selectedYear'      => $yearId,
            'totalData'         => $totalData,
            'totalPages'        => $totalPages,
            'currentPage'       => $page,
            'limit'             => $limit,
        ]);
    }

    public function boarding() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $sql = "SELECT s.*, d.name as dorm_name
                FROM students s
                LEFT JOIN dorms d ON s.dorm_id = d.id
                WHERE s.status = 'ACTIVE' AND s.dorm_id IS NOT NULL";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%";
        }
        $students = $db->query($sql . " ORDER BY d.name, s.full_name", $params)->fetchAll();
        $dorms = $db->query("SELECT * FROM dorms ORDER BY name")->fetchAll();

        View::render('reports/boarding', [
            'title' => 'Rapor Asrama',
            'students' => $students,
            'dorms' => $dorms,
            'search' => $search,
        ]);
    }
}
