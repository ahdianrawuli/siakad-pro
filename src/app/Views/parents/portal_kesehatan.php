<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Kesehatan';
$pageSubtitle = $student ? htmlspecialchars($student['full_name']) : 'Pilih santri terlebih dahulu';
$pageBadge    = 'Kunjungan: ' . count($records ?? []);
$pageBadgeIcon = 'fa-heart-pulse';
$infoItems    = [
    'Halaman ini menampilkan riwayat kunjungan santri ke Poskestren.',
    'Status: Rawat Jalan, Rawat Inap, atau Rujuk RS.',
    'Hubungi petugas poskestren jika ada informasi yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php $baseUrl = '/portal/orangtua/kesehatan'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-200 font-semibold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-stethoscope text-pink-500"></i> Riwayat Kunjungan Poskestren
        </div>
        <?php if (empty($records)): ?>
        <p class="text-center text-slate-400 py-10">Tidak ada riwayat kunjungan kesehatan.</p>
        <?php else: ?>
        <div class="divide-y divide-slate-100">
            <?php foreach ($records as $r):
                $statusMap = ['RAWAT_JALAN'=>['Rawat Jalan','blue'],'RAWAT_INAP'=>['Rawat Inap','orange'],'RUJUK_RS'=>['Rujuk RS','red']];
                [$slbl,$scol] = $statusMap[$r['status']] ?? [$r['status'],'gray'];
            ?>
            <div class="px-5 py-4 hover:bg-slate-50 transition">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-slate-800"><?= date('d M Y', strtotime($r['date'])) ?></span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-<?= $scol ?>-100 text-<?= $scol ?>-700"><?= $slbl ?></span>
                    </div>
                    <span class="text-xs text-slate-400"><?= htmlspecialchars($r['officer_name'] ?? 'Petugas') ?></span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                    <div class="bg-slate-50 rounded-lg p-2"><span class="text-xs text-slate-400 block">Keluhan</span><?= htmlspecialchars($r['complaint']) ?></div>
                    <?php if ($r['diagnosis']): ?><div class="bg-slate-50 rounded-lg p-2"><span class="text-xs text-slate-400 block">Diagnosis</span><?= htmlspecialchars($r['diagnosis']) ?></div><?php endif; ?>
                    <?php if ($r['treatment']): ?><div class="bg-slate-50 rounded-lg p-2"><span class="text-xs text-slate-400 block">Tindakan</span><?= htmlspecialchars($r['treatment']) ?></div><?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
