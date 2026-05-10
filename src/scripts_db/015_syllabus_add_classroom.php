<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Migrasi: Tambah classroom_id ke syllabus_documents ---\n";

try {
    $db = Database::getInstance();

    // Hapus semua data lama
    $db->query("DELETE FROM syllabus_documents");
    echo "[OK] Data syllabus_documents dihapus.\n";

    // Hapus kolom grade_level, tambah classroom_id
    $cols = $db->query("SHOW COLUMNS FROM syllabus_documents LIKE 'classroom_id'")->fetch();
    if (!$cols) {
        $db->query("ALTER TABLE syllabus_documents DROP COLUMN grade_level");
        $db->query("ALTER TABLE syllabus_documents ADD COLUMN classroom_id INT NOT NULL AFTER academic_year_id");
        $db->query("ALTER TABLE syllabus_documents ADD CONSTRAINT fk_syllabus_classroom FOREIGN KEY (classroom_id) REFERENCES classrooms(id)");
        echo "[OK] Kolom classroom_id ditambahkan.\n";
    } else {
        echo "[SKIP] Kolom classroom_id sudah ada.\n";
    }

    // Inject data contoh
    $year  = $db->query("SELECT id FROM academic_years WHERE is_active = 1 LIMIT 1")->fetch();
    $rooms = $db->query("SELECT id, name, major FROM classrooms ORDER BY major, name")->fetchAll();
    $subjs = $db->query("SELECT id FROM subjects ORDER BY id")->fetchAll();
    $teacher = $db->query("SELECT id FROM users WHERE role_id = 3 LIMIT 1")->fetch();

    if ($year && $rooms && $subjs && $teacher) {
        $types = ['SILABUS', 'RPP', 'PROTA', 'PROSEM'];
        $count = 0;
        foreach ($rooms as $room) {
            foreach ($types as $ti => $type) {
                $subj = $subjs[($count) % count($subjs)];
                $db->query(
                    "INSERT INTO syllabus_documents (teacher_id, subject_id, academic_year_id, classroom_id, type, title, file_path) VALUES (?,?,?,?,?,?,?)",
                    [$teacher['id'], $subj['id'], $year['id'], $room['id'], $type, "$type - {$room['name']}", "sample_{$room['id']}_{$ti}.pdf"]
                );
                $count++;
            }
        }
        echo "[OK] " . $count . " data contoh diinjeksi.\n";
    } else {
        echo "[SKIP] Data master tidak cukup untuk inject contoh.\n";
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Selesai ---\n";
