<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Models\WhatsappService;

class AnnouncementController {
    public function __construct() {
        Middleware::auth();
    }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        $target = $_GET['target'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM announcements WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (!empty($target)) {
            $sql .= " AND target_audience = ?";
            $params[] = $target;
        }

        $totalData = $db->query("SELECT COUNT(*) FROM (" . $sql . ") as t", $params)->fetchColumn();

        $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $announcements = $db->query($sql, $params)->fetchAll();

        View::render('announcements/index', [
            'title' => 'Manajemen Pengumuman',
            'announcements' => $announcements,
            'totalData' => $totalData,
            'totalPages' => ceil($totalData / $limit),
            'currentPage' => $page,
            'limit' => $limit,
            'search' => $search,
            'target' => $target
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $title   = trim($_POST['title']);
        $content = trim($_POST['content']);
        $target  = $_POST['target_audience'] ?? 'ALL';
        $status  = $_POST['status'] ?? 'PUBLISHED';

        $db->query("INSERT INTO announcements (title, content, target_audience, status, created_by) VALUES (?, ?, ?, ?, ?)", [
            $title, $content, $target, $status, Session::get('user_id')
        ]);

        if ($status === 'PUBLISHED') {
            try { $this->broadcastWhatsApp($title, $content, $target); } catch (\Exception $e) {}
        }

        Session::setFlash('success', 'Pengumuman berhasil disimpan.');
        header('Location: /announcements');
    }

    public function update() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $target = $_POST['target_audience'];
        $status = $_POST['status'];

        try {
            $db->query("UPDATE announcements SET title = ?, content = ?, target_audience = ?, status = ? WHERE id = ?", [
                $title, $content, $target, $status, $id
            ]);
            Session::setFlash('success', 'Pengumuman berhasil diperbarui.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal memperbarui pengumuman.');
        }

        header('Location: /announcements');
    }

    public function delete() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        try {
            $db->query("DELETE FROM announcements WHERE id = ?", [$id]);
            Session::setFlash('success', 'Pengumuman berhasil dihapus.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Gagal menghapus pengumuman.');
        }
        header('Location: /announcements');
    }

    private function broadcastWhatsApp($title, $content, $target) {
        $db = Database::getInstance();
        $numbers = [];

        if ($target === 'ALL' || $target === 'STUDENTS' || $target === 'PARENTS') {
            $students = $db->query("SELECT parent_phone, father_phone, mother_phone, guardian_phone FROM students WHERE status = 'ACTIVE'")->fetchAll();
            foreach ($students as $s) {
                if ($target === 'ALL' || $target === 'PARENTS') {
                    if (!empty($s['parent_phone']))   $numbers[] = $s['parent_phone'];
                    if (!empty($s['father_phone']))   $numbers[] = $s['father_phone'];
                    if (!empty($s['mother_phone']))   $numbers[] = $s['mother_phone'];
                    if (!empty($s['guardian_phone'])) $numbers[] = $s['guardian_phone'];
                }
            }
        }

        if ($target === 'ALL' || $target === 'TEACHERS') {
            $teachers = $db->query("SELECT phone_number FROM teachers WHERE status = 'ACTIVE'")->fetchAll();
            foreach ($teachers as $t) {
                if (!empty($t['phone_number'])) $numbers[] = $t['phone_number'];
            }
        }

        if ($target === 'ALL' || $target === 'STAFF') {
            $staff = $db->query("SELECT phone_number FROM staff_members WHERE status = 'ACTIVE'")->fetchAll();
            foreach ($staff as $s) {
                if (!empty($s['phone_number'])) $numbers[] = $s['phone_number'];
            }
        }

        $uniqueNumbers = array_unique(array_filter($numbers));
        $message = "📢 *PENGUMUMAN BARU*\n\n*{$title}*\n\n{$content}\n\n_Pesan otomatis dari SIAKAD PRO Pesantren Thawalib Parabek_";

        foreach ($uniqueNumbers as $number) {
            WhatsappService::send($number, $message);
        }
    }
}
