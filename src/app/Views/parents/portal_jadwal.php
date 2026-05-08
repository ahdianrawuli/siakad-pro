<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Jadwal Pelajaran';
$pageSubtitle = $student ? 'Kelas: ' . htmlspecialchars($student['class_name'] ?? '-') : 'Pilih santri terlebih dahulu';
$pageBadgeIcon = 'fa-calendar-days';
$infoItems    = [
    'Halaman ini menampilkan jadwal pelajaran santri per hari.',
    'Jadwal dikelompokkan dari Senin hingga Sabtu.',
    'Hubungi admin jika jadwal belum muncul atau ada perubahan.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php $baseUrl = '/portal/orangtua/jadwal'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($days as $day):
            if (empty($grouped[$day])) continue;
        ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-2.5 bg-green-700 font-semibold text-white text-sm"><?= $day ?></div>
            <div class="divide-y divide-slate-100">
                <?php foreach ($grouped[$day] as $sc): ?>
                <div class="flex items-center px-5 py-3 gap-4 hover:bg-slate-50 transition">
                    <div class="text-xs font-mono text-slate-500 w-24 shrink-0 bg-slate-100 rounded-lg px-2 py-1 text-center">
                        <?= substr($sc['start_time'],0,5) ?> – <?= substr($sc['end_time'],0,5) ?>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-800 text-sm"><?= htmlspecialchars($sc['subject_name']) ?></p>
                        <p class="text-xs text-slate-500"><?= htmlspecialchars($sc['teacher_name'] ?? '-') ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty(array_filter($grouped ?? []))): ?>
        <div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">Jadwal belum tersedia.</div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
