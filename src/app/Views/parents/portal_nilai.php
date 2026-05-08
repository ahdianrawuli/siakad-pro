<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-chart-bar text-yellow-500 mr-2"></i>Nilai Akademik</h1>
        <?php if ($activeYear): ?><p class="text-sm text-gray-500">Tahun Ajaran: <?= htmlspecialchars($activeYear['name']) ?></p><?php endif; ?>
    </div>

    <?php $baseUrl = '/portal/orangtua/nilai'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-xl p-10 text-center text-gray-400">Akun belum terhubung ke data siswa.</div>
    <?php elseif (empty($grades)): ?>
    <div class="bg-white rounded-xl p-10 text-center text-gray-400">Nilai belum tersedia.</div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-center">Tugas</th>
                        <th class="px-4 py-3 text-center">UTS</th>
                        <th class="px-4 py-3 text-center">UAS</th>
                        <th class="px-4 py-3 text-center">Akhir</th>
                        <th class="px-4 py-3 text-center">KKM</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($grades as $g):
                        $final = $g['final_score'] ?? $g['average'] ?? null;
                        $kkm   = $g['kkm'] ?? 70;
                        $pass  = $final !== null && $final >= $kkm;
                    ?>
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($g['subject_name']) ?></td>
                        <td class="px-4 py-3 text-center text-gray-600"><?= $g['task_score'] ?? '-' ?></td>
                        <td class="px-4 py-3 text-center text-gray-600"><?= $g['mid_score'] ?? '-' ?></td>
                        <td class="px-4 py-3 text-center text-gray-600"><?= $g['final_exam_score'] ?? '-' ?></td>
                        <td class="px-4 py-3 text-center font-bold <?= $final !== null ? ($pass ? 'text-green-700' : 'text-red-600') : 'text-gray-400' ?>"><?= $final ?? '-' ?></td>
                        <td class="px-4 py-3 text-center text-gray-500"><?= $kkm ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($final !== null): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $pass ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= $pass ? 'Tuntas' : 'Remedial' ?>
                            </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
