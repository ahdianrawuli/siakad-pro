<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Riwayat Kesehatan</h1>
    <p class="text-sm text-gray-500 mb-6"><?= htmlspecialchars($student['full_name']) ?></p>

    <?php \App\Core\Session::flash(); ?>

    <?php if (empty($records)): ?>
        <div class="bg-white rounded-xl border border-gray-100 p-12 text-center text-gray-400">
            <i class="fa-solid fa-heart-pulse text-4xl mb-3 text-green-400"></i>
            <p class="font-medium text-gray-600">Alhamdulillah, tidak ada riwayat kunjungan ke poskestren.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
        <?php foreach ($records as $r):
            $statusColor = ['RAWAT_JALAN'=>'blue','RAWAT_INAP'=>'orange','RUJUK_RS'=>'red'][$r['status']] ?? 'gray';
            $statusLabel = ['RAWAT_JALAN'=>'Rawat Jalan','RAWAT_INAP'=>'Rawat Inap','RUJUK_RS'=>'Rujuk RS'][$r['status']] ?? $r['status'];
        ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="font-bold text-gray-800"><?= date('d M Y', strtotime($r['date'])) ?></p>
                        <p class="text-xs text-gray-400">Petugas: <?= htmlspecialchars($r['officer_name'] ?? '-') ?></p>
                    </div>
                    <span class="shrink-0 bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-700 text-xs font-bold px-2 py-1 rounded-full"><?= $statusLabel ?></span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase mb-1">Keluhan</p>
                        <p class="text-gray-700"><?= htmlspecialchars($r['complaint']) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase mb-1">Diagnosa</p>
                        <p class="text-gray-700"><?= htmlspecialchars($r['diagnosis'] ?? '-') ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase mb-1">Tindakan / Obat</p>
                        <p class="text-gray-700"><?= htmlspecialchars($r['treatment'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
