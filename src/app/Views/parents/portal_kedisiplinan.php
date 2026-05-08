<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-triangle-exclamation text-red-500 mr-2"></i>Kedisiplinan</h1>
    </div>

    <?php $baseUrl = '/portal/orangtua/kedisiplinan'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-xl p-10 text-center text-gray-400">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border p-4 text-center">
            <div class="text-3xl font-bold text-red-600"><?= count($violations) ?></div>
            <div class="text-sm text-gray-500 mt-1">Total Pelanggaran</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center">
            <div class="text-3xl font-bold text-orange-600"><?= $totalPoints ?></div>
            <div class="text-sm text-gray-500 mt-1">Total Poin</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center">
            <?php
            $level = $totalPoints < 30 ? ['Baik','green'] : ($totalPoints < 60 ? ['Perlu Perhatian','yellow'] : ['Kritis','red']);
            ?>
            <div class="text-2xl font-bold text-<?= $level[1] ?>-600"><?= $level[0] ?></div>
            <div class="text-sm text-gray-500 mt-1">Status</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">Riwayat Pelanggaran</div>
        <?php if (empty($violations)): ?>
        <p class="text-center text-gray-400 py-10"><i class="fa-solid fa-check-circle text-green-400 text-2xl mb-2 block"></i>Tidak ada catatan pelanggaran.</p>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-left">Pelanggaran</th>
                    <th class="px-4 py-2 text-center">Kategori</th>
                    <th class="px-4 py-2 text-center">Poin</th>
                    <th class="px-4 py-2 text-left">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($violations as $v):
                    $catColor = ['RINGAN'=>'yellow','SEDANG'=>'orange','BERAT'=>'red'][$v['category']] ?? 'gray';
                ?>
                <tr>
                    <td class="px-4 py-2 text-gray-600"><?= date('d M Y', strtotime($v['date'])) ?></td>
                    <td class="px-4 py-2 font-medium text-gray-800"><?= htmlspecialchars($v['violation_name']) ?></td>
                    <td class="px-4 py-2 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-<?= $catColor ?>-100 text-<?= $catColor ?>-700"><?= $v['category'] ?></span>
                    </td>
                    <td class="px-4 py-2 text-center font-bold text-red-600">-<?= $v['points'] ?></td>
                    <td class="px-4 py-2 text-gray-500 text-xs"><?= htmlspecialchars($v['note'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
