<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Ekstrakurikuler';
$pageSubtitle = htmlspecialchars($student['full_name']) . ' — ' . htmlspecialchars($student['class_name'] ?? '-');
$pageBadge    = 'Ekskul Diikuti: ' . count($myEkskul);
$pageBadgeIcon = 'fa-person-running';
$infoItems    = [
    'Halaman ini menampilkan ekskul yang Anda ikuti beserta riwayat kehadiran.',
    'Kehadiran ekskul dicatat oleh pembina/pelatih setiap pertemuan.',
    'H = Hadir, A = Alpa, I = Izin, S = Sakit.',
    'Hubungi pembina ekskul jika ada data yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <!-- Ekskul yang diikuti -->
    <h2 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
        <i class="fa-solid fa-person-running text-green-600"></i> Ekskul Saya
    </h2>
    <?php if (empty($myEkskul)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 mb-6">
            <i class="fa-solid fa-person-running text-3xl mb-2 block opacity-30"></i>
            <p class="text-sm">Belum terdaftar di ekskul manapun.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <?php foreach ($myEkskul as $e): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-bold text-slate-800"><?= htmlspecialchars($e['name']) ?></h3>
                    <span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-full <?= $e['status'] === 'ACTIVE' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' ?>"><?= $e['status'] ?></span>
                </div>
                <?php if ($e['description']): ?><p class="text-xs text-slate-500 mb-3"><?= htmlspecialchars($e['description']) ?></p><?php endif; ?>
                <div class="flex flex-wrap gap-3 text-xs text-slate-600">
                    <?php if ($e['schedule_day']): ?><span class="bg-slate-100 px-2 py-1 rounded-lg"><i class="fa-solid fa-calendar mr-1"></i><?= htmlspecialchars($e['schedule_day']) ?> <?= htmlspecialchars($e['schedule_time'] ?? '') ?></span><?php endif; ?>
                    <?php if ($e['location']): ?><span class="bg-slate-100 px-2 py-1 rounded-lg"><i class="fa-solid fa-location-dot mr-1"></i><?= htmlspecialchars($e['location']) ?></span><?php endif; ?>
                    <?php if ($e['coach_name']): ?><span class="bg-slate-100 px-2 py-1 rounded-lg"><i class="fa-solid fa-user mr-1"></i><?= htmlspecialchars($e['coach_name']) ?></span><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Riwayat Kehadiran Ekskul -->
    <h2 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
        <i class="fa-solid fa-clipboard-check text-blue-500"></i> Riwayat Kehadiran
    </h2>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Ekskul</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($attendance)): ?>
                <tr><td colspan="3" class="text-center py-10 text-slate-400">Belum ada data kehadiran.</td></tr>
                <?php else: ?>
                <?php foreach ($attendance as $a): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 text-slate-600"><?= date('d M Y', strtotime($a['date'])) ?></td>
                    <td class="px-5 py-3 font-medium text-slate-800"><?= htmlspecialchars($a['ekskul_name']) ?></td>
                    <td class="px-5 py-3 text-center">
                        <?php
                        $statusMap = ['H'=>['Hadir','green'],'A'=>['Alpa','red'],'I'=>['Izin','yellow'],'S'=>['Sakit','blue']];
                        [$label,$color] = $statusMap[$a['status']] ?? [$a['status'],'gray'];
                        ?>
                        <span class="bg-<?= $color ?>-100 text-<?= $color ?>-700 text-xs font-bold px-2.5 py-1 rounded-full"><?= $label ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
