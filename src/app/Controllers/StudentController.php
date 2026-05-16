<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class StudentController {

    public function __construct() {
        Middleware::auth();
        if (Session::get('user_role') !== 'siswa') {
            header('Location: /dashboard');
            exit;
        }
        // Set flag siswa aktif ke session agar sidebar bisa membacanya
        if (Session::get('is_active_student') === null) {
            $student = $this->getActiveStudent(Session::get('user_id'));
            Session::set('is_active_student', $student ? true : false);
        }
    }

    public function index() { $this->dashboard(); }

    // Helper: ambil data kandidat berdasarkan user_id
    private function getCandidate($userId) {
        $db = Database::getInstance();
        return $db->query(
            "SELECT sc.*, t.name as track_name, t.level as track_level
             FROM student_candidates sc
             LEFT JOIN ppdb_tracks t ON sc.ppdb_track_id = t.id
             WHERE sc.user_id = ?",
            [$userId]
        )->fetch();
    }

    private function getActiveStudent($userId) {
        $db = Database::getInstance();
        return $db->query(
            "SELECT s.*, c.name as class_name FROM students s
             LEFT JOIN classrooms c ON s.classroom_id = c.id
             WHERE s.user_id = ?",
            [$userId]
        )->fetch();
    }

    // =========================================================================
    // 1. DASHBOARD
    // =========================================================================
    public function dashboard() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        // Siswa aktif
        $student = $this->getActiveStudent($userId);
        if ($student) {
            $unpaidBills = $db->query("SELECT COUNT(*) FROM bills WHERE student_id = ? AND status = 'UNPAID'", [$student['id']])->fetchColumn();
            View::render('student/dashboard', [
                'title' => 'Dashboard Siswa',
                'student' => $student,
                'is_active' => true,
                'unpaid_bills' => $unpaidBills
            ]);
            return;
        }

        // Calon santri
        $candidate = $this->getCandidate($userId);
        if ($candidate) {
            $payment = $db->query("SELECT status FROM ppdb_payments WHERE candidate_id = ? ORDER BY id DESC LIMIT 1", [$candidate['id']])->fetch();
            $docCount = $db->query("SELECT COUNT(*) FROM ppdb_documents WHERE candidate_id = ?", [$candidate['id']])->fetchColumn();
            $progress = [
                'registered' => true,
                'paid'       => in_array($payment['status'] ?? '', ['VERIFIED']),
                'document'   => $docCount > 0,
                'verified'   => in_array($candidate['registration_status'], ['VERIFIED','ACCEPTED']),
            ];
            View::render('student/dashboard', [
                'title'     => 'Panel Santri',
                'candidate' => $candidate,
                'progress'  => $progress,
                'is_active' => false,
            ]);
            return;
        }

        die("Akun tidak terhubung ke data siswa. Hubungi Admin.");
    }

    // =========================================================================
    // 2. PROFIL
    // =========================================================================
    public function profile() {
        $userId = Session::get('user_id');

        $student = $this->getActiveStudent($userId);
        if ($student) {
            $father = [
                'name'          => $student['father_name'] ?? null,
                'nik'           => null,
                'phone_number'  => $student['father_phone'] ?? null,
                'job'           => $student['father_job'] ?? null,
                'education'     => null,
            ];
            $mother = [
                'name'          => $student['mother_name'] ?? null,
                'nik'           => null,
                'phone_number'  => $student['mother_phone'] ?? null,
                'job'           => $student['mother_job'] ?? null,
                'education'     => null,
            ];
            // Alias agar kompatibel dengan view
            $student['place_of_birth'] = $student['birth_place'] ?? null;
            $student['date_of_birth']  = $student['birth_date'] ?? null;

            View::render('student/profile', [
                'title'        => 'Profil Saya',
                'student'      => $student,
                'father'       => $father,
                'mother'       => $mother,
                'is_candidate' => false,
            ]);
            return;
        }

        $candidate = $this->getCandidate($userId);
        if ($candidate) {
            View::render('student/profile', ['title' => 'Data Santri', 'candidate' => $candidate, 'is_candidate' => true]);
            return;
        }

        die("Data profil tidak ditemukan.");
    }

    // =========================================================================
    // 3. BIODATA
    // =========================================================================
    public function biodata() {
        $userId = Session::get('user_id');

        $student = $this->getActiveStudent($userId);
        if ($student) {
            // Alias key agar kompatibel dengan view biodata
            $student['date_of_birth']      = $student['birth_date'] ?? null;
            $student['place_of_birth']     = $student['birth_place'] ?? null;
            $student['name']               = $student['full_name'] ?? null;
            $student['nik']                = null; // kolom tidak ada di tabel students
            $student['no_kk']              = null;
            $student['school_origin']      = null;
            $student['school_origin_npsn'] = null;
            $student['birth_order']        = null;
            $student['number_of_siblings'] = null;

            $father = [
                'name'          => $student['father_name'] ?? null,
                'nik'           => null,
                'phone_number'  => $student['father_phone'] ?? null,
                'place_of_birth'=> null,
                'date_of_birth' => null,
                'address'       => null,
                'education'     => null,
                'job'           => $student['father_job'] ?? null,
                'income_per_month' => null,
            ];
            $mother = [
                'name'          => $student['mother_name'] ?? null,
                'nik'           => null,
                'phone_number'  => $student['mother_phone'] ?? null,
                'place_of_birth'=> null,
                'date_of_birth' => null,
                'address'       => null,
                'education'     => null,
                'job'           => $student['mother_job'] ?? null,
                'income_per_month' => null,
            ];

            View::render('student/biodata', [
                'title'     => 'Biodata',
                'student'   => $student,
                'candidate' => null,
                'father'    => $father,
                'mother'    => $mother,
            ]);
            return;
        }

        $candidate = $this->getCandidate($userId);
        if ($candidate) {
            // Map candidate fields ke format yang diharapkan view biodata
            $studentMapped = [
                'full_name'          => $candidate['full_name'],
                'name'               => $candidate['full_name'],
                'nisn'               => $candidate['nisn'],
                'nik'                => $candidate['nik'],
                'no_kk'              => $candidate['kk_number'],
                'school_origin'      => $candidate['school_origin'],
                'school_origin_npsn' => $candidate['npsn'],
                'place_of_birth'     => $candidate['birth_place'],
                'date_of_birth'      => $candidate['birth_date'],
                'gender'             => $candidate['gender'],
                'address'            => $candidate['address'],
                'birth_order'        => $candidate['child_order'],
                'number_of_siblings' => $candidate['siblings_count'],
                'province'           => $candidate['province'],
                'city'               => $candidate['city'],
                'district'           => $candidate['district'],
                'village'            => $candidate['village'],
                'postal_code'        => $candidate['postal_code'],
                'whatsapp_number'    => $candidate['whatsapp_number'],
                'father_name'        => $candidate['father_name'],
                'father_phone'       => $candidate['father_phone'],
                'father_job'         => $candidate['father_job'],
                'mother_name'        => $candidate['mother_name'],
                'mother_phone'       => $candidate['mother_phone'],
                'mother_job'         => $candidate['mother_job'],
                'registration_no'    => $candidate['registration_no'],
                'registration_status'=> $candidate['registration_status'],
                'track_name'         => $candidate['track_name'],
            ];
            View::render('student/biodata', [
                'title'     => 'Data Santri / Biodata',
                'student'   => $studentMapped,
                'candidate' => $candidate,
                'father'    => [
                    'name'          => $candidate['father_name'],
                    'phone'         => $candidate['father_phone'],
                    'job'           => $candidate['father_job'],
                    'education'     => $candidate['father_education'],
                    'income'        => $candidate['father_income'],
                    'nik'           => $candidate['father_nik'],
                    'place_of_birth'=> null,
                    'date_of_birth' => null,
                    'address'       => null,
                    'email'         => null,
                ],
                'mother'    => [
                    'name'          => $candidate['mother_name'],
                    'phone'         => $candidate['mother_phone'],
                    'job'           => $candidate['mother_job'],
                    'education'     => $candidate['mother_education'],
                    'income'        => $candidate['mother_income'],
                    'nik'           => $candidate['mother_nik'],
                    'place_of_birth'=> null,
                    'date_of_birth' => null,
                    'address'       => null,
                    'email'         => null,
                ],
            ]);
            return;
        }

        Session::setFlash('error', 'Data tidak ditemukan.');
        header('Location: /student/dashboard');
        exit;
    }

    // =========================================================================
    // 4. PEMBAYARAN
    // =========================================================================
    public function payment() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        $student = $this->getActiveStudent($userId);
        if ($student) {
            $bills = $db->query(
                "SELECT b.*, ft.name as fee_type_name FROM bills b
                 LEFT JOIN fee_types ft ON b.fee_type_id = ft.id
                 WHERE b.student_id = ? ORDER BY b.created_at DESC",
                [$student['id']]
            )->fetchAll();
            View::render('student/payment', [
                'title'   => 'Keuangan Saya',
                'student' => $student,
                'bills'   => $bills,
            ]);
            return;
        }

        $candidate = $this->getCandidate($userId);
        if ($candidate) {
            $payment = $db->query("SELECT * FROM ppdb_payments WHERE candidate_id = ? ORDER BY id DESC LIMIT 1", [$candidate['id']])->fetch();
            View::render('student/payment', ['title' => 'Pembayaran Pendaftaran', 'payment' => $payment, 'candidate' => $candidate]);
            return;
        }

        header('Location: /student/dashboard');
        exit;
    }

    public function storePayment() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $candidate = $this->getCandidate($userId);
        if (!$candidate) { header('Location: /student/dashboard'); exit; }

        if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['proof_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','pdf'])) {
                $newFilename = 'PAY-' . $candidate['id'] . '-' . time() . '.' . $ext;
                $dest = __DIR__ . '/../../public/uploads/payments/' . $newFilename;
                if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
                if (move_uploaded_file($_FILES['proof_file']['tmp_name'], $dest)) {
                    $db->query("INSERT INTO ppdb_payments (candidate_id, amount, payment_date, proof_file, status) VALUES (?,?,?,?,'PENDING')", [
                        $candidate['id'], $_POST['amount'] ?? 0, $_POST['payment_date'] ?? date('Y-m-d'), $newFilename
                    ]);
                    Session::setFlash('success', 'Bukti pembayaran dikirim. Tunggu verifikasi.');
                } else { Session::setFlash('error', 'Gagal upload.'); }
            } else { Session::setFlash('error', 'Format salah. Gunakan JPG, PNG, atau PDF.'); }
        } else { Session::setFlash('error', 'Pilih file bukti transfer.'); }
        header('Location: /student/payment');
    }

    // =========================================================================
    // 5. DOKUMEN
    // =========================================================================
    public function documents() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        $student = $db->query("SELECT id FROM students WHERE user_id = ?", [$userId])->fetch();
        $targetId = $student ? $student['id'] : ($this->getCandidate($userId)['id'] ?? null);

        if (!$targetId) {
            Session::setFlash('error', 'Data siswa tidak ditemukan.');
            header('Location: /student/dashboard');
            exit;
        }

        $docsRaw = $db->query("SELECT * FROM ppdb_documents WHERE candidate_id = ?", [$targetId])->fetchAll();
        $documents = [];
        foreach ($docsRaw as $d) $documents[$d['doc_type']] = $d;

        View::render('student/documents', ['title' => 'Kelengkapan Dokumen', 'documents' => $documents, 'student_id' => $targetId]);
    }

    public function storeDocument() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $student = $db->query("SELECT id FROM students WHERE user_id = ?", [$userId])->fetch();
        $targetId = $student ? $student['id'] : ($this->getCandidate($userId)['id'] ?? null);
        if (!$targetId) { header('Location: /student/dashboard'); exit; }

        $type = $_POST['doc_type'] ?? 'LAINNYA';
        if (isset($_FILES['doc_file']) && $_FILES['doc_file']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['doc_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','pdf'])) {
                $newFilename = $type . '-' . $targetId . '-' . time() . '.' . $ext;
                $dest = __DIR__ . '/../../public/uploads/documents/' . $newFilename;
                if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
                if (move_uploaded_file($_FILES['doc_file']['tmp_name'], $dest)) {
                    $exist = $db->query("SELECT id FROM ppdb_documents WHERE candidate_id = ? AND doc_type = ?", [$targetId, $type])->fetch();
                    if ($exist) $db->query("UPDATE ppdb_documents SET file_path = ?, status = 'PENDING' WHERE id = ?", [$newFilename, $exist['id']]);
                    else $db->query("INSERT INTO ppdb_documents (candidate_id, doc_type, file_path, status) VALUES (?,?,?,'PENDING')", [$targetId, $type, $newFilename]);
                    Session::setFlash('success', "Dokumen $type berhasil diupload.");
                } else { Session::setFlash('error', 'Gagal upload file.'); }
            } else { Session::setFlash('error', 'Format file salah (JPG/PNG/PDF only).'); }
        }
        header('Location: /student/documents');
    }

    // =========================================================================
    // 6. JADWAL PELAJARAN (SISWA AKTIF)
    // =========================================================================
    public function schedule() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        $student = $this->getActiveStudent($userId);
        if (!$student) { header('Location: /student/dashboard'); exit; }

        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1 LIMIT 1")->fetch();
        $schedules = [];
        if ($activeYear) {
            $schedules = $db->query(
                "SELECT sch.day, sch.start_time, sch.end_time, s.name as subject_name, t.full_name as teacher_name
                 FROM schedules sch
                 JOIN subjects s ON sch.subject_id = s.id
                 LEFT JOIN teachers t ON sch.teacher_id = t.id
                 WHERE sch.classroom_id = ? AND sch.academic_year_id = ?
                 ORDER BY FIELD(sch.day,'SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','AHAD'), sch.start_time",
                [$student['classroom_id'], $activeYear['id']]
            )->fetchAll();
        }

        // Kelompokkan per hari (konversi ke title case agar cocok dengan view)
        $byDay = [];
        foreach ($schedules as $s) {
            $day = ucfirst(strtolower($s['day']));
            $byDay[$day][] = $s;
        }

        View::render('student/schedule', [
            'title'   => 'Jadwal Pelajaran',
            'student' => $student,
            'byDay'   => $byDay,
        ]);
    }

    // =========================================================================
    // 6b. REKAP ABSENSI (SISWA AKTIF)
    // =========================================================================
    public function attendance() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        $student = $this->getActiveStudent($userId);
        if (!$student) { header('Location: /student/dashboard'); exit; }

        $month = $_GET['month'] ?? date('Y-m');

        $logs = $db->query(
            "SELECT date, status, notes FROM attendances
             WHERE student_id = ? AND DATE_FORMAT(date,'%Y-%m') = ?
             ORDER BY date ASC",
            [$student['id'], $month]
        )->fetchAll();

        // Rekap
        $recap = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        foreach ($logs as $l) {
            if (isset($recap[$l['status']])) $recap[$l['status']]++;
        }

        View::render('student/attendance', [
            'title'   => 'Rekap Absensi',
            'student' => $student,
            'logs'    => $logs,
            'recap'   => $recap,
            'month'   => $month,
        ]);
    }

    // =========================================================================
    // 6c. NILAI AKADEMIK (SISWA AKTIF)
    // =========================================================================
    public function grades() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        $student = $this->getActiveStudent($userId);
        if (!$student) { header('Location: /student/dashboard'); exit; }

        $activeYear = $db->query("SELECT id, name FROM academic_years WHERE is_active = 1 LIMIT 1")->fetch();
        $weights = $activeYear ? ($db->query("SELECT * FROM grading_weights WHERE academic_year_id = ?", [$activeYear['id']])->fetch() ?: ['weight_daily'=>40,'weight_uts'=>30,'weight_uas'=>30]) : ['weight_daily'=>40,'weight_uts'=>30,'weight_uas'=>30];
        $wd = $weights['weight_daily'] / 100;
        $wu = $weights['weight_uts']   / 100;
        $wa = $weights['weight_uas']   / 100;

        $grades = $db->query(
            "SELECT s.name as subject_name, s.kkm,
                ROUND(AVG(CASE WHEN sg.type = 'HARIAN' THEN sg.score END), 1) as task_score,
                MAX(CASE WHEN sg.type = 'UTS' THEN sg.score END) as mid_score,
                MAX(CASE WHEN sg.type = 'UAS' THEN sg.score END) as final_exam_score
             FROM student_grades sg
             JOIN schedules sch ON sg.schedule_id = sch.id
             JOIN subjects s ON sch.subject_id = s.id
             WHERE sg.student_id = ?
             GROUP BY s.id, s.name, s.kkm
             ORDER BY s.name ASC",
            [$student['id']]
        )->fetchAll();

        // Hitung nilai akhir berbobot
        foreach ($grades as &$g) {
            $daily = $g['task_score'] ?? null;
            $uts   = $g['mid_score'] ?? null;
            $uas   = $g['final_exam_score'] ?? null;
            if ($daily !== null || $uts !== null || $uas !== null) {
                $g['final_score'] = round(
                    (($daily ?? 0) * $wd) + (($uts ?? 0) * $wu) + (($uas ?? 0) * $wa), 1
                );
            } else {
                $g['final_score'] = null;
            }
        }
        unset($g);

        View::render('student/grades', [
            'title'       => 'Nilai Akademik',
            'student'     => $student,
            'grades'      => $grades,
            'activeYear'  => $activeYear,
        ]);
    }

    // =========================================================================
    // 7. RESUME PENDAFTARAN
    // =========================================================================
    public function resume() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();

        $candidate = $this->getCandidate($userId);
        if ($candidate) {
            $payment = $db->query("SELECT * FROM ppdb_payments WHERE candidate_id = ? ORDER BY id DESC LIMIT 1", [$candidate['id']])->fetch();
            $docs = $db->query("SELECT * FROM ppdb_documents WHERE candidate_id = ?", [$candidate['id']])->fetchAll();
            View::render('student/resume', [
                'title'     => 'Resume Pendaftaran',
                'candidate' => $candidate,
                'payment'   => $payment,
                'docs'      => $docs,
            ]);
            return;
        }

        $student = $this->getActiveStudent($userId);
        if ($student) {
            View::render('student/resume', ['title' => 'Resume Siswa', 'student' => $student, 'candidate' => null]);
            return;
        }

        header('Location: /student/dashboard');
    }
    // billing alias → redirect ke finance/billing
    public function billing() { $this->payment(); }

    // =========================================================================
    // 8. PENGUMUMAN
    // =========================================================================
    public function announcements() {
        $db = Database::getInstance();
        $announcements = $db->query(
            "SELECT * FROM announcements WHERE status = 'PUBLISHED' AND (target_audience IN ('ALL','STUDENTS')) ORDER BY created_at DESC LIMIT 30"
        )->fetchAll();
        View::render('student/announcements', ['title' => 'Pengumuman', 'announcements' => $announcements]);
    }

    // =========================================================================
    // 9. EKSTRAKURIKULER
    // =========================================================================
    public function extracurricular() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $student = $this->getActiveStudent($userId);
        if (!$student) { header('Location: /student/dashboard'); exit; }

        try {
            $myEkskul = $db->query(
                "SELECT e.name, e.description, e.status,
                        es.day_name as schedule_day, es.start_time as schedule_time, es.location,
                        (SELECT u.name FROM extracurricular_coaches ec JOIN users u ON ec.user_id=u.id WHERE ec.extracurricular_id=e.id LIMIT 1) as coach_name
                 FROM student_extracurriculars se
                 JOIN extracurriculars e ON se.extracurricular_id = e.id
                 LEFT JOIN extracurricular_schedules es ON es.extracurricular_id = e.id
                 WHERE se.student_id = ?
                 GROUP BY e.id",
                [$student['id']]
            )->fetchAll();
        } catch (\Exception $e) {
            $myEkskul = [];
        }

        try {
            $attendance = $db->query(
                "SELECT ea.date, ea.status, e.name as ekskul_name
                 FROM extracurricular_attendances ea
                 JOIN extracurricular_schedules es ON ea.schedule_id = es.id
                 JOIN extracurriculars e ON es.extracurricular_id = e.id
                 WHERE ea.student_id = ?
                 ORDER BY ea.date DESC LIMIT 20",
                [$student['id']]
            )->fetchAll();
        } catch (\Exception $e) {
            $attendance = [];
        }

        View::render('student/extracurricular', [
            'title'      => 'Ekstrakurikuler',
            'student'    => $student,
            'myEkskul'   => $myEkskul,
            'attendance' => $attendance,
        ]);
    }

    // =========================================================================
    // 10. ASRAMA / BOARDING
    // =========================================================================
    public function boarding() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $student = $this->getActiveStudent($userId);
        if (!$student) { header('Location: /student/dashboard'); exit; }

        $dorm = null;
        if (!empty($student['dorm_id'])) {
            $dorm = $db->query("SELECT * FROM dorms WHERE id = ?", [$student['dorm_id']])->fetch();
        }

        $activeYear = $db->query("SELECT id FROM academic_years WHERE is_active = 1 LIMIT 1")->fetch();
        $boardingGrades = $activeYear ? $db->query(
            "SELECT * FROM boarding_grades WHERE student_id = ? AND academic_year_id = ?",
            [$student['id'], $activeYear['id']]
        )->fetchAll() : [];

        try {
            $worshipLogs = $db->query(
                "SELECT wl.*, u.name as teacher_name FROM worship_logs wl
                 LEFT JOIN users u ON wl.teacher_id = u.id
                 WHERE wl.student_id = ? ORDER BY wl.date DESC LIMIT 20",
                [$student['id']]
            )->fetchAll();
        } catch (\Exception $e) {
            $worshipLogs = [];
        }

        try {
            $permits = $db->query(
                "SELECT * FROM permits WHERE student_id = ? ORDER BY created_at DESC LIMIT 10",
                [$student['id']]
            )->fetchAll();
        } catch (\Exception $e) {
            $permits = [];
        }

        View::render('student/boarding', [
            'title'         => 'Asrama',
            'student'       => $student,
            'dorm'          => $dorm,
            'boardingGrades'=> $boardingGrades,
            'worshipLogs'   => $worshipLogs,
            'permits'       => $permits,
        ]);
    }

    // =========================================================================
    // 11. PELANGGARAN & PRESTASI
    // =========================================================================
    public function discipline() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $student = $this->getActiveStudent($userId);
        if (!$student) { header('Location: /student/dashboard'); exit; }

        try {
            $violations = $db->query(
                "SELECT sv.date, sv.note, vt.name as type_name, vt.points, vt.category
                 FROM student_violations sv
                 JOIN violation_types vt ON sv.violation_type_id = vt.id
                 WHERE sv.student_id = ? ORDER BY sv.date DESC",
                [$student['id']]
            )->fetchAll();
        } catch (\Exception $e) {
            $violations = [];
        }

        try {
            $achievements = $db->query(
                "SELECT * FROM student_achievements WHERE student_id = ? ORDER BY date DESC",
                [$student['id']]
            )->fetchAll();
        } catch (\Exception $e) {
            $achievements = [];
        }

        $totalPoints = array_sum(array_column($violations, 'points'));

        View::render('student/discipline', [
            'title'        => 'Pelanggaran & Prestasi',
            'student'      => $student,
            'violations'   => $violations,
            'achievements' => $achievements,
            'totalPoints'  => $totalPoints,
        ]);
    }

    // =========================================================================
    // 12. SURAT KETERANGAN
    // =========================================================================
    public function letter() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $student = $this->getActiveStudent($userId);
        if (!$student) { header('Location: /student/dashboard'); exit; }

        try {
            $templates = $db->query("SELECT id, code, name FROM letter_templates ORDER BY name")->fetchAll();
        } catch (\Exception $e) {
            $templates = [];
        }
        View::render('student/letter', [
            'title'     => 'Surat Keterangan',
            'student'   => $student,
            'templates' => $templates,
        ]);
    }

    public function printLetter() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $student = $this->getActiveStudent($userId);
        if (!$student) { header('Location: /student/dashboard'); exit; }

        $code = $_GET['code'] ?? '';
        $template = $db->query("SELECT * FROM letter_templates WHERE code = ?", [$code])->fetch();
        if (!$template) { header('Location: /student/letter'); exit; }

        $classroom = $db->query("SELECT name FROM classrooms WHERE id = ?", [$student['classroom_id']])->fetch();
        $school = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'school_name'")->fetchColumn();

        $content = str_replace(
            ['{nama}', '{nis}', '{kelas}', '{sekolah}', '{tanggal}'],
            [
                $student['full_name'],
                $student['nis'],
                $classroom['name'] ?? '-',
                $school ?: 'Pesantren Thawalib Parabek',
                date('d F Y'),
            ],
            $template['content']
        );

        View::render('student/print_letter', [
            'title'    => $template['name'],
            'student'  => $student,
            'template' => $template,
            'content'  => $content,
            'classroom'=> $classroom,
        ]);
    }

    // =========================================================================
    // 13. KESEHATAN (POSKESTREN)
    // =========================================================================
    public function health() {
        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $student = $this->getActiveStudent($userId);
        if (!$student) { header('Location: /student/dashboard'); exit; }

        try {
            $records = $db->query(
                "SELECT hr.*, u.name as officer_name FROM health_records hr
                 LEFT JOIN users u ON hr.officer_id = u.id
                 WHERE hr.student_id = ? ORDER BY hr.date DESC",
                [$student['id']]
            )->fetchAll();
        } catch (\Exception $e) {
            $records = [];
        }

        View::render('student/health', [
            'title'   => 'Riwayat Kesehatan',
            'student' => $student,
            'records' => $records,
        ]);
    }

    public function examCard() { $this->printExamCard(); }

    public function printExamCard() {
        $userId = Session::get('user_id');

        // Siswa aktif: tidak ada kartu ujian PPDB
        $student = $this->getActiveStudent($userId);
        if ($student) {
            Session::setFlash('info', 'Belum ada jadwal ujian semester yang aktif.');
            header('Location: /student/dashboard');
            exit;
        }

        $candidate = $this->getCandidate($userId);
        if ($candidate) {
            if (!in_array($candidate['registration_status'], ['VERIFIED','ACCEPTED','PAID'])) {
                Session::setFlash('error', 'Status pendaftaran belum diverifikasi. Tunggu konfirmasi admin.');
                header('Location: /student/dashboard');
                exit;
            }
            View::render('student/exam_card', [
                'title'   => 'Kartu Ujian',
                'student' => [
                    'full_name'  => $candidate['full_name'],
                    'nis'        => $candidate['registration_no'],
                    'class_name' => 'Calon Santri - ' . ($candidate['track_name'] ?? 'Reguler'),
                    'photo'      => null,
                ],
                'exam' => [
                    'period'   => 'Seleksi Masuk PPDB',
                    'dates'    => 'Ahad, 20 Juli 2026',
                    'location' => 'Aula Utama Pesantren',
                ],
            ]);
            return;
        }

        header('Location: /student/dashboard');
    }
}
