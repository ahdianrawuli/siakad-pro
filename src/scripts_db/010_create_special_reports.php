<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Migrasi: Modul Rapor Khusus (Asrama & Ekstra) ---\n";

try {
    $db = Database::getInstance();

    // 1. Tabel Master Ekstrakurikuler
    $sqlEx = "CREATE TABLE IF NOT EXISTS extracurriculars (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        coach_id INT NULL COMMENT 'Pembina (Guru)',
        description TEXT,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (coach_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=INNODB;";
    $db->query($sqlEx);
    echo "[OK] Tabel 'extracurriculars' siap.\n";

    // 2. Tabel Anggota Ekstrakurikuler (Mapping Siswa)
    $sqlExMem = "CREATE TABLE IF NOT EXISTS student_extracurriculars (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        extracurricular_id INT NOT NULL,
        academic_year_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE,
        FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
    ) ENGINE=INNODB;";
    $db->query($sqlExMem);
    echo "[OK] Tabel 'student_extracurriculars' siap.\n";

    // 3. Tabel Nilai Ekstrakurikuler
    $sqlExG = "CREATE TABLE IF NOT EXISTS extracurricular_grades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_extracurricular_id INT NOT NULL,
        grade CHAR(1) NOT NULL COMMENT 'A, B, C, D',
        description TEXT,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_extracurricular_id) REFERENCES student_extracurriculars(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id)
    ) ENGINE=INNODB;";
    $db->query($sqlExG);
    echo "[OK] Tabel 'extracurricular_grades' siap.\n";

    // 4. Tabel Rapor Asrama (Summary Nilai Kepesantrenan)
    $sqlBoard = "CREATE TABLE IF NOT EXISTS boarding_grades (
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
    echo "[OK] Tabel 'boarding_grades' siap.\n";

    // 5. Setup Menu
    // Parent Rapor (ID 60 - Asumsi)
    $checkParent = $db->query("SELECT id FROM menus WHERE title = 'Rapor' OR title LIKE '%Laporan%'")->fetch();
    if ($checkParent) {
        $parentId = $checkParent['id'];
        
        // Menu Rapor Asrama
        $check1 = $db->query("SELECT id FROM menus WHERE url = '/report/boarding'")->fetch();
        if (!$check1) {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, 'Rapor Asrama', '/report/boarding', 'moon', 3, 1)", [$parentId]);
            $newId = $db->getConnection()->lastInsertId();
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]); 
        }

        // Menu Rapor Ekstrakurikuler (Gabung dengan manajemen)
        $check2 = $db->query("SELECT id FROM menus WHERE url = '/extracurricular'")->fetch();
        if (!$check2) {
            // Masukkan ke Kesiswaan (ID 30) atau Rapor? Kita taruh di RAPOR sesuai request user.
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, 'Rapor Ekstrakurikuler', '/extracurricular', 'activity', 4, 1)", [$parentId]);
            $newId = $db->getConnection()->lastInsertId();
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]); 
        }
    } else {
        // Buat Parent Rapor jika belum ada
        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                    VALUES (NULL, 'Rapor / Laporan', '#', 'printer', 60, 1)");
        $parentId = $db->getConnection()->lastInsertId();
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$parentId]); 
        
        // Insert Sub Menu ulang
        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                    VALUES (?, 'Rapor Asrama', '/report/boarding', 'moon', 1, 1)", [$parentId]);
        $sub1 = $db->getConnection()->lastInsertId();
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$sub1]);

        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                    VALUES (?, 'Rapor Ekstrakurikuler', '/extracurricular', 'activity', 2, 1)", [$parentId]);
        $sub2 = $db->getConnection()->lastInsertId();
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$sub2]);
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Migrasi Selesai ---\n";
