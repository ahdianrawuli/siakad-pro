<?php
namespace App\Controllers;
use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class SupportController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $role = Session::get('user_role');

        $sql = "SELECT t.*, u.name as reporter FROM tickets t JOIN users u ON t.user_id = u.id";
        
        // Jika bukan Admin/Super Admin, hanya lihat tiket sendiri
        if (!in_array($role, ['super-admin', 'admin', 'guru'])) {
            $sql .= " WHERE t.user_id = $userId";
        }
        $sql .= " ORDER BY t.status ASC, t.created_at DESC"; // Open paling atas

        $tickets = $db->query($sql)->fetchAll();
        View::render('support/index', ['title' => 'Pusat Bantuan', 'tickets' => $tickets]);
    }

    public function create() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        
        // 1. Buat Tiket
        $db->query("INSERT INTO tickets (user_id, subject, category, status) VALUES (?, ?, ?, 'OPEN')", [
            $userId, $_POST['subject'], $_POST['category']
        ]);
        $ticketId = $db->getConnection()->lastInsertId();

        // 2. Insert Pesan Pertama
        $db->query("INSERT INTO ticket_replies (ticket_id, user_id, message) VALUES (?, ?, ?)", [
            $ticketId, $userId, $_POST['message']
        ]);

        Session::setFlash('success', 'Tiket bantuan berhasil dibuat.');
        header('Location: /support');
    }

    public function detail() {
        $id = $_GET['id'];
        $db = Database::getInstance();
        
        $ticket = $db->query("SELECT * FROM tickets WHERE id = ?", [$id])->fetch();
        $replies = $db->query("
            SELECT r.*, u.name as sender_name, u.role_id 
            FROM ticket_replies r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.ticket_id = ? ORDER BY r.created_at ASC
        ", [$id])->fetchAll();

        View::render('support/detail', ['ticket' => $ticket, 'replies' => $replies]);
    }

    public function reply() {
        $ticketId = $_POST['ticket_id'];
        $userId = Session::get('user_id');
        
        $db = Database::getInstance();
        $db->query("INSERT INTO ticket_replies (ticket_id, user_id, message) VALUES (?, ?, ?)", [
            $ticketId, $userId, $_POST['message']
        ]);

        // Jika admin yang balas, set status ANSWERED. Jika user balas, set OPEN lagi.
        $role = Session::get('user_role');
        $newStatus = (in_array($role, ['super-admin', 'admin'])) ? 'ANSWERED' : 'OPEN';
        
        // Update status tiket
        $db->query("UPDATE tickets SET status = ? WHERE id = ?", [$newStatus, $ticketId]);

        header("Location: /support/detail?id=$ticketId");
    }
}
