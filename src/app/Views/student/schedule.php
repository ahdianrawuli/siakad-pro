<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6 pb-24">
<?php
$pageTitle     = 'Jadwal Pelajaran';
$pageSubtitle  = 'Kelas ' . htmlspecialchars($student['class_name'] ?? '-');
$pageBadgeIcon = 'fa-calendar-days';
$infoItems     = [
    'Jadwal dikelompokkan per hari dari Senin hingga Sabtu.',
    'Hari ini ditandai dengan highlight khusus.',
    'Hubungi admin jika jadwal belum muncul atau ada perubahan.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

<?php \App\Core\Session::flash(); ?>

<?php
$days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad'];
$dayMap = [
    'Sunday'=>'Ahad','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
    'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
];
$todayDay = $dayMap[date('l')] ?? '';

$dayConfig = [
    'Senin'  => ['color'=>'blue',   'bg'=>'bg-blue-600',   'light'=>'bg-blue-50',   'border'=>'border-blue-200',  'text'=>'text-blue-700',  'icon'=>'fa-1'],
    'Selasa' => ['color'=>'violet', 'bg'=>'bg-violet-600', 'light'=>'bg-violet-50', 'border'=>'border-violet-200','text'=>'text-violet-700','icon'=>'fa-2'],
    'Rabu'   => ['color'=>'emerald','bg'=>'bg-emerald-600','light'=>'bg-emerald-50','border'=>'border-emerald-200','text'=>'text-emerald-700','icon'=>'fa-3'],
    'Kamis'  => ['color'=>'amber',  'bg'=>'bg-amber-500',  'light'=>'bg-amber-50',  'border'=>'border-amber-200', 'text'=>'text-amber-700', 'icon'=>'fa-4'],
    'Jumat'  => ['color'=>'rose',   'bg'=>'bg-rose-600',   'light'=>'bg-rose-50',   'border'=>'border-rose-200',  'text'=>'text-rose-700',  'icon'=>'fa-5'],
    'Sabtu'  => ['color'=>'slate',  'bg'=>'bg-slate-600',  'light'=>'bg-slate-50',  'border'=>'border-slate-200', 'text'=>'text-slate-700', 'icon'=>'fa-6'],
    'Ahad'   => ['color'=>'teal',   'bg'=>'bg-teal-600',   'light'=>'bg-teal-50',   'border'=>'border-teal-200',  'text'=>'text-teal-700',  'icon'=>'fa-7'],
];

// Hitung total jam pelajaran
$totalSessions = 0;
foreach ($byDay as $sessions) $totalSessions += count($sessions);
?>

<?php if (empty($byDay)): ?>
<div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
    <i class="fa-solid fa-calendar-xmark text-5xl mb-4 block opacity-20"></i>
    <p class="font-semibold text-slate-500">Jadwal belum tersedia untuk kelas Anda.</p>
    <p class="text-sm text-slate-400 mt-1">Hubungi admin atau wali kelas untuk informasi lebih lanjut.</p>
</div>
<?php else: ?>

<!-- Summary strip -->
<div class="flex flex-wrap gap-3 mb-5">
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-2.5 flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-book-open text-green-600 text-sm"></i>
        <span class="text-sm font-bold text-slate-700"><?= $totalSessions ?> <span class="font-normal text-slate-400">sesi/minggu</span></span>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-2.5 flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-calendar-week text-blue-500 text-sm"></i>
        <span class="text-sm font-bold text-slate-700"><?= count($byDay) ?> <span class="font-normal text-slate-400">hari aktif</span></span>
    </div>
    <?php if ($todayDay && isset($byDay[$todayDay])): ?>
    <div class="bg-green-50 rounded-xl border border-green-200 px-4 py-2.5 flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-circle text-green-500 text-xs animate-pulse"></i>
        <span class="text-sm font-bold text-green-700">Hari ini: <?= $todayDay ?> — <?= count($byDay[$todayDay]) ?> sesi</span>
    </div>
    <?php endif; ?>
</div>

<!-- Jadwal per hari -->
<div class="space-y-4">
<?php foreach ($days as $day):
    if (empty($byDay[$day])) continue;
    $cfg     = $dayConfig[$day] ?? $dayConfig['Sabtu'];
    $isToday = ($day === $todayDay);
    $sessions = $byDay[$day];
?>
<div class="rounded-2xl overflow-hidden shadow-sm border <?= $isToday ? 'border-green-400 ring-2 ring-green-300/50' : 'border-slate-200' ?>">

    <!-- Day header -->
    <div class="<?= $cfg['bg'] ?> px-5 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="fa-solid fa-calendar-day text-white text-sm"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-white tracking-wide"><?= $day ?></h3>
                <?php if ($isToday): ?>
                <span class="text-[10px] font-bold text-white/80 uppercase tracking-widest">Hari Ini</span>
                <?php endif; ?>
            </div>
        </div>
        <span class="bg-white/20 text-white text-xs font-bold px-2.5 py-1 rounded-full">
            <?= count($sessions) ?> sesi
        </span>
    </div>

    <!-- Sessions -->
    <div class="bg-white divide-y divide-slate-100">
        <?php foreach ($sessions as $i => $s):
            $now      = date('H:i:s');
            $isActive = $isToday && $now >= $s['start_time'] && $now <= $s['end_time'];
        ?>
        <div class="flex items-center gap-4 px-5 py-3.5 <?= $isActive ? $cfg['light'] : 'hover:bg-slate-50' ?> transition group">

            <!-- Nomor urut -->
            <div class="w-6 h-6 rounded-full <?= $isActive ? $cfg['bg'] : 'bg-slate-100' ?> flex items-center justify-center shrink-0">
                <span class="text-[10px] font-extrabold <?= $isActive ? 'text-white' : 'text-slate-400' ?>"><?= $i + 1 ?></span>
            </div>

            <!-- Waktu -->
            <div class="shrink-0 text-center">
                <div class="text-xs font-bold <?= $isActive ? $cfg['text'] : 'text-slate-700' ?> font-mono">
                    <?= substr($s['start_time'], 0, 5) ?>
                </div>
                <div class="w-px h-3 bg-slate-300 mx-auto my-0.5"></div>
                <div class="text-xs font-mono text-slate-400">
                    <?= substr($s['end_time'], 0, 5) ?>
                </div>
            </div>

            <!-- Divider -->
            <div class="w-px h-10 <?= $isActive ? $cfg['bg'] : 'bg-slate-200' ?> shrink-0 rounded-full"></div>

            <!-- Mata pelajaran -->
            <div class="flex-1 min-w-0">
                <p class="font-bold text-slate-800 text-sm truncate <?= $isActive ? $cfg['text'] : '' ?>">
                    <?= htmlspecialchars($s['subject_name']) ?>
                    <?php if ($isActive): ?>
                    <span class="ml-2 inline-flex items-center gap-1 text-[9px] font-bold bg-green-100 text-green-700 px-1.5 py-0.5 rounded-full">
                        <i class="fa-solid fa-circle text-[6px] animate-pulse"></i> BERLANGSUNG
                    </span>
                    <?php endif; ?>
                </p>
                <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-chalkboard-user text-[10px]"></i>
                    <?= htmlspecialchars($s['teacher_name'] ?? 'Guru belum ditentukan') ?>
                </p>
            </div>

            <!-- Durasi -->
            <?php
            $start = strtotime($s['start_time']);
            $end   = strtotime($s['end_time']);
            $dur   = round(($end - $start) / 60);
            ?>
            <div class="shrink-0 text-right">
                <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">
                    <?= $dur ?> mnt
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
