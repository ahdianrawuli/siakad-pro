<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Migrasi: Modul Absensi Pegawai & Izin KBM ---\n";

try {
    $db = Database::getInstance();

    // 1. Tabel Absensi Guru & Staff
    $sqlStaffAtt = "CREATE TABLE IF NOT EXISTS staff_attendances (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        date DATE NOT NULL,
        status ENUM('HADIR', 'SAKIT', 'IZIN', 'ALPA', 'CUTI') DEFAULT 'HADIR',
        time_in TIME DEFAULT NULL,
        time_out TIME DEFAULT NULL,
        notes TEXT,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id),
        UNIQUE KEY unique_attendance (user_id, date)
    ) ENGINE=INNODB;";
    $db->query($sqlStaffAtt);
    echo "[OK] Tabel 'staff_attendances' siap.\n";

    // 2. Tabel Izin Khusus KBM (Dispensasi Akademik)
    // Berbeda dengan izin asrama. Ini izin TIDAK IKUT KELAS.
    $sqlKbm = "CREATE TABLE IF NOT EXISTS kbm_permits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        academic_year_id INT NOT NULL,
        date DATE NOT NULL,
        type ENUM('SAKIT', 'IZIN', 'DISPENSASI', 'LOMBA', 'SKORSING') NOT NULL,
        reason TEXT NOT NULL,
        status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'APPROVED',
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
        FOREIGN KEY (created_by) REFERENCES users(id)
    ) ENGINE=INNODB;";
    $db->query($sqlKbm);
    echo "[OK] Tabel 'kbm_permits' siap.\n";

    // 3. Tambah Menu 'Absensi Pegawai' ke Parent 'Kepegawaian'
    $parentStaff = $db->query("SELECT id FROM menus WHERE title = 'Kepegawaian'")->fetch();
    if ($parentStaff) {
        $parentId = $parentStaff['id'];
        $check = $db->query("SELECT id FROM menus WHERE url = '/staff/attendance'")->fetch();
        if (!$check) {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, 'Absensi Pegawai', '/staff/attendance', 'clock', 4, 1)", [$parentId]);
            $newId = $db->getConnection()->lastInsertId();
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]); // Admin
            echo "[CREATE] Menu 'Absensi Pegawai' ditambahkan.\n";
        }
    }

    // 4. Tambah Menu 'Dispensasi KBM' ke Parent 'Akademik' (ID 40)
    // Cek ID Akademik (biasanya 40, tapi kita cari by title biar aman)
    $parentAcad = $db->query("SELECT id FROM menus WHERE title = 'Akademik' OR id = 40 LIMIT 1")->fetch();
    if ($parentAcad) {
        $parentId = $parentAcad['id'];
        $check = $db->query("SELECT id FROM menus WHERE url = '/academic/kbm-permits'")->fetch();
        if (!$check) {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, 'Dispensasi KBM', '/academic/kbm-permits', 'file-text', 98, 1)", [$parentId]);
            $newId = $db->getConnection()->lastInsertId();
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]); // Admin
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (3, ?)", [$newId]); // Guru
            echo "[CREATE] Menu 'Dispensasi KBM' ditambahkan.\n";
        }
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Migrasi Selesai ---\n";

