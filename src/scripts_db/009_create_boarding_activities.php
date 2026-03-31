<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Migrasi: Modul Aktivitas & Wali Asrama ---\n";

try {
    $db = Database::getInstance();

    // 1. Tabel Jadwal Kegiatan Asrama
    $sqlAct = "CREATE TABLE IF NOT EXISTS boarding_activities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        day ENUM('SETIAP HARI', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU') DEFAULT 'SETIAP HARI',
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        description VARCHAR(255),
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;";
    $db->query($sqlAct);
    echo "[OK] Tabel 'boarding_activities' siap.\n";

    // 2. Tabel Wali Asrama (Musyrif) - Many-to-Many (1 Asrama bisa banyak Musyrif)
    $sqlSup = "CREATE TABLE IF NOT EXISTS dorm_supervisors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dorm_id INT NOT NULL,
        user_id INT NOT NULL COMMENT 'Guru atau Staff yang jadi Wali',
        assigned_date DATE NOT NULL,
        status ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (dorm_id) REFERENCES dorms(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id)
    ) ENGINE=INNODB;";
    $db->query($sqlSup);
    echo "[OK] Tabel 'dorm_supervisors' siap.\n";

    // 3. Tambah Menu ke Parent 'Kepesantrenan' (ID 50 - Asumsi ID Kepesantrenan/Asrama)
    // Kita cari dulu ID parent-nya berdasarkan URL atau Title agar dinamis
    $parent = $db->query("SELECT id FROM menus WHERE url = '#' AND (title LIKE '%Asrama%' OR title LIKE '%Kepesantrenan%') LIMIT 1")->fetch();
    
    if ($parent) {
        $parentId = $parent['id'];
        
        // Menu Jadwal
        $check1 = $db->query("SELECT id FROM menus WHERE url = '/boarding/activities'")->fetch();
        if (!$check1) {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, 'Jadwal Kegiatan', '/boarding/activities', 'calendar', 3, 1)", [$parentId]);
            $newId = $db->getConnection()->lastInsertId();
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]); 
            echo "[CREATE] Menu 'Jadwal Kegiatan' ditambahkan.\n";
        }

        // Menu Wali Asrama
        $check2 = $db->query("SELECT id FROM menus WHERE url = '/boarding/supervisors'")->fetch();
        if (!$check2) {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, 'Wali Asrama', '/boarding/supervisors', 'user-check', 2, 1)", [$parentId]);
            $newId = $db->getConnection()->lastInsertId();
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]); 
            echo "[CREATE] Menu 'Wali Asrama' ditambahkan.\n";
        }

    } else {
        echo "[WARNING] Menu Parent 'Asrama' tidak ditemukan.\n";
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Migrasi Selesai ---\n";
