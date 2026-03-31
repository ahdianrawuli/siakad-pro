<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Migrasi: Modul Kedisiplinan Lanjutan ---\n";

try {
    $db = Database::getInstance();

    // 1. Tabel Pelacakan Santri (Activity Logs)
    $sqlTracking = "CREATE TABLE IF NOT EXISTS student_activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        activity_type ENUM('LOCATION', 'ACTIVITY', 'INCIDENT') DEFAULT 'ACTIVITY',
        location VARCHAR(100) NOT NULL COMMENT 'Contoh: Kantin, Masjid, Gerbang Depan',
        description TEXT,
        logged_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id)
    ) ENGINE=INNODB;";
    $db->query($sqlTracking);
    echo "[OK] Tabel 'student_activity_logs' siap.\n";

    // 2. Tabel Mutasi Asrama (History Perpindahan)
    $sqlMut = "CREATE TABLE IF NOT EXISTS dorm_mutations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        old_dorm_id INT NULL,
        new_dorm_id INT NOT NULL,
        reason TEXT NOT NULL,
        mutation_date DATE NOT NULL,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (old_dorm_id) REFERENCES dorms(id) ON DELETE SET NULL,
        FOREIGN KEY (new_dorm_id) REFERENCES dorms(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id)
    ) ENGINE=INNODB;";
    $db->query($sqlMut);
    echo "[OK] Tabel 'dorm_mutations' siap.\n";

    // 3. Tambah Menu
    // A. Pelacakan Santri -> Masuk ke Parent 'Kesiswaan' (ID 30)
    $checkMenu = $db->query("SELECT id FROM menus WHERE url = '/discipline/tracking'")->fetch();
    if (!$checkMenu) {
        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                    VALUES (30, 'Pelacakan Santri', '/discipline/tracking', 'map-pin', 8, 1)"); // Urutan 8 di Kesiswaan
        $newId = $db->getConnection()->lastInsertId();
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]); // Super Admin
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (2, ?)", [$newId]); // Admin Sekolah
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (3, ?)", [$newId]); // Guru (misal Piket)
        echo "[CREATE] Menu 'Pelacakan Santri' ditambahkan.\n";
    }

    // B. Laporan Wali Kelas -> Masuk ke Parent 'Kesiswaan' (Sesuai request User) atau 'Area Saya'
    // Kita taruh di 'Kesiswaan' agar admin bisa lihat semua rekap.
    $checkMenuRep = $db->query("SELECT id FROM menus WHERE url = '/homeroom/report-all'")->fetch();
    if (!$checkMenuRep) {
        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                    VALUES (30, 'Laporan Wali Kelas', '/homeroom/report-all', 'printer', 9, 1)");
        $newId = $db->getConnection()->lastInsertId();
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]);
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (2, ?)", [$newId]);
        echo "[CREATE] Menu 'Laporan Wali Kelas' (Admin View) ditambahkan.\n";
    }

    // C. Mutasi Asrama -> Masuk ke Parent 'Kepesantrenan/Asrama' (ID 50)
    $checkMenuMut = $db->query("SELECT id FROM menus WHERE url = '/boarding/mutations'")->fetch();
    if (!$checkMenuMut) {
        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                    VALUES (50, 'Mutasi Kamar', '/boarding/mutations', 'refresh-cw', 6, 1)");
        $newId = $db->getConnection()->lastInsertId();
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]);
        echo "[CREATE] Menu 'Mutasi Kamar' ditambahkan.\n";
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Migrasi Selesai ---\n";
