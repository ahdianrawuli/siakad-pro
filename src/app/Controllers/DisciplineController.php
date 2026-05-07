<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Models\WhatsappService;

class DisciplineController {
    public function __construct() { Middleware::auth(); }

    // ==========================================================
    // 1. MASTER PELANGGARAN
    // ==========================================================
    public function master() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';

        $sql = "SELECT * FROM master_violations WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR code LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY severity DESC, name ASC";

        try {
            $violations = $db->query($sql, $params)->fetchAll();
        } catch (\Exception $e) {
            // Setup master table if not exists (fallback for UI testing)
            $db->query("CREATE TABLE IF NOT EXISTS `master_violations` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `code` varchar(50) NOT NULL,
                `name` varchar(255) NOT NULL,
                `points` int(11) NOT NULL DEFAULT 0,
                `severity` enum('RINGAN','SEDANG','BERAT') DEFAULT 'RINGAN',
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            )");
            $violations = $db->query($sql, $params)->fetchAll();
        }

        View::render('discipline/master', [
            'title' => 'Master Pelanggaran',
            'violations' => $violations,
            'search' => $search
        ]);
    }

    public function storeMaster() {
        $db = Database::getInstance();
        try {
            $db->query("INSERT INTO master_violations (code, name, points, severity) VALUES (?, ?, ?, ?)", [
                $_POST['code'], $_POST['name'], $_POST['points'], $_POST['severity']
            ]);
            Session::setFlash('success', 'Master Pelanggaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal menambah Master Pelanggaran: ' . $e->getMessage());
        }
        header('Location: /discipline/master-violations');
    }


    // ==========================================================
    // 2. MODUL PELANGGARAN SANTRI (DISIPLIN)
    // ==========================================================
    public function index() {
        $db = Database::getInstance();
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $dateFilter = $_GET['date'] ?? '';

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($dateFilter)) {
            $where .= " AND sv.date = ?";
            $params[] = $dateFilter;
        }

        $countSql = "SELECT COUNT(*) as total FROM student_violations sv JOIN students s ON sv.student_id = s.id $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        $sql = "SELECT sv.*, s.full_name, s.nis, c.name as class_name, 
                       vt.name as violation_name, vt.points, vt.category
                FROM student_violations sv
                JOIN students s ON sv.student_id = s.id
                LEFT JOIN classrooms c ON s.classroom_id = c.id
                JOIN violation_types vt ON sv.violation_type_id = vt.id
                $where
                ORDER BY sv.date DESC, sv.created_at DESC 
                LIMIT $limit OFFSET $offset";

        $violations = $db->query($sql, $params)->fetchAll();
        $types = $db->query("SELECT * FROM violation_types ORDER BY points ASC")->fetchAll();
        $students = $db->query("SELECT id, full_name, nis FROM students WHERE status='ACTIVE' ORDER BY full_name ASC")->fetchAll();

        View::render('discipline/index', [
            'title' => 'Kedisiplinan Siswa', 
            'violations' => $violations,
            'types' => $types,
            'students' => $students,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'dateFilter' => $dateFilter
        ]);
    }

    public function storeViolation() {
        $db = Database::getInstance();
        $studentId = $_POST['student_id'];
        $typeId = $_POST['violation_type_id'];

        $db->query("INSERT INTO student_violations (student_id, violation_type_id, date, note, reported_by) VALUES (?, ?, ?, ?, ?)", [
            $studentId, $typeId, $_POST['date'], $_POST['note'], Session::get('user_id')
        ]);

        // Kirim WA
        $sqlStudent = "SELECT full_name, COALESCE(parent_phone, father_phone, mother_phone, guardian_phone) as whatsapp_number FROM students WHERE id = ?";
        $student = $db->query($sqlStudent, [$studentId])->fetch();
        $violation = $db->query("SELECT name, points FROM violation_types WHERE id = ?", [$typeId])->fetch();
        
        if ($student && !empty($student['whatsapp_number'])) {
            $msg = "*PEMBERITAHUAN PELANGGARAN*\n\nNama: {$student['full_name']}\nPelanggaran: {$violation['name']}\nPoin: {$violation['points']}\nTgl: " . date('d-m-Y', strtotime($_POST['date'])) . "\n\nMohon pembinaan dari orang tua.";
            try { WhatsappService::send($student['whatsapp_number'], $msg); } catch (\Exception $e) {}
        }

        Session::setFlash('success', 'Pelanggaran berhasil dicatat.');
        header('Location: /discipline/student-violations');
    }

    public function deleteViolation() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = Database::getInstance();
            $db->query("DELETE FROM student_violations WHERE id = ?", [$id]);
            Session::setFlash('success', 'Data pelanggaran dihapus.');
        }
        header('Location: /discipline/student-violations');
    }


    // ==========================================================
    // 2. MODUL PRESTASI SISWA
    // ==========================================================
    public function achievements() {
        $db = Database::getInstance();
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (s.full_name LIKE ? OR sa.title LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $countSql = "SELECT COUNT(*) as total FROM student_achievements sa JOIN students s ON sa.student_id = s.id $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        $sql = "SELECT sa.*, s.full_name, s.nis, c.name as class_name 
                FROM student_achievements sa
                JOIN students s ON sa.student_id = s.id
                LEFT JOIN classrooms c ON s.classroom_id = c.id
                $where
                ORDER BY sa.date DESC 
                LIMIT $limit OFFSET $offset";

        $achievements = $db->query($sql, $params)->fetchAll();
        $students = $db->query("SELECT id, full_name, nis FROM students WHERE status='ACTIVE' ORDER BY full_name ASC")->fetchAll();

        View::render('discipline/achievements', [
            'title' => 'Prestasi Siswa',
            'achievements' => $achievements,
            'students' => $students,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search
        ]);
    }

    public function storeAchievement() {
        $db = Database::getInstance();
        $db->query("INSERT INTO student_achievements (student_id, title, level, date, description) VALUES (?, ?, ?, ?, ?)", [
            $_POST['student_id'], $_POST['title'], $_POST['level'], $_POST['date'], $_POST['description']
        ]);
        
        $studentId = $_POST['student_id'];
        $sqlStudent = "SELECT full_name, COALESCE(parent_phone, father_phone, mother_phone, guardian_phone) as whatsapp_number FROM students WHERE id = ?";
        $student = $db->query($sqlStudent, [$studentId])->fetch();
        
        if ($student && !empty($student['whatsapp_number'])) {
            $msg = "*KABAR GEMBIRA - PRESTASI SISWA*\n\nSelamat! Ananda *{$student['full_name']}* telah meraih prestasi:\n\n🏅 *{$_POST['title']}*\n🏆 Tingkat: {$_POST['level']}\n📅 Tgl: " . date('d-m-Y', strtotime($_POST['date'])) . "\n\nTerima kasih atas dukungan Ayah/Bunda.";
            try { WhatsappService::send($student['whatsapp_number'], $msg); } catch (\Exception $e) {}
        }

        Session::setFlash('success', 'Prestasi berhasil dicatat.');
        header('Location: /student-affairs/achievements');
    }

    public function updateAchievement() {
        $db = Database::getInstance();
        $db->query("UPDATE student_achievements SET student_id=?, title=?, level=?, date=?, description=? WHERE id=?", [
            $_POST['student_id'], $_POST['title'], $_POST['level'], $_POST['date'], $_POST['description'], $_POST['id']
        ]);
        Session::setFlash('success', 'Data prestasi berhasil diperbarui.');
        header('Location: /student-affairs/achievements');
    }

    public function deleteAchievement() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = Database::getInstance();
            $db->query("DELETE FROM student_achievements WHERE id = ?", [$id]);
            Session::setFlash('success', 'Data prestasi berhasil dihapus.');
        }
        header('Location: /student-affairs/achievements');
    }

    // ==========================================================
    // 3. MODUL KONSELING (BK) - FULL UPDATE
    // ==========================================================
    public function counseling() {
        $db = Database::getInstance();
        
        // Parameter Filter
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $dateFilter = $_GET['date'] ?? '';

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (s.full_name LIKE ? OR cl.issue LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($dateFilter)) {
            $where .= " AND cl.date = ?";
            $params[] = $dateFilter;
        }

        // Hitung Total
        $countSql = "SELECT COUNT(*) as total FROM counseling_logs cl JOIN students s ON cl.student_id = s.id $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        // Fetch Data
        $sql = "SELECT cl.*, s.full_name, s.nis, c.name as class_name, u.name as counselor_name
                FROM counseling_logs cl
                JOIN students s ON cl.student_id = s.id
                LEFT JOIN classrooms c ON s.classroom_id = c.id
                JOIN users u ON cl.counselor_id = u.id
                $where
                ORDER BY cl.date DESC 
                LIMIT $limit OFFSET $offset";

        $logs = $db->query($sql, $params)->fetchAll();
        $students = $db->query("SELECT id, full_name, nis FROM students WHERE status='ACTIVE' ORDER BY full_name ASC")->fetchAll();

        View::render('discipline/counseling', [
            'title' => 'Bimbingan Konseling', 
            'logs' => $logs, 
            'students' => $students,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'dateFilter' => $dateFilter
        ]);
    }

    public function storeCounseling() {
        $db = Database::getInstance();
        $db->query("INSERT INTO counseling_logs (student_id, counselor_id, date, issue, result) VALUES (?, ?, ?, ?, ?)", [
            $_POST['student_id'], Session::get('user_id'), $_POST['date'], $_POST['issue'], $_POST['result']
        ]);
        Session::setFlash('success', 'Sesi konseling berhasil dicatat.');
        header('Location: /student-affairs/counseling');
    }

    public function updateCounseling() {
        $db = Database::getInstance();
        $db->query("UPDATE counseling_logs SET student_id=?, date=?, issue=?, result=? WHERE id=?", [
            $_POST['student_id'], $_POST['date'], $_POST['issue'], $_POST['result'], $_POST['id']
        ]);
        Session::setFlash('success', 'Data konseling berhasil diperbarui.');
        header('Location: /student-affairs/counseling');
    }

    public function deleteCounseling() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = Database::getInstance();
            $db->query("DELETE FROM counseling_logs WHERE id = ?", [$id]);
            Session::setFlash('success', 'Data konseling berhasil dihapus.');
        }
        header('Location: /student-affairs/counseling');
    }
}
