<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Riwayat Kesehatan';
$pageSubtitle = htmlspecialchars($student['full_name']);
$pageBadge    = 'Total Kunjungan: ' . count($records);
$pageBadgeIcon = 'fa-heart-pulse';
$infoItems    = [
    'Halaman ini menampilkan riwayat kunjungan Anda ke Poskestren.',
    'Setiap kunjungan mencatat keluhan, diagnosa, dan tindakan yang diberikan.',
    'Status: Rawat Jalan (berobat biasa), Rawat Inap (dirawat di poskestren), Rujuk RS (dirujuk ke rumah sakit).',
    'Hubungi petugas poskestren jika ada data yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <?php if (empty($records)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
            <i class="fa-solid fa-heart-pulse text-4xl mb-3 text-green-400 block"></i>
            <p class="font-medium text-slate-600">Alhamdulillah, tidak ada riwayat kunjungan ke poskestren.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
        <?php foreach ($records as $r):
            $statusColor = ['RAWAT_JALAN'=>'blue','RAWAT_INAP'=>'orange','RUJUK_RS'=>'red'][$r['status']] ?? 'gray';
            $statusLabel = ['RAWAT_JALAN'=>'Rawat Jalan','RAWAT_INAP'=>'Rawat Inap','RUJUK_RS'=>'Rujuk RS'][$r['status']] ?? $r['status'];
        ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="font-bold text-slate-800"><?= date('d M Y', strtotime($r['date'])) ?></p>
                        <p class="text-xs text-slate-400">Petugas: <?= htmlspecialchars($r['officer_name'] ?? '-') ?></p>
                    </div>
                    <span class="shrink-0 bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-700 text-xs font-bold px-2.5 py-1 rounded-full"><?= $statusLabel ?></span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">Keluhan</p>
                        <p class="text-slate-700"><?= htmlspecialchars($r['complaint']) ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">Diagnosa</p>
                        <p class="text-slate-700"><?= htmlspecialchars($r['diagnosis'] ?? '-') ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">Tindakan / Obat</p>
                        <p class="text-slate-700"><?= htmlspecialchars($r['treatment'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
