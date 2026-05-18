<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class AcademicController {
    public function __construct() {
        Middleware::auth();
    }

    public function subjectTeachers() {
        View::render('academic/subject_teachers', ['title' => 'Guru Mata Pelajaran']);
    }

    public function homeroomTeachers() {
        View::render('academic/homeroom_teachers', ['title' => 'Wali Kelas']);
    }

    public function calendarView() {
        View::render('academic/calendar_view', ['title' => 'Kalender Akademik']);
    }

    public function syllabusView() {
        View::render('academic/syllabus_view', ['title' => 'Silabus']);
    }

    // ==========================================================
    // 1. MANAJEMEN MATA PELAJARAN (SUBJECTS)
    // ==========================================================
    public function subjects() {
        $db = Database::getInstance();

        $search = $_GET['search'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM subjects WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR code LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Hitung Total Data
        $totalSql = str_replace("SELECT *", "SELECT COUNT(*)", $sql);
        $totalDataRow = $db->query($totalSql, $params)->fetch();
        $totalData = $totalDataRow['COUNT(*)'] ?? 0;
        $totalPages = ceil($totalData / $limit);

        // Ambil Data
        $sql .= " ORDER BY type ASC, name ASC LIMIT $limit OFFSET $offset";
        $subjects = $db->query($sql, $params)->fetchAll();

        View::render('academic/subjects', [
            'title' => 'Mata Pelajaran',
            'subjects' => $subjects,
            'totalData' => $totalData,
            'search' => $search,
            'limit' => $limit,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function storeSubject() {
        $db = Database::getInstance();
        try {
            $db->query("INSERT INTO subjects (code, name, type, kkm) VALUES (?, ?, ?, ?)", [
                $_POST['code'], $_POST['name'], $_POST['type'], $_POST['kkm']
            ]);
            Session::setFlash('success', 'Mata Pelajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal: Kode Mapel mungkin sudah ada.');
        }
        header('Location: /academic/subjects');
    }

    public function updateSubject() {
        $db = Database::getInstance();
        try {
            $db->query("UPDATE subjects SET code = ?, name = ?, type = ?, kkm = ? WHERE id = ?", [
                $_POST['code'], $_POST['name'], $_POST['type'], $_POST['kkm'], $_POST['id']
            ]);
            Session::setFlash('success', 'Mata Pelajaran berhasil diperbarui.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal: Kode Mapel mungkin sudah digunakan.');
        }
        header('Location: /academic/subjects');
    }

    public function deleteSubject() {
        $db = Database::getInstance();
        try {
            $db->query("DELETE FROM subjects WHERE id = ?", [$_GET['id'] ?? 0]);
            Session::setFlash('success', 'Mata Pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal: Data masih digunakan di jadwal pelajaran.');
        }
        header('Location: /academic/subjects');
    }

    // ==========================================================
    // 2. MANAJEMEN JADWAL PELAJARAN (SCHEDULES)
    // ==========================================================
    public function schedules() {
        $db = Database::getInstance();
        
        // Cek Tahun Ajaran Aktif
        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch();
        $activeYearId = $activeYear['id'] ?? 0;

        // Parameter Filter & Pagination
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        
        $classId = $_GET['class_id'] ?? '';
        $day = $_GET['day'] ?? '';
        $search = $_GET['search'] ?? '';

        $where = "WHERE sch.academic_year_id = ?";
        $params = [$activeYearId];

        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        if (!empty($classId)) {
            $where .= " AND sch.classroom_id = ?";
            $params[] = $classId;
        }
        if (!empty($day)) {
            $where .= " AND sch.day = ?";
            $params[] = $day;
        }
        if (!empty($search)) {
            $where .= " AND (s.name LIKE ? OR u.name LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%";
        }

        // Hitung Total Data
        $countSql = "SELECT COUNT(*) as total 
                     FROM schedules sch
                     JOIN subjects s ON sch.subject_id = s.id
                     JOIN classrooms c ON sch.classroom_id = c.id
                     JOIN users u ON sch.teacher_id = u.id
                     $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        // Ambil Data Jadwal
        $sql = "SELECT sch.*, s.name as subject_name, c.name as class_name, u.name as teacher_name
                FROM schedules sch
                JOIN subjects s ON sch.subject_id = s.id
                JOIN classrooms c ON sch.classroom_id = c.id
                JOIN users u ON sch.teacher_id = u.id
                $where
                ORDER BY c.name ASC, FIELD(sch.day, 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'AHAD'), sch.start_time ASC
                LIMIT $limit OFFSET $offset";

        $schedules = $db->query($sql, $params)->fetchAll();

        // Data Pendukung Dropdown
        [$swc, $spc] = ScopeFilter::apply('c2');
        $classrooms = $db->query("SELECT c2.* FROM classrooms c2 WHERE 1=1 $swc ORDER BY c2.level, c2.name", $spc)->fetchAll();
        $subjects = $db->query("SELECT * FROM subjects ORDER BY name")->fetchAll();
        $teachers = $db->query("SELECT id, name FROM users WHERE role_id IN (1, 3) ORDER BY name")->fetchAll();

        View::render('academic/schedules', [
            'title' => 'Jadwal Pelajaran',
            'schedules' => $schedules,
            'classrooms' => $classrooms,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'academic_year_id' => $activeYearId,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'filterClass' => $classId,
            'filterDay' => $day,
            'search' => $search
        ]);
    }

    public function printSchedule() {
        $db = Database::getInstance();
        $activeYear = $db->query("SELECT * FROM academic_years WHERE is_active = 1")->fetch();
        $activeYearId = $activeYear['id'] ?? 0;

        $classId = $_GET['class_id'] ?? '';
        $day     = $_GET['day'] ?? '';

        $where  = "WHERE sch.academic_year_id = ?";
        $params = [$activeYearId];
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);
        if ($classId) { $where .= " AND sch.classroom_id = ?"; $params[] = $classId; }
        if ($day)     { $where .= " AND sch.day = ?";          $params[] = $day; }

        $schedules = $db->query("
            SELECT sch.*, s.name as subject_name, c.name as class_name, u.name as teacher_name
            FROM schedules sch
            JOIN subjects s ON sch.subject_id = s.id
            JOIN classrooms c ON sch.classroom_id = c.id
            JOIN users u ON sch.teacher_id = u.id
            $where
            ORDER BY c.name, FIELD(sch.day,'SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','AHAD'), sch.start_time
        ", $params)->fetchAll();

        $classroom = $classId ? $db->query("SELECT * FROM classrooms WHERE id = ?", [$classId])->fetch() : null;

        View::render('academic/print_schedule', [
            'schedules'  => $schedules,
            'classroom'  => $classroom,
            'day'        => $day,
            'activeYear' => $activeYear,
        ]);
    }

    public function storeSchedule() {
        $this->processSchedule('store');
    }

    public function updateSchedule() {
        $this->processSchedule('update');
    }

    private function processSchedule($mode) {
        $db = Database::getInstance();
        $yearId = $_POST['academic_year_id'];
        $classId = $_POST['classroom_id'];
        $teacherId = $_POST['teacher_id'];
        $day = $_POST['day'];
        $start = $_POST['start_time'];
        $end = $_POST['end_time'];
        $id = $_POST['id'] ?? null;

        // Validasi Bentrok Guru
        $sqlClashTeacher = "SELECT id FROM schedules WHERE academic_year_id = ? AND day = ? AND teacher_id = ? 
                            AND ((start_time < ? AND end_time > ?) OR (start_time >= ? AND start_time < ?))";
        $paramsTeacher = [$yearId, $day, $teacherId, $end, $start, $start, $end];
        
        if ($mode == 'update') {
            $sqlClashTeacher .= " AND id != ?";
            $paramsTeacher[] = $id;
        }

        if ($db->query($sqlClashTeacher, $paramsTeacher)->fetch()) {
            Session::setFlash('error', 'GAGAL: Guru tersebut sudah mengajar di kelas lain pada jam ini.');
            header('Location: /academic/schedules');
            exit;
        }

        // Validasi Bentrok Kelas
        $sqlClashClass = "SELECT id FROM schedules WHERE academic_year_id = ? AND day = ? AND classroom_id = ? 
                          AND ((start_time < ? AND end_time > ?) OR (start_time >= ? AND start_time < ?))";
        $paramsClass = [$yearId, $day, $classId, $end, $start, $start, $end];

        if ($mode == 'update') {
            $sqlClashClass .= " AND id != ?";
            $paramsClass[] = $id;
        }

        if ($db->query($sqlClashClass, $paramsClass)->fetch()) {
            Session::setFlash('error', 'GAGAL: Kelas ini sudah ada jadwal mapel lain pada jam ini.');
            header('Location: /academic/schedules');
            exit;
        }

        if ($mode == 'store') {
            $db->query("INSERT INTO schedules (academic_year_id, classroom_id, subject_id, teacher_id, day, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?)", 
                [$yearId, $classId, $_POST['subject_id'], $teacherId, $day, $start, $end]);
            Session::setFlash('success', 'Jadwal berhasil ditambahkan.');
        } else {
            $db->query("UPDATE schedules SET classroom_id=?, subject_id=?, teacher_id=?, day=?, start_time=?, end_time=? WHERE id=?", 
                [$classId, $_POST['subject_id'], $teacherId, $day, $start, $end, $id]);
            Session::setFlash('success', 'Jadwal berhasil diperbarui.');
        }
        header('Location: /academic/schedules');
    }

    public function deleteSchedule() {
        $db = Database::getInstance();
        $db->query("DELETE FROM schedules WHERE id = ?", [$_GET['id'] ?? 0]);
        Session::setFlash('success', 'Jadwal berhasil dihapus.');
        header('Location: /academic/schedules');
    }

    // ==========================================================
    // 3. INPUT NILAI (GRADES)
    // ==========================================================

    public function grades() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $role = Session::get('user_role');
        
        // Filter & Pagination
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';

        $where = "WHERE 1=1";
        $params = [];

        // Jika Login sebagai Guru, hanya tampilkan jadwal dia sendiri
        if ($role == 'guru') {
            $where .= " AND sch.teacher_id = ?";
            $params[] = $userId;
        }

        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        if (!empty($search)) {
            $where .= " AND (s.name LIKE ? OR c.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Hitung Total Data
        $countSql = "SELECT COUNT(*) as total 
                     FROM schedules sch
                     JOIN subjects s ON sch.subject_id = s.id 
                     JOIN classrooms c ON sch.classroom_id = c.id
                     $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        // Ambil Data Jadwal
        $sql = "SELECT sch.*, s.name as subject_name, c.name as class_name, u.name as teacher_name,
                (SELECT COUNT(*) FROM students WHERE classroom_id = c.id AND status='ACTIVE') as student_count
                FROM schedules sch
                JOIN subjects s ON sch.subject_id = s.id
                JOIN classrooms c ON sch.classroom_id = c.id
                JOIN users u ON sch.teacher_id = u.id
                $where
                ORDER BY c.name ASC, s.name ASC
                LIMIT $limit OFFSET $offset";

        $schedules = $db->query($sql, $params)->fetchAll();

        View::render('academic/grades_list', [
            'title' => 'Input Nilai Siswa',
            'schedules' => $schedules,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search
        ]);
    }

    public function manageGrades() {
        $scheduleId = $_GET['schedule_id'];
        $db = Database::getInstance();

        $schedule = $db->query("
            SELECT sch.*, s.name as subject_name, s.id as subject_id, c.name as class_name, c.id as classroom_id, sch.academic_year_id
            FROM schedules sch JOIN subjects s ON sch.subject_id = s.id 
            JOIN classrooms c ON sch.classroom_id = c.id WHERE sch.id = ?
        ", [$scheduleId])->fetch();
        if (!$schedule) die("Jadwal tidak ditemukan.");

        $students = $db->query("SELECT * FROM students WHERE classroom_id = ? AND status='ACTIVE' ORDER BY full_name", [$schedule['classroom_id']])->fetchAll();

        // Ambil SEMUA jadwal mapel ini di kelas ini (bisa banyak guru/hari)
        $allScheduleIds = $db->query("SELECT id FROM schedules WHERE subject_id=? AND classroom_id=? AND academic_year_id=?",
            [$schedule['subject_id'], $schedule['classroom_id'], $schedule['academic_year_id']])->fetchAll(\PDO::FETCH_COLUMN);
        $inClause = implode(',', $allScheduleIds);

        // Ambil semua nilai dari semua jadwal mapel+kelas ini
        $gradesRaw = $db->query("SELECT sg.*, u.name as teacher_name FROM student_grades sg LEFT JOIN users u ON sg.created_by = u.id WHERE sg.schedule_id IN ($inClause) ORDER BY sg.type, sg.category, sg.seq_num")->fetchAll();

        // Mapping
        $gradeMap = [];
        $harianColumns = [];
        foreach ($gradesRaw as $g) {
            if ($g['type'] === 'HARIAN') {
                $key = $g['category'] . '_' . $g['seq_num'];
                $gradeMap[$g['student_id']][$key] = ['score' => $g['score'], 'by' => $g['teacher_name']];
                $harianColumns[$key] = ['category' => $g['category'], 'seq_num' => $g['seq_num'], 'description' => $g['description'], 'date' => $g['date'], 'by' => $g['teacher_name']];
            } else {
                $gradeMap[$g['student_id']][$g['type']] = ['score' => $g['score'], 'by' => $g['teacher_name']];
            }
        }
        ksort($harianColumns, SORT_NATURAL);

        $weights = $db->query("SELECT * FROM grading_weights WHERE academic_year_id = ?", [$schedule['academic_year_id']])->fetch()
            ?: ['weight_daily' => 40, 'weight_uts' => 30, 'weight_uas' => 30];

        View::render('academic/grades_form', [
            'title'         => 'Input Nilai: ' . $schedule['subject_name'],
            'schedule'      => $schedule,
            'students'      => $students,
            'gradeMap'      => $gradeMap,
            'harianColumns' => $harianColumns,
            'weights'       => $weights,
        ]);
    }

    public function storeGrades() {
        $scheduleId = $_POST['schedule_id'];
        $type       = $_POST['grade_type']; // HARIAN, UTS, UAS
        $category   = $_POST['category'] ?? 'UH'; // UH, TUGAS, QUIZ
        $seqNum     = (int)($_POST['seq_num'] ?? 1);
        $date       = $_POST['grade_date'] ?? date('Y-m-d');
        $description = $_POST['description'] ?? '';
        $scores     = $_POST['scores'] ?? []; // [student_id => score]
        $userId     = Session::get('user_id');
        $db         = Database::getInstance();

        try {
            $db->getConnection()->beginTransaction();

            foreach ($scores as $studentId => $score) {
                if ($score === '' || $score === null) continue;

                if ($type === 'HARIAN') {
                    $exist = $db->query("SELECT id FROM student_grades WHERE student_id=? AND schedule_id=? AND type='HARIAN' AND category=? AND seq_num=?",
                        [$studentId, $scheduleId, $category, $seqNum])->fetch();
                } else {
                    $exist = $db->query("SELECT id FROM student_grades WHERE student_id=? AND schedule_id=? AND type=?",
                        [$studentId, $scheduleId, $type])->fetch();
                }

                if ($exist) {
                    $db->query("UPDATE student_grades SET score=?, date=?, description=?, created_by=? WHERE id=?",
                        [$score, $date, $description, $userId, $exist['id']]);
                } else {
                    $db->query("INSERT INTO student_grades (student_id, schedule_id, type, category, seq_num, date, description, score, created_by) VALUES (?,?,?,?,?,?,?,?,?)",
                        [$studentId, $scheduleId, $type, $category, $seqNum, $date, $description, $score, $userId]);
                }
            }

            $db->getConnection()->commit();
            Session::setFlash('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        header("Location: /academic/grades/manage?schedule_id=$scheduleId");
    }

    // ==========================================================
    public function printGrades() {
        $scheduleId = $_GET['schedule_id'] ?? '';
        if (!$scheduleId) { header('Location: /academic/grades'); exit; }
        $db = Database::getInstance();

        $schedule = $db->query("SELECT sch.*, s.name as subject_name, s.id as subject_id, c.name as class_name, c.id as classroom_id, u.name as teacher_name
            FROM schedules sch JOIN subjects s ON sch.subject_id=s.id
            JOIN classrooms c ON sch.classroom_id=c.id JOIN users u ON sch.teacher_id=u.id
            WHERE sch.id=?", [$scheduleId])->fetch();

        $students = $db->query("SELECT * FROM students WHERE classroom_id=? AND status='ACTIVE' ORDER BY full_name", [$schedule['classroom_id']])->fetchAll();

        // Ambil semua jadwal mapel+kelas ini
        $allScheduleIds = $db->query("SELECT id FROM schedules WHERE subject_id=? AND classroom_id=? AND academic_year_id=?",
            [$schedule['subject_id'], $schedule['classroom_id'], $schedule['academic_year_id']])->fetchAll(\PDO::FETCH_COLUMN);
        $inClause = implode(',', $allScheduleIds);

        $gradesRaw = $db->query("SELECT * FROM student_grades WHERE schedule_id IN ($inClause) ORDER BY type, category, seq_num")->fetchAll();

        $gradeMap = []; $harianColumns = [];
        foreach ($gradesRaw as $g) {
            if ($g['type'] === 'HARIAN') {
                $key = $g['category'] . '_' . $g['seq_num'];
                $gradeMap[$g['student_id']][$key] = $g['score'];
                $harianColumns[$key] = ['category' => $g['category'], 'seq_num' => $g['seq_num'], 'date' => $g['date'], 'description' => $g['description']];
            } else {
                $gradeMap[$g['student_id']][$g['type']] = $g['score'];
            }
        }
        ksort($harianColumns, SORT_NATURAL);

        $weights = $db->query("SELECT * FROM grading_weights WHERE academic_year_id=?", [$schedule['academic_year_id']])->fetch()
            ?: ['weight_daily'=>40,'weight_uts'=>30,'weight_uas'=>30];

        View::render('academic/print_grades', [
            'schedule' => $schedule, 'students' => $students,
            'gradeMap' => $gradeMap, 'harianColumns' => $harianColumns, 'weights' => $weights,
        ]);
    }

    // 4. MANAJEMEN TAHUN AJARAN (ACADEMIC YEARS)
    // ==========================================================
    public function years() {
        $db = Database::getInstance();

        $search = $_GET['search'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM academic_years WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND name LIKE ?";
            $params[] = "%$search%";
        }

        $totalSql = str_replace("SELECT *", "SELECT COUNT(*)", $sql);
        $totalDataRow = $db->query($totalSql, $params)->fetch();
        $totalData = $totalDataRow['COUNT(*)'] ?? 0;

        $sql .= " ORDER BY name DESC, semester DESC LIMIT $limit OFFSET $offset";
        $years = $db->query($sql, $params)->fetchAll();

        $totalPages = ceil($totalData / $limit);

        View::render('academic/years', [
            'title' => 'Tahun Ajaran',
            'years' => $years,
            'totalData' => $totalData,
            'search' => $search,
            'limit' => $limit,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function storeYear() {
        $db = Database::getInstance();
        $db->query("INSERT INTO academic_years (name, semester, is_active) VALUES (?, ?, 0)", [
            $_POST['name'], $_POST['semester']
        ]);
        Session::setFlash('success', 'Tahun ajaran berhasil dibuat.');
        header('Location: /academic/years');
    }

    public function updateYear() {
        $db = Database::getInstance();
        $db->query("UPDATE academic_years SET name=?, semester=? WHERE id=?", [
            $_POST['name'], $_POST['semester'], $_POST['id']
        ]);
        Session::setFlash('success', 'Tahun ajaran diperbarui.');
        header('Location: /academic/years');
    }

    public function deleteYear() {
        $id = (int)$_GET['id'];
        $db = Database::getInstance();
        $active = $db->query("SELECT is_active FROM academic_years WHERE id=?", [$id])->fetchColumn();
        if ($active) {
            Session::setFlash('error', 'Tidak bisa menghapus tahun ajaran yang sedang aktif.');
        } else {
            $db->query("DELETE FROM academic_years WHERE id=?", [$id]);
            Session::setFlash('success', 'Tahun ajaran dihapus.');
        }
        header('Location: /academic/years');
    }

    public function activateYear() {
        $id = $_GET['id'];
        $db = Database::getInstance();
        
        try {
            $db->getConnection()->beginTransaction();
            $db->query("UPDATE academic_years SET is_active = 0");
            $db->query("UPDATE academic_years SET is_active = 1 WHERE id = ?", [$id]);
            $db->getConnection()->commit();
            Session::setFlash('success', 'Tahun ajaran aktif berhasil diganti.');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal mengganti tahun ajaran.');
        }
        
        header('Location: /academic/years');
    }

    // ==========================================================
    // 5. JURNAL MENGAJAR GURU (JOURNALS)
    // ==========================================================
    public function journals() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $role   = Session::get('user_role');
        $yearId = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch()['id'] ?? 0;
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 12;
        $offset = ($page - 1) * $limit;

        [$sw, $sp] = ScopeFilter::apply('c');
        $scopeWhere = $sw ? "AND 1=1 $sw" : "";

        $where = "WHERE sch.academic_year_id = ? $scopeWhere";
        $baseParams = [$yearId, ...$sp];

        if ($role == 'guru') {
            $where .= " AND sch.teacher_id = ?";
            $baseParams[] = $userId;
        }

        $totalData  = $db->query("SELECT COUNT(*) FROM schedules sch JOIN classrooms c ON sch.classroom_id = c.id $where", $baseParams)->fetchColumn();
        $totalPages = max(1, ceil($totalData / $limit));

        $sql = "SELECT sch.*, s.name as subject_name, c.name as class_name,
                   (SELECT COUNT(*) FROM teaching_journals tj WHERE tj.schedule_id = sch.id) as total_entries
                FROM schedules sch
                JOIN subjects s ON sch.subject_id = s.id
                JOIN classrooms c ON sch.classroom_id = c.id
                $where ORDER BY c.name, s.name LIMIT $limit OFFSET $offset";

        $schedules = $db->query($sql, $baseParams)->fetchAll();

        View::render('academic/journal_index', [
            'title'       => 'Jurnal Mengajar',
            'schedules'   => $schedules,
            'totalData'   => $totalData,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'limit'       => $limit,
        ]);
    }

    public function journalHistory() {
        $scheduleId = $_GET['schedule_id'];
        $db = Database::getInstance();

        $page   = (int)($_GET['page'] ?? 1);
        $limit  = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $dateFilter = $_GET['date'] ?? '';

        $schedule = $db->query("
            SELECT sch.*, s.name as subject_name, c.name as class_name 
            FROM schedules sch
            JOIN subjects s ON sch.subject_id = s.id 
            JOIN classrooms c ON sch.classroom_id = c.id
            WHERE sch.id = ?
        ", [$scheduleId])->fetch();

        $where = "WHERE schedule_id = ?";
        $params = [$scheduleId];
        if (!empty($search)) { $where .= " AND topic LIKE ?"; $params[] = "%$search%"; }
        if (!empty($dateFilter)) { $where .= " AND date = ?"; $params[] = $dateFilter; }

        $totalData  = $db->query("SELECT COUNT(*) FROM teaching_journals $where", $params)->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        $journals = $db->query("SELECT * FROM teaching_journals $where ORDER BY date DESC, created_at DESC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        $students = $db->query("SELECT * FROM students WHERE classroom_id = ? AND status='ACTIVE' ORDER BY full_name", [$schedule['classroom_id']])->fetchAll();

        $attendanceData = [];
        foreach ($journals as $j) {
            $att = $db->query("SELECT student_id, status FROM journal_attendance WHERE journal_id = ?", [$j['id']])->fetchAll();
            foreach ($att as $a) { $attendanceData[$j['id']][$a['student_id']] = $a['status']; }
        }

        View::render('academic/journal_history', [
            'schedule'       => $schedule,
            'journals'       => $journals,
            'students'       => $students,
            'attendanceData' => $attendanceData,
            'totalData'      => $totalData,
            'totalPages'     => $totalPages,
            'currentPage'    => $page,
            'limit'          => $limit,
            'search'         => $search,
            'dateFilter'     => $dateFilter,
        ]);
    }

    public function journalCreate() {
        $scheduleId = $_GET['schedule_id'];
        $db = Database::getInstance();

        $schedule = $db->query("SELECT * FROM schedules WHERE id = ?", [$scheduleId])->fetch();
        $students = $db->query("
            SELECT * FROM students 
            WHERE classroom_id = ? 
            ORDER BY full_name ASC
        ", [$schedule['classroom_id']])->fetchAll();

        View::render('academic/journal_form', [
            'schedule' => $schedule,
            'students' => $students
        ]);
    }

    public function journalStore() {
        $db = Database::getInstance();
        $scheduleId = $_POST['schedule_id'];
        
        try {
            $db->getConnection()->beginTransaction();
            $db->query("INSERT INTO teaching_journals (schedule_id, date, topic, notes) VALUES (?, ?, ?, ?)", [
                $scheduleId, $_POST['date'], $_POST['topic'], $_POST['notes']
            ]);
            $journalId = $db->getConnection()->lastInsertId();

            $attendance = $_POST['attendance'] ?? []; 
            $sqlAtt = "INSERT INTO journal_attendance (journal_id, student_id, status) VALUES (?, ?, ?)";

            foreach ($attendance as $studentId => $status) {
                $db->query($sqlAtt, [$journalId, $studentId, $status]);
            }

            $db->getConnection()->commit();
            Session::setFlash('success', 'Jurnal pertemuan berhasil disimpan.');

        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal menyimpan jurnal.');
        }

        header("Location: /academic/journals/history?schedule_id=$scheduleId");
    }

    public function journalUpdate() {
        $db = Database::getInstance();
        $journalId = $_POST['id'];
        $scheduleId = $_POST['schedule_id'];

        try {
            $db->getConnection()->beginTransaction();
            $db->query("UPDATE teaching_journals SET date=?, topic=?, notes=? WHERE id=?", [
                $_POST['date'], $_POST['topic'], $_POST['notes'], $journalId
            ]);
            
            $db->query("DELETE FROM journal_attendance WHERE journal_id = ?", [$journalId]);
            $attendance = $_POST['attendance'] ?? [];
            $sqlAtt = "INSERT INTO journal_attendance (journal_id, student_id, status) VALUES (?, ?, ?)";
            foreach ($attendance as $studentId => $status) {
                $db->query($sqlAtt, [$journalId, $studentId, $status]);
            }

            $db->getConnection()->commit();
            Session::setFlash('success', 'Jurnal diperbarui.');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal update jurnal.');
        }
        header("Location: /academic/journals/history?schedule_id=$scheduleId");
    }

    public function journalDelete() {
        $id = $_GET['id'];
        $scheduleId = $_GET['schedule_id'];
        $db = Database::getInstance();
        $db->query("DELETE FROM teaching_journals WHERE id = ?", [$id]);
        Session::setFlash('success', 'Jurnal dihapus.');
        header("Location: /academic/journals/history?schedule_id=$scheduleId");
    }

    // ==========================================================
    // 6. KONFIGURASI BOBOT NILAI (WEIGHTS)
    // ==========================================================
    public function weights() {
        $db = Database::getInstance();
        $activeYear = $db->query("SELECT * FROM academic_years WHERE is_active = 1")->fetch();
        
        if (!$activeYear) {
            die("Belum ada tahun ajaran aktif.");
        }

        $weight = $db->query("SELECT * FROM grading_weights WHERE academic_year_id = ?", [$activeYear['id']])->fetch();

        if (!$weight) {
            $weight = ['weight_daily' => 40, 'weight_uts' => 30, 'weight_uas' => 30];
        }

        View::render('academic/weights', [
            'title' => 'Pengaturan Bobot Nilai',
            'year' => $activeYear,
            'weight' => $weight
        ]);
    }

    public function storeWeights() {
        $yearId = $_POST['academic_year_id'];
        $daily = $_POST['weight_daily'];
        $uts = $_POST['weight_uts'];
        $uas = $_POST['weight_uas'];

        if (($daily + $uts + $uas) != 100) {
            Session::setFlash('error', 'Total bobot harus pas 100%. Saat ini: ' . ($daily + $uts + $uas) . '%');
            header('Location: /academic/weights');
            exit;
        }

        $db = Database::getInstance();
        $check = $db->query("SELECT id FROM grading_weights WHERE academic_year_id = ?", [$yearId])->fetch();
        
        if ($check) {
            $db->query("UPDATE grading_weights SET weight_daily=?, weight_uts=?, weight_uas=? WHERE id=?", [$daily, $uts, $uas, $check['id']]);
        } else {
            $db->query("INSERT INTO grading_weights (academic_year_id, weight_daily, weight_uts, weight_uas) VALUES (?, ?, ?, ?)", [$yearId, $daily, $uts, $uas]);
        }

        Session::setFlash('success', 'Konfigurasi bobot nilai berhasil disimpan.');
        header('Location: /academic/weights');
    }
}
