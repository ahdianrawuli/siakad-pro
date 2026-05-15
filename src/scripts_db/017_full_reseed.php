<?php
require_once __DIR__ . '/bootstrap.php';
use App\Core\Database;

echo "=== FULL DATA RE-SEED ===\n";
$db = Database::getInstance();

// ============================================================
// 1. HAPUS DATA LAMA (urutan FK-safe)
// ============================================================
$tables = ['syllabus_documents','schedules','student_grades','student_extracurriculars',
    'extracurricular_attendances','extracurricular_coaches','extracurricular_schedules',
    'attendances','student_violations','permits','health_records','worship_logs',
    'boarding_activities','dorm_supervisors','bills','transactions',
    'student_candidates','ppdb_payments','alumni','students','dorms','classrooms',
    'teaching_assignments','subjects'];

foreach ($tables as $t) {
    try { $db->query("SET FOREIGN_KEY_CHECKS=0"); $db->query("DELETE FROM $t"); $db->query("SET FOREIGN_KEY_CHECKS=1"); } catch (\Exception $e) {}
}
echo "[OK] Data lama dihapus.\n";

// ============================================================
// 2. CLASSROOMS (14 kelas)
// ============================================================
$db->query("ALTER TABLE classrooms AUTO_INCREMENT = 1");
$classrooms = [
    ['VII Umar','MTS',7],['VII Khadijah','MTS',7],
    ['VIII Utsman','MTS',8],['VIII Fatimah','MTS',8],
    ['IX Ali','MTS',9],['IX Aisyah','MTS',9],
    ['X Khalid','MA',10],['X Maryam','MA',10],
    ['XI Salman','MA',11],['XI Zainab','MA',11],
    ['XII Bilal','MA',12],['XII Hafsah','MA',12],
    ['PDF Ar-Rahman','PDF',1],['PDF Al-Furqan','PDF',2],
];
foreach ($classrooms as $c) {
    $db->query("INSERT INTO classrooms (name, major, level) VALUES (?,?,?)", $c);
}
echo "[OK] 14 classrooms.\n";

// ============================================================
// 3. SUBJECTS (20 mapel)
// ============================================================
$db->query("ALTER TABLE subjects AUTO_INCREMENT = 1");
$subjects = [
    ['QH','Al-Quran & Hadits','Diniyah',75],['FQ','Fiqih','Diniyah',75],['AA','Aqidah Akhlak','Diniyah',75],
    ['SKI','Sejarah Kebudayaan Islam','Diniyah',75],['BA','Bahasa Arab','Diniyah',75],['THF','Tahfidz Al-Quran','Tahfidz',75],
    ['MTK','Matematika','Umum',70],['BIN','Bahasa Indonesia','Umum',70],['BIG','Bahasa Inggris','Umum',70],
    ['IPA','Ilmu Pengetahuan Alam','Umum',70],['IPS','Ilmu Pengetahuan Sosial','Umum',70],
    ['PKN','Pendidikan Kewarganegaraan','Umum',70],['SBP','Seni Budaya & Prakarya','Umum',70],
    ['PJK','Pendidikan Jasmani','Umum',70],['TIK','Teknologi Informasi','Umum',70],
    ['KIM','Kimia','Umum',70],['FIS','Fisika','Umum',70],['BIO','Biologi','Umum',70],
    ['EKO','Ekonomi','Umum',70],['GEO','Geografi','Umum',70],
];
foreach ($subjects as $s) {
    $db->query("INSERT INTO subjects (code, name, type, kkm) VALUES (?,?,?,?)", $s);
}
echo "[OK] 20 subjects.\n";

// ============================================================
// 4. TEACHERS (12 guru) — role_id=3
// ============================================================
$teacherNames = [
    'Drs. H. Ahmad Fauzi, M.Pd','Ustadzah Fatimah, S.Ag','Ust. Muhammad Rizki, Lc',
    'Hj. Siti Aminah, M.Pd.I','Ust. Zulkifli, S.Pd','Ustadzah Maryam, S.Pd',
    'Ust. Ibrahim, M.A','Ustadzah Khadijah, S.Pd','Ust. Yusuf, S.Pd.I',
    'Ustadzah Aisyah, M.Pd','Ust. Hamzah, S.Ag','Ustadzah Hafshah, S.Pd',
];
$existingTeachers = $db->query("SELECT COUNT(*) FROM users WHERE role_id=3")->fetchColumn();
if ($existingTeachers < 12) {
    foreach ($teacherNames as $i => $name) {
        $username = 'guru' . ($i+1);
        $exists = $db->query("SELECT id FROM users WHERE username=?", [$username])->fetch();
        if (!$exists) {
            $db->query("INSERT INTO users (name, username, email, password, role_id, status) VALUES (?,?,?,?,3,'active')", [
                $name, $username, $username.'@siakad.com', password_hash('password', PASSWORD_BCRYPT)
            ]);
        }
    }
}
$teachers = $db->query("SELECT id FROM users WHERE role_id=3 ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
echo "[OK] " . count($teachers) . " teachers.\n";

// ============================================================
// 5. DORMS (6 asrama)
// ============================================================
$db->query("ALTER TABLE dorms AUTO_INCREMENT = 1");
$dorms = [
    ['Asrama Al-Fatih (MTS Putra)','MTS','L',60],
    ['Asrama Ibnu Sina (MA Putra)','MA','L',60],
    ['Asrama Khadijah (MTS Putri)','MTS','P',60],
    ['Asrama Aisyah (MA Putri)','MA','P',60],
    ['Asrama Ar-Rahim (PDF Putra)','PDF','L',40],
    ['Asrama Ar-Rahim (PDF Putri)','PDF','P',40],
];
foreach ($dorms as $d) {
    $db->query("INSERT INTO dorms (name, unit, gender, capacity) VALUES (?,?,?,?)", $d);
}
$dormIds = $db->query("SELECT id, unit, gender FROM dorms ORDER BY id")->fetchAll();
echo "[OK] 6 dorms.\n";

// ============================================================
// 6. STUDENTS (93 santri) + assign ke kelas & asrama
// ============================================================
$db->query("ALTER TABLE students AUTO_INCREMENT = 1");
$firstNames = ['Ahmad','Muhammad','Abdullah','Umar','Ali','Hasan','Husain','Bilal','Zaid','Khalid','Salman','Yusuf','Ibrahim','Ismail','Hamzah'];
$firstNamesFemale = ['Fatimah','Aisyah','Khadijah','Maryam','Hafshah','Zainab','Ruqayyah','Safiyyah','Ummu','Halimah','Aminah','Asma','Sumayyah','Hajar','Ramlah'];
$lastNames = ['Hakim','Fauzan','Syahputra','Hidayat','Rahman','Fikri','Maulana','Akbar','Rizki','Pratama','Saputra','Ramadhan','Ilham','Fadli','Hadi'];

$allClassrooms = $db->query("SELECT id, major, level FROM classrooms ORDER BY id")->fetchAll();
$studentCount = 0;

foreach ($allClassrooms as $ci => $class) {
    // 6-7 siswa per kelas, campuran L/P
    $perClass = ($ci < 12) ? 7 : 5; // PDF lebih sedikit
    for ($j = 0; $j < $perClass; $j++) {
        $gender = ($j % 2 === 0) ? 'L' : 'P';
        $fn = $gender === 'L' ? $firstNames[array_rand($firstNames)] : $firstNamesFemale[array_rand($firstNamesFemale)];
        $ln = $lastNames[array_rand($lastNames)];
        $fullName = "$fn $ln";
        $nis = '2025' . str_pad($studentCount + 1, 4, '0', STR_PAD_LEFT);

        // Cari dorm yang sesuai
        $dormId = null;
        foreach ($dormIds as $d) {
            if ($d['unit'] === $class['major'] && $d['gender'] === $gender) { $dormId = $d['id']; break; }
        }

        $db->query("INSERT INTO students (full_name, nis, gender, classroom_id, dorm_id, status, birth_place, birth_date) VALUES (?,?,?,?,?,'ACTIVE','Bukittinggi',?)", [
            $fullName, $nis, $gender, $class['id'], $dormId, '200' . rand(5,9) . '-' . str_pad(rand(1,12),2,'0',STR_PAD_LEFT) . '-' . str_pad(rand(1,28),2,'0',STR_PAD_LEFT)
        ]);
        $studentCount++;
    }
}
echo "[OK] $studentCount students.\n";

// ============================================================
// 7. ACADEMIC YEAR
// ============================================================
$yearExists = $db->query("SELECT id FROM academic_years WHERE is_active=1")->fetch();
if (!$yearExists) {
    $db->query("INSERT INTO academic_years (name, semester, is_active) VALUES ('2025/2026','Ganjil',1)");
}
echo "[OK] Academic year.\n";

// ============================================================
// 8. FIX MENU — tambah Manajemen Role, Denah Kamar, Rekap Pelanggaran
// ============================================================
$menuFixes = [
    [90, 'Manajemen Role', '/settings/roles', 'user-shield'],
    [90, 'Manajemen Menu', '/settings/menus', 'bars'],
    [50, 'Denah Kamar', '/asrama/map', 'map'],
    [50, 'Rekap Pelanggaran', '/asrama/violations', 'triangle-exclamation'],
];
foreach ($menuFixes as $m) {
    $exists = $db->query("SELECT id FROM menus WHERE url=?", [$m[2]])->fetch();
    if (!$exists) {
        $db->query("INSERT INTO menus (parent_id, title, url, icon, order_num, is_active) VALUES (?,?,?,?,99,1)", [$m[0], $m[1], $m[2], $m[3]]);
        $newId = $db->getConnection()->lastInsertId();
        $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1,?)", [$newId]);
        echo "[ADD] Menu: {$m[1]}\n";
    }
}

// Pastikan semua menu punya akses super-admin (role_id=1)
$allMenus = $db->query("SELECT id FROM menus WHERE is_active=1")->fetchAll(PDO::FETCH_COLUMN);
foreach ($allMenus as $mid) {
    $db->query("INSERT IGNORE INTO role_menus (role_id, menu_id) VALUES (1,?)", [$mid]);
}
echo "[OK] Menu & permissions fixed.\n";

// ============================================================
// 9. VIOLATION TYPES
// ============================================================
$vtCount = $db->query("SELECT COUNT(*) FROM violation_types")->fetchColumn();
if ($vtCount < 5) {
    $vts = [
        ['Terlambat masuk kelas',5,'RINGAN'],['Tidak mengerjakan tugas',5,'RINGAN'],
        ['Tidak memakai seragam lengkap',10,'RINGAN'],['Berkelahi',25,'SEDANG'],
        ['Merokok',30,'SEDANG'],['Membawa HP tanpa izin',15,'RINGAN'],
        ['Bolos pelajaran',20,'SEDANG'],['Merusak fasilitas',50,'BERAT'],
        ['Mencuri',75,'BERAT'],
    ];
    foreach ($vts as $v) {
        $db->query("INSERT IGNORE INTO violation_types (name, points, category) VALUES (?,?,?)", $v);
    }
}
echo "[OK] Violation types.\n";

// ============================================================
// 10. PPDB TRACKS
// ============================================================
$trackCount = $db->query("SELECT COUNT(*) FROM ppdb_tracks")->fetchColumn();
if ($trackCount < 3) {
    $tracks = [
        ['Reguler MTs','MTS','REG-MTS',80],['Tahfidz MTs','MTS','THF-MTS',40],
        ['Reguler MA','MA','REG-MA',80],['Tahfidz MA','MA','THF-MA',40],
        ['Prestasi','MA','PRES-MA',20],['Reguler PDF','PDF','REG-PDF',50],
    ];
    foreach ($tracks as $t) {
        $db->query("INSERT INTO ppdb_tracks (name, level, code, quota, is_active) VALUES (?,?,?,?,1)", $t);
    }
}
echo "[OK] PPDB tracks.\n";

echo "\n=== SELESAI ===\n";
