<?php
require_once __DIR__ . '/bootstrap.php';

use App\Core\Database;

echo "--- Memulai Migrasi: Modul Akademik Lanjutan ---\n";

try {
    $db = Database::getInstance();

    // 1. Tabel Kurikulum
    $sqlCurr = "CREATE TABLE IF NOT EXISTS curriculums (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL COMMENT 'Contoh: Kurikulum Merdeka, K-13',
        code VARCHAR(50) DEFAULT NULL,
        description TEXT DEFAULT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;";
    $db->query($sqlCurr);
    echo "[OK] Tabel 'curriculums' siap.\n";

    // 2. Tabel SK Pembagian Tugas Mengajar (Teaching Assignments)
    // Memisahkan penugasan guru dari jadwal harian.
    $sqlAssign = "CREATE TABLE IF NOT EXISTS teaching_assignments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        academic_year_id INT NOT NULL,
        teacher_id INT NOT NULL,
        subject_id INT NOT NULL,
        classroom_id INT NOT NULL,
        sk_number VARCHAR(100) DEFAULT NULL COMMENT 'Nomor SK Penugasan',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
        FOREIGN KEY (teacher_id) REFERENCES users(id),
        FOREIGN KEY (subject_id) REFERENCES subjects(id),
        FOREIGN KEY (classroom_id) REFERENCES classrooms(id)
    ) ENGINE=INNODB;";
    $db->query($sqlAssign);
    echo "[OK] Tabel 'teaching_assignments' siap.\n";

    // 3. Tabel Silabus & RPP
    $sqlSyl = "CREATE TABLE IF NOT EXISTS syllabus_documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        teacher_id INT NOT NULL,
        subject_id INT NOT NULL,
        academic_year_id INT NOT NULL,
        grade_level VARCHAR(20) NOT NULL COMMENT 'Tingkat Kelas: 7, 8, 10',
        type ENUM('SILABUS', 'RPP', 'PROTA', 'PROSEM', 'MODUL') NOT NULL,
        title VARCHAR(200) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (teacher_id) REFERENCES users(id),
        FOREIGN KEY (subject_id) REFERENCES subjects(id),
        FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
    ) ENGINE=INNODB;";
    $db->query($sqlSyl);
    echo "[OK] Tabel 'syllabus_documents' siap.\n";

    // 4. Daftarkan Menu ke Sidebar (Di bawah Parent 'AKADEMIK' -> ID 40)
    $parentId = 40; 
    
    // Pastikan parent Akademik ada (jaga-jaga)
    $checkParent = $db->query("SELECT id FROM menus WHERE id = 40")->fetch();
    if (!$checkParent) {
        die("[ERROR] Menu Parent 'Akademik' (ID 40) tidak ditemukan. Cek database.\n");
    }

    $menus = [
        ['title' => 'Kurikulum',       'url' => '/academic/curriculum',  'icon' => 'book'],
        ['title' => 'SK Mengajar',     'url' => '/academic/assignments', 'icon' => 'briefcase'],
        ['title' => 'Silabus & RPP',   'url' => '/academic/syllabus',    'icon' => 'folder']
    ];

    foreach ($menus as $m) {
        $check = $db->query("SELECT id FROM menus WHERE url = ?", [$m['url']])->fetch();
        if (!$check) {
            $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) 
                        VALUES (?, ?, ?, ?, 99, 1)", [$parentId, $m['title'], $m['url'], $m['icon']]);
            $newId = $db->getConnection()->lastInsertId();
            
            // Beri akses ke Super Admin (1) dan Guru (3)
            $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$newId]);
            
            // Khusus Silabus, Guru boleh akses
            if ($m['url'] == '/academic/syllabus') {
                $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (3, ?)", [$newId]);
            }
            
            echo "[CREATE] Menu '{$m['title']}' ditambahkan.\n";
        } else {
            echo "[SKIP] Menu '{$m['title']}' sudah ada.\n";
            // Pastikan permission tetap ada (Fixing Permission)
             $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1, ?)", [$check['id']]);
        }
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "--- Migrasi Selesai ---\n";

