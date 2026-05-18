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

        // Ambil semua mapel yang diajar di kelas siswa pada tahun ajaran ini
        $allSubjects = $db->query("
            SELECT s.name as subject_name, s.type as subject_type, s.kkm
            FROM schedules sch
            JOIN subjects s ON sch.subject_id = s.id
            WHERE sch.classroom_id = ? AND sch.academic_year_id = ?
            GROUP BY s.id, s.name, s.type, s.kkm
            ORDER BY s.type, s.name
        ", [$student['classroom_id'], $year['id'] ?? 0])->fetchAll();

        // Ambil semua nilai siswa untuk tahun ajaran ini
        $gradesRaw = $db->query("
            SELECT sg.type, sg.category, sg.score, s.name as subject_name
            FROM student_grades sg
            JOIN schedules sch ON sg.schedule_id = sch.id
            JOIN subjects s ON sch.subject_id = s.id
            WHERE sg.student_id = ? AND sch.academic_year_id = ?
        ", [$studentId, $year['id'] ?? 0])->fetchAll();

        // Kelompokkan nilai per mata pelajaran
        $scoreMap = [];
        foreach ($gradesRaw as $g) {
            $key = $g['subject_name'];
            if ($g['type'] === 'HARIAN') {
                $scoreMap[$key]['harian'][] = (float)$g['score'];
            } elseif ($g['type'] === 'UTS') {
                $scoreMap[$key]['uts'] = (float)$g['score'];
            } elseif ($g['type'] === 'UAS') {
                $scoreMap[$key]['uas'] = (float)$g['score'];
            }
        }

        // Hitung nilai akhir per mapel (semua mapel muncul)
        $grades = ['NASIONAL' => [], 'PESANTREN' => [], 'MULOK' => []];
        foreach ($allSubjects as $subj) {
            $key = $subj['subject_name'];
            $harian = $scoreMap[$key]['harian'] ?? [];
            // Rata-rata harian hanya dari UH (category UH), bukan Tugas/Quiz
            $uhScores = [];
            foreach ($gradesRaw as $g) {
                if ($g['subject_name'] === $key && $g['type'] === 'HARIAN' && ($g['category'] ?? 'UH') === 'UH') {
                    $uhScores[] = (float)$g['score'];
                }
            }
            $daily = !empty($uhScores) ? array_sum($uhScores) / count($uhScores) : 0;
            $uts   = $scoreMap[$key]['uts'] ?? 0;
            $uas   = $scoreMap[$key]['uas'] ?? 0;
            $hasScore = !empty($harian) || $uts > 0 || $uas > 0;
            $final = $hasScore ? round(($daily * $weights['weight_daily'] + $uts * $weights['weight_uts'] + $uas * $weights['weight_uas']) / 100, 1) : null;
            $predicate = $final !== null ? ($final >= 90 ? 'A' : ($final >= 80 ? 'B' : ($final >= 70 ? 'C' : 'D'))) : '-';

            // Deskripsi capaian otomatis
            if ($final === null) {
                $desc = 'Belum ada nilai';
            } elseif ($final >= 90) {
                $desc = 'Sangat baik dalam menguasai materi ' . $subj['subject_name'];
            } elseif ($final >= 80) {
                $desc = 'Baik dalam menguasai materi ' . $subj['subject_name'];
            } elseif ($final >= 70) {
                $desc = 'Cukup dalam menguasai materi ' . $subj['subject_name'];
            } else {
                $desc = 'Perlu peningkatan dalam materi ' . $subj['subject_name'];
            }

            $type = in_array($subj['subject_type'], ['NASIONAL','PESANTREN','MULOK']) ? $subj['subject_type'] : 'NASIONAL';
            $grades[$type][] = [
                'subject_name' => $subj['subject_name'],
                'kkm'          => $subj['kkm'],
                'avg_harian'   => !empty($uhScores) ? round(array_sum($uhScores) / count($uhScores), 1) : '-',
                'uts'          => $uts ?: '-',
                'uas'          => $uas ?: '-',
                'final_score'  => $final ?? '-',
                'predicate'    => $predicate,
                'description'  => $desc,
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
            'weights'    => $weights,
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
