<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">Pelanggaran & Prestasi</h1>

    <?php \App\Core\Session::flash(); ?>

    <!-- Ringkasan Poin -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-red-50 border border-red-100 rounded-xl p-4">
            <p class="text-xs text-red-500 font-bold uppercase mb-1">Total Poin Pelanggaran</p>
            <p class="text-3xl font-extrabold text-red-700"><?= $totalPoints ?></p>
            <p class="text-xs text-red-400 mt-1">Batas maksimal: 100 poin</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
            <p class="text-xs text-yellow-600 font-bold uppercase mb-1">Total Prestasi</p>
            <p class="text-3xl font-extrabold text-yellow-700"><?= count($achievements) ?></p>
            <p class="text-xs text-yellow-500 mt-1">Penghargaan diraih</p>
        </div>
    </div>

    <!-- Prestasi -->
    <h2 class="font-bold text-gray-700 mb-3">🏆 Prestasi</h2>
    <?php if (empty($achievements)): ?>
        <div class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-400 mb-6">
            <p class="text-sm">Belum ada prestasi tercatat.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3 mb-6">
        <?php foreach ($achievements as $a):
            $levelColor = ['NASIONAL'=>'purple','PROVINSI'=>'blue','KABUPATEN'=>'green','KECAMATAN'=>'yellow','SEKOLAH'=>'gray'][$a['level']] ?? 'gray';
        ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-start gap-4">
                <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-800"><?= htmlspecialchars($a['title']) ?></h3>
                    <p class="text-xs text-gray-500 mt-0.5"><?= date('d M Y', strtotime($a['date'])) ?></p>
                    <?php if ($a['description']): ?><p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($a['description']) ?></p><?php endif; ?>
                </div>
                <span class="shrink-0 bg-<?= $levelColor ?>-100 text-<?= $levelColor ?>-700 text-xs font-bold px-2 py-1 rounded-full"><?= $a['level'] ?></span>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Pelanggaran -->
    <h2 class="font-bold text-gray-700 mb-3">⚠️ Catatan Pelanggaran</h2>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Pelanggaran</th>
                    <th class="px-4 py-3 text-center">Poin</th>
                    <th class="px-4 py-3 text-center">Kategori</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($violations)): ?>
                <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada catatan pelanggaran. 👍</td></tr>
                <?php else: ?>
                <?php foreach ($violations as $v):
                    $catColor = ['RINGAN'=>'yellow','SEDANG'=>'orange','BERAT'=>'red'][$v['category']] ?? 'gray';
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600"><?= date('d M Y', strtotime($v['date'])) ?></td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800"><?= htmlspecialchars($v['type_name']) ?></p>
                        <?php if ($v['note']): ?><p class="text-xs text-gray-400"><?= htmlspecialchars($v['note']) ?></p><?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-red-600"><?= $v['points'] ?></td>
                    <td class="px-4 py-3 text-center"><span class="bg-<?= $catColor ?>-100 text-<?= $catColor ?>-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= $v['category'] ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
