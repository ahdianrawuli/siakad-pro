<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class SyllabusController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $roleId = Session::get('role_id');

        $search = $_GET['search'] ?? '';
        $type   = $_GET['type'] ?? '';
        $page   = (int)($_GET['page'] ?? 1);
        $limit  = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;

        $where = "1=1";
        $params = [];
        if ($roleId == 3) { $where .= " AND sd.teacher_id = ?"; $params[] = $userId; }
        if ($search) { $where .= " AND (sd.title LIKE ? OR s.name LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($type)   { $where .= " AND sd.type = ?"; $params[] = $type; }

        $totalData  = $db->query("SELECT COUNT(*) FROM syllabus_documents sd JOIN subjects s ON sd.subject_id = s.id WHERE $where", $params)->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        $sql = "SELECT sd.*, s.name as subject_name, ay.name as year_name, u.name as teacher_name
                FROM syllabus_documents sd
                JOIN subjects s ON sd.subject_id = s.id
                JOIN academic_years ay ON sd.academic_year_id = ay.id
                JOIN users u ON sd.teacher_id = u.id
                WHERE $where ORDER BY sd.created_at DESC LIMIT $limit OFFSET $offset";

        $documents = $db->query($sql, $params)->fetchAll();
        $subjects  = $db->query("SELECT id, name FROM subjects ORDER BY name")->fetchAll();
        $years     = $db->query("SELECT id, name FROM academic_years ORDER BY id DESC")->fetchAll();

        View::render('academic/syllabus/index', [
            'title'       => 'Silabus & RPP',
            'documents'   => $documents,
            'subjects'    => $subjects,
            'years'       => $years,
            'user'        => ['id' => $userId, 'role_id' => $roleId],
            'search'      => $search,
            'typeFilter'  => $type,
            'totalData'   => $totalData,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'limit'       => $limit,
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $userId = Session::get('user_id'); // FIX
        
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $uploadDir = __DIR__ . '/../../public/uploads/syllabus/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $_FILES['file']['name']);
            $destPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destPath)) {
                $db->query("INSERT INTO syllabus_documents (teacher_id, subject_id, academic_year_id, grade_level, type, title, file_path) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)", [
                    $_POST['teacher_id'] ?? $userId, 
                    $_POST['subject_id'],
                    $_POST['academic_year_id'],
                    $_POST['grade_level'],
                    $_POST['type'],
                    $_POST['title'],
                    $fileName
                ]);
                Session::setFlash('success', 'Dokumen berhasil diunggah.');
            } else {
                Session::setFlash('error', 'Gagal upload file.');
            }
        } else {
            Session::setFlash('error', 'Pilih file terlebih dahulu.');
        }

        header('Location: /finance/spp');
    }

    public function delete() {
        $db = Database::getInstance();
        $id = $_GET['id'];
        
        $doc = $db->query("SELECT file_path FROM syllabus_documents WHERE id=?", [$id])->fetch();
        if ($doc) {
            $filePath = __DIR__ . '/../../public/uploads/syllabus/' . $doc['file_path'];
            if (file_exists($filePath)) unlink($filePath);
        }

        $db->query("DELETE FROM syllabus_documents WHERE id=?", [$id]);
        Session::setFlash('success', 'Dokumen dihapus.');
        header('Location: /finance/spp');
    }

    public function download() {
        $file = $_GET['file'];
        $path = __DIR__ . '/../../public/uploads/syllabus/' . $file;
        
        if (file_exists($path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($path).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        } else {
            die("File tidak ditemukan.");
        }
    }
}

