<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Pelanggaran & Prestasi';
$pageSubtitle = htmlspecialchars($student['full_name'] ?? '');
$pageBadge    = 'Total Poin: ' . $totalPoints . ' / 100';
$pageBadgeIcon = 'fa-triangle-exclamation';
$infoItems    = [
    'Halaman ini menampilkan catatan pelanggaran dan prestasi Anda.',
    'Setiap pelanggaran memiliki poin sesuai kategori: Ringan, Sedang, atau Berat.',
    'Jika total poin mencapai 100, akan ada tindakan dari pihak pesantren.',
    'Prestasi yang diraih juga dicatat di sini sebagai apresiasi.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <!-- Ringkasan Poin -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-red-50 border border-red-100 rounded-2xl p-4">
            <p class="text-xs text-red-500 font-bold uppercase mb-1">Total Poin Pelanggaran</p>
            <p class="text-3xl font-extrabold text-red-700"><?= $totalPoints ?></p>
            <div class="mt-2 bg-red-100 rounded-full h-2">
                <div class="bg-red-500 h-2 rounded-full transition-all" style="width: <?= min($totalPoints, 100) ?>%"></div>
            </div>
            <p class="text-xs text-red-400 mt-1">Batas maksimal: 100 poin</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-4">
            <p class="text-xs text-yellow-600 font-bold uppercase mb-1">Total Prestasi</p>
            <p class="text-3xl font-extrabold text-yellow-700"><?= count($achievements) ?></p>
            <p class="text-xs text-yellow-500 mt-1">Penghargaan diraih</p>
        </div>
    </div>

    <!-- Prestasi -->
    <h2 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
        <i class="fa-solid fa-trophy text-yellow-500"></i> Prestasi
    </h2>
    <?php if (empty($achievements)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 mb-6">
            <p class="text-sm">Belum ada prestasi tercatat.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3 mb-6">
        <?php foreach ($achievements as $a):
            $levelColor = ['NASIONAL'=>'purple','PROVINSI'=>'blue','KABUPATEN'=>'green','KECAMATAN'=>'yellow','SEKOLAH'=>'gray'][$a['level']] ?? 'gray';
        ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 flex items-start gap-4">
                <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-slate-800"><?= htmlspecialchars($a['title']) ?></h3>
                    <p class="text-xs text-slate-500 mt-0.5"><?= date('d M Y', strtotime($a['date'])) ?></p>
                    <?php if ($a['description']): ?><p class="text-sm text-slate-600 mt-1"><?= htmlspecialchars($a['description']) ?></p><?php endif; ?>
                </div>
                <span class="shrink-0 bg-<?= $levelColor ?>-100 text-<?= $levelColor ?>-700 text-xs font-bold px-2.5 py-1 rounded-full"><?= $a['level'] ?></span>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Pelanggaran -->
    <h2 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation text-red-500"></i> Catatan Pelanggaran
    </h2>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Pelanggaran</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Poin</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Kategori</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($violations)): ?>
                <tr><td colspan="4" class="text-center py-10 text-slate-400">Tidak ada catatan pelanggaran. 👍</td></tr>
                <?php else: ?>
                <?php foreach ($violations as $v):
                    $catColor = ['RINGAN'=>'yellow','SEDANG'=>'orange','BERAT'=>'red'][$v['category']] ?? 'gray';
                ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 text-slate-600"><?= date('d M Y', strtotime($v['date'])) ?></td>
                    <td class="px-5 py-3">
                        <p class="font-medium text-slate-800"><?= htmlspecialchars($v['type_name']) ?></p>
                        <?php if ($v['note']): ?><p class="text-xs text-slate-400"><?= htmlspecialchars($v['note']) ?></p><?php endif; ?>
                    </td>
                    <td class="px-5 py-3 text-center font-bold text-red-600"><?= $v['points'] ?></td>
                    <td class="px-5 py-3 text-center"><span class="bg-<?= $catColor ?>-100 text-<?= $catColor ?>-700 text-xs font-bold px-2.5 py-0.5 rounded-full"><?= $v['category'] ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
