<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Jadwal Pelajaran';
$pageSubtitle = 'Kelas ' . htmlspecialchars($student['class_name'] ?? '-');
$pageBadgeIcon = 'fa-calendar-days';
$infoItems    = [
    'Halaman ini menampilkan jadwal pelajaran kelas Anda.',
    'Jadwal dikelompokkan per hari dari Senin hingga Sabtu.',
    'Hubungi admin jika jadwal belum muncul atau ada perubahan.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <?php
    $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $dayColors = ['Senin'=>'blue','Selasa'=>'green','Rabu'=>'purple','Kamis'=>'orange','Jumat'=>'red','Sabtu'=>'slate'];
    ?>

    <?php if (empty($byDay)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
            <i class="fa-solid fa-calendar-xmark text-4xl mb-3 block opacity-30"></i>
            <p class="font-medium">Jadwal belum tersedia untuk kelas Anda.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($days as $day): ?>
                <?php if (empty($byDay[$day])) continue; ?>
                <?php $color = $dayColors[$day] ?? 'slate'; ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-<?= $color ?>-600 px-5 py-2.5">
                        <h3 class="font-bold text-white text-sm tracking-wide"><?= $day ?></h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($byDay[$day] as $s): ?>
                        <div class="flex items-center px-5 py-3 gap-4 hover:bg-slate-50 transition">
                            <div class="text-xs font-mono text-slate-500 w-24 shrink-0 bg-slate-100 rounded-lg px-2 py-1 text-center">
                                <?= substr($s['start_time'],0,5) ?> – <?= substr($s['end_time'],0,5) ?>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($s['subject_name']) ?></p>
                                <p class="text-xs text-slate-500"><?= htmlspecialchars($s['teacher_name'] ?? '-') ?></p>
                            </div>
                            <?php if (!empty($s['room'])): ?>
                            <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-lg"><?= htmlspecialchars($s['room']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
