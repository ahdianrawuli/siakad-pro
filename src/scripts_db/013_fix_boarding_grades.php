<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Perbaikan Tabel Rapor Asrama ---\n";

try {
    $db = Database::getInstance();

    // 1. Hapus Tabel Lama (Reset Schema)
    echo "[RESET] Menghapus tabel 'boarding_grades' lama...\n";
    $db->query("DROP TABLE IF EXISTS boarding_grades");

    // 2. Buat Ulang dengan Kolom Lengkap
    echo "[CREATE] Membuat ulang tabel 'boarding_grades'...\n";
    $sqlBoard = "CREATE TABLE boarding_grades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        academic_year_id INT NOT NULL,
        tahfidz_grade CHAR(1) COMMENT 'Nilai Tahfidz',
        tahfidz_desc TEXT,
        language_grade CHAR(1) COMMENT 'Nilai Bahasa',
        language_desc TEXT,
        character_grade CHAR(1) COMMENT 'Nilai Akhlaq/Disiplin',
        character_desc TEXT,
        homeroom_note TEXT COMMENT 'Catatan Musyrif/Wali Asrama',
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
        FOREIGN KEY (created_by) REFERENCES users(id)
    ) ENGINE=INNODB;";
    
    $db->query($sqlBoard);
    echo "[OK] Tabel berhasil diperbarui dengan struktur yang benar.\n";

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Perbaikan Selesai ---\n";
