<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Middleware;
use App\Core\Database;

class BoardingMutationController {
    public function __construct() { Middleware::auth(); }

    public function index() {
        $db = Database::getInstance();
        $search = $_GET['search'] ?? '';
        
        // Ambil History Mutasi
        $sql = "SELECT dm.*, s.full_name, s.nis, 
                       old.name as old_dorm, new.name as new_dorm,
                       u.name as admin_name
                FROM dorm_mutations dm
                JOIN students s ON dm.student_id = s.id
                LEFT JOIN dorms old ON dm.old_dorm_id = old.id
                JOIN dorms new ON dm.new_dorm_id = new.id
                LEFT JOIN users u ON dm.created_by = u.id
                WHERE s.full_name LIKE ?
                ORDER BY dm.mutation_date DESC, dm.created_at DESC";
        
        $mutations = $db->query($sql, ["%$search%"])->fetchAll();

        // Data untuk Form
        $students = $db->query("SELECT s.id, s.full_name, s.dorm_id, d.name as current_dorm 
                                FROM students s 
                                LEFT JOIN dorms d ON s.dorm_id = d.id 
                                WHERE s.status='ACTIVE' ORDER BY s.full_name")->fetchAll();
        $dorms = $db->query("SELECT * FROM dorms ORDER BY name ASC")->fetchAll();

        View::render('boarding/mutations/index', [
            'title' => 'Mutasi Kamar Santri',
            'mutations' => $mutations,
            'students' => $students,
            'dorms' => $dorms,
            'search' => $search
        ]);
    }

    public function store() {
        $db = Database::getInstance();
        $studentId = $_POST['student_id'];
        $newDormId = $_POST['new_dorm_id'];
        $reason = $_POST['reason'];
        $date = $_POST['mutation_date'];
        
        // 1. Ambil Dorm Lama
        $student = $db->query("SELECT dorm_id FROM students WHERE id = ?", [$studentId])->fetch();
        $oldDormId = $student['dorm_id'];

        if ($oldDormId == $newDormId) {
            Session::setFlash('error', 'Asrama tujuan sama dengan asrama asal.');
            header('Location: /boarding/mutations');
            exit;
        }

        try {
            $db->getConnection()->beginTransaction();

            // 2. Catat History (FIX: Gunakan Session::get('user_id'))
            $adminId = Session::get('user_id'); // Perbaikan di sini
            
            $db->query("INSERT INTO dorm_mutations (student_id, old_dorm_id, new_dorm_id, reason, mutation_date, created_by) 
                        VALUES (?, ?, ?, ?, ?, ?)", 
                        [$studentId, $oldDormId, $newDormId, $reason, $date, $adminId]);

            // 3. Update Master Siswa
            $db->query("UPDATE students SET dorm_id = ? WHERE id = ?", [$newDormId, $studentId]);

            $db->getConnection()->commit();
            Session::setFlash('success', 'Mutasi santri berhasil diproses.');

        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Session::setFlash('error', 'Gagal memproses mutasi: ' . $e->getMessage());
        }

        header('Location: /boarding/mutations');
    }
}

