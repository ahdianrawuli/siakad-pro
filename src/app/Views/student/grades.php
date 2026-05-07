<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Nilai Akademik</h1>
        <p class="text-sm text-gray-500">
            <?= htmlspecialchars($student['full_name']) ?> — Kelas <?= htmlspecialchars($student['class_name'] ?? '-') ?>
            <?php if ($activeYear): ?> | TA <?= htmlspecialchars($activeYear['name']) ?><?php endif; ?>
        </p>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Mata Pelajaran</th>
                    <th class="px-4 py-3 text-center">Tugas</th>
                    <th class="px-4 py-3 text-center">UTS</th>
                    <th class="px-4 py-3 text-center">UAS</th>
                    <th class="px-4 py-3 text-center">Nilai Akhir</th>
                    <th class="px-4 py-3 text-center">KKM</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($grades)): ?>
                <tr><td colspan="7" class="text-center py-10 text-gray-400">Nilai belum tersedia.</td></tr>
                <?php else: ?>
                <?php foreach ($grades as $g):
                    $final = $g['final_score'] ?? $g['average'] ?? null;
                    $kkm   = $g['kkm'] ?? 70;
                    $pass  = $final !== null && $final >= $kkm;
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($g['subject_name']) ?></td>
                    <td class="px-4 py-3 text-center text-gray-600"><?= $g['task_score'] ?? '-' ?></td>
                    <td class="px-4 py-3 text-center text-gray-600"><?= $g['mid_score'] ?? '-' ?></td>
                    <td class="px-4 py-3 text-center text-gray-600"><?= $g['final_exam_score'] ?? '-' ?></td>
                    <td class="px-4 py-3 text-center font-bold <?= $final !== null ? ($pass ? 'text-green-700' : 'text-red-600') : 'text-gray-400' ?>">
                        <?= $final ?? '-' ?>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-500"><?= $kkm ?></td>
                    <td class="px-4 py-3 text-center">
                        <?php if ($final !== null): ?>
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold <?= $pass ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= $pass ? 'Tuntas' : 'Remedial' ?>
                        </span>
                        <?php else: ?>
                        <span class="text-gray-400 text-xs">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
