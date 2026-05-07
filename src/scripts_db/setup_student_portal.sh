#!/bin/sh
# Jalankan: docker exec siakad-pro-php_fpm-1 sh /var/www/html/scripts_db/setup_student_portal.sh

php << 'PHPEOF'
<?php
error_reporting(E_ALL);
$pdo = new PDO('mysql:host=mariadb_db;dbname=siakad_db;charset=utf8mb4', 'siakad_user', 'secret_password_secure_123');

$tables = [
    'announcements' => "CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        content TEXT NOT NULL,
        category VARCHAR(50) NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    'extracurriculars' => "CREATE TABLE IF NOT EXISTS extracurriculars (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT NULL,
        schedule_day VARCHAR(20) NULL,
        schedule_time VARCHAR(20) NULL,
        location VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    'extracurricular_coaches' => "CREATE TABLE IF NOT EXISTS extracurricular_coaches (
        extracurricular_id INT NOT NULL,
        user_id INT NOT NULL,
        PRIMARY KEY (extracurricular_id, user_id),
        FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    'student_extracurriculars' => "CREATE TABLE IF NOT EXISTS student_extracurriculars (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        extracurricular_id INT NOT NULL,
        status ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE
    )",
    'extracurricular_schedules' => "CREATE TABLE IF NOT EXISTS extracurricular_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        extracurricular_id INT NOT NULL,
        date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (extracurricular_id) REFERENCES extracurriculars(id) ON DELETE CASCADE
    )",
    'extracurricular_attendances' => "CREATE TABLE IF NOT EXISTS extracurricular_attendances (
        id INT AUTO_INCREMENT PRIMARY KEY,
        schedule_id INT NOT NULL,
        student_id INT NOT NULL,
        status ENUM('H','A','I','S') DEFAULT 'H',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (schedule_id) REFERENCES extracurricular_schedules(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )",
];

echo "=== SETUP TABEL ===\n";
foreach ($tables as $name => $sql) {
    try {
        $pdo->exec($sql);
        $c = $pdo->query("SELECT COUNT(*) FROM $name")->fetchColumn();
        echo "OK  $name ($c rows)\n";
    } catch (Exception $e) {
        echo "ERR $name: " . $e->getMessage() . "\n";
    }
}

echo "\n=== SEED DATA ===\n";
if ($pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn() == 0) {
    $pdo->exec("INSERT INTO announcements (title,content,category) VALUES
        ('Libur Hari Raya Idul Fitri','Pesantren libur mulai tanggal 10-20 April 2026.','AKADEMIK'),
        ('Pendaftaran Ekstrakurikuler Dibuka','Pendaftaran ekskul tahun ajaran baru dibuka hingga akhir bulan ini.','KEGIATAN')");
    echo "SEED 2 announcements\n";
} else {
    echo "SKIP announcements (sudah ada data)\n";
}

echo "\n=== SETUP MENU ===\n";
$menus = [
    [213,'Pengumuman','/student/announcements','bullhorn',13],
    [214,'Ekstrakurikuler','/student/extracurricular','person-running',14],
    [215,'Asrama','/student/boarding','bed',15],
    [216,'Pelanggaran & Prestasi','/student/discipline','shield-halved',16],
    [217,'Surat Keterangan','/student/letter','file-lines',17],
    [218,'Kesehatan','/student/health','heart-pulse',18],
];
foreach ($menus as [$id,$title,$url,$icon,$order]) {
    if (!$pdo->query("SELECT id FROM menus WHERE id=$id")->fetch()) {
        $pdo->prepare("INSERT INTO menus (id,parent_id,title,url,icon,order_num,is_active) VALUES (?,NULL,?,?,?,?,1)")
            ->execute([$id,$title,$url,$icon,$order]);
        $pdo->prepare("INSERT IGNORE INTO role_menus (role_id,menu_id) VALUES (4,?)")->execute([$id]);
        echo "ADD  [$id] $title\n";
    } else {
        echo "SKIP [$id] $title\n";
    }
}

echo "\n=== TEST SEMUA QUERY ===\n";
$sid = 5;
$student = $pdo->query("SELECT s.*,c.name as class_name FROM students s LEFT JOIN classrooms c ON s.classroom_id=c.id WHERE s.id=$sid")->fetch(PDO::FETCH_ASSOC);
$year = $pdo->query("SELECT id FROM academic_years WHERE is_active=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$tests = [
    'dashboard'       => "SELECT COUNT(*) FROM bills WHERE student_id=$sid AND status='UNPAID'",
    'schedule'        => "SELECT COUNT(*) FROM schedules WHERE classroom_id={$student['classroom_id']} AND academic_year_id={$year['id']}",
    'attendance'      => "SELECT COUNT(*) FROM attendances WHERE student_id=$sid",
    'grades'          => "SELECT COUNT(*) FROM student_grades sg JOIN schedules sch ON sg.schedule_id=sch.id JOIN subjects s ON sch.subject_id=s.id WHERE sg.student_id=$sid",
    'payment'         => "SELECT COUNT(*) FROM bills WHERE student_id=$sid",
    'documents'       => "SELECT COUNT(*) FROM ppdb_documents WHERE candidate_id=$sid",
    'announcements'   => "SELECT COUNT(*) FROM announcements WHERE is_active=1",
    'extracurricular' => "SELECT COUNT(*) FROM student_extracurriculars WHERE student_id=$sid",
    'boarding/dorm'   => "SELECT COUNT(*) FROM dorms",
    'boarding/grades' => "SELECT COUNT(*) FROM boarding_grades WHERE student_id=$sid",
    'worship_logs'    => "SELECT COUNT(*) FROM worship_logs WHERE student_id=$sid",
    'permits'         => "SELECT COUNT(*) FROM permits WHERE student_id=$sid",
    'violations'      => "SELECT COUNT(*) FROM student_violations WHERE student_id=$sid",
    'achievements'    => "SELECT COUNT(*) FROM student_achievements WHERE student_id=$sid",
    'letter_templates'=> "SELECT COUNT(*) FROM letter_templates",
    'health_records'  => "SELECT COUNT(*) FROM health_records WHERE student_id=$sid",
];

$allOk = true;
foreach ($tests as $name => $sql) {
    try {
        $c = $pdo->query($sql)->fetchColumn();
        echo str_pad($name,25)." OK ($c)\n";
    } catch (Exception $e) {
        echo str_pad($name,25)." ERR: ".$e->getMessage()."\n";
        $allOk = false;
    }
}

echo "\n=== MENU SISWA AKTIF ===\n";
$stmt = $pdo->prepare("SELECT m.id,m.title,m.url FROM menus m JOIN role_menus rm ON m.id=rm.menu_id WHERE rm.role_id=4 AND m.is_active=1 AND m.id NOT BETWEEN 900 AND 909 ORDER BY m.order_num");
$stmt->execute();
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $m) {
    echo "  [{$m['id']}] {$m['title']} -> {$m['url']}\n";
}

echo "\n".($allOk ? "✅ SEMUA OK" : "❌ ADA ERROR")."\n";
PHPEOF
