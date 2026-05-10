<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;
use App\Core\ScopeFilter;

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

        [$sw, $sp] = ScopeFilter::apply('c');
        $where .= $sw; $params = array_merge($params, $sp);

        if ($search !== '') {
            $where   .= " AND (s.full_name LIKE ? OR b.title LIKE ? OR s.nis LIKE ?)";
            $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
        }
        if ($status !== '') { $where .= " AND ll.status=?"; $params[] = $status; }

        $total = $db->query("SELECT COUNT(*) FROM library_loans ll
            JOIN students s ON ll.student_id=s.id
            JOIN library_books b ON ll.book_id=b.id
            LEFT JOIN classrooms c ON s.classroom_id=c.id
            WHERE $where", $params)->fetchColumn();

        $loans = $db->query("SELECT ll.*, s.full_name, s.nis, b.title as book_title, b.code as book_code
            FROM library_loans ll
            JOIN students s ON ll.student_id=s.id
            JOIN library_books b ON ll.book_id=b.id
            LEFT JOIN classrooms c ON s.classroom_id=c.id
            WHERE $where ORDER BY ll.created_at DESC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        $books = $db->query("SELECT * FROM library_books ORDER BY title")->fetchAll();
        [$sw2, $sp2] = ScopeFilter::apply('c');
        $students = $db->query(
            "SELECT s.id, s.full_name, s.nis FROM students s LEFT JOIN classrooms c ON s.classroom_id=c.id WHERE s.status='ACTIVE' $sw2 ORDER BY s.full_name",
            $sp2
        )->fetchAll();

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

    public function storeBook() {
        $db = Database::getInstance();
        if ($db->query("SELECT id FROM library_books WHERE code = ?", [trim($_POST['code'])])->fetch()) {
            Session::setFlash('error', 'Kode buku sudah ada.');
            header('Location: /library'); exit;
        }
        $db->query("INSERT INTO library_books (code, title, author, category, stock) VALUES (?,?,?,?,?)", [
            trim($_POST['code']), $_POST['title'], $_POST['author'] ?? null,
            $_POST['category'] ?? null, (int)($_POST['stock'] ?? 1)
        ]);
        Session::setFlash('success', 'Buku berhasil ditambahkan.');
        header('Location: /library');
    }

    public function deleteBook() {
        $db = Database::getInstance();
        $inUse = $db->query("SELECT id FROM library_loans WHERE book_id = ? AND status = 'DIPINJAM'", [(int)$_GET['id']])->fetch();
        if ($inUse) { Session::setFlash('error', 'Buku masih dipinjam, tidak dapat dihapus.'); header('Location: /library'); exit; }
        $db->query("DELETE FROM library_books WHERE id = ?", [(int)$_GET['id']]);
        Session::setFlash('success', 'Buku dihapus.');
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
