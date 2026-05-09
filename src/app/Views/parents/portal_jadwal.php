<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6 pb-24">
<?php
$pageTitle     = 'Jadwal Pelajaran';
$pageSubtitle  = $student ? 'Kelas: ' . htmlspecialchars($student['class_name'] ?? '-') : 'Pilih santri terlebih dahulu';
$pageBadgeIcon = 'fa-calendar-days';
$infoItems     = [
    'Jadwal dikelompokkan per hari dari Senin hingga Sabtu.',
    'Hari ini ditandai dengan highlight khusus.',
    'Hubungi admin jika jadwal belum muncul atau ada perubahan.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

<?php $baseUrl = '/portal/orangtua/jadwal'; require __DIR__ . '/_child_selector.php'; ?>

<?php if (!$student): ?>
<div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">Akun belum terhubung ke data siswa.</div>
<?php else: ?>

<?php
$dayMap = [
    'Sunday'=>'Ahad','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
    'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
];
$todayDay = $dayMap[date('l')] ?? '';

$dayConfig = [
    'SENIN'  => ['bg'=>'bg-blue-600',   'light'=>'bg-blue-50',   'text'=>'text-blue-700'],
    'SELASA' => ['bg'=>'bg-violet-600', 'light'=>'bg-violet-50', 'text'=>'text-violet-700'],
    'RABU'   => ['bg'=>'bg-emerald-600','light'=>'bg-emerald-50','text'=>'text-emerald-700'],
    'KAMIS'  => ['bg'=>'bg-amber-500',  'light'=>'bg-amber-50',  'text'=>'text-amber-700'],
    'JUMAT'  => ['bg'=>'bg-rose-600',   'light'=>'bg-rose-50',   'text'=>'text-rose-700'],
    'SABTU'  => ['bg'=>'bg-slate-600',  'light'=>'bg-slate-50',  'text'=>'text-slate-700'],
    'AHAD'   => ['bg'=>'bg-teal-600',   'light'=>'bg-teal-50',   'text'=>'text-teal-700'],
];

// Hitung total sesi
$totalSessions = 0;
foreach ($grouped as $g) $totalSessions += count($g);
?>

<!-- Summary strip -->
<div class="flex flex-wrap gap-3 mb-5">
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-2.5 flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-book-open text-green-600 text-sm"></i>
        <span class="text-sm font-bold text-slate-700"><?= $totalSessions ?> <span class="font-normal text-slate-400">sesi/minggu</span></span>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-2.5 flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-calendar-week text-blue-500 text-sm"></i>
        <span class="text-sm font-bold text-slate-700"><?= count(array_filter($grouped)) ?> <span class="font-normal text-slate-400">hari aktif</span></span>
    </div>
    <?php $todayKey = strtoupper($todayDay); if ($todayKey && !empty($grouped[$todayKey])): ?>
    <div class="bg-green-50 rounded-xl border border-green-200 px-4 py-2.5 flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-circle text-green-500 text-xs animate-pulse"></i>
        <span class="text-sm font-bold text-green-700">Hari ini: <?= $todayDay ?> — <?= count($grouped[$todayKey]) ?> sesi</span>
    </div>
    <?php endif; ?>
</div>

<?php
$hasAny = false;
foreach ($days as $day):
    if (empty($grouped[$day])) continue;
    $hasAny  = true;
    $cfg     = $dayConfig[$day] ?? $dayConfig['SABTU'];
    $isToday = (strtoupper($todayDay) === $day);
    $sessions = $grouped[$day];
?>
<div class="rounded-2xl overflow-hidden shadow-sm border mb-4 <?= $isToday ? 'border-green-400 ring-2 ring-green-300/50' : 'border-slate-200' ?>">

    <div class="<?= $cfg['bg'] ?> px-5 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="fa-solid fa-calendar-day text-white text-sm"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-white tracking-wide"><?= ucfirst(strtolower($day)) ?></h3>
                <?php if ($isToday): ?><span class="text-[10px] font-bold text-white/80 uppercase tracking-widest">Hari Ini</span><?php endif; ?>
            </div>
        </div>
        <span class="bg-white/20 text-white text-xs font-bold px-2.5 py-1 rounded-full"><?= count($sessions) ?> sesi</span>
    </div>

    <div class="bg-white divide-y divide-slate-100">
        <?php foreach ($sessions as $i => $sc):
            $now      = date('H:i:s');
            $isActive = $isToday && $now >= $sc['start_time'] && $now <= $sc['end_time'];
        ?>
        <div class="flex items-center gap-4 px-5 py-3.5 <?= $isActive ? $cfg['light'] : 'hover:bg-slate-50' ?> transition">
            <div class="w-6 h-6 rounded-full <?= $isActive ? $cfg['bg'] : 'bg-slate-100' ?> flex items-center justify-center shrink-0">
                <span class="text-[10px] font-extrabold <?= $isActive ? 'text-white' : 'text-slate-400' ?>"><?= $i + 1 ?></span>
            </div>
            <div class="shrink-0 text-center">
                <div class="text-xs font-bold <?= $isActive ? $cfg['text'] : 'text-slate-700' ?> font-mono"><?= substr($sc['start_time'],0,5) ?></div>
                <div class="w-px h-3 bg-slate-300 mx-auto my-0.5"></div>
                <div class="text-xs font-mono text-slate-400"><?= substr($sc['end_time'],0,5) ?></div>
            </div>
            <div class="w-px h-10 <?= $isActive ? $cfg['bg'] : 'bg-slate-200' ?> shrink-0 rounded-full"></div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-slate-800 text-sm truncate <?= $isActive ? $cfg['text'] : '' ?>">
                    <?= htmlspecialchars($sc['subject_name']) ?>
                    <?php if ($isActive): ?>
                    <span class="ml-2 inline-flex items-center gap-1 text-[9px] font-bold bg-green-100 text-green-700 px-1.5 py-0.5 rounded-full">
                        <i class="fa-solid fa-circle text-[6px] animate-pulse"></i> BERLANGSUNG
                    </span>
                    <?php endif; ?>
                </p>
                <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-chalkboard-user text-[10px]"></i>
                    <?= htmlspecialchars($sc['teacher_name'] ?? 'Guru belum ditentukan') ?>
                </p>
            </div>
            <?php
            $dur = round((strtotime($sc['end_time']) - strtotime($sc['start_time'])) / 60);
            ?>
            <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-1 rounded-lg shrink-0"><?= $dur ?> mnt</span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<?php if (!$hasAny): ?>
<div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">
    <i class="fa-solid fa-calendar-xmark text-4xl mb-3 block opacity-20"></i>
    <p class="font-medium">Jadwal belum tersedia.</p>
</div>
<?php endif; ?>

<?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
