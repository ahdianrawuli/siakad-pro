<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;
use App\Models\WhatsappService;

class DisciplineController {
    public function __construct() { Middleware::auth(); }

    // ==========================================================
    // 1. MASTER PELANGGARAN
    // ==========================================================
    public function master() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $where = "WHERE 1=1";
        $params = [];
        if (!empty($search)) { $where .= " AND (name LIKE ? OR category LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        $violations = $db->query("SELECT * FROM violation_types $where ORDER BY points ASC", $params)->fetchAll();
        View::render('discipline/master', ['title' => 'Master Pelanggaran', 'violations' => $violations, 'search' => $search]);
    }

    public function updateMaster() {
        $db = Database::getInstance();
        $db->query("UPDATE violation_types SET name=?, points=?, category=? WHERE id=?", [
            $_POST['name'], $_POST['points'], $_POST['severity'], $_POST['id']
        ]);
        Session::setFlash('success', 'Data pelanggaran diperbarui.');
        header('Location: /discipline/master-violations');
    }

    public function deleteMaster() {
        $db = Database::getInstance();
        $db->query("DELETE FROM violation_types WHERE id=?", [$_GET['id']]);
        Session::setFlash('success', 'Data pelanggaran dihapus.');
        header('Location: /discipline/master-violations');
    }

    public function storeMaster() {
        $db = Database::getInstance();
        $db->query("INSERT INTO violation_types (name, points, category) VALUES (?, ?, ?)", [
            $_POST['name'], $_POST['points'], $_POST['severity']
        ]);
        Session::setFlash('success', 'Master Pelanggaran berhasil ditambahkan.');
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
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo   = $_GET['date_to'] ?? date('Y-m-d');
        $classFilter = $_GET['class_id'] ?? '';

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (s.full_name LIKE ? OR s.nis LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if (!empty($dateFrom)) { $where .= " AND sv.date >= ?"; $params[] = $dateFrom; }
        if (!empty($dateTo))   { $where .= " AND sv.date <= ?"; $params[] = $dateTo; }
        if (!empty($classFilter)) { $where .= " AND s.classroom_id = ?"; $params[] = $classFilter; }
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        $countSql = "SELECT COUNT(*) as total FROM student_violations sv
                     JOIN students s ON sv.student_id = s.id
                     LEFT JOIN classrooms c ON s.classroom_id = c.id
                     $where";
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
        [$sw2, $sp2] = ScopeFilter::apply('c');
        $students = $db->query(
            "SELECT s.id, s.full_name, s.nis FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id WHERE s.status='ACTIVE' $sw2 ORDER BY s.full_name ASC",
            $sp2
        )->fetchAll();

        View::render('discipline/index', [
            'title' => 'Kedisiplinan Siswa', 
            'violations' => $violations,
            'types' => $types,
            'students' => $students,
            'classrooms' => $db->query("SELECT c.* FROM classrooms c WHERE 1=1 $sw2 ORDER BY c.name", $sp2)->fetchAll(),
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'dateFrom' => $dateFrom, 'dateTo' => $dateTo, 'classFilter' => $classFilter,
        ]);
    }

    public function printViolations() {
        $db = Database::getInstance();
        $classFilter = $_GET['class_id'] ?? '';
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo   = $_GET['date_to'] ?? date('Y-m-d');

        $where = "WHERE 1=1";
        $params = [];
        if (!empty($dateFrom)) { $where .= " AND sv.date >= ?"; $params[] = $dateFrom; }
        if (!empty($dateTo))   { $where .= " AND sv.date <= ?"; $params[] = $dateTo; }
        if (!empty($classFilter)) { $where .= " AND s.classroom_id = ?"; $params[] = $classFilter; }
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        $violations = $db->query("SELECT sv.*, s.full_name, s.nis, c.name as class_name, vt.name as violation_name, vt.points, vt.category
            FROM student_violations sv JOIN students s ON sv.student_id=s.id
            LEFT JOIN classrooms c ON s.classroom_id=c.id
            JOIN violation_types vt ON sv.violation_type_id=vt.id
            $where ORDER BY sv.date DESC, s.full_name", $params)->fetchAll();

        $classroom = $classFilter ? $db->query("SELECT name FROM classrooms WHERE id=?", [$classFilter])->fetch() : null;

        View::render('discipline/print', ['violations'=>$violations,'classroom'=>$classroom,'dateFrom'=>$dateFrom,'dateTo'=>$dateTo]);
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
        $classFilter = $_GET['class_id'] ?? '';
        $dateFrom = !empty($_GET['date_from']) ? $_GET['date_from'] : date('Y-01-01');
        $dateTo   = !empty($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($search)) { $where .= " AND (s.full_name LIKE ? OR sa.title LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($classFilter)) { $where .= " AND s.classroom_id = ?"; $params[] = $classFilter; }
        $where .= " AND sa.date BETWEEN ? AND ?"; $params[] = $dateFrom; $params[] = $dateTo;
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        $countSql = "SELECT COUNT(*) as total FROM student_achievements sa
                     JOIN students s ON sa.student_id = s.id
                     LEFT JOIN classrooms c ON s.classroom_id = c.id
                     $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        $sql = "SELECT sa.*, s.full_name, s.nis, c.name as class_name 
                FROM student_achievements sa
                JOIN students s ON sa.student_id = s.id
                LEFT JOIN classrooms c ON s.classroom_id = c.id
                $where ORDER BY sa.date DESC LIMIT $limit OFFSET $offset";

        $achievements = $db->query($sql, $params)->fetchAll();
        [$sw2, $sp2] = ScopeFilter::apply('c');
        $students = $db->query(
            "SELECT s.id, s.full_name, s.nis FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id WHERE s.status='ACTIVE' $sw2 ORDER BY s.full_name ASC", $sp2
        )->fetchAll();
        $classrooms = $db->query("SELECT c.* FROM classrooms c WHERE 1=1 $sw2 ORDER BY c.name", $sp2)->fetchAll();

        View::render('discipline/achievements', [
            'title' => 'Prestasi Siswa',
            'achievements' => $achievements,
            'students' => $students,
            'classrooms' => $classrooms,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'classFilter' => $classFilter,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function printAchievements() {
        $db = Database::getInstance();
        $classFilter = $_GET['class_id'] ?? '';
        $dateFrom = !empty($_GET['date_from']) ? $_GET['date_from'] : date('Y-01-01');
        $dateTo   = !empty($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

        $where = "WHERE sa.date BETWEEN ? AND ?";
        $params = [$dateFrom, $dateTo];
        if (!empty($classFilter)) { $where .= " AND s.classroom_id = ?"; $params[] = $classFilter; }
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        $achievements = $db->query("SELECT sa.*, s.full_name, s.nis, c.name as class_name
            FROM student_achievements sa JOIN students s ON sa.student_id=s.id
            LEFT JOIN classrooms c ON s.classroom_id=c.id
            $where ORDER BY sa.date DESC", $params)->fetchAll();

        $classroom = $classFilter ? $db->query("SELECT name FROM classrooms WHERE id=?", [$classFilter])->fetch() : null;
        View::render('discipline/print_achievements', ['achievements'=>$achievements,'classroom'=>$classroom,'dateFrom'=>$dateFrom,'dateTo'=>$dateTo]);
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
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $classFilter = $_GET['class_id'] ?? '';
        $dateFrom = !empty($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
        $dateTo   = !empty($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($search)) { $where .= " AND (s.full_name LIKE ? OR cl.issue LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if (!empty($classFilter)) { $where .= " AND s.classroom_id = ?"; $params[] = $classFilter; }
        $where .= " AND cl.date BETWEEN ? AND ?"; $params[] = $dateFrom; $params[] = $dateTo;
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        $countSql = "SELECT COUNT(*) as total FROM counseling_logs cl JOIN students s ON cl.student_id = s.id LEFT JOIN classrooms c ON s.classroom_id = c.id $where";
        $totalData = $db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalData / $limit);

        $sql = "SELECT cl.*, s.full_name, s.nis, c.name as class_name, u.name as counselor_name
                FROM counseling_logs cl JOIN students s ON cl.student_id = s.id
                LEFT JOIN classrooms c ON s.classroom_id = c.id
                JOIN users u ON cl.counselor_id = u.id
                $where ORDER BY cl.date DESC LIMIT $limit OFFSET $offset";

        $logs = $db->query($sql, $params)->fetchAll();
        [$sw2, $sp2] = ScopeFilter::apply('c');
        $students = $db->query("SELECT s.id, s.full_name, s.nis FROM students s LEFT JOIN classrooms c ON s.classroom_id = c.id WHERE s.status='ACTIVE' $sw2 ORDER BY s.full_name ASC", $sp2)->fetchAll();
        $classrooms = $db->query("SELECT c.* FROM classrooms c WHERE 1=1 $sw2 ORDER BY c.name", $sp2)->fetchAll();

        View::render('discipline/counseling', [
            'title' => 'Bimbingan Konseling',
            'logs' => $logs,
            'students' => $students,
            'classrooms' => $classrooms,
            'totalData' => $totalData,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'classFilter' => $classFilter,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function printCounseling() {
        $db = Database::getInstance();
        $classFilter = $_GET['class_id'] ?? '';
        $dateFrom = !empty($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
        $dateTo   = !empty($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');

        $where = "WHERE cl.date BETWEEN ? AND ?";
        $params = [$dateFrom, $dateTo];
        if (!empty($classFilter)) { $where .= " AND s.classroom_id = ?"; $params[] = $classFilter; }
        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        $logs = $db->query("SELECT cl.*, s.full_name, s.nis, c.name as class_name, u.name as counselor_name
            FROM counseling_logs cl JOIN students s ON cl.student_id=s.id
            LEFT JOIN classrooms c ON s.classroom_id=c.id
            JOIN users u ON cl.counselor_id=u.id
            $where ORDER BY cl.date DESC", $params)->fetchAll();

        $classroom = $classFilter ? $db->query("SELECT name FROM classrooms WHERE id=?", [$classFilter])->fetch() : null;
        View::render('discipline/print_counseling', ['logs'=>$logs,'classroom'=>$classroom,'dateFrom'=>$dateFrom,'dateTo'=>$dateTo]);
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
