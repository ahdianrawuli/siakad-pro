<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Kedisiplinan';
$pageSubtitle = $student ? htmlspecialchars($student['full_name']) : 'Pilih santri terlebih dahulu';
$pageBadge    = 'Total Poin: ' . ($totalPoints ?? 0) . ' / 100';
$pageBadgeIcon = 'fa-triangle-exclamation';
$infoItems    = [
    'Halaman ini menampilkan catatan pelanggaran santri.',
    'Setiap pelanggaran memiliki poin sesuai kategori: Ringan, Sedang, atau Berat.',
    'Jika total poin mencapai 100, akan ada tindakan dari pihak pesantren.',
    'Hubungi wali kelas atau BK jika ada data yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php $baseUrl = '/portal/orangtua/kedisiplinan'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-extrabold text-red-600"><?= count($violations) ?></div>
            <div class="text-sm text-slate-500 mt-1">Total Pelanggaran</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-extrabold text-orange-600"><?= $totalPoints ?></div>
            <div class="text-sm text-slate-500 mt-1">Total Poin</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <?php
            $level = $totalPoints < 30 ? ['Baik','green'] : ($totalPoints < 60 ? ['Perlu Perhatian','yellow'] : ['Kritis','red']);
            ?>
            <div class="text-2xl font-extrabold text-<?= $level[1] ?>-600"><?= $level[0] ?></div>
            <div class="text-sm text-slate-500 mt-1">Status</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-200 font-semibold text-slate-700">Riwayat Pelanggaran</div>
        <?php if (empty($violations)): ?>
        <p class="text-center text-slate-400 py-10">
            <i class="fa-solid fa-circle-check text-green-400 text-2xl mb-2 block"></i>
            Tidak ada catatan pelanggaran.
        </p>
        <?php else: ?>
        <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Pelanggaran</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Kategori</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Poin</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($violations as $v):
                    $catColor = ['RINGAN'=>'yellow','SEDANG'=>'orange','BERAT'=>'red'][$v['category']] ?? 'gray';
                ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 text-slate-600"><?= date('d M Y', strtotime($v['date'])) ?></td>
                    <td class="px-5 py-3 font-medium text-slate-800"><?= htmlspecialchars($v['violation_name']) ?></td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-<?= $catColor ?>-100 text-<?= $catColor ?>-700"><?= $v['category'] ?></span>
                    </td>
                    <td class="px-5 py-3 text-center font-bold text-red-600">-<?= $v['points'] ?></td>
                    <td class="px-5 py-3 text-slate-500 text-xs"><?= htmlspecialchars($v['note'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
