<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class LibraryController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $search  = trim($_GET['search'] ?? '');
        $status  = $_GET['status'] ?? '';
        $limit   = (int)($_GET['limit'] ?? 10);
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $offset  = ($page - 1) * $limit;

        $where  = "1=1";
        $params = [];
        if ($search !== '') {
            $where   .= " AND (s.full_name LIKE ? OR b.title LIKE ? OR s.nis LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
        }
        if ($status !== '') { $where .= " AND ll.status=?"; $params[] = $status; }

        $total = $db->query("SELECT COUNT(*) FROM library_loans ll
            JOIN students s ON ll.student_id=s.id
            JOIN library_books b ON ll.book_id=b.id
            WHERE $where", $params)->fetchColumn();

        $loans = $db->query("SELECT ll.*, s.full_name, s.nis, b.title as book_title, b.code as book_code
            FROM library_loans ll
            JOIN students s ON ll.student_id=s.id
            JOIN library_books b ON ll.book_id=b.id
            WHERE $where ORDER BY ll.created_at DESC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        $books    = $db->query("SELECT * FROM library_books ORDER BY title")->fetchAll();
        $students = $db->query("SELECT id, full_name, nis FROM students WHERE status='ACTIVE' ORDER BY full_name")->fetchAll();

        // Stats
        $stats = $db->query("SELECT
            COUNT(*) as total,
            SUM(status='DIPINJAM') as dipinjam,
            SUM(status='DIKEMBALIKAN') as kembali,
            SUM(status='TERLAMBAT') as terlambat
            FROM library_loans")->fetch();

        View::render('library/index', [
            'title'       => 'Perpustakaan',
            'loans'       => $loans,
            'books'       => $books,
            'students'    => $students,
            'stats'       => $stats,
            'search'      => $search,
            'status'      => $status,
            'limit'       => $limit,
            'currentPage' => $page,
            'totalPages'  => $limit > 0 ? (int)ceil($total / $limit) : 1,
            'totalData'   => $total,
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $loanDate = $_POST['loan_date'];
        $dueDate  = date('Y-m-d', strtotime($loanDate . ' +14 days'));

        $db->query("INSERT INTO library_loans (book_id, student_id, loan_date, due_date, status, notes, created_by) VALUES (?,?,?,?,'DIPINJAM',?,?)",
            [$_POST['book_id'], $_POST['student_id'], $loanDate, $dueDate, $_POST['notes'] ?? null, $userId]);

        Session::setFlash('success', 'Peminjaman berhasil dicatat.');
        header('Location: /library');
    }

    public function returnBook() {
        $db = Database::getInstance();
        $id = $_POST['id'];
        $returnDate = $_POST['return_date'] ?? date('Y-m-d');

        $loan = $db->query("SELECT due_date FROM library_loans WHERE id=?", [$id])->fetch();
        $status = strtotime($returnDate) > strtotime($loan['due_date']) ? 'TERLAMBAT' : 'DIKEMBALIKAN';

        $db->query("UPDATE library_loans SET return_date=?, status=? WHERE id=?", [$returnDate, $status, $id]);
        Session::setFlash('success', 'Buku berhasil dikembalikan.');
        header('Location: /library');
    }
}
