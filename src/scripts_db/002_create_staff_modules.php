<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Migrasi: Modul Kepegawaian & Struktur ---\n";

try {
    $db = Database::getInstance();

    // 1. Tabel Master Posisi/Jabatan (TU, Satpam, Pustakawan, dll)
    $sqlPos = "CREATE TABLE IF NOT EXISTS staff_positions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        code VARCHAR(50) DEFAULT NULL,
        type ENUM('STRUKTURAL', 'FUNGSIONAL', 'TEKNIS') DEFAULT 'TEKNIS',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;";
    $db->query($sqlPos);
    echo "[OK] Tabel 'staff_positions' siap.\n";

    // 2. Tabel Data Staff (Non-Guru)
    $sqlStaff = "CREATE TABLE IF NOT EXISTS staff_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL UNIQUE,
        position_id INT NULL,
        nip VARCHAR(50) UNIQUE,
        full_name VARCHAR(100) NOT NULL,
        gender ENUM('L','P') NOT NULL,
        phone VARCHAR(20) NULL,
        email VARCHAR(100) NULL,
        address TEXT NULL,
        status ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (position_id) REFERENCES staff_positions(id) ON DELETE SET NULL
    ) ENGINE=INNODB;";
    $db->query($sqlStaff);
    echo "[OK] Tabel 'staff_members' siap.\n";

    // 3. Tabel Struktur Organisasi (Hierarki)
    $sqlStruct = "CREATE TABLE IF NOT EXISTS school_structures (
        id INT AUTO_INCREMENT PRIMARY KEY,
        parent_id INT NULL,
        staff_id INT NULL,
        title VARCHAR(100) NOT NULL COMMENT 'Jabatan dalam struktur (bisa beda dgn posisi asli)',
        level INT DEFAULT 1,
        order_num INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (parent_id) REFERENCES school_structures(id) ON DELETE CASCADE,
        FOREIGN KEY (staff_id) REFERENCES staff_members(id) ON DELETE SET NULL
    ) ENGINE=INNODB;";
    $db->query($sqlStruct);
    echo "[OK] Tabel 'school_structures' siap.\n";

    // 4. Tambah Role 'Staff' (ID: 7) jika belum ada
    $checkRole = $db->query("SELECT id FROM roles WHERE slug = 'staff'")->fetch();
    if (!$checkRole) {
        // Pastikan kolom sesuai dengan siakad_db.sql (name, slug, description)
        $db->query("INSERT INTO roles (name, slug, description) VALUES ('Staff / Karyawan', 'staff', 'Akses untuk staff non-guru')");
        echo "[OK] Role 'Staff' ditambahkan.\n";
    }

    // 5. Tambah Menu 'Kepegawaian'
    $checkMenu = $db->query("SELECT id FROM menus WHERE title = 'Kepegawaian'")->fetch();
    if (!$checkMenu) {
        // Parent Menu
        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                    VALUES (NULL, 'Kepegawaian', '#', 'briefcase', 15, 1)");
        $parentId = $db->getConnection()->lastInsertId();

        // Sub Menus
        $menus = [
            ['title' => 'Master Jabatan', 'url' => '/staff/positions', 'icon' => 'tag'],
            ['title' => 'Data Staff', 'url' => '/staff/members', 'icon' => 'users'],
            ['title' => 'Struktur Organisasi', 'url' => '/staff/structure', 'icon' => 'share-2']
        ];

        foreach ($menus as $m) {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, ?, ?, ?, 1, 1)", [$parentId, $m['title'], $m['url'], $m['icon']]);
            $subId = $db->getConnection()->lastInsertId();
            
            // REVISI: Menggunakan tabel 'role_menus' (jamak)
            // Beri akses ke Super Admin (1)
            $db->query("INSERT INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$subId]);
        }
        
        // Beri akses parent ke Super Admin
        $db->query("INSERT INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$parentId]);

        echo "[OK] Menu 'Kepegawaian' berhasil ditambahkan.\n";
    } else {
        echo "[SKIP] Menu 'Kepegawaian' sudah ada.\n";
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}

echo "--- Migrasi Selesai ---\n";

