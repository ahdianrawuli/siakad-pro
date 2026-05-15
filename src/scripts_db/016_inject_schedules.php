<?php
require_once __DIR__ . '/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();
$yearId = $db->query("SELECT id FROM academic_years WHERE is_active=1 LIMIT 1")->fetchColumn();
$teachers = $db->query("SELECT id FROM users WHERE role_id=3 ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
$classrooms = $db->query("SELECT id, major FROM classrooms ORDER BY major, level, name")->fetchAll();

// Hapus jadwal lama
$db->query("DELETE FROM schedules WHERE academic_year_id = ?", [$yearId]);
echo "Jadwal lama dihapus.\n";

$days = ['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU'];

// Mapel per jenjang (IDs 1-20)
$subjectMap = [
    'MTS' => [1,2,3,4,5,7,8,9,10,11,12,13,14], // Diniyah + Umum (tanpa Kimia/Fisika/Bio/Eko/Geo)
    'MA'  => [1,2,3,4,5,7,8,9,16,17,18,19,20],  // Diniyah + IPA/IPS
    'PDF' => [1,2,3,4,5,6],                       // Diniyah + Tahfidz
];

// Slot waktu per hari
$slots = [
    ['07:00','07:45'],['07:45','08:30'],['08:30','09:15'],
    ['09:30','10:15'],['10:15','11:00'],['11:00','11:45'],
];

$count = 0;
$ti = 0; // teacher index rotator

foreach ($classrooms as $room) {
    $major = $room['major'];
    $subjects = $subjectMap[$major] ?? $subjectMap['MTS'];

    // Distribusi mapel ke hari (setiap hari 5-6 slot)
    $schedule = []; // [day => [subject_id, ...]]
    $si = 0;
    foreach ($days as $day) {
        $schedule[$day] = [];
        for ($slot = 0; $slot < count($slots); $slot++) {
            $schedule[$day][] = $subjects[$si % count($subjects)];
            $si++;
        }
    }

    foreach ($days as $day) {
        foreach ($slots as $idx => $time) {
            $subjectId = $schedule[$day][$idx];
            $teacherId = $teachers[$ti % count($teachers)];
            $ti++;

            $db->query(
                "INSERT INTO schedules (academic_year_id, classroom_id, subject_id, teacher_id, day, start_time, end_time) VALUES (?,?,?,?,?,?,?)",
                [$yearId, $room['id'], $subjectId, $teacherId, $day, $time[0], $time[1]]
            );
            $count++;
        }
    }
}

echo "[OK] $count jadwal diinjeksi untuk " . count($classrooms) . " kelas.\n";
