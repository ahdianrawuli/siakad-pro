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
        $classrooms = $db->query("SELECT * FROM classrooms ORDER BY level, name")->fetchAll();
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
        $limit = $_GET['limit'] ?? 10;
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
            SELECT sch.*, s.name as subject_name, c.name as class_name, sch.academic_year_id
            FROM schedules sch
            JOIN subjects s ON sch.subject_id = s.id 
            JOIN classrooms c ON sch.classroom_id = c.id
            WHERE sch.id = ?
        ", [$scheduleId])->fetch();

        if (!$schedule) die("Jadwal tidak ditemukan.");

        $students = $db->query("
            SELECT * FROM students 
            WHERE classroom_id = ? 
            ORDER BY full_name ASC
        ", [$schedule['classroom_id']])->fetchAll();

        // Ambil Data Nilai Mentah
        $gradesRaw = $db->query("SELECT * FROM student_grades WHERE schedule_id = ?", [$scheduleId])->fetchAll();
        
        // MAPPING DATABASE (ENUM) KE VIEW
        // Kita petakan langsung sesuai kolom yang ada di database
        $gradeMap = [];
        foreach ($gradesRaw as $g) {
            // Key array sesuai dengan ENUM di database: 'UH1', 'UH2', 'TUGAS', 'UTS', 'UAS'
            $gradeMap[$g['student_id']][$g['type']] = $g['score'];
        }

        $weights = $db->query("
            SELECT * FROM grading_weights WHERE academic_year_id = ?
        ", [$schedule['academic_year_id']])->fetch();

        if (!$weights) {
            $weights = ['weight_daily' => 40, 'weight_uts' => 30, 'weight_uas' => 30]; 
        }

        View::render('academic/grades_form', [
            'title' => 'Input Nilai: ' . $schedule['subject_name'],
            'schedule' => $schedule,
            'students' => $students,
            'gradeMap' => $gradeMap,
            'weights' => $weights 
        ]);
    }

    public function storeGrades() {
        $scheduleId = $_POST['schedule_id'];
        $grades = $_POST['grades'] ?? []; 
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        try {
            $db->getConnection()->beginTransaction();

            foreach ($grades as $studentId => $types) {
                // $type sekarang akan berisi: 'UH1', 'UH2', 'TUGAS', 'UTS', 'UAS'
                foreach ($types as $type => $score) {
                    
                    // Skip jika kosong (tapi izinkan nilai 0)
                    if ($score === '' || $score === null) continue;

                    // Validasi Tipe agar sesuai ENUM Database
                    $validTypes = ['UH1', 'UH2', 'TUGAS', 'UTS', 'UAS'];
                    if (!in_array($type, $validTypes)) continue;

                    $exist = $db->query("
                        SELECT id FROM student_grades 
                        WHERE student_id = ? AND schedule_id = ? AND type = ?
                    ", [$studentId, $scheduleId, $type])->fetch();

                    if ($exist) {
                        $db->query("
                            UPDATE student_grades SET score = ?, created_by = ? 
                            WHERE id = ?
                        ", [$score, $userId, $exist['id']]);
                    } else {
                        $db->query("
                            INSERT INTO student_grades (student_id, schedule_id, type, score, created_by)
                            VALUES (?, ?, ?, ?, ?)
                        ", [$studentId, $scheduleId, $type, $score, $userId]);
                    }
                }
            }

            $db->getConnection()->commit();
            Session::setFlash('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal menyimpan nilai. Pastikan input valid.');
        }

        header("Location: /academic/grades/manage?schedule_id=$scheduleId");
    }

    // ==========================================================
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
        $role = Session::get('user_role');
        $yearId = $db->query("SELECT id FROM academic_years WHERE is_active = 1")->fetch()['id'] ?? 0;

        $sql = "
            SELECT sch.*, s.name as subject_name, c.name as class_name, 
                   (SELECT COUNT(*) FROM teaching_journals tj WHERE tj.schedule_id = sch.id) as total_entries
            FROM schedules sch
            JOIN subjects s ON sch.subject_id = s.id
            JOIN classrooms c ON sch.classroom_id = c.id
            WHERE sch.academic_year_id = :yearId
        ";

        if ($role == 'guru') {
            $sql .= " AND sch.teacher_id = :uid";
            $schedules = $db->query($sql, ['yearId' => $yearId, 'uid' => $userId])->fetchAll();
        } else {
            $schedules = $db->query($sql, ['yearId' => $yearId])->fetchAll();
        }

        View::render('academic/journal_index', ['title' => 'Jurnal Mengajar', 'schedules' => $schedules]);
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
