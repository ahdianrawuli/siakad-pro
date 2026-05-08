<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-heart-pulse text-pink-500 mr-2"></i>Kesehatan</h1>
    </div>

    <?php $baseUrl = '/portal/orangtua/kesehatan'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-xl p-10 text-center text-gray-400">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">Riwayat Kunjungan Poskestren</div>
        <?php if (empty($records)): ?>
        <p class="text-center text-gray-400 py-10">Tidak ada riwayat kunjungan kesehatan.</p>
        <?php else: ?>
        <div class="divide-y divide-gray-50">
            <?php foreach ($records as $r):
                $statusMap = ['RAWAT_JALAN'=>['Rawat Jalan','blue'],'RAWAT_INAP'=>['Rawat Inap','orange'],'RUJUK_RS'=>['Rujuk RS','red']];
                [$slbl,$scol] = $statusMap[$r['status']] ?? [$r['status'],'gray'];
            ?>
            <div class="px-5 py-4">
                <div class="flex items-start justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-800"><?= date('d M Y', strtotime($r['date'])) ?></span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-<?= $scol ?>-100 text-<?= $scol ?>-700"><?= $slbl ?></span>
                    </div>
                    <span class="text-xs text-gray-400"><?= htmlspecialchars($r['officer_name'] ?? 'Petugas') ?></span>
                </div>
                <p class="text-sm text-gray-700"><span class="font-medium">Keluhan:</span> <?= htmlspecialchars($r['complaint']) ?></p>
                <?php if ($r['diagnosis']): ?><p class="text-sm text-gray-600"><span class="font-medium">Diagnosis:</span> <?= htmlspecialchars($r['diagnosis']) ?></p><?php endif; ?>
                <?php if ($r['treatment']): ?><p class="text-sm text-gray-600"><span class="font-medium">Tindakan:</span> <?= htmlspecialchars($r['treatment']) ?></p><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
