<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

class StudentAffairsController {
    public function __construct() {
        Middleware::auth();
    }

    // LIST DATA SISWA (DENGAN FILTER & PAGINATION)
    public function index() {
        $db = Database::getInstance();

        $page      = max(1, (int)($_GET['page']       ?? 1));
        $limit     = (int)($_GET['limit']     ?? 10);
        $offset    = ($page - 1) * $limit;
        $search    = $_GET['search']    ?? '';
        $classId   = $_GET['class_id']  ?? '';
        $status    = $_GET['status']    ?? 'ACTIVE';

        [$scopeWhere, $scopeParams] = ScopeFilter::apply('c');

        $where  = "WHERE 1=1" . $scopeWhere;
        $params = $scopeParams;
        if (!empty($search))  { $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ? OR c.name LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($classId)) { $where .= " AND s.classroom_id = ?"; $params[] = $classId; }
        if ($status !== 'ALL') { $where .= " AND s.status = ?"; $params[] = $status ?: 'ACTIVE'; }

        $countQuery = "SELECT COUNT(*) FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id $where";
        $totalData  = $db->query($countQuery, $params)->fetchColumn();
        $totalPages = max(1, ceil($totalData / $limit));

        $students   = $db->query(
            "SELECT s.*, c.name as class_name FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id $where ORDER BY c.name ASC, s.full_name ASC LIMIT $limit OFFSET $offset",
            $params
        )->fetchAll();
        $classrooms = $db->query("SELECT * FROM classrooms c WHERE 1=1 $scopeWhere ORDER BY name", $scopeParams)->fetchAll();
        $scope      = ScopeFilter::get();
        $dormWhere  = $scope !== 'GLOBAL' ? "WHERE unit = '$scope'" : "";
        $dorms      = $db->query("SELECT id, name FROM dorms $dormWhere ORDER BY name")->fetchAll();

        View::render('student_affairs/index', [
            'title'       => 'Data Induk Siswa',
            'students'    => $students,
            'classrooms'  => $classrooms,
            'dorms'       => $dorms,
            'totalData'   => $totalData,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'limit'       => $limit,
            'search'      => $search,
            'classId'     => $classId,
            'status'      => $status,
        ]);
    }

    // SIMPAN SISWA BARU
    public function store() {
        $db  = Database::getInstance();
        $nis = trim($_POST['nis'] ?? '');
        if ($db->query("SELECT id FROM students WHERE nis = ?", [$nis])->fetch()) {
            Session::setFlash('error', 'NIS sudah terdaftar!');
            header('Location: /student-affairs/students'); exit;
        }
        $db->query(
            "INSERT INTO students (nis, nisn, full_name, gender, birth_place, birth_date, address, classroom_id,
             father_name, father_job, father_phone, mother_name, mother_job, mother_phone,
             guardian_name, guardian_relation, guardian_phone, status)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'ACTIVE')",
            [
                $nis, $_POST['nisn'] ?? null, $_POST['full_name'], $_POST['gender'],
                $_POST['birth_place'] ?? null, $_POST['birth_date'] ?: null, $_POST['address'] ?? null,
                $_POST['classroom_id'] ?: null,
                $_POST['father_name'] ?? null, $_POST['father_job'] ?? null, $_POST['father_phone'] ?? null,
                $_POST['mother_name'] ?? null, $_POST['mother_job'] ?? null, $_POST['mother_phone'] ?? null,
                $_POST['guardian_name'] ?? null, $_POST['guardian_relation'] ?? null, $_POST['guardian_phone'] ?? null,
            ]
        );
        Session::setFlash('success', 'Siswa berhasil ditambahkan.');
        header('Location: /student-affairs/students');
    }

    // UPDATE DATA SISWA (LENGKAP)
    public function update() {
        $db = Database::getInstance();
        $id = (int)$_POST['id'];
        $db->query(
            "UPDATE students SET nis=?, nisn=?, full_name=?, gender=?, birth_place=?, birth_date=?, address=?,
             classroom_id=?, dorm_id=?, status=?,
             father_name=?, father_job=?, father_phone=?,
             mother_name=?, mother_job=?, mother_phone=?,
             guardian_name=?, guardian_relation=?, guardian_phone=?, guardian_address=?
             WHERE id=?",
            [
                $_POST['nis'], $_POST['nisn'] ?? null, $_POST['full_name'], $_POST['gender'],
                $_POST['birth_place'] ?? null, $_POST['birth_date'] ?: null, $_POST['address'] ?? null,
                $_POST['classroom_id'] ?: null, $_POST['dorm_id'] ?: null, $_POST['status'] ?? 'ACTIVE',
                $_POST['father_name'] ?? null, $_POST['father_job'] ?? null, $_POST['father_phone'] ?? null,
                $_POST['mother_name'] ?? null, $_POST['mother_job'] ?? null, $_POST['mother_phone'] ?? null,
                $_POST['guardian_name'] ?? null, $_POST['guardian_relation'] ?? null,
                $_POST['guardian_phone'] ?? null, $_POST['guardian_address'] ?? null,
                $id,
            ]
        );
        Session::setFlash('success', 'Data siswa berhasil diperbarui.');
        header('Location: /student-affairs/students');
    }

    // HAPUS SISWA
    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $db = Database::getInstance();
        $db->query("DELETE FROM students WHERE id = ?", [$id]);
        Session::setFlash('success', 'Data siswa berhasil dihapus.');
        header('Location: /student-affairs/students');
    }

    // DETAIL SISWA
    public function detail() {
        $id = (int)($_GET['id'] ?? 0);
        $db = Database::getInstance();
        $student = $db->query(
            "SELECT s.*, c.name as class_name, d.name as dorm_name
             FROM students s
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             LEFT JOIN dorms d ON s.dorm_id = d.id
             WHERE s.id = ?", [$id]
        )->fetch();
        if (!$student) { header('Location: /student-affairs/students'); exit; }

        $grades = $db->query(
            "SELECT sub.name as subject_name, AVG(g.score) as avg_score
             FROM student_grades g JOIN schedules sc ON g.schedule_id = sc.id JOIN subjects sub ON sc.subject_id = sub.id
             WHERE g.student_id = ? GROUP BY sub.id ORDER BY sub.name", [$id]
        )->fetchAll();

        $attendance = $db->query(
            "SELECT status, COUNT(*) as total FROM attendances WHERE student_id = ? GROUP BY status", [$id]
        )->fetchAll();

        $bills = $db->query(
            "SELECT title, amount, status FROM bills WHERE student_id = ? ORDER BY created_at DESC LIMIT 5", [$id]
        )->fetchAll();

        $violations = $db->query(
            "SELECT vt.name, sv.date FROM student_violations sv JOIN violation_types vt ON sv.violation_type_id = vt.id WHERE sv.student_id = ? ORDER BY sv.date DESC LIMIT 5", [$id]
        )->fetchAll();

        View::render('student_affairs/detail', [
            'title'      => 'Profil Siswa',
            'student'    => $student,
            'grades'     => $grades,
            'attendance' => $attendance,
            'bills'      => $bills,
            'violations' => $violations,
        ]);
    }

    // EXPORT EXCEL
    public function export() {
        $db      = Database::getInstance();
        $classId = $_GET['class_id'] ?? '';
        $status  = $_GET['status']   ?? 'ACTIVE';
        $where   = "WHERE 1=1";
        $params  = [];
        if (!empty($classId)) { $where .= " AND s.classroom_id = ?"; $params[] = $classId; }
        if ($status !== 'ALL') { $where .= " AND s.status = ?"; $params[] = $status ?: 'ACTIVE'; }

        $rows = $db->query(
            "SELECT s.nis, s.nisn, s.full_name, s.gender, s.birth_place, s.birth_date, s.address,
                    c.name as class_name, s.status,
                    s.father_name, s.father_phone, s.mother_name, s.mother_phone,
                    s.guardian_name, s.guardian_phone
             FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id $where ORDER BY c.name, s.full_name",
            $params
        )->fetchAll();

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="data-siswa-' . date('Ymd') . '.xls"');
        echo "\xEF\xBB\xBF";
        echo "NIS\tNISN\tNama Lengkap\tL/P\tTempat Lahir\tTgl Lahir\tAlamat\tKelas\tStatus\tNama Ayah\tHP Ayah\tNama Ibu\tHP Ibu\tNama Wali\tHP Wali\n";
        foreach ($rows as $r) {
            echo implode("\t", [
                $r['nis'], $r['nisn'] ?? '', $r['full_name'], $r['gender'],
                $r['birth_place'] ?? '', $r['birth_date'] ?? '', str_replace(["\n","\t"], ' ', $r['address'] ?? ''),
                $r['class_name'] ?? '', $r['status'],
                $r['father_name'] ?? '', $r['father_phone'] ?? '',
                $r['mother_name'] ?? '', $r['mother_phone'] ?? '',
                $r['guardian_name'] ?? '', $r['guardian_phone'] ?? '',
            ]) . "\n";
        }
        exit;
    }

    // IMPORT DARI EXCEL/CSV
    public function import() {
        $file = $_FILES['import_file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'File tidak valid.'); header('Location: /student-affairs/students'); exit;
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            Session::setFlash('error', 'Format file harus CSV.'); header('Location: /student-affairs/students'); exit;
        }

        $db      = Database::getInstance();
        $handle  = fopen($file['tmp_name'], 'r');
        $header  = fgetcsv($handle, 0, ','); // skip header row
        $success = 0; $skip = 0;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (empty($row[0])) continue; // skip baris kosong
            $nis = trim($row[0]);
            if ($db->query("SELECT id FROM students WHERE nis = ?", [$nis])->fetch()) { $skip++; continue; }
            $db->query(
                "INSERT INTO students (nis, nisn, full_name, gender, birth_place, birth_date, address, status) VALUES (?,?,?,?,?,?,?,'ACTIVE')",
                [$nis, $row[1] ?? null, $row[2] ?? '', $row[3] ?? 'L', $row[4] ?? null, $row[5] ?: null, $row[6] ?? null]
            );
            $success++;
        }
        fclose($handle);
        Session::setFlash('success', "Import selesai: $success ditambahkan, $skip dilewati (NIS duplikat).");
        header('Location: /student-affairs/students');
    }

    public function printBiodata() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /student-affairs/students'); exit; }

        $db = Database::getInstance();
        $student = $db->query(
            "SELECT s.*, c.name as class_name FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id WHERE s.id = ?",
            [$id]
        )->fetch();

        if (!$student) { header('Location: /student-affairs/students'); exit; }

        View::render('student_affairs/print_biodata', [
            'title' => 'Biodata Siswa',
            'student' => $student
        ]);
    }

// ==========================================================
    // MODUL ABSENSI SISWA (REVISED)
    // ==========================================================

    // 1. HALAMAN UTAMA: RIWAYAT ABSENSI (History Log)
    public function attendance() {
        $db = Database::getInstance();

        // Parameter Filter & Pagination
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $dateFilter = $_GET['date'] ?? '';
        $classFilter = $_GET['class_id'] ?? '';

        // Base Query
        $where = "WHERE 1=1";
        $params = [];

        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        // Logika Filter
        if (!empty($search)) {
            $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($dateFilter)) {
            $where .= " AND a.date = ?";
            $params[] = $dateFilter;
        }
        if (!empty($classFilter)) {
            $where .= " AND a.classroom_id = ?";
            $params[] = $classFilter;
        }

        // Hitung Total Data
        $countSql = "SELECT COUNT(*) as total FROM attendances a 
                     JOIN students s ON a.student_id = s.id
                     LEFT JOIN classrooms c ON s.classroom_id = c.id
                     $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        // Ambil Data Log
        $sql = "SELECT a.*, s.full_name, s.nis, c.name as class_name, u.name as recorder_name
                FROM attendances a
                JOIN students s ON a.student_id = s.id
                LEFT JOIN classrooms c ON a.classroom_id = c.id
                LEFT JOIN users u ON a.recorded_by = u.id
                $where
                ORDER BY a.date DESC, c.name ASC, s.full_name ASC
                LIMIT $limit OFFSET $offset";
        
        $logs = $db->query($sql, $params)->fetchAll();
        $classrooms = $db->query("SELECT * FROM classrooms ORDER BY name ASC")->fetchAll();

        View::render('student_affairs/attendance', [
            'title' => 'Riwayat Absensi Siswa',
            'logs' => $logs,
            'classrooms' => $classrooms,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'dateFilter' => $dateFilter,
            'classFilter' => $classFilter
        ]);
    }

    // 2. HALAMAN FORM INPUT ABSENSI (Massal per Kelas)
    public function createAttendance() {
        $db = Database::getInstance();
        $classId = $_GET['class_id'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d');
        
        $students = [];
        $existing = [];

        if ($classId) {
            $students = $db->query("SELECT * FROM students WHERE classroom_id = ? AND status = 'ACTIVE' ORDER BY full_name ASC", [$classId])->fetchAll();
            $logs = $db->query("SELECT student_id, status, notes FROM attendances WHERE classroom_id = ? AND date = ?", [$classId, $date])->fetchAll();
            foreach($logs as $l) {
                $existing[$l['student_id']] = ['status' => $l['status'], 'notes' => $l['notes']];
            }
        }

        // Filter dropdown kelas berdasarkan scope
        [$sw, $sp] = ScopeFilter::apply('c');
        $classrooms = $db->query("SELECT c.* FROM classrooms c WHERE 1=1 $sw ORDER BY c.name ASC", $sp)->fetchAll();

        View::render('student_affairs/attendance_form', [
            'title'         => 'Input Absensi Harian',
            'classrooms'    => $classrooms,
            'students'      => $students,
            'selectedClass' => $classId,
            'selectedDate'  => $date,
            'existing'      => $existing
        ]);
    }

    // 3. PROSES SIMPAN ABSENSI
    public function storeAttendance() {
        $classId = $_POST['classroom_id'];
        $date = $_POST['date'];
        $attendanceData = $_POST['attendance'] ?? []; // Array [student_id => status]
        $notesData = $_POST['notes'] ?? []; // Array [student_id => notes]

        if (empty($classId) || empty($date)) {
            Session::setFlash('error', 'Kelas dan Tanggal wajib diisi.');
            header('Location: /attendance/students/create');
            exit;
        }

        $db = Database::getInstance();
        try {
            $db->getConnection()->beginTransaction();

            // Hapus data lama di kelas & tanggal tsb (Reset agar tidak duplikat)
            $db->query("DELETE FROM attendances WHERE classroom_id = ? AND date = ?", [$classId, $date]);

            $sql = "INSERT INTO attendances (student_id, classroom_id, date, status, notes, recorded_by) VALUES (?, ?, ?, ?, ?, ?)";
            $adminId = Session::get('user_id');

            foreach ($attendanceData as $studentId => $status) {
                $note = $notesData[$studentId] ?? null;
                $db->query($sql, [$studentId, $classId, $date, $status, $note, $adminId]);
            }

            $db->getConnection()->commit();

            // Kirim notifikasi WA ke orang tua untuk siswa tidak hadir (A/I/S)
            $this->notifyAbsences($db, $attendanceData, $notesData, $date);

            Session::setFlash('success', 'Data absensi berhasil disimpan.');
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }

        // Redirect kembali ke form input (agar bisa lihat hasilnya)
        header("Location: /attendance/students/create?class_id=$classId&date=$date");
    }

    private function notifyAbsences($db, array $attendanceData, array $notesData, string $date): void {
        $absentStatuses = ['A', 'I', 'S']; // Alpha, Izin, Sakit
        $statusLabel = ['A' => 'Tanpa Keterangan (Alpha)', 'I' => 'Izin', 'S' => 'Sakit'];
        $dateFormatted = date('d F Y', strtotime($date));

        foreach ($attendanceData as $studentId => $status) {
            if (!in_array($status, $absentStatuses)) continue;

            $student = $db->query(
                "SELECT s.full_name, s.parent_phone, c.name as class_name
                 FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id
                 WHERE s.id = ?", [$studentId]
            )->fetch();

            if (empty($student['parent_phone'])) continue;

            $note = !empty($notesData[$studentId]) ? "\nKeterangan: " . $notesData[$studentId] : '';
            $label = $statusLabel[$status] ?? $status;

            $message = "Assalamu'alaikum Wali/Orang Tua,\n\n"
                . "Kami informasikan bahwa putra/putri Anda:\n"
                . "*{$student['full_name']}* (Kelas {$student['class_name']})\n\n"
                . "Tercatat *TIDAK HADIR* pada:\n"
                . "Tanggal : $dateFormatted\n"
                . "Status   : $label"
                . $note . "\n\n"
                . "Mohon konfirmasi ke pihak sekolah jika ada pertanyaan.\n"
                . "Terima kasih. — SIAKAD Parabek";

            \App\Models\WhatsappService::send($student['parent_phone'], $message);
        }
    }

    // 4. HAPUS SATU LOG ABSENSI
    public function deleteAttendance() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = Database::getInstance();
            $db->query("DELETE FROM attendances WHERE id = ?", [$id]);
            Session::setFlash('success', 'Data absensi berhasil dihapus.');
        }
        header('Location: /attendance/students');
    }

}
