<?php
require_once __DIR__ . '/bootstrap.php';
use App\Core\Database;

echo "--- Setup Modul Ekstrakurikuler ---\n";

try {
    $db = Database::getInstance();

    // 1. Tabel Master Ekstrakurikuler
    $db->query("CREATE TABLE IF NOT EXISTS extracurriculars (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        status ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;");

    // 2. Tabel Pembina (Guru) Ekstrakurikuler
    $db->query("CREATE TABLE IF NOT EXISTS extracurricular_coaches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        extracurricular_id INT NOT NULL,
        user_id INT NOT NULL COMMENT 'ID User (Guru/Staff)',
        position VARCHAR(50) DEFAULT 'Pembina',
        FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=INNODB;");

    // 3. Tabel Jadwal Ekstrakurikuler
    $db->query("CREATE TABLE IF NOT EXISTS extracurricular_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        extracurricular_id INT NOT NULL,
        day_name VARCHAR(20) NOT NULL COMMENT 'Senin, Selasa, dst',
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        location VARCHAR(100),
        FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE
    ) ENGINE=INNODB;");

    // 4. Tabel Anggota (Siswa) Ekstrakurikuler
    $db->query("CREATE TABLE IF NOT EXISTS student_extracurriculars (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        extracurricular_id INT NOT NULL,
        joined_at DATE DEFAULT CURRENT_DATE,
        status ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE
    ) ENGINE=INNODB;");

    // 5. Tabel Absensi Ekstrakurikuler
    $db->query("CREATE TABLE IF NOT EXISTS extracurricular_attendances (
        id INT AUTO_INCREMENT PRIMARY KEY,
        extracurricular_id INT NOT NULL,
        student_id INT NOT NULL,
        date DATE NOT NULL,
        status ENUM('HADIR', 'SAKIT', 'IZIN', 'ALPA') NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_by INT,
        FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    ) ENGINE=INNODB;");

    echo "[OK] Tabel Ekstrakurikuler berhasil dibuat/diupdate.\n";

    // --- SETUP MENU SIDEBAR ---
    
    // 1. Buat Parent Menu 'Ekstrakurikuler'
    $parentCheck = $db->query("SELECT id FROM menus WHERE title = 'Ekstrakurikuler' AND parent_id IS NULL")->fetch();
    if (!$parentCheck) {
        $db->query("INSERT INTO menus (title, url, icon, order_num, is_active) VALUES ('Ekstrakurikuler', '#', 'activity', 35, 1)");
        $parentId = $db->getConnection()->lastInsertId();
    } else {
        $parentId = $parentCheck['id'];
    }

    // 2. Buat Sub-Menu
    $menus = [
        ['title' => 'Data & Jadwal', 'url' => '/extracurricular/master'],
        ['title' => 'Anggota Ekskul', 'url' => '/extracurricular/members'],
        ['title' => 'Absensi Ekskul', 'url' => '/extracurricular/attendance'],
    ];

    foreach ($menus as $m) {
        $check = $db->query("SELECT id FROM menus WHERE url = ?", [$m['url']])->fetch();
        if (!$check) {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) VALUES (?, ?, ?, 'circle', 99, 1)", 
                [$parentId, $m['title'], $m['url']]);
            $newId = $db->getConnection()->lastInsertId();
            
            // Beri akses ke Admin (1) dan Guru (3)
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]);
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (3, ?)", [$newId]);
        }
    }

    // Beri akses Parent ke Admin & Guru
    $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$parentId]);
    $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (3, ?)", [$parentId]);

    echo "[OK] Menu Sidebar berhasil ditambahkan.\n";

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

